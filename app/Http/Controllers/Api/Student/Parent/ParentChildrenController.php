<?php

namespace App\Http\Controllers\Api\Student\Parent;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Student\Assignment\ListAssignmentGradeRequest;
use App\Http\Requests\Api\Student\Attendance\ListAttendanceRequest;
use App\Http\Requests\Api\Student\Invoice\ListInvoiceRequest;
use App\Http\Requests\Api\Student\Schedule\ScheduleByDayRequest;
use App\Http\Resources\Api\Student\Assignment\StudentAssignmentGradeResource;
use App\Http\Resources\Api\Student\Attendance\StudentAttendanceResource;
use App\Http\Resources\Api\Student\Group\StudentGroupResource;
use App\Http\Resources\Api\Student\Invoice\StudentInvoiceResource;
use App\Http\Resources\Api\Student\Parent\ParentChildResource;
use App\Http\Resources\Api\Student\Schedule\StudentScheduleResource;
use App\Services\Api\Student\Assignment\StudentAssignmentService;
use App\Services\Api\Student\Attendance\StudentAttendanceService;
use App\Services\Api\Student\Group\StudentGroupService;
use App\Services\Api\Student\Invoice\StudentInvoiceService;
use App\Services\Api\Student\Parent\ParentService;
use App\Services\Api\Student\Schedule\StudentScheduleService;
use App\Support\Api\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class ParentChildrenController extends Controller
{
    public function __construct(
        private readonly ParentService $parentService,
        private readonly StudentAttendanceService $attendanceService,
        private readonly StudentInvoiceService $invoiceService,
        private readonly StudentAssignmentService $assignmentService,
        private readonly StudentGroupService $groupService,
        private readonly StudentScheduleService $scheduleService,
    ) {}

    public function index(): JsonResponse
    {
        $parent = auth('student_account')->user();
        $children = $parent->children()->with(['teacher', 'branch', 'profile'])->get();

        return ApiResponse::collection(
            resource: ParentChildResource::collection($children),
            message: 'قائمة الأبناء.',
        );
    }

    public function show(int $studentId): JsonResponse
    {
        $parent = auth('student_account')->user();
        $child = $parent->children()->find($studentId);

        if (! $child) {
            return ApiResponse::notFound('هذا الطالب غير مرتبط بحسابك.');
        }

        return ApiResponse::resource(
            resource: new ParentChildResource($child),
            message: 'بيانات الابن.',
        );
    }

    public function attendance(ListAttendanceRequest $request, int $studentId): JsonResponse
    {
        $fakeAccount = $this->parentService->prepareChildAccount(auth('student_account')->user(), $studentId);
        $paginator = $this->attendanceService->listForAccount($fakeAccount, $request->validated());

        return ApiResponse::paginated(
            paginator: $paginator,
            resourceClass: StudentAttendanceResource::class,
            message: 'سجل حضور الابن.',
        );
    }

    public function attendanceStats(ListAttendanceRequest $request, int $studentId): JsonResponse
    {
        $fakeAccount = $this->parentService->prepareChildAccount(auth('student_account')->user(), $studentId);
        $month = $request->input('month') ?: now()->format('Y-m');
        $stats = $this->attendanceService->statsForAccount($fakeAccount, $month);

        return ApiResponse::success(data: $stats, message: 'إحصائيات الحضور.', extra: ['month' => $month]);
    }

    public function invoices(ListInvoiceRequest $request, int $studentId): JsonResponse
    {
        $fakeAccount = $this->parentService->prepareChildAccount(auth('student_account')->user(), $studentId);
        $paginator = $this->invoiceService->listForAccount($fakeAccount, $request->validated());
        $stats = $this->invoiceService->statsForAccount($fakeAccount, $request->input('month') ?: now()->format('Y-m'));

        return ApiResponse::paginated(
            paginator: $paginator,
            resourceClass: StudentInvoiceResource::class,
            message: 'سجل فواتير الابن.',
            extra: ['stats' => $stats]
        );
    }

    public function invoiceStats(ListInvoiceRequest $request, int $studentId): JsonResponse
    {
        $fakeAccount = $this->parentService->prepareChildAccount(auth('student_account')->user(), $studentId);
        $month = $request->input('month') ?: now()->format('Y-m');
        $stats = $this->invoiceService->statsForAccount($fakeAccount, $month);

        return ApiResponse::success(data: $stats, message: 'إحصائيات الفواتير.', extra: ['month' => $month]);
    }

    public function assignments(ListAssignmentGradeRequest $request, int $studentId): JsonResponse
    {
        $fakeAccount = $this->parentService->prepareChildAccount(auth('student_account')->user(), $studentId);
        $paginator = $this->assignmentService->listForAccount($fakeAccount, $request->validated());
        $stats = $this->assignmentService->statsForAccount($fakeAccount, $request->input('month') ?: now()->format('Y-m'));

        return ApiResponse::paginated(
            paginator: $paginator,
            resourceClass: StudentAssignmentGradeResource::class,
            message: 'سجل تقييمات الابن.',
            extra: ['stats' => $stats]
        );
    }

    public function assignmentStats(ListAssignmentGradeRequest $request, int $studentId): JsonResponse
    {
        $fakeAccount = $this->parentService->prepareChildAccount(auth('student_account')->user(), $studentId);
        $month = $request->input('month') ?: now()->format('Y-m');
        $stats = $this->assignmentService->statsForAccount($fakeAccount, $month);

        return ApiResponse::success(data: $stats, message: 'إحصائيات التقييمات.', extra: ['month' => $month]);
    }

    public function schedules(ScheduleByDayRequest $request, int $studentId): JsonResponse
    {
        $fakeAccount = $this->parentService->prepareChildAccount(auth('student_account')->user(), $studentId);

        Log::info('Fake Account Debug', [
            'fake_account_id' => $fakeAccount->id,
            'has_students_relation' => $fakeAccount->relationLoaded('students'),
            'students_count' => $fakeAccount->students->count(),
            'student_ids' => $fakeAccount->students->pluck('id')->toArray(),
        ]);

        $data = $this->scheduleService->getDashboardForAccount($fakeAccount, $request->input('day'));

        return ApiResponse::success(
            data: [
                'selected_day' => $data['selected_day'],
                'teachers' => $data['teachers'],
                'schedules' => StudentScheduleResource::collection($data['schedules'])->resolve(),
            ],
            message: 'جدول الابن.',
        );
    }

    public function groups(int $studentId): JsonResponse
    {
        $fakeAccount = $this->parentService->prepareChildAccount(auth('student_account')->user(), $studentId);
        $groups = $this->groupService->allForAccount($fakeAccount);
        $stats = $this->groupService->statsForAccount($fakeAccount);

        return ApiResponse::collection(
            resource: StudentGroupResource::collection($groups),
            message: 'مجموعات الابن.',
            extra: ['stats' => $stats]
        );
    }
}
