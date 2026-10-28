<?php

namespace App\Http\Controllers\Api\Student\Profile;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\Student\Profile\StudentProfileResource;
use App\Services\Api\Student\Profile\StudentProfileService;
use App\Support\Api\ApiResponse;
use Illuminate\Http\JsonResponse;

class StudentProfileController extends Controller
{
    public function __construct(
        private readonly StudentProfileService $profileService,
    ) {}


    public function index(): JsonResponse
    {
        $account = auth('student_account')->user();

        $students = $this->profileService->allForAccount($account);

        return ApiResponse::collection(
            resource: StudentProfileResource::collection($students),
            message: 'بيانات جميع الطلاب المربوطين بحسابك.',
        );
    }


    public function show(int $studentId): JsonResponse
    {
        $account = auth('student_account')->user();

        $student = $this->profileService->findForAccount($account, $studentId);

        return ApiResponse::resource(
            resource: new StudentProfileResource($student),
            message: 'بيانات الطالب.',
        );
    }
}
