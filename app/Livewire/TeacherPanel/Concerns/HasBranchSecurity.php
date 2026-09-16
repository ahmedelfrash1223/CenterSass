<?php

namespace App\Livewire\TeacherPanel\Concerns;

trait HasBranchSecurity
{
    /**
     * فحص إن الفروع المختارة من الفروع المتاحة
     * @return bool صحيح لو كلها متاحة
     */
    protected function ensureBranchesAuthorized(array $branchIds, string $errorField = 'branch_ids'): bool
    {
        if (empty($branchIds)) {
            return true;
        }

        $user = auth('teacher')->user();
        $accessible = array_map('strval', $user->getAccessibleBranchIds());
        $unauthorized = array_diff($branchIds, $accessible);

        if (!empty($unauthorized)) {
            $this->addError($errorField, 'إحدى الفروع غير متاحة لك.');
            return false;
        }

        return true;
    }

    /**
     * فحص الفرع الواحد
     */
    protected function ensureBranchAccess(int $branchId, string $errorField = 'branch_id'): bool
    {
        $user = auth('teacher')->user();

        if (!$user->hasAccessToBranch($branchId)) {
            $this->addError($errorField, 'الفرع غير متاح لك.');
            return false;
        }

        return true;
    }

    /**
     * جلب الفروع المتاحة للـ view
     */
    protected function getAccessibleBranches(bool $activeOnly = true)
    {
        $user = auth('teacher')->user();
        $branchIds = $user->getAccessibleBranchIds();

        $query = \App\Models\Branch::whereIn('id', $branchIds);

        if ($activeOnly) {
            $query->where('is_active', 1);
        }

        return $query->orderBy('name')->get();
    }
}
