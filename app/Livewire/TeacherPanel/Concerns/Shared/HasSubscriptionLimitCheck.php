<?php

namespace App\Livewire\TeacherPanel\Concerns\Shared;

trait HasSubscriptionLimitCheck
{
    /**
     * فحص الحد + dispatch error
     * @return bool صحيح لو مسموح بالاستمرار
     */
    protected function checkLimitOrFail(int $featureKey, int $currentCount): bool
    {
        $user = auth('teacher')->user();
        $limit = $user->checkFeatureLimit($featureKey, $currentCount);

        if (!$limit['can']) {
            $this->dispatch('show-error-message', message: $limit['message']);
            return false;
        }

        return true;
    }

    /**
     * فحص الحد + redirect + flash
     */
    protected function checkLimitOrRedirect(int $featureKey, int $currentCount, string $flashKey, string $route): bool
    {
        $user = auth('teacher')->user();
        $limit = $user->checkFeatureLimit($featureKey, $currentCount);

        if (!$limit['can']) {
            session()->flash($flashKey, $limit['message']);
            $this->redirect(route($route), navigate: true);
            return false;
        }

        return true;
    }

    /**
     * جلب الحد الحالي لتمريره للـ view
     */
    protected function getLimitData(int $featureKey, int $currentCount): array
    {
        return auth('teacher')->user()->checkFeatureLimit($featureKey, $currentCount);
    }
}
