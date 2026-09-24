<?php

namespace App\Traits\SubscriptionRequest;

use App\Enums\Admin\SubscriptionRequestStatusEnum;

trait HasStatusPresentation
{
    public function getStatusLabelAttribute(): string
    {
        return SubscriptionRequestStatusEnum::tryFrom($this->status)?->label() ?? 'غير معروف';
    }

    public function getStatusColorAttribute(): string
    {
        return SubscriptionRequestStatusEnum::tryFrom($this->status)?->color() ?? 'secondary';
    }

    public function getStatusBadgeAttribute(): string
    {
        $color = $this->status_color;
        $label = $this->status_label;

        return "<span class='badge bg-{$color}-bg-opacity-10 text-{$color}'>{$label}</span>";
    }
}
