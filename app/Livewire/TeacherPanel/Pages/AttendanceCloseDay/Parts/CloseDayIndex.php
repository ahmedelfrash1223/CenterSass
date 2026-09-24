<?php

namespace App\Livewire\TeacherPanel\Pages\AttendanceCloseDay\Parts;

use App\Enums\Teacher\TeacherPermissionEnum;
use App\Models\Branch;
use App\Models\Group;
use App\Services\Teacher\AttendanceService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class CloseDayIndex extends Component
{
    public string $date = '';
    public string $branchFilter = '';
    public array $selectedGroupIds = [];

    public bool $confirmingCloseSelected = false;
    public bool $confirmingCloseAll = false;

    protected $listeners = ['refreshData' => '$refresh'];

    public function mount(): void
    {
        $this->date = now()->toDateString();

        $user = Auth::guard('teacher')->user();

        abort_unless(
            $user->hasPermission(TeacherPermissionEnum::ATTENDANCE_CLOSE_DAY),
            403
        );
    }

    public function updatedDate(): void
    {
        $this->selectedGroupIds = [];
        $this->confirmingCloseSelected = false;
        $this->confirmingCloseAll = false;
    }

    public function updatedBranchFilter(): void
    {
        $this->selectedGroupIds = [];
        $this->confirmingCloseSelected = false;
        $this->confirmingCloseAll = false;
    }


    public function selectAllGroups(): void
    {
        $service = app(AttendanceService::class);
        $date = Carbon::parse($this->date);

        $this->selectedGroupIds = $this->getGroups()
            ->filter(function ($group) use ($service, $date) {
                $isClosed = $service->isGroupClosed($group, $date);
                $isFinished = $service->groupSchedulesFinishedToday($group, $date);
                return !$isClosed && $isFinished;
            })
            ->pluck('id')
            ->map(fn($id) => (string) $id)
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

        $group = Group::find($groupId);

        if ($group && $service->isGroupClosed($group, $date)) {
            $this->dispatch('show-error-message', message: 'المجموعة مقفلة بالفعل.');
            return;
        }

        $id = (string) $groupId;

        if (in_array($id, $this->selectedGroupIds)) {
            $this->selectedGroupIds = array_values(
                array_diff($this->selectedGroupIds, [$id])
            );
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
        $this->confirmingCloseSelected = false;

        $date = Carbon::parse($this->date);

        $nonClosedIds = collect($this->selectedGroupIds)
            ->filter(function ($groupId) use ($service, $date) {
                $group = Group::find($groupId);
                return $group && !$service->isGroupClosed($group, $date);
            })
            ->values()
            ->toArray();

        if (empty($nonClosedIds)) {
            $this->dispatch('show-error-message', message: 'كل المجموعات المختارة مقفلة بالفعل.');
            return;
        }

        $this->executeClose($service, $nonClosedIds);
    }

    public function closeAllGroups(AttendanceService $service): void
    {
        $this->confirmingCloseAll = false;

        $date = Carbon::parse($this->date);

        $ids = $this->getGroups()
            ->filter(fn($group) => !$service->isGroupClosed($group, $date))
            ->pluck('id')
            ->map(fn($id) => (string) $id)
            ->toArray();

        if (empty($ids)) {
            $this->dispatch('show-error-message', message: 'كل المجموعات مقفلة بالفعل.');
            return;
        }

        $this->executeClose($service, $ids);
    }

    protected function executeClose(AttendanceService $service, array $groupIds): void
    {
        $user = Auth::guard('teacher')->user();

        if (!$user->hasPermission(TeacherPermissionEnum::ATTENDANCE_CLOSE_DAY)) {
            $this->dispatch('show-error-message', message: 'ليس لديك صلاحية لتقفيل اليوم.');
            return;
        }

        if (empty($groupIds)) {
            $this->dispatch('show-error-message', message: 'اختر مجموعة واحدة على الأقل.');
            return;
        }

        $date = Carbon::parse($this->date);
        $closed = 0;
        $totalAbsences = 0;
        $errors = [];

        foreach ($groupIds as $groupId) {
            $group = Group::find($groupId);

            if (!$group) continue;

            if (!$user->hasAccessToBranch((int) $group->branch_id)) continue;

            $result = $service->closeGroup($group, $date, $user);

            if ($result['success']) {
                $closed++;
                $totalAbsences += $result['created'];
            } else {
                $errors[] = $group->name . ': ' . $result['message'];
            }
        }

        $this->selectedGroupIds = [];

        if ($closed > 0) {
            $this->dispatch('show-success-message', message: "تم تقفيل {$closed} مجموعة، وإنشاء {$totalAbsences} سجل غياب.");
        }

        if (!empty($errors)) {
            $message = count($errors) > 3
                ? implode(' | ', array_slice($errors, 0, 3)) . ' ...'
                : implode(' | ', $errors);

            $this->dispatch('show-error-message', message: $message);
        }
    }

    protected function getGroups()
    {
        $user = Auth::guard('teacher')->user();
        $date = Carbon::parse($this->date);

        return app(AttendanceService::class)->getGroupsWithSchedulesOnDate(
            $user,
            $date,
            $this->branchFilter !== '' ? (int) $this->branchFilter : null
        );
    }

    public function render(AttendanceService $service)
    {
        $user = Auth::guard('teacher')->user();
        $accessibleBranchIds = $user->getAccessibleBranchIds();

        $date = Carbon::parse($this->date);
        $groups = $this->getGroups();

        $pendingGroups   = collect();
        $completedGroups = collect();
        $closedGroups    = collect();

        $groupsWithStats = $groups->map(function ($group) use ($service, $date, &$pendingGroups, &$completedGroups, &$closedGroups) {
            $group->stats       = $service->getGroupStats($group, $date);
            $group->is_finished = $service->groupSchedulesFinishedToday($group, $date);
            $group->is_closed   = $service->isGroupClosed($group, $date);

            if ($group->is_closed) {
                $closedGroups->push($group);
            } elseif ($group->is_finished) {
                $completedGroups->push($group);
            } else {
                $pendingGroups->push($group);
            }

            return $group;
        });

        $overallTotal   = $groupsWithStats->sum(fn($g) => $g->stats['total_students']);
        $overallPresent = $groupsWithStats->sum(fn($g) => $g->stats['present']);
        $overallLate    = $groupsWithStats->sum(fn($g) => $g->stats['late']);
        $overallAbsent  = $groupsWithStats->sum(fn($g) => $g->stats['absent']);

        return view('teacher-panel.pages.attendance-close-day.parts.close-day-index', [
            'groups'          => $groupsWithStats,
            'pendingGroups'   => $pendingGroups,
            'completedGroups' => $completedGroups,
            'closedGroups'    => $closedGroups,
            'branches'        => Branch::whereIn('id', $accessibleBranchIds)->orderBy('name')->get(),
            'overall' => [
                'total'   => $overallTotal,
                'present' => $overallPresent,
                'late'    => $overallLate,
                'absent'  => $overallAbsent,
            ],
        ]);
    }
}
