<?php

namespace App\Http\Controllers\Api\Student\Assignment;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Student\Assignment\ListAssignmentGradeRequest;
use App\Http\Resources\Api\Student\Assignment\StudentAssignmentGradeResource;
use App\Services\Api\Student\Assignment\StudentAssignmentService;
use App\Support\Api\ApiResponse;
use Illuminate\Http\JsonResponse;

class StudentAssignmentController extends Controller
{
    public function __construct(
        private readonly StudentAssignmentService $assignmentService,
    ) {}


    public function index(ListAssignmentGradeRequest $request): JsonResponse
    {
        $account = auth('student_account')->user();

        $paginator = $this->assignmentService->listForAccount($account, $request->validated());

        $stats = $this->assignmentService->statsForAccount(
            $account,
            $request->input('month') ?: now()->format('Y-m')
        );

        return ApiResponse::paginated(
            paginator: $paginator,
            resourceClass: StudentAssignmentGradeResource::class,
            message: 'سجل التقييمات.',
            extra: ['stats' => $stats]
        );
    }

    public function stats(ListAssignmentGradeRequest $request): JsonResponse
    {
        $account = auth('student_account')->user();
        $month = $request->input('month') ?: now()->format('Y-m');
        $stats = $this->assignmentService->statsForAccount($account, $month);

        return ApiResponse::success(
            data: $stats,
            message: 'إحصائيات التقييمات.',
            extra: ['month' => $month],
        );
    }
}
