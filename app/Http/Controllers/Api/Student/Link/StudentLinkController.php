<?php

namespace App\Http\Controllers\Api\Student\Link;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Student\Link\ScanStudentRequest;
use App\Http\Requests\Api\Student\Link\UnlinkStudentRequest;
use App\Http\Resources\Api\Student\Link\LinkedStudentResource;
use App\Services\Api\Student\Link\StudentLinkService;
use App\Support\Api\ApiResponse;
use Illuminate\Http\JsonResponse;

class StudentLinkController extends Controller
{
    public function __construct(
        private readonly StudentLinkService $linkService,
    ) {}


    public function scan(ScanStudentRequest $request): JsonResponse
    {
        $account = auth('student_account')->user();

        $result = $this->linkService->linkByBarcode($account, $request->input('barcode'));

        $message = $result['already_linked']
            ? 'هذا الطالب مربوط بالفعل بحسابك.'
            : 'تم ربط الطالب بنجاح.';

        return ApiResponse::resource(
            resource: new LinkedStudentResource($result['student']),
            message: $message,
            extra: ['already_linked' => $result['already_linked']],
        );
    }


    public function index(): JsonResponse
    {
        $account = auth('student_account')->user();
        $students = $this->linkService->linkedStudents($account);

        return ApiResponse::collection(
            resource: LinkedStudentResource::collection($students),
            message: 'قائمة الطلاب المربوطين.',
        );
    }


    public function unlink(UnlinkStudentRequest $request): JsonResponse
    {
        $account = auth('student_account')->user();

        $this->linkService->unlink($account, (int) $request->input('student_id'));

        return ApiResponse::success(message: 'تم فصل الطالب من حسابك.');
    }
}
