<?php

namespace App\Livewire\TeacherPanel\Pages\AttendanceCloseDay\Parts;

use App\Enums\Teacher\ScheduleCancellationReasonEnum;
use App\Enums\Teacher\TeacherPermissionEnum;
use App\Models\Attendance;
use App\Models\Group;
use App\Models\GroupSchedule;
use App\Services\Teacher\AttendanceService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class CloseDayShow extends Component
{
    public Group $group;

    public string $date = '';

    public bool $confirmingClose = false;

    public ?int $confirmingReopenId = null;

    public bool $isClosed = false;

    public ?int $cancellingScheduleId = null;

    public string $cancellationReason = '';

    public string $cancellationNotes = '';

    public function mount(Group $group): void
    {
        $user = Auth::guard('teacher')->user();

        abort_unless(
            $user && $user->hasPermission(TeacherPermissionEnum::ATTENDANCE_CLOSE_DAY),
            403
        );

        abort_unless($group->teacher_id === $user->getOwnerId(), 404);

        if (! $user->isOwner()) {
            abort_unless(
                in_array($group->branch_id, $user->getAccessibleBranchIds()),
                404
            );
        }

        $this->group = $group;
        $this->date = request('date') ?? now()->toDateString();

        $this->refreshIsClosed();
    }

    protected function authorizeAction(): void
    {
        $user = Auth::guard('teacher')->user();

        abort_unless(
            $user && $user->hasPermission(TeacherPermissionEnum::ATTENDANCE_CLOSE_DAY),
            403
        );

        abort_unless($this->group->teacher_id === $user->getOwnerId(), 404);

        if (! $user->isOwner()) {
            abort_unless(
                in_array($this->group->branch_id, $user->getAccessibleBranchIds()),
                404
            );
        }
    }

    protected function refreshIsClosed(): void
    {
        $this->isClosed = app(AttendanceService::class)
            ->isGroupClosed($this->group, Carbon::parse($this->date));
    }

    public function updatedDate(): void
    {
        $this->confirmingClose = false;
        $this->confirmingReopenId = null;
        $this->cancellingScheduleId = null;
        $this->refreshIsClosed();
    }

    public function confirmCloseGroup(): void
    {
        $this->authorizeAction();
        $this->refreshIsClosed();

        if ($this->isClosed) {
            $this->dispatch('show-error-message', message: 'المجموعة مقفلة بالفعل في هذا اليوم.');

            return;
        }

        $this->confirmingClose = true;
        $this->confirmingReopenId = null;
        $this->cancellingScheduleId = null;
    }

    public function cancelClose(): void
    {
        $this->confirmingClose = false;
        $this->confirmingReopenId = null;
        $this->cancellingScheduleId = null;
        $this->resetValidation();
    }

    public function closeGroup(AttendanceService $service): void
    {
        $this->authorizeAction();
        $this->confirmingClose = false;

        $result = $service->closeGroup(
            $this->group,
            Carbon::parse($this->date),
            Auth::guard('teacher')->user()
        );

        if ($result['success']) {
            $this->dispatch('show-success-message', message: $result['message']);
            $this->refreshIsClosed();
        } else {
            $this->dispatch('show-error-message', message: $result['message']);
        }
    }

    public function confirmReopenSchedule(int $scheduleId): void
    {
        $this->authorizeAction();

        $schedule = $this->getSchedule($scheduleId);

        if ($schedule->is_cancelled) {
            $this->dispatch('show-error-message', message: 'الحصة ملغاة وليست مقفلة بالغياب التلقائي.');

            return;
        }

        $this->confirmingReopenId = $scheduleId;
        $this->confirmingClose = false;
        $this->cancellingScheduleId = null;
    }

    public function reopenSchedule(int $scheduleId): void
    {
        $this->authorizeAction();

        $schedule = $this->getSchedule($scheduleId);

        if ($schedule->is_cancelled) {
            $this->dispatch('show-error-message', message: 'الحصة ملغاة بالفعل.');
            $this->confirmingReopenId = null;

            return;
        }

        $this->confirmingReopenId = null;

        $deleted = Attendance::query()
            ->where('schedule_id', $schedule->id)
            ->whereDate('attendance_date', $this->date)
            ->where('is_auto_closed', true)
            ->where('status', 'absent')
            ->delete();

        if ($deleted > 0) {
            $remainingAutoAbsences = Attendance::query()
                ->where('group_id', $this->group->id)
                ->whereDate('attendance_date', $this->date)
                ->where('is_auto_closed', true)
                ->exists();

            if (
                ! $remainingAutoAbsences
                && $this->group->last_closed_date
                && $this->group->last_closed_date->isSameDay(Carbon::parse($this->date))
            ) {
                $this->group->update(['last_closed_date' => null]);
            }

            $this->refreshIsClosed();

            $this->dispatch(
                'show-success-message',
                message: "تم فتح الحصة، وحذف {$deleted} سجل غياب تلقائي."
            );
        } else {
            $this->dispatch('show-error-message', message: 'لا يوجد غياب تلقائي لهذه الحصة.');
        }
    }

    public function startCancelSchedule(int $scheduleId): void
    {
        $this->authorizeAction();
        $this->refreshIsClosed();

        if ($this->isClosed) {
            $this->dispatch('show-error-message', message: 'افتح اليوم أولاً قبل إلغاء أي حصة.');

            return;
        }

        $schedule = $this->getSchedule($scheduleId);

        if ($schedule->is_cancelled) {
            $this->dispatch('show-error-message', message: 'الحصة ملغاة بالفعل.');

            return;
        }

        $this->cancellingScheduleId = $scheduleId;
        $this->cancellationReason = '';
        $this->cancellationNotes = '';
        $this->confirmingClose = false;
        $this->confirmingReopenId = null;
        $this->resetValidation();
    }

    public function cancelSchedule(AttendanceService $service): void
    {
        $this->authorizeAction();

        $this->validate([
            'cancellationReason' => ['required', 'string', 'in:'.implode(',', array_keys(ScheduleCancellationReasonEnum::options()))],
            'cancellationNotes' => ['nullable', 'string', 'max:1000'],
        ], [
            'cancellationReason.required' => 'اختر سبب إلغاء الحصة.',
            'cancellationReason.in' => 'سبب الإلغاء المحدد غير صحيح.',
            'cancellationNotes.max' => 'الملاحظات يجب ألا تتجاوز 1000 حرف.',
        ]);

        if (! $this->cancellingScheduleId) {
            $this->dispatch('show-error-message', message: 'اختر الحصة التي تريد إلغاءها.');

            return;
        }

        $reason = ScheduleCancellationReasonEnum::tryFrom($this->cancellationReason);

        if (! $reason) {
            $this->addError('cancellationReason', 'سبب الإلغاء المحدد غير صحيح.');

            return;
        }

        try {
            $result = $service->cancelSchedule(
                $this->group,
                $this->cancellingScheduleId,
                Carbon::parse($this->date),
                Auth::guard('teacher')->user(),
                $reason,
                $this->cancellationNotes !== '' ? $this->cancellationNotes : null
            );

            $this->cancellingScheduleId = null;
            $this->cancellationReason = '';
            $this->cancellationNotes = '';
            $this->resetValidation();

            $this->dispatch('show-success-message', message: $result['message']);
        } catch (ValidationException $exception) {
            $this->dispatch(
                'show-error-message',
                message: collect($exception->errors())->flatten()->first() ?? 'تعذر إلغاء الحصة.'
            );
        }
    }

    public function restoreCancelledSchedule(AttendanceService $service, int $scheduleId): void
    {
        $this->authorizeAction();

        try {
            $result = $service->restoreSchedule(
                $this->group,
                $scheduleId,
                Carbon::parse($this->date)
            );

            $this->dispatch('show-success-message', message: $result['message']);
        } catch (ValidationException $exception) {
            $this->dispatch(
                'show-error-message',
                message: collect($exception->errors())->flatten()->first() ?? 'تعذر إعادة الحصة.'
            );
        }
    }

    protected function getSchedule(int $scheduleId): GroupSchedule
    {
        return GroupSchedule::query()
            ->where('group_id', $this->group->id)
            ->whereKey($scheduleId)
            ->where('day', Carbon::parse($this->date)->format('l'))
            ->firstOrFail();
    }

    public function render(AttendanceService $service)
    {
        $date = Carbon::parse($this->date);
        $stats = $service->getGroupStats($this->group, $date);

        $students = $this->group->students()
            ->wherePivot('status', 'active')
            ->orderBy('name')
            ->get();

        $attendances = Attendance::query()
            ->where('group_id', $this->group->id)
            ->whereDate('attendance_date', $this->date)
            ->with(['schedule.subject'])
            ->get()
            ->groupBy('student_id');

        return view('teacher-panel.pages.attendance-close-day.parts.close-day-show', [
            'stats' => $stats,
            'students' => $students,
            'attendances' => $attendances,
            'isClosed' => $this->isClosed,
            'cancellationReasons' => ScheduleCancellationReasonEnum::options(),
        ]);
    }
}
