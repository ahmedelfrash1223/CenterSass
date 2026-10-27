<?php

namespace App\Http\Resources\Api\Student\Link;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubscribedTeacherResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'phone' => $this->phone,
            'email' => $this->email,
            'code' => $this->code,
            'subscription_started_at' => $this->subscription_started_at
                ? \Carbon\Carbon::parse($this->subscription_started_at)->toIso8601String()
                : null,
            'is_active' => (bool) $this->is_active,
        ];
    }
}
