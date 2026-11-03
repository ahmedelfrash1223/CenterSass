<?php

namespace App\Http\Controllers\Api\Parent;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Parent\ChildScheduleRequest;
use App\Http\Requests\Api\Student\Assignment\ListAssignmentGradeRequest;
use App\Http\Requests\Api\Student\Attendance\ListAttendanceRequest;
use App\Http\Requests\Api\Student\Invoice\ListInvoiceRequest;
use App\Http\Resources\Api\Parent\ParentChildResource;
use App\Http\Resources\Api\Student\Assignment\StudentAssignmentGradeResource;
use App\Http\Resources\Api\Student\Attendance\StudentAttendanceResource;
use App\Http\Resources\Api\Student\Group\StudentGroupResource;
use App\Http\Resources\Api\Student\Invoice\StudentInvoiceResource;
use App\Services\Api\Parent\ParentAssignmentService;
use App\Services\Api\Parent\ParentAttendanceService;
use App\Services\Api\Parent\ParentGroupService;
use App\Services\Api\Parent\ParentScheduleService;
use App\Services\Api\Parent\ParentService;
use App\Services\Api\Student\Invoice\StudentInvoiceService;
use App\Support\Api\ApiResponse;
use Illuminate\Http\JsonResponse;

class ParentChildrenController extends Controller
{
    public function __construct(
        private readonly ParentService $parentService,
        private readonly ParentScheduleService $scheduleService,
        private readonly ParentAttendanceService $attendanceService,
        private readonly ParentAssignmentService $assignmentService,
        private readonly ParentGroupService $groupService,
        private readonly StudentInvoiceService $invoiceService,
    ) {}


    public function index(): JsonResponse
    {
        $parent = auth('student_account')->user();
        $children = $this->parentService->getChildren($parent);

        return ApiResponse::collection(
            resource: ParentChildResource::collection($children),
            message: 'قائمة الأبناء.',
        );
    }


    public function show(int $studentId): JsonResponse
    {
        $parent = auth('student_account')->user();
        $child = $this->parentService->ensureChildBelongsToParent($parent, $studentId);

        return ApiResponse::resource(
            resource: new ParentChildResource($child),
            message: 'بيانات الابن.',
        );
    }


    public function schedules(ChildScheduleRequest $request, int $studentId): JsonResponse
    {
        $parent = auth('student_account')->user();
        $child = $this->parentService->ensureChildBelongsToParent($parent, $studentId);

        $data = $this->scheduleService->getChildSchedules($child, $request->input('day'));

        return ApiResponse::success(data: $data, message: 'جدول الابن.');
    }


    public function attendance(ListAttendanceRequest $request, int $studentId): JsonResponse
    {
        $parent = auth('student_account')->user();
        $child = $this->parentService->ensureChildBelongsToParent($parent, $studentId);

        $paginator = $this->attendanceService->listForChild($child, $request->validated());

        return ApiResponse::paginated(
            paginator: $paginator,
            resourceClass: StudentAttendanceResource::class,
            message: 'سجل حضور الابن.',
        );
    }


    public function attendanceStats(ListAttendanceRequest $request, int $studentId): JsonResponse
    {
        $parent = auth('student_account')->user();
        $child = $this->parentService->ensureChildBelongsToParent($parent, $studentId);

        $month = $request->input('month') ?: now()->format('Y-m');
        $stats = $this->attendanceService->statsForChild($child, $month);

        return ApiResponse::success(data: $stats, message: 'إحصائيات الحضور.', extra: ['month' => $month]);
    }

    public function invoices(ListInvoiceRequest $request, int $studentId): JsonResponse
    {
        $parent = auth('student_account')->user();
        $child = $this->parentService->ensureChildBelongsToParent($parent, $studentId);

        $fakeAccount = new \App\Models\StudentAccount();
        $fakeAccount->id = $parent->id;
        $fakeAccount->account_type = 'student';
        $fakeAccount->setRelation('students', collect([$child]));

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
        $parent = auth('student_account')->user();
        $child = $this->parentService->ensureChildBelongsToParent($parent, $studentId);

        $fakeAccount = new \App\Models\StudentAccount();
        $fakeAccount->id = $parent->id;
        $fakeAccount->account_type = 'student';
        $fakeAccount->setRelation('students', collect([$child]));

        $month = $request->input('month') ?: now()->format('Y-m');
        $stats = $this->invoiceService->statsForAccount($fakeAccount, $month);

        return ApiResponse::success(data: $stats, message: 'إحصائيات الفواتير.', extra: ['month' => $month]);
    }


    public function assignments(ListAssignmentGradeRequest $request, int $studentId): JsonResponse
    {
        $parent = auth('student_account')->user();
        $child = $this->parentService->ensureChildBelongsToParent($parent, $studentId);

        $paginator = $this->assignmentService->listForChild($child, $request->validated());
        $stats = $this->assignmentService->statsForChild($child, $request->input('month') ?: now()->format('Y-m'));

        return ApiResponse::paginated(
            paginator: $paginator,
            resourceClass: StudentAssignmentGradeResource::class,
            message: 'سجل تقييمات الابن.',
            extra: ['stats' => $stats]
        );
    }


    public function assignmentStats(ListAssignmentGradeRequest $request, int $studentId): JsonResponse
    {
        $parent = auth('student_account')->user();
        $child = $this->parentService->ensureChildBelongsToParent($parent, $studentId);

        $month = $request->input('month') ?: now()->format('Y-m');
        $stats = $this->assignmentService->statsForChild($child, $month);

        return ApiResponse::success(data: $stats, message: 'إحصائيات التقييمات.', extra: ['month' => $month]);
    }


    public function groups(int $studentId): JsonResponse
    {
        $parent = auth('student_account')->user();
        $child = $this->parentService->ensureChildBelongsToParent($parent, $studentId);

        $groups = $this->groupService->getChildGroups($child);
        $stats = $this->groupService->getChildGroupsStats($child);

        return ApiResponse::collection(
            resource: StudentGroupResource::collection($groups),
            message: 'مجموعات الابن.',
            extra: ['stats' => $stats]
        );
    }
}
