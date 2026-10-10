<?php

namespace App\Livewire\TeacherPanel\Pages\AttendanceCloseDay\Parts;

use App\Enums\Teacher\ScheduleCancellationReasonEnum;
use App\Enums\Teacher\TeacherPermissionEnum;
use App\Models\Branch;
use App\Models\Group;
use App\Services\Teacher\AttendanceService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class CloseDayIndex extends Component
{
    public string $date = '';

    public string $branchFilter = '';

    public array $selectedGroupIds = [];

    public bool $confirmingCloseSelected = false;

    public bool $confirmingCloseAll = false;

    public ?int $cancellingGroupId = null;

    public ?int $cancellingScheduleId = null;

    public string $cancellationReason = '';

    public string $cancellationNotes = '';

    protected $listeners = ['refreshData' => '$refresh'];

    public function mount(): void
    {
        $this->date = now()->toDateString();
        $user = Auth::guard('teacher')->user();

        abort_unless(
            $user && $user->hasPermission(TeacherPermissionEnum::ATTENDANCE_CLOSE_DAY),
            403
        );
    }

    public function updatedDate(): void
    {
        $this->resetSelections();
        $this->resetCancellationForm();
    }

    public function updatedBranchFilter(): void
    {
        $this->resetSelections();
        $this->resetCancellationForm();
    }

    protected function resetSelections(): void
    {
        $this->selectedGroupIds = [];
        $this->confirmingCloseSelected = false;
        $this->confirmingCloseAll = false;
    }

    protected function resetCancellationForm(): void
    {
        $this->cancellingGroupId = null;
        $this->cancellingScheduleId = null;
        $this->cancellationReason = '';
        $this->cancellationNotes = '';
        $this->resetValidation();
    }

    protected function authorizeAttendanceAction(): object
    {
        $user = Auth::guard('teacher')->user();

        abort_unless(
            $user && $user->hasPermission(TeacherPermissionEnum::ATTENDANCE_CLOSE_DAY),
            403
        );

        return $user;
    }

    protected function findAccessibleGroup(int $groupId): Group
    {
        $group = $this->getGroups()->firstWhere('id', $groupId);

        abort_unless($group, 404);

        return $group;
    }

    public function selectAllGroups(): void
    {
        $service = app(AttendanceService::class);
        $date = Carbon::parse($this->date);

        $this->selectedGroupIds = $this->getGroups()
            ->filter(function ($group) use ($service, $date) {
                $hasActiveSchedules = $group->schedules
                    ->where('is_cancelled', false)
                    ->isNotEmpty();

                return $hasActiveSchedules
                    && ! $service->isGroupClosed($group, $date)
                    && $service->groupSchedulesFinishedToday($group, $date);
            })
            ->pluck('id')
            ->map(fn ($id) => (string) $id)
            ->values()
            ->toArray();
    }

    public function deselectAllGroups(): void
    {
        $this->selectedGroupIds = [];
    }

    public function toggleGroupSelection(int $groupId): void
    {
        $service = app(AttendanceService::class);
        $date = Carbon::parse($this->date);

        $group = $this->findAccessibleGroup($groupId);

        if ($service->isGroupClosed($group, $date)) {
            $this->dispatch('show-error-message', message: 'المجموعة مقفلة بالفعل.');

            return;
        }

        if ($group->schedules->where('is_cancelled', false)->isEmpty()) {
            $this->dispatch('show-error-message', message: 'لا يمكن تقفيل مجموعة جميع حصصها ملغية.');

            return;
        }

        $id = (string) $groupId;

        if (in_array($id, $this->selectedGroupIds, true)) {
            $this->selectedGroupIds = array_values(array_diff($this->selectedGroupIds, [$id]));
        } else {
            $this->selectedGroupIds[] = $id;
        }
    }

    public function confirmCloseSelected(): void
    {
        if (empty($this->selectedGroupIds)) {
            $this->dispatch('show-error-message', message: 'اختر مجموعة واحدة على الأقل.');

            return;
        }

        $this->confirmingCloseSelected = true;
        $this->confirmingCloseAll = false;
    }

    public function cancelClose(): void
    {
        $this->confirmingCloseSelected = false;
        $this->confirmingCloseAll = false;
    }

    public function confirmCloseAll(): void
    {
        $this->confirmingCloseAll = true;
        $this->confirmingCloseSelected = false;
    }

    public function closeSelected(AttendanceService $service): void
    {
        $this->authorizeAttendanceAction();
        $this->confirmingCloseSelected = false;
        $date = Carbon::parse($this->date);

        $groupIds = collect($this->selectedGroupIds)
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->toArray();

        if (empty($groupIds)) {
            $this->dispatch('show-error-message', message: 'اختر مجموعة واحدة على الأقل.');

            return;
        }

        $this->executeClose($service, $groupIds, $date);
    }

    public function closeAllGroups(AttendanceService $service): void
    {
        $this->authorizeAttendanceAction();
        $this->confirmingCloseAll = false;
        $date = Carbon::parse($this->date);

        $ids = $this->getGroups()
            ->filter(function ($group) use ($service, $date) {
                return $group->schedules->where('is_cancelled', false)->isNotEmpty()
                    && ! $service->isGroupClosed($group, $date);
            })
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->values()
            ->toArray();

        if (empty($ids)) {
            $this->dispatch('show-error-message', message: 'لا توجد مجموعات يمكن تقفيلها. تحقق من الحصص الملغية والمجموعات المقفلة.');

            return;
        }

        $this->executeClose($service, $ids, $date);
    }

    protected function executeClose(AttendanceService $service, array $groupIds, Carbon $date): void
    {
        $user = $this->authorizeAttendanceAction();
        $availableGroups = $this->getGroups()->keyBy('id');
        $closed = 0;
        $totalAbsences = 0;
        $errors = [];

        foreach ($groupIds as $groupId) {
            $group = $availableGroups->get((int) $groupId);

            if (! $group) {
                continue;
            }

            if ($group->schedules->where('is_cancelled', false)->isEmpty()) {
                $errors[] = $group->name.': لا يمكن تقفيل مجموعة جميع حصصها ملغية.';

                continue;
            }

            if (! $user->hasAccessToBranch((int) $group->branch_id)) {
                $errors[] = $group->name.': ليس لديك صلاحية الوصول إلى هذا الفرع.';

                continue;
            }

            $result = $service->closeGroup($group, $date, $user);

            if ($result['success']) {
                $closed++;
                $totalAbsences += $result['created'];
            } else {
                $errors[] = $group->name.': '.$result['message'];
            }
        }

        $this->selectedGroupIds = [];

        if ($closed > 0) {
            $this->dispatch(
                'show-success-message',
                message: "تم تقفيل {$closed} مجموعة، وإنشاء {$totalAbsences} سجل غياب."
            );
        }

        if ($errors !== []) {
            $message = count($errors) > 3
                ? implode(' | ', array_slice($errors, 0, 3)).' ...'
                : implode(' | ', $errors);

            $this->dispatch('show-error-message', message: $message);
        }
    }

    public function startCancelSchedule(int $groupId, int $scheduleId): void
    {
        $this->authorizeAttendanceAction();
        $group = $this->findAccessibleGroup($groupId);
        $service = app(AttendanceService::class);
        $date = Carbon::parse($this->date);

        if ($service->isGroupClosed($group, $date)) {
            $this->dispatch('show-error-message', message: 'لا يمكن إلغاء حصة في مجموعة مقفلة. افتح اليوم أولاً.');

            return;
        }

        $schedule = $group->schedules->firstWhere('id', $scheduleId);

        if (! $schedule || $schedule->is_cancelled) {
            $this->dispatch('show-error-message', message: 'الحصة غير موجودة أو ملغاة بالفعل.');

            return;
        }

        $this->resetCancellationForm();
        $this->cancellingGroupId = $group->id;
        $this->cancellingScheduleId = $schedule->id;
    }

    public function cancelSchedule(AttendanceService $service): void
    {
        $user = $this->authorizeAttendanceAction();

        $this->validate([
            'cancellingGroupId' => ['required', 'integer'],
            'cancellingScheduleId' => ['required', 'integer'],
            'cancellationReason' => ['required', Rule::in(array_keys(ScheduleCancellationReasonEnum::options()))],
            'cancellationNotes' => ['nullable', 'string', 'max:1000'],
        ], [
            'cancellationReason.required' => 'اختر سبب إلغاء الحصة.',
            'cancellationReason.in' => 'سبب الإلغاء المحدد غير صحيح.',
            'cancellationNotes.max' => 'الملاحظات يجب ألا تتجاوز 1000 حرف.',
        ]);

        try {
            $group = $this->findAccessibleGroup((int) $this->cancellingGroupId);

            if (! $user->hasAccessToBranch((int) $group->branch_id)) {
                abort(403);
            }

            $service->cancelSchedule(
                $group,
                (int) $this->cancellingScheduleId,
                Carbon::parse($this->date),
                $user,
                ScheduleCancellationReasonEnum::from($this->cancellationReason),
                trim($this->cancellationNotes) !== '' ? trim($this->cancellationNotes) : null
            );

            $this->resetCancellationForm();
            $this->dispatch('show-success-message', message: 'تم إلغاء الحصة بنجاح.');
        } catch (ValidationException $exception) {
            $message = collect($exception->errors())->flatten()->first() ?? 'تعذر إلغاء الحصة.';
            $this->dispatch('show-error-message', message: $message);
        }
    }

    public function restoreCancelledSchedule(int $groupId, int $scheduleId, AttendanceService $service): void
    {
        $user = $this->authorizeAttendanceAction();

        try {
            $group = $this->findAccessibleGroup($groupId);

            if (! $user->hasAccessToBranch((int) $group->branch_id)) {
                abort(403);
            }

            $service->restoreSchedule($group, $scheduleId, Carbon::parse($this->date));

            $this->dispatch('show-success-message', message: 'تمت إعادة الحصة إلى الجدول بنجاح.');
        } catch (ValidationException $exception) {
            $message = collect($exception->errors())->flatten()->first() ?? 'تعذر إعادة الحصة.';
            $this->dispatch('show-error-message', message: $message);
        }
    }

    protected function getGroups()
    {
        $user = Auth::guard('teacher')->user();

        return app(AttendanceService::class)->getGroupsWithSchedulesOnDate(
            $user,
            Carbon::parse($this->date),
            $this->branchFilter !== '' ? (int) $this->branchFilter : null
        );
    }

    public function render(AttendanceService $service)
    {
        $user = Auth::guard('teacher')->user();
        $accessibleBranchIds = $user->getAccessibleBranchIds();
        $date = Carbon::parse($this->date);
        $groups = $this->getGroups();

        $pendingGroups = collect();
        $completedGroups = collect();
        $closedGroups = collect();
        $cancelledSchedules = collect();

        $groupsWithStats = $groups->map(function ($group) use (
            $service,
            $date,
            &$pendingGroups,
            &$completedGroups,
            &$closedGroups,
            &$cancelledSchedules
        ) {
            $group->stats = $service->getGroupStats($group, $date);
            $group->active_schedule_count = $group->schedules->where('is_cancelled', false)->count();
            $group->is_finished = $service->groupSchedulesFinishedToday($group, $date);
            $group->is_closed = $service->isGroupClosed($group, $date);

            foreach ($group->schedules->where('is_cancelled', true) as $schedule) {
                $cancelledSchedules->push([
                    'group' => $group,
                    'schedule' => $schedule,
                ]);
            }

            // المجموعة التي جميع حصصها ملغية تظهر في قسم الحصص الملغية فقط.
            if ($group->active_schedule_count === 0) {
                return $group;
            }

            if ($group->is_closed) {
                $closedGroups->push($group);
            } elseif ($group->is_finished) {
                $completedGroups->push($group);
            } else {
                $pendingGroups->push($group);
            }

            return $group;
        });

        $closableGroups = $groupsWithStats->filter(
            fn ($group) => $group->active_schedule_count > 0 && ! $group->is_closed
        );

        $overallTotal = $groupsWithStats->sum(fn ($group) => $group->stats['total_students']);
        $overallPresent = $groupsWithStats->sum(fn ($group) => $group->stats['present']);
        $overallLate = $groupsWithStats->sum(fn ($group) => $group->stats['late']);
        $overallAbsent = $groupsWithStats->sum(fn ($group) => $group->stats['absent']);

        return view('teacher-panel.pages.attendance-close-day.parts.close-day-index', [
            'groups' => $groupsWithStats,
            'pendingGroups' => $pendingGroups,
            'completedGroups' => $completedGroups,
            'closedGroups' => $closedGroups,
            'cancelledSchedules' => $cancelledSchedules,
            'closableGroups' => $closableGroups,
            'cancellationReasons' => ScheduleCancellationReasonEnum::options(),
            'branches' => Branch::whereIn('id', $accessibleBranchIds)->orderBy('name')->get(),
            'overall' => [
                'total' => $overallTotal,
                'present' => $overallPresent,
                'late' => $overallLate,
                'absent' => $overallAbsent,
            ],
        ]);
    }
}
