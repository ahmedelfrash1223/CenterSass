<?php

namespace App\Http\Resources\Api\Student\Auth;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AccountResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'account_type' => $this->account_type,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'avatar' => $this->avatar,
            'provider' => $this->provider?->value,
            'provider_label' => $this->provider?->label(),
            'status' => $this->status?->value,
            'status_label' => $this->status?->label(),
            'email_verified' => $this->isEmailVerified(),
            'phone_verified' => $this->isPhoneVerified(),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
