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
    }

    public function updatedBranchFilter(): void
    {
        $this->selectedGroupIds = [];
    }

    /**
     * Select every visible group (checkbox in table header).
     */
    public function selectAllGroups(): void
    {
        $this->selectedGroupIds = $this->getGroups()
            ->pluck('id')
            ->map(fn($id) => (string)$id)
            ->toArray();
    }

    /**
     * Deselect every group.
     */
    public function deselectAllGroups(): void
    {
        $this->selectedGroupIds = [];
    }

    /**
     * Toggle a single group selection.
     */
    public function toggleGroupSelection(int $groupId): void
    {
        $id = (string)$groupId;

        if (in_array($id, $this->selectedGroupIds)) {
            $this->selectedGroupIds = array_values(
                array_diff($this->selectedGroupIds, [$id])
            );
        } else {
            $this->selectedGroupIds[] = $id;
        }
    }

    /**
     * Open confirmation modal for closing selected groups.
     */
    public function confirmCloseSelected(): void
    {
        if (empty($this->selectedGroupIds)) {
            $this->dispatch('show-error-message', message: 'اختر مجموعة واحدة على الأقل.');
            return;
        }

        $this->confirmingCloseSelected = true;
    }

    /**
     * Open confirmation modal for closing all groups.
     */
    public function confirmCloseAll(): void
    {
        $this->confirmingCloseAll = true;
    }

    public function cancelClose(): void
    {
        $this->confirmingCloseSelected = false;
        $this->confirmingCloseAll = false;
    }

    /**
     * Execute close for selected groups.
     */
    public function closeSelected(AttendanceService $service): void
    {
        $this->confirmingCloseSelected = false;
        $this->executeClose($service, $this->selectedGroupIds);
    }

    /**
     * Execute close for all groups in scope.
     */
    public function closeAllGroups(AttendanceService $service): void
    {
        $this->confirmingCloseAll = false;

        $ids = $this->getGroups()
            ->pluck('id')
            ->map(fn($id) => (string)$id)
            ->toArray();

        $this->executeClose($service, $ids);
    }

    /**
     * Shared close logic for both bulk and per-group operations.
     */
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

            if (!$group) {
                continue;
            }

            if (!$user->hasAccessToBranch((int)$group->branch_id)) {
                continue;
            }

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
            $this->dispatch('show-success-message', message: "تم تقفيل {$closed} مجموعة، وإنشاء {$totalAbsences} سجل غياب."
            );
        }

        if (!empty($errors)) {
            $message = count($errors) > 3
                ? implode(' | ', array_slice($errors, 0, 3)) . ' ...'
                : implode(' | ', $errors);

            $this->dispatch('show-error-message', message: $message);
        }
    }

    /**
     * Groups scoped by the current user + branch filter.
     */
    protected function getGroups()
    {
        $user = Auth::guard('teacher')->user();
        $accessibleBranchIds = $user->getAccessibleBranchIds();

        return Group::query()
            ->with('branch')
            ->when(!$user->isOwner(), fn($q) => $q->whereIn('branch_id', $accessibleBranchIds))
            ->when($this->branchFilter !== '', fn($q) => $q->where('branch_id', $this->branchFilter))
            ->orderBy('name')
            ->get();
    }

    public function render(AttendanceService $service)
    {
        $user = Auth::guard('teacher')->user();
        $accessibleBranchIds = $user->getAccessibleBranchIds();

        $date = Carbon::parse($this->date);
        $groups = $this->getGroups();

        $groupsWithStats = $groups->map(function ($group) use ($service, $date) {
            $group->stats = $service->getGroupStats($group, $date);
            return $group;
        });

        $overallTotal = $groupsWithStats->sum(fn($g) => $g->stats['total_students']);
        $overallPresent = $groupsWithStats->sum(fn($g) => $g->stats['present']);
        $overallLate = $groupsWithStats->sum(fn($g) => $g->stats['late']);
        $overallAbsent = $groupsWithStats->sum(fn($g) => $g->stats['absent']);

        return view('teacher-panel.pages.attendance-close-day.parts.close-day-index', [
            'groups' => $groupsWithStats,
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
