<?php

namespace App\Http\Resources\Api\Student\Parent;

use App\Http\Resources\Api\Student\Profile\StudentProfileResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ParentChildResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $studentData = (new StudentProfileResource($this->resource))->resolve();

        return $studentData;
    }
}
