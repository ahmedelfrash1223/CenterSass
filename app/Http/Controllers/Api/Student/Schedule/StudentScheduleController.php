<?php

namespace App\Http\Controllers\Api\Student\Schedule;

use App\Http\Controllers\Controller;
use App\Services\Api\Student\Schedule\StudentScheduleService;
use App\Support\Api\ApiResponse;
use Illuminate\Http\JsonResponse;

class StudentScheduleController extends Controller
{
    public function __construct(
        private readonly StudentScheduleService $scheduleService,
    ) {}


    public function index(): JsonResponse
    {
        $account = auth('student_account')->user();

        $data = $this->scheduleService->groupedForAccount($account);

        return ApiResponse::success(
            data: $data,
            message: 'جدول الطالب الكامل.',
        );
    }


    public function today(): JsonResponse
    {
        $account = auth('student_account')->user();

        $data = $this->scheduleService->todayForAccount($account);

        return ApiResponse::success(
            data: $data,
            message: 'مواعيد اليوم.',
            extra: ['today' => now()->format('Y-m-d'), 'day' => now()->format('l')],
        );
    }
}
