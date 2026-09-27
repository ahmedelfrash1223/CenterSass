<?php

namespace App\Livewire\TeacherPanel\Concerns\Shared;

use App\Enums\Teacher\ExamQuestionTypeEnum;
use App\Enums\Teacher\GradeEnum;
use App\Models\Branch;
use App\Models\Group;
use App\Models\TeacherSubject;
use Illuminate\Support\Facades\Auth;

trait LoadsExamFormData
{
    public function loadExamFormData(): array
    {
        return [
            'branches'      => $this->examAccessibleBranches(),
            'grades'        => collect(GradeEnum::options())
                ->map(fn ($label, $value) => ['value' => $value, 'label' => $label])
                ->values()
                ->toArray(),
            'subjects'      => $this->examAccessibleSubjects(),
            'questionTypes' => collect(ExamQuestionTypeEnum::cases())
                ->mapWithKeys(fn ($case) => [$case->value => $case->label()])
                ->toArray(),
        ];
    }

    // =============================================
    // Branches
    // =============================================

    protected function examAccessibleBranches()
    {
        $user = Auth::guard('teacher')->user();
        $ids = $user->getAccessibleBranchIds();

        return Branch::whereIn('id', $ids)->orderBy('name')->get();
    }

    protected function examAccessibleBranchIds(): array
    {
        return $this->examAccessibleBranches()
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->toArray();
    }

    // =============================================
    // Grades
    // =============================================

    protected function examAccessibleGrades(): array
    {
        return array_keys(GradeEnum::options());
    }

    // =============================================
    // Subjects (TeacherSubject)
    // =============================================

    protected function examAccessibleSubjects()
    {
        $user = Auth::guard('teacher')->user();

        return TeacherSubject::query()
            ->where('teacher_id', $user->getOwnerId())
            ->where('is_active', true)
            ->orderBy('grade')
            ->orderBy('subject')
            ->get()
            ->map(fn ($ts) => [
                'id'            => (int) $ts->id,
                'grade'         => (string) $ts->grade,
                'subject'       => (string) $ts->subject,
                'subject_label' => (string) ($ts->subject ?? '—'),
                'grade_label'   => GradeEnum::tryFrom($ts->grade)?->label() ?? (string) $ts->grade,
            ])
            ->toArray();
    }

    protected function examAccessibleSubjectIds(): array
    {
        return collect($this->examAccessibleSubjects())
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->toArray();
    }

    // =============================================
    // Groups
    // =============================================

    protected function loadExamGroups(int $branchId, string $grade, int $subjectId): array
    {
        $user = Auth::guard('teacher')->user();

        return Group::query()
            ->where('teacher_id', $user->getOwnerId())
            ->where('branch_id', $branchId)
            ->where('grade', $grade)
            ->whereHas('schedules', fn ($q) => $q->where('subject_id', $subjectId))
            ->when(!$user->isOwner(), fn ($q) => $q->whereIn('branch_id', $user->getAccessibleBranchIds()))
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(fn ($g) => ['id' => (int) $g->id, 'name' => $g->name])
            ->toArray();
    }

    protected function examAccessibleGroups()
    {
        $user = Auth::guard('teacher')->user();

        return Group::query()
            ->where('teacher_id', $user->getOwnerId())
            ->when(!$user->isOwner(), fn ($q) => $q->whereIn('branch_id', $user->getAccessibleBranchIds()))
            ->get(['id', 'name', 'branch_id', 'grade']);
    }

    // =============================================
    // Authorization
    // =============================================

    protected function ensureExamTargetAuthorized(int $branchId, string $grade, int $subjectId, int $groupId): bool
    {
        $user = Auth::guard('teacher')->user();

        if (!$user->hasAccessToBranch($branchId)) {
            return false;
        }

        if (!in_array($grade, $this->examAccessibleGrades(), true)) {
            return false;
        }

        if (!in_array($subjectId, $this->examAccessibleSubjectIds(), true)) {
            return false;
        }

        return Group::where('id', $groupId)
            ->where('teacher_id', $user->getOwnerId())
            ->where('branch_id', $branchId)
            ->where('grade', $grade)
            ->exists();
    }
}
