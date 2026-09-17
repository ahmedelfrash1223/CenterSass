<?php

namespace App\Livewire\TeacherPanel\Pages\Attendance\Parts;

use App\Enums\Teacher\TeacherPermissionEnum;
use App\Models\Attendance;
use App\Models\Group;
use App\Models\Student;
use App\Livewire\TeacherPanel\Concerns\HasAttendanceScanning;
use App\Livewire\TeacherPanel\Concerns\HasManualAttendance;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class AttendanceScanner extends Component
{
    use HasAttendanceScanning;
    use HasManualAttendance;

    public string $mode = 'usb';
    public string $scanInput = '';

    public ?array $lastResult = null;

    public function mount(): void
    {
        $user = Auth::guard('teacher')->user();

        abort_unless(
            $user->hasPermission(TeacherPermissionEnum::ATTENDANCE_MANAGE),
            403
        );

        $this->dispatch('focus-usb-input');
    }

    // =============================================
    // MAIN SCAN HANDLER
    // =============================================

    public function handleScan(string $value = ''): void
    {
        $this->dispatch('focus-usb-input');

        $value = trim($value);

        if ($value === '') {
            return;
        }

        $this->lastResult = null;

        $user = Auth::guard('teacher')->user();

        if (!$user->hasPermission(TeacherPermissionEnum::ATTENDANCE_MANAGE)) {
            $this->lastResult = [
                'success' => false,
                'message' => 'ليس لديك صلاحية لتسجيل الحضور.',
            ];
            return;
        }

        $student = $this->findStudent($value);

        if (!$student) {
            $this->lastResult = [
                'success' => false,
                'message' => 'الكارت غير معروف أو غير مسجل.',
                'scanned' => $value,
            ];
            return;
        }

        // Ownership check
        if ($student->teacher_id !== $user->getOwnerId()) {
            $this->lastResult = [
                'success' => false,
                'message' => 'هذا الطالب لا ينتمي لحسابك.',
            ];
            return;
        }

        // Branch scope — safe null check
        if (empty($student->branch_id)) {
            $this->lastResult = [
                'success' => false,
                'message' => 'هذا الطالب غير مرتبط بأي فرع. رجاءً تعيين فرع للطالب أولاً.',
                'student_name' => $student->name,
            ];
            return;
        }

        if (!$user->hasAccessToBranch((int) $student->branch_id)) {
            $this->lastResult = [
                'success' => false,
                'message' => 'هذا الطالب لا ينتمي لفروعك المتاحة.',
                'student_name' => $student->name,
            ];
            return;
        }

        $this->lastResult = $this->registerAttendance($student, $user);
    }
    // =============================================
    // MANUAL MODE
    // =============================================

    public function registerManual(): void
    {
        if (empty($this->manualStudentIds)) {
            $this->dispatch('show-error-message', message: 'يرجى اختيار طالب واحد على الأقل.');
            return;
        }

        $user = Auth::guard('teacher')->user();

        if (!$user->hasPermission(TeacherPermissionEnum::ATTENDANCE_MANAGE)) {
            $this->dispatch('show-error-message', message: 'ليس لديك صلاحية لتسجيل الحضور.');
            return;
        }

        $registered = 0;
        $skipped = 0;
        $failed = 0;

        foreach ($this->manualStudentIds as $studentId) {
            $student = Student::find($studentId);

            if (!$student) {
                $failed++;
                continue;
            }

            if ($student->teacher_id !== $user->getOwnerId()) {
                $failed++;
                continue;
            }

            if (!$user->hasAccessToBranch($student->branch_id)) {
                $failed++;
                continue;
            }

            $result = $this->registerAttendance($student, $user);

            if ($result['success'] ?? false) {
                $registered++;
            } elseif (!empty($result['skipped'])) {
                $skipped++;
            } else {
                $failed++;
            }
        }

        $this->manualStudentIds = [];

        if ($registered > 0) {
            $this->lastResult = [
                'success' => true,
                'message' => "تم تسجيل حضور {$registered} طالب.",
                'student_name' => null,
                'registered' => [],
                'skipped' => [],
            ];

            $summary = "تم تسجيل: {$registered}";
            if ($skipped > 0) $summary .= " | مسجل بالفعل: {$skipped}";
            if ($failed > 0) $summary .= " | فشل: {$failed}";

            $this->dispatch('show-success-message', message: $summary);
        } else {
            $this->lastResult = [
                'success' => false,
                'message' => 'لم يتم تسجيل أي طالب. قد يكونوا مسجلين بالفعل أو لا توجد حصة جارية.',
            ];
        }
    }

    // =============================================
    // MODE SWITCHING
    // =============================================

    public function switchMode(string $mode): void
    {
        $this->mode = $mode;
        $this->scanInput = '';
        $this->lastResult = null;
        $this->manualGroupId = '';
        $this->manualStudentIds = [];

        if ($mode === 'usb') {
            $this->dispatch('focus-usb-input');
        }
    }

    public function clearResult(): void
    {
        $this->lastResult = null;
        $this->dispatch('focus-usb-input');
    }

    // =============================================
    // RENDER
    // =============================================

    public function render()
    {
        $user = Auth::guard('teacher')->user();
        $accessibleBranchIds = $user->getAccessibleBranchIds();

        $groups = Group::query()
            ->when(!$user->isOwner(), fn($q) => $q->whereIn('branch_id', $accessibleBranchIds))
            ->orderBy('name')
            ->get();

        // Manual students + today's registered IDs
        $manualStudents = collect();
        $alreadyRegisteredIds = [];

        if ($this->manualGroupId) {
            $manualStudents = Student::whereHas('activeGroups', function ($q) {
                $q->where('groups.id', $this->manualGroupId);
            })
                ->orderBy('name')
                ->get();

            $alreadyRegisteredIds = Attendance::where('group_id', $this->manualGroupId)
                ->where('attendance_date', now()->toDateString())
                ->pluck('student_id')
                ->unique()
                ->map(fn($id) => (string)$id)
                ->toArray();
        }

        $recentScans = Attendance::with(['student', 'group', 'schedule.subject'])
            ->where('teacher_id', $user->getOwnerId())
            ->where('attendance_date', now()->toDateString())
            ->when(!$user->isOwner(), fn($q) => $q->whereIn('branch_id', $accessibleBranchIds))
            ->orderByDesc('id')
            ->limit(10)
            ->get();

        return view('teacher-panel.pages.attendance.parts.attendance-scanner', [
            'groups' => $groups,
            'manualStudents' => $manualStudents,
            'alreadyRegisteredIds' => $alreadyRegisteredIds,
            'recentScans' => $recentScans,
        ]);
    }
}
