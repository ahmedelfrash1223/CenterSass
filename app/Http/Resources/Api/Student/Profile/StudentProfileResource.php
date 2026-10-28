<?php

namespace App\Http\Resources\Api\Student\Profile;

use App\Enums\Teacher\GradeEnum;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentProfileResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'name' => $this->name,
            'phone' => $this->phone,
            'grade' => $this->grade,
            'grade_label' => GradeEnum::tryFrom($this->grade)?->label() ?? $this->grade,
            'is_active' => (bool) $this->is_active,

            'teacher' => $this->teacher ? [
                'id' => $this->teacher->id,
                'name' => $this->teacher->name,
                'phone' => $this->teacher->phone,
                'email' => $this->teacher->email,
                'code' => $this->teacher->code,
            ] : null,

            'branch' => $this->branch ? [
                'id' => $this->branch->id,
                'name' => $this->branch->name,
                'address' => $this->branch->address,
                'phone' => $this->branch->phone,
            ] : null,

            'barcode' => [
                'digits' => $this->barcode_digits,
                'code' => $this->code,
                'value' => $this->barcode_value,
                'qr_value' => $this->qr_value,
            ],



            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
