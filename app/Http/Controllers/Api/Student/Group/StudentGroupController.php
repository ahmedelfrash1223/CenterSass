<?php

namespace App\Http\Controllers\Api\Student\Group;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\Student\Group\StudentGroupCollectionResource;
use App\Http\Resources\Api\Student\Group\StudentGroupResource;
use App\Services\Api\Student\Group\StudentGroupService;
use App\Support\Api\ApiResponse;
use Illuminate\Http\JsonResponse;

class StudentGroupController extends Controller
{
    public function __construct(
        private readonly StudentGroupService $groupService,
    ) {}


    public function index(): JsonResponse
    {
        $account = auth('student_account')->user();

        $groups = $this->groupService->allForAccount($account);
        $stats = $this->groupService->statsForAccount($account);

        $resource = (new StudentGroupCollectionResource($groups))
            ->additional($stats);

        return ApiResponse::collection(
            resource: $resource,
            message: 'قائمة المجموعات.',
        );
    }


    public function show(int $groupId): JsonResponse
    {
        $account = auth('student_account')->user();

        $group = $this->groupService->findForAccount($account, $groupId);

        return ApiResponse::resource(
            resource: new StudentGroupResource($group),
            message: 'بيانات المجموعة.',
        );
    }
}
