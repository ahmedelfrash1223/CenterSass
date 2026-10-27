<?php

namespace App\Http\Resources\Api\Student\Link;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LinkedStudentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'     => $this->id,
            'code'   => $this->code,
            'name'   => $this->name,
            'grade'  => $this->grade,
            'grade_label' => \App\Enums\Teacher\GradeEnum::tryFrom($this->grade)?->label() ?? $this->grade,
            'branch' => $this->branch ? [
                'id'   => $this->branch->id,
                'name' => $this->branch->name,
            ] : null,
            'teacher' => $this->teacher ? [
                'id'   => $this->teacher->id,
                'name' => $this->teacher->name,
                'phone' => $this->teacher->phone,
            ] : null,
            'linked_at' => $this->pivot?->linked_at
                ? \Carbon\Carbon::parse($this->pivot->linked_at)->toIso8601String()
                : null,
        ];
    }
}
