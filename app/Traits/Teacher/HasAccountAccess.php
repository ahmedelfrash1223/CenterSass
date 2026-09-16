<?php

namespace App\Traits\Teacher;

trait HasAccountAccess
{
    public function getOwnerAccountAttribute(): ?self
    {
        return $this->isOwner() ? $this : $this->owner;
    }

    public function getOwnerId(): int
    {
        if ($this->isOwner()) {
            return $this->id;
        }

        return $this->owner_id ?? $this->id;
    }

    public function getOwnerSubscriptionAttribute()
    {
        return $this->getOwnerAccountAttribute()?->activeSubscription;
    }
}
