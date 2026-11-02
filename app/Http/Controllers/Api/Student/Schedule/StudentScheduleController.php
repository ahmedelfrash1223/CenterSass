<?php

namespace App\Http\Controllers\Api\Student\Schedule;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Student\Schedule\ScheduleByDayRequest;
use App\Http\Resources\Api\Student\Schedule\StudentScheduleResource;
use App\Services\Api\Student\Schedule\StudentScheduleService;
use App\Support\Api\ApiResponse;
use Illuminate\Http\JsonResponse;

class StudentScheduleController extends Controller
{
    public function __construct(
        private readonly StudentScheduleService $scheduleService,
    ) {}

    public function index(ScheduleByDayRequest $request): JsonResponse
    {
        $account = auth('student_account')->user();

        $data = $this->scheduleService->getDashboardForAccount(
            $account,
            $request->input('day')
        );

        return ApiResponse::success(
            data: [
                'selected_day' => $data['selected_day'],
                'teachers' => $data['teachers'],
                'schedules' => StudentScheduleResource::collection($data['schedules'])->resolve(),
            ],
            message: 'شاشة المواعيد.',
        );
    }
}
