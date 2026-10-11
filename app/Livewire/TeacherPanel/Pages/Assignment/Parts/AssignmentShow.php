<?php

namespace App\Livewire\TeacherPanel\Pages\Assignment\Parts;

use App\Enums\Teacher\AssignmentGradeEnum;
use App\Enums\Teacher\TeacherPermissionEnum;
use App\Models\Assignment;
use App\Models\AssignmentStudent;
use App\Models\Student;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class AssignmentShow extends Component
{
    public Assignment $assignment;

    /** @var array<int, array{score:string, grade:string, notes:string}> */
    public array $grades = [];

    public ?int $editingStudentId = null;

    public function mount(Assignment $assignment): void
    {
        $user = Auth::guard('teacher')->user();

        abort_unless(
            $user->hasPermission(TeacherPermissionEnum::ASSIGNMENT_VIEW),
            403
        );

        abort_unless($assignment->teacher_id === $user->getOwnerId(), 404);

        if (! $user->isOwner()) {
            abort_unless(
                in_array($assignment->branch_id, $user->getAccessibleBranchIds()),
                404
            );
        }

        $this->assignment = $assignment->load(['group', 'branch']);

        $this->initializeGrades();
    }

    protected function studentsQuery()
    {
        $user = Auth::guard('teacher')->user();
        $accessibleBranchIds = $user->getAccessibleBranchIds();

        $query = Student::query()
            ->whereHas('activeGroups', fn ($q) => $q->where('groups.id', $this->assignment->group_id))
            ->orderBy('name');

        if (! $user->isOwner()) {
            empty($accessibleBranchIds)
                ? $query->whereRaw('1 = 0')
                : $query->whereIn('branch_id', $accessibleBranchIds);
        }

        return $query;
    }

    protected function initializeGrades(): void
    {
        $existing = AssignmentStudent::where('assignment_id', $this->assignment->id)
            ->get()
            ->keyBy('student_id');

        $students = $this->studentsQuery()->get();

        foreach ($students as $student) {
            if (! $existing->has($student->id)) {
                AssignmentStudent::create([
                    'assignment_id' => $this->assignment->id,
                    'student_id' => $student->id,
                ]);
            }
        }

        $grades = AssignmentStudent::where('assignment_id', $this->assignment->id)->get();

        $this->grades = [];

        foreach ($grades as $g) {
            $this->grades[$g->student_id] = [
                'score' => $g->score !== null ? (string) $g->score : '',
                'grade' => $g->grade?->value ?? '',
                'notes' => $g->notes ?? '',
            ];
        }
    }

    public function editStudent(int $studentId): void
    {
        $user = Auth::guard('teacher')->user();

        if (! $user->hasPermission(TeacherPermissionEnum::ASSIGNMENT_GRADE)) {
            $this->dispatch('show-error-message', message: 'ليس لديك صلاحية لتقييم الطلاب.');

            return;
        }

        if (! $this->canAccessStudent($studentId)) {
            $this->dispatch('show-error-message', message: 'ليس لديك صلاحية على هذا الطالب.');

            return;
        }

        $this->editingStudentId = $studentId;
    }

    public function cancelEdit(): void
    {
        $this->editingStudentId = null;
    }

    public function autoGrade(int $studentId): void
    {
        $row = $this->grades[$studentId] ?? null;

        if (! $row || $row['score'] === '') {
            return;
        }

        $percentage = ((float) $row['score'] / (float) $this->assignment->max_score) * 100;

        $this->grades[$studentId]['grade'] = AssignmentGradeEnum::fromPercentage($percentage)->value;
    }

    public function saveGrade(int $studentId): void
    {
        $user = Auth::guard('teacher')->user();

        if (! $user->hasPermission(TeacherPermissionEnum::ASSIGNMENT_GRADE)) {
            $this->dispatch('show-error-message', message: 'ليس لديك صلاحية لتقييم الطلاب.');

            return;
        }

        if (! $this->canAccessStudent($studentId)) {
            $this->dispatch('show-error-message', message: 'ليس لديك صلاحية على هذا الطالب.');

            return;
        }

        $row = $this->grades[$studentId] ?? null;

        if (! $row) {
            $this->dispatch('show-error-message', message: 'بيانات الطالب غير متاحة.');

            return;
        }

        $max = (float) $this->assignment->max_score;
        $score = $row['score'] === '' ? null : (float) $row['score'];

        if ($score !== null && ($score < 0 || $score > $max)) {
            $this->dispatch('show-error-message', message: "الدرجة لازم تكون بين 0 و {$max}.");

            return;
        }

        DB::transaction(function () use ($studentId, $score, $row) {
            $pivot = AssignmentStudent::firstOrCreate([
                'assignment_id' => $this->assignment->id,
                'student_id' => $studentId,
            ]);

            $gradeValue = $row['grade'] ?: null;

            if ($score !== null && ! $gradeValue) {
                $percentage = ($score / (float) $this->assignment->max_score) * 100;
                $gradeValue = AssignmentGradeEnum::fromPercentage($percentage)->value;
            }

            $pivot->update([
                'score' => $score,
                'grade' => $gradeValue,
                'notes' => $row['notes'] ?: null,
                'graded_by' => Auth::guard('teacher')->id(),
                'graded_at' => now(),
            ]);

            $this->grades[$studentId]['grade'] = $gradeValue ?? '';
        });

        $this->editingStudentId = null;
        $this->dispatch('show-success-message', message: 'تم حفظ التقييم.');
    }

    protected function canAccessStudent(int $studentId): bool
    {
        $user = Auth::guard('teacher')->user();

        $student = Student::find($studentId);

        if (! $student) {
            return false;
        }

        if ($student->teacher_id !== $user->getOwnerId()) {
            return false;
        }

        if (! $user->isOwner() && ! in_array($student->branch_id, $user->getAccessibleBranchIds())) {
            return false;
        }

        return $student->activeGroups()->where('groups.id', $this->assignment->group_id)->exists();
    }

    public function render()
    {
        $user = Auth::guard('teacher')->user();

        $students = $this->studentsQuery()->get();

        $totalStudents = $students->count();

        $graded = AssignmentStudent::where('assignment_id', $this->assignment->id)
            ->whereNotNull('score')
            ->count();

        $average = AssignmentStudent::where('assignment_id', $this->assignment->id)
            ->whereNotNull('score')
            ->avg('score');

        return view('teacher-panel.pages.assignment.parts.assignment-show', [
            'students' => $students,
            'grades' => $this->grades,
            'canGrade' => $user->hasPermission(TeacherPermissionEnum::ASSIGNMENT_GRADE),
            'gradeOptions' => AssignmentGradeEnum::options(),
            'totalStudents' => $totalStudents,
            'gradedCount' => $graded,
            'average' => $average ? round((float) $average, 2) : null,
        ]);
    }
}
