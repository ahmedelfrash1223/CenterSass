<?php

namespace App\Http\Controllers\Api\Student\Home;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\Student\Home\HomeDashboardResource;
use App\Services\Api\Student\Home\HomeDashboardService;
use App\Support\Api\ApiResponse;
use Illuminate\Http\JsonResponse;

class HomeController extends Controller
{
    public function __construct(
        private readonly HomeDashboardService $dashboardService,
    ) {}

    public function index(): JsonResponse
    {
        $account = auth('student_account')->user();

        $data = $this->dashboardService->getDashboardData($account);

        return ApiResponse::resource(
            resource: new HomeDashboardResource($data),
            message: 'بيانات الصفحة الرئيسية.',
        );
    }
}
