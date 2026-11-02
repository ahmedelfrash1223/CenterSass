<?php

namespace App\Http\Resources\Api\Student\Home;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NextClassResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'schedule_id' => $this->resource['schedule_id'] ?? null,
            'group_name' => $this->resource['group_name'] ?? null,
            'subject_name' => $this->resource['subject_name'] ?? null,
            'teacher_name' => $this->resource['teacher_name'] ?? null,
            'day' => $this->resource['day'] ?? null,
            'day_label' => $this->resource['day_label'] ?? null,
            'start_time' => $this->resource['start_time'] ?? null,
            'end_time' => $this->resource['end_time'] ?? null,
            'time_status' => $this->resource['time_status'] ?? null,
            'time_status_label' => $this->resource['time_status_label'] ?? null,
            'show_scan_button' => $this->resource['show_scan_button'] ?? false,
        ];
    }
}
