<?php

namespace App\Http\Controllers\Api\Student\Attendance;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Student\Attendance\ListAttendanceRequest;
use App\Http\Resources\Api\Student\Attendance\StudentAttendanceResource;
use App\Services\Api\Student\Attendance\StudentAttendanceService;
use App\Support\Api\ApiResponse;
use Illuminate\Http\JsonResponse;

class StudentAttendanceController extends Controller
{
    public function __construct(
        private readonly StudentAttendanceService $attendanceService,
    ) {}


    public function index(ListAttendanceRequest $request): JsonResponse
    {
        $account = auth('student_account')->user();

        $paginator = $this->attendanceService->listForAccount($account, $request->validated());

        return ApiResponse::paginated(
            paginator: $paginator,
            resourceClass: StudentAttendanceResource::class,
            message: 'سجل الحضور.',
        );
    }


    public function stats(ListAttendanceRequest $request): JsonResponse
    {
        $account = auth('student_account')->user();

        $stats = $this->attendanceService->statsForAccount(
            $account,
            $request->input('month')
        );

        return ApiResponse::success(
            data: $stats,
            message: 'إحصائيات الحضور.',
            extra: ['month' => $request->input('month', now()->format('Y-m'))],
        );
    }
}
