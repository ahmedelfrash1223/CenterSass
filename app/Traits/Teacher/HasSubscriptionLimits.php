<?php

namespace App\Traits\Teacher;

trait HasSubscriptionLimits
{
    public function getSubscriptionFeatureValue(int $featureKey): ?int
    {
        $owner = $this->isOwner() ? $this : $this->owner;

        if (! $owner) {
            return null;
        }

        $subscription = $owner->activeSubscription;

        if (! $subscription) {
            return null;
        }

        $feature = $subscription->features()
            ->where('feature_key', $featureKey)
            ->first();

        if (! $feature) {
            return null;
        }

        $value = $feature->value;

        if (is_array($value)) {
            return (int) ($value['value'] ?? $value[0] ?? 0);
        }

        return (int) $value;
    }

    public function hasActiveSubscription(): bool
    {
        $owner = $this->isOwner() ? $this : $this->owner;

        if (! $owner) {
            return false;
        }

        return $owner->activeSubscription()->exists();
    }

    public function checkFeatureLimit(int $featureKey, int $currentCount): array
    {
        $owner = $this->isOwner() ? $this : $this->owner;

        if (! $owner || ! $owner->hasActiveSubscription()) {
            return [
                'can' => false,
                'current' => $currentCount,
                'max' => 0,
                'message' => 'لا يوجد اشتراك نشط. يرجى تجديد الاشتراك.',
            ];
        }

        $max = $this->getSubscriptionFeatureValue($featureKey);

        if ($max === null || $max === 0) {
            return [
                'can' => true,
                'current' => $currentCount,
                'max' => null,
                'message' => '',
            ];
        }

        if ($currentCount >= $max) {
            return [
                'can' => false,
                'current' => $currentCount,
                'max' => $max,
                'message' => "لقد وصلت للحد الأقصى ({$currentCount}/{$max}). يرجى ترقية الاشتراك.",
            ];
        }

        return [
            'can' => true,
            'current' => $currentCount,
            'max' => $max,
            'message' => '',
        ];
    }
}
