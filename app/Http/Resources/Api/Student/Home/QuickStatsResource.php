<?php

namespace App\Http\Resources\Api\Student\Home;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuickStatsResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'attendance' => [
                'percentage' => $this->resource['attendance']['percentage'] ?? 0,
                'status' => $this->resource['attendance']['status'] ?? 'warning',
            ],
            'invoices' => [
                'total_remaining' => $this->resource['invoices']['total_remaining'] ?? 0,
                'currency' => $this->resource['invoices']['currency'] ?? 'ج.م',
                'has_dues' => $this->resource['invoices']['has_dues'] ?? false,
            ],
            'assignments' => [
                'average_percentage' => $this->resource['assignments']['average_percentage'] ?? 0,
                'graded_count' => $this->resource['assignments']['graded_count'] ?? 0,
            ],
        ];
    }
}
