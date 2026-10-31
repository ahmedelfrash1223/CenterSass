<?php

namespace App\Http\Resources\Api\Student\Group;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class StudentGroupCollectionResource extends ResourceCollection
{
    public $collects = StudentGroupResource::class;

    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection,
            'meta' => [
                'total_groups' => $this->resource->count(),
                'total_teachers' => $this->additional['total_teachers'] ?? 0,
                'total_subjects' => $this->additional['total_subjects'] ?? 0,
                'total_schedules' => $this->additional['total_schedules'] ?? 0,
            ],
        ];
    }
}
