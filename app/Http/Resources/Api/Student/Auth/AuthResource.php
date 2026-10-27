<?php

namespace App\Http\Resources\Api\Student\Auth;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuthResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'token' => $this->resource['token'],
            'token_type' => 'Bearer',
            'account' => new AccountResource($this->resource['account']),
            'linked_students_count' => $this->resource['account']->students()->count(),
        ];
    }
}
