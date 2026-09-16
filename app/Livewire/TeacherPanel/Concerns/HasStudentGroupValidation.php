<?php

namespace App\Livewire\TeacherPanel\Concerns;

use App\Models\Group;

trait HasStudentGroupValidation
{
    /**
     * Ensure every selected group belongs to accessible branches.
     */
    protected function ensureGroupsAuthorized(array $groupIds): bool
    {
        if (empty($groupIds)) {
            return true;
        }

        $user = auth('teacher')->user();
        $accessibleBranchIds = $user->getAccessibleBranchIds();

        $hasInvalid = Group::whereIn('id', $groupIds)
            ->when(!$user->isOwner(), fn($q) => $q->whereNotIn('branch_id', $accessibleBranchIds))
            ->exists();

        if ($hasInvalid) {
            $this->addError('group_ids', 'إحدى المجموعات غير متاحة لك.');
            return false;
        }

        return true;
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
            $alreadyJoinedIds = \App\Models\Student::find($excludeStudentId)
                ?->activeGroups()
                ->pluck('groups.id')
                ->map(fn($id) => (string) $id)
                ->toArray() ?? [];
        }

        foreach ($groups as $group) {
            if ($group->max_students <= 0) {
                continue;
            }

            $count = $group->students_count;

            // For updates: don't count the student against capacity of already-joined groups
            if (in_array((string) $group->id, $alreadyJoinedIds)) {
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
