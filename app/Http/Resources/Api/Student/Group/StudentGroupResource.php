<?php

namespace App\Http\Resources\Api\Student\Group;

use App\Enums\Teacher\GradeEnum;
use App\Enums\Teacher\GroupStatusEnum;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentGroupResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,

            'grade' => $this->grade,
            'grade_label' => GradeEnum::tryFrom($this->grade)?->label() ?? $this->grade,

            'status' => $this->status,
            'status_label' => GroupStatusEnum::tryFrom($this->status)?->label() ?? $this->status,
            'status_color' => GroupStatusEnum::tryFrom($this->status)?->color() ?? 'secondary',

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

            'joined_at' => $this->pivot?->joined_at
                ? \Carbon\Carbon::parse($this->pivot->joined_at)->toIso8601String()
                : null,

            'students_count' => $this->whenCounted('students'),

            'has_pricing' => (bool) $this->has_pricing,
            'pricing_type' => $this->pricing_type,
            'pricing_amount' => (float) $this->pricing_amount,

            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
