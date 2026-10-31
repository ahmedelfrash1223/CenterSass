<?php

namespace App\Http\Controllers\Api\Student\Profile;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\Student\Profile\StudentIdCardResource;
use App\Support\Api\ApiResponse;
use Illuminate\Http\JsonResponse;

class StudentIdCardController extends Controller
{
    public function index(): JsonResponse
    {
        $account = auth('student_account')->user();

        $account->load(['students.teacher']);

        return ApiResponse::resource(
            resource: new StudentIdCardResource($account),
            message: 'بيانات كارت الطالب.',
        );
    }
}
