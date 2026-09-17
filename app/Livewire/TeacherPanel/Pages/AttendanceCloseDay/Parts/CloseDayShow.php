<?php

namespace App\Livewire\TeacherPanel\Pages\AttendanceCloseDay\Parts;

use App\Enums\Teacher\TeacherPermissionEnum;
use App\Models\Attendance;
use App\Models\Group;
use App\Services\Teacher\AttendanceService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class CloseDayShow extends Component
{
    public Group $group;
    public string $date = '';

    public bool $confirmingClose = false;
    public ?int $confirmingReopenId = null;

    public function mount(Group $group): void
    {
        $user = Auth::guard('teacher')->user();

        abort_unless(
            $user->hasPermission(TeacherPermissionEnum::ATTENDANCE_CLOSE_DAY),
            403
        );

        abort_unless(
            $group->teacher_id === $user->getOwnerId(),
            404
        );

        if (!$user->isOwner()) {
            abort_unless(
                in_array($group->branch_id, $user->getAccessibleBranchIds()),
                404
            );
        }

        $this->group = $group;
        $this->date = request('date') ?? now()->toDateString();
    }

    /**
     * Open confirmation modal for closing the group.
     */
    public function confirmCloseGroup(): void
    {
        $user = Auth::guard('teacher')->user();

        if (!$user->hasPermission(TeacherPermissionEnum::ATTENDANCE_CLOSE_DAY)) {
            $this->dispatch('show-error-message', message: 'ليس لديك صلاحية لتقفيل اليوم.');
            return;
        }

        $this->confirmingClose = true;
    }

    public function cancelClose(): void
    {
        $this->confirmingClose = false;
        $this->confirmingReopenId = null;
    }

    /**
     * Close attendance for this group.
     */
    public function closeGroup(AttendanceService $service): void
    {
        $this->confirmingClose = false;

        $user = Auth::guard('teacher')->user();

        if (!$user->hasPermission(TeacherPermissionEnum::ATTENDANCE_CLOSE_DAY)) {
            $this->dispatch('show-error-message', message: 'ليس لديك صلاحية لتقفيل اليوم.');
            return;
        }

        $result = $service->closeGroup($this->group, Carbon::parse($this->date), $user);

        if ($result['success']) {
            $this->dispatch('show-success-message', message: $result['message']);
        } else {
            $this->dispatch('show-error-message', message: $result['message']);
        }
    }

    /**
     * Open confirmation modal for reopening a schedule.
     */
    public function confirmReopenSchedule(int $scheduleId): void
    {
        $user = Auth::guard('teacher')->user();

        if (!$user->hasPermission(TeacherPermissionEnum::ATTENDANCE_CLOSE_DAY)) {
            $this->dispatch('show-error-message', message: 'ليس لديك صلاحية.');
            return;
        }

        $this->confirmingReopenId = $scheduleId;
    }

    /**
     * Reopen a schedule — deletes all auto-closed "absent" records.
     */
    public function reopenSchedule(int $scheduleId): void
    {
        $this->confirmingReopenId = null;

        $user = Auth::guard('teacher')->user();

        if (!$user->hasPermission(TeacherPermissionEnum::ATTENDANCE_CLOSE_DAY)) {
            $this->dispatch('show-error-message', message: 'ليس لديك صلاحية.');
            return;
        }

        $deleted = Attendance::where('schedule_id', $scheduleId)
            ->where('attendance_date', $this->date)
            ->where('is_auto_closed', true)
            ->where('status', 'absent')
            ->delete();

        if ($deleted > 0) {
            $this->dispatch('show-success-message', message: "تم فتح الحصة، وحذف {$deleted} سجل غياب.");
        } else {
            $this->dispatch('show-error-message', message: 'لا يوجد غياب تلقائي لهذه الحصة.');
        }
    }

    public function render(AttendanceService $service)
    {
        $date = Carbon::parse($this->date);

        $stats = $service->getGroupStats($this->group, $date);

        $students = $this->group->students()
            ->wherePivot('status', 'active')
            ->orderBy('name')
            ->get();

        $attendances = Attendance::where('group_id', $this->group->id)
            ->where('attendance_date', $this->date)
            ->with(['schedule'])
            ->get()
            ->groupBy('student_id');

        return view('teacher-panel.pages.attendance-close-day.parts.close-day-show', [
            'stats' => $stats,
            'students' => $students,
            'attendances' => $attendances,
        ]);
    }
}
