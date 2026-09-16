<?php

namespace App\Livewire\TeacherPanel\Pages\Group\Parts;

use App\Enums\Teacher\GradeEnum;
use App\Enums\Teacher\TeacherPermissionEnum;
use App\Models\Branch;
use App\Models\Group;
use App\Models\Student;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class GroupShow extends Component
{
    public Group $group;

    // Add students section
    public string $searchStudent = '';
    public string $searchBranch = '';
    public string $searchGrade = '';
    public array $selectedStudentIds = [];
    public bool $showAddStudent = false;

    // Delete confirmation for student removal
    public ?int $deleteStudentId = null;

    public function mount(Group $group): void
    {
        $user = Auth::guard('teacher')->user();

        // Group must belong to owner account
        abort_unless($group->teacher_id === $user->getOwnerId(), 404);

        // Non-owner: group branch must be in accessible branches
        if (!$user->isOwner()) {
            abort_unless(
                in_array($group->branch_id, $user->getAccessibleBranchIds()),
                404
            );
        }

        $this->group = $group->load([
            'branch',
            'creator',
            'schedules.subject',
            'schedules.teacher',
            'students' => fn($q) => $q->wherePivot('status', 'active'),
        ]);
    }

    // =============================================
    // ADD STUDENTS
    // =============================================

    public function toggleAddStudent(): void
    {
        $this->showAddStudent = !$this->showAddStudent;
        $this->resetAddStudentForm();
    }

    private function resetAddStudentForm(): void
    {
        $this->searchStudent = '';
        $this->searchBranch = '';
        $this->searchGrade = '';
        $this->selectedStudentIds = [];
    }

    public function updatedSearchStudent(): void { $this->selectedStudentIds = []; }
    public function updatedSearchBranch(): void { $this->selectedStudentIds = []; }
    public function updatedSearchGrade(): void { $this->selectedStudentIds = []; }

    public function toggleStudentSelection(int $studentId): void
    {
        if (in_array($studentId, $this->selectedStudentIds)) {
            $this->selectedStudentIds = array_values(
                array_diff($this->selectedStudentIds, [$studentId])
            );
        } else {
            $this->selectedStudentIds[] = $studentId;
        }
    }

    public function selectAllVisible(): void
    {
        $visibleIds = $this->getAvailableStudents()->pluck('id')->toArray();
        $this->selectedStudentIds = array_values(array_unique(
            array_merge($this->selectedStudentIds, $visibleIds)
        ));
    }

    public function deselectAll(): void
    {
        $this->selectedStudentIds = [];
    }

    public function addStudents(): void
    {
        $user = Auth::guard('teacher')->user();

        if (!$user->hasPermission(TeacherPermissionEnum::GROUP_UPDATE)) {
            $this->dispatch('show-error-message', message: 'ليس لديك صلاحية لتعديل المجموعة.');
            return;
        }

        if (empty($this->selectedStudentIds)) {
            $this->dispatch('show-error-message', message: 'يرجى اختيار طالب واحد على الأقل.');
            return;
        }

        // Respect group capacity
        if ($this->group->max_students > 0) {
            $currentCount = $this->group->students()->wherePivot('status', 'active')->count();
            $remaining = $this->group->max_students - $currentCount;

            if ($remaining <= 0) {
                $this->dispatch('show-error-message', message: 'المجموعة ممتلئة.');
                return;
            }

            if (count($this->selectedStudentIds) > $remaining) {
                $this->dispatch('show-error-message', message: "لا يمكن إضافة أكثر من {$remaining} طالب. المجموعة ممتلئة.");
                return;
            }
        }

        $ownerId = $user->getOwnerId();
        $students = Student::where('teacher_id', $ownerId)
            ->whereIn('id', $this->selectedStudentIds)
            ->get();

        $added = 0;
        $failed = 0;

        foreach ($students as $student) {
            $this->group->addStudent($student) ? $added++ : $failed++;
        }

        $this->refreshGroupStudents();
        $this->resetAddStudentForm();
        $this->showAddStudent = false;

        match (true) {
            $added > 0 && $failed === 0 => $this->dispatch('show-success-message', message: "تم إضافة {$added} طالب للمجموعة بنجاح."),
            $added > 0 && $failed > 0 => $this->dispatch('show-success-message', message: "تم إضافة {$added} طالب. وفشل إضافة {$failed} طالب."),
            default => $this->dispatch('show-error-message', message: 'لم يتم إضافة أي طالب. قد يكونوا موجودين بالفعل.'),
        };
    }

    private function getAvailableStudents()
    {
        $user = Auth::guard('teacher')->user();
        $ownerId = $user->getOwnerId();

        $query = Student::where('teacher_id', $ownerId)
            ->when($this->searchStudent !== '', fn($q) => $q->where(function ($sub) {
                $sub->where('name', 'like', "%{$this->searchStudent}%")
                    ->orWhere('email', 'like', "%{$this->searchStudent}%")
                    ->orWhere('username', 'like', "%{$this->searchStudent}%")
                    ->orWhere('phone', 'like', "%{$this->searchStudent}%");
            }))
            ->when($this->searchBranch !== '', fn($q) => $q->where('branch_id', $this->searchBranch))
            ->when($this->searchGrade !== '', fn($q) => $q->where('grade', $this->searchGrade))
            ->whereDoesntHave('groups', function ($q) {
                $q->where('group_id', $this->group->id)
                    ->where('group_student.status', 'active');
            })
            ->orderBy('name')
            ->limit(50);

        // Scope non-owner to accessible branches
        if (!$user->isOwner()) {
            $accessibleBranchIds = $user->getAccessibleBranchIds();
            empty($accessibleBranchIds)
                ? $query->whereRaw('1 = 0')
                : $query->whereIn('branch_id', $accessibleBranchIds);
        }

        return $query->get();
    }

    // =============================================
    // REMOVE STUDENT
    // =============================================

    public function confirmRemoveStudent(int $studentId): void
    {
        if (!Auth::guard('teacher')->user()->hasPermission(TeacherPermissionEnum::GROUP_UPDATE)) {
            $this->dispatch('show-error-message', message: 'ليس لديك صلاحية لتعديل المجموعة.');
            return;
        }

        $this->deleteStudentId = $studentId;
    }

    public function cancelRemoveStudent(): void
    {
        $this->deleteStudentId = null;
    }

    public function removeStudent(): void
    {
        if (!$this->deleteStudentId) {
            return;
        }

        $user = Auth::guard('teacher')->user();

        if (!$user->hasPermission(TeacherPermissionEnum::GROUP_UPDATE)) {
            $this->dispatch('show-error-message', message: 'ليس لديك صلاحية لتعديل المجموعة.');
            $this->deleteStudentId = null;
            return;
        }

        $student = Student::findOrFail($this->deleteStudentId);

        // Only remove students within the same owner account
        abort_unless($student->teacher_id === $user->getOwnerId(), 404);

        $this->group->removeStudent($student);

        $this->deleteStudentId = null;
        $this->refreshGroupStudents();

        $this->dispatch('show-success-message', message: 'تم إزالة الطالب من المجموعة.');
    }

    private function refreshGroupStudents(): void
    {
        $this->group->refresh();
        $this->group->load([
            'students' => fn($q) => $q->wherePivot('status', 'active'),
        ]);
    }

    public function render()
    {
        $user = Auth::guard('teacher')->user();
        $accessibleBranchIds = $user->getAccessibleBranchIds();

        return view('teacher-panel.pages.group.parts.group-show', [
            'availableStudents' => $this->getAvailableStudents(),
            'activeStudentsCount' => $this->group->students->count(),
            'canUpdate' => $user->hasPermission(TeacherPermissionEnum::GROUP_UPDATE),
            'branches' => Branch::whereIn('id', $accessibleBranchIds)->orderBy('name')->get(),
            'grades' => GradeEnum::options(),
        ]);
    }
}
