<?php

namespace App\Http\Controllers\Api\Student\Guardian;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\Student\Guardian\StudentGuardianResource;
use App\Services\Api\Student\Guardian\StudentGuardianService;
use App\Support\Api\ApiResponse;
use Illuminate\Http\JsonResponse;

class StudentGuardianController extends Controller
{
    public function __construct(
        private readonly StudentGuardianService $guardianService,
    ) {}


    public function index(): JsonResponse
    {
        $account = auth('student_account')->user();

        $students = $this->guardianService->allForAccount($account);

        return ApiResponse::collection(
            resource: StudentGuardianResource::collection($students),
            message: 'بيانات ولي الأمر لكل المدرسين المشترك معهم.',
        );
    }


    public function show(int $studentId): JsonResponse
    {
        $account = auth('student_account')->user();

        $student = $this->guardianService->findForAccount($account, $studentId);

        return ApiResponse::resource(
            resource: new StudentGuardianResource($student),
            message: 'بيانات ولي الأمر.',
        );
    }
}
