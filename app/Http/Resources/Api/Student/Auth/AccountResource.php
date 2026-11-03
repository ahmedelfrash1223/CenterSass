<?php

namespace App\Http\Resources\Api\Student\Auth;

use App\Enums\Student\AccountTypeEnum;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AccountResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $accountTypeEnum = $this->account_type instanceof AccountTypeEnum
            ? $this->account_type
            : AccountTypeEnum::tryFrom($this->account_type ?? '');

        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'account_type' => $accountTypeEnum?->value ?? $this->account_type,
            'account_type_label' => $accountTypeEnum?->label(),
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'avatar' => $this->avatar,
            'provider' => $this->provider?->value ?? $this->provider,
            'provider_label' => $this->provider?->label(),
            'status' => $this->status?->value ?? $this->status,
            'status_label' => $this->status?->label(),
            'email_verified' => $this->isEmailVerified(),
            'phone_verified' => $this->isPhoneVerified(),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
