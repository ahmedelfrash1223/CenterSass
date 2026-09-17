<?php

namespace App\Livewire\TeacherPanel\Concerns;

use App\Models\Group;
use App\Models\Student;

trait HasStudentGroupValidation
{
    /**
     * Ensure every selected group:
     *   - belongs to the same owner account
     *   - for non-owners: belongs to an accessible branch
     */
    protected function ensureGroupsAuthorized(array $groupIds): bool
    {
        if (empty($groupIds)) {
            return true;
        }

        $user = auth('teacher')->user();
        $ownerId = $user->getOwnerId();
        $accessibleBranchIds = $user->getAccessibleBranchIds();

        $validCount = Group::whereIn('id', $groupIds)
            ->where('teacher_id', $ownerId)
            ->when(!$user->isOwner(), function ($q) use ($accessibleBranchIds) {
                $q->whereIn('branch_id', $accessibleBranchIds);
            })
            ->count();

        if ($validCount === count($groupIds)) {
            return true;
        }

        $this->addError('group_ids', 'إحدى المجموعات غير متاحة لك.');
        return false;
    }

    /**
     * Validate each selected group's capacity.
     * If $excludeStudentId is passed, discount that student from the count (for updates).
     */
    protected function validateGroupCapacity(?int $excludeStudentId = null): array
    {
        $errors = [];

        if (empty($this->group_ids)) {
            return $errors;
        }

        $groups = Group::whereIn('id', $this->group_ids)
            ->withCount(['students' => fn($q) => $q->where('group_student.status', 'active')])
            ->get();

        // Groups the student already belongs to (for update case)
        $alreadyJoinedIds = [];
        if ($excludeStudentId) {
            $alreadyJoinedIds = Student::find($excludeStudentId)
                ?->activeGroups()
                ->pluck('groups.id')
                ->map(fn($id) => (string)$id)
                ->toArray() ?? [];
        }

        foreach ($groups as $group) {
            if ($group->max_students <= 0) {
                continue;
            }

            $count = $group->students_count;

            if (in_array((string)$group->id, $alreadyJoinedIds)) {
                $count--;
            }

            if ($count >= $group->max_students) {
                $errors[] = "المجموعة \"{$group->name}\" ممتلئة ({$group->students_count}/{$group->max_students}).";
            }
        }

        return $errors;
    }

    /**
     * Apply capacity errors to the form and dispatch a flash error.
     */
    protected function failWithCapacityErrors(array $errors): void
    {
        foreach ($errors as $error) {
            $this->addError('group_ids', $error);
        }

        $this->dispatch('show-error-message', message: implode(' ', $errors));
    }
}
