<?php

namespace App\Http\Resources\Api\Parent;

use App\Enums\Teacher\GradeEnum;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ParentChildResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'code' => $this->code,
            'grade' => $this->grade,
            'grade_label' => GradeEnum::tryFrom($this->grade)?->label() ?? $this->grade,
            'is_active' => (bool) $this->is_active,
            'teacher' => $this->teacher ? [
                'id' => $this->teacher->id,
                'name' => $this->teacher->name,
            ] : null,
            'branch' => $this->branch ? [
                'id' => $this->branch->id,
                'name' => $this->branch->name,
            ] : null,
        ];
    }
}
