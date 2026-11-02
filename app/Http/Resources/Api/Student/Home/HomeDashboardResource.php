<?php

namespace App\Http\Resources\Api\Student\Home;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HomeDashboardResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'student' => [
                'name' => $this->resource['student']['name'] ?? null,
                'email' => $this->resource['student']['email'] ?? null,
                'avatar' => $this->resource['student']['avatar'] ?? null,
                'subscribed_teachers_count' => $this->resource['student']['subscribed_teachers_count'] ?? 0,
            ],
            'next_class' => $this->resource['next_class']
                ? new NextClassResource($this->resource['next_class'])
                : null,
            'quick_stats' => new QuickStatsResource($this->resource['quick_stats']),
        ];
    }
}
