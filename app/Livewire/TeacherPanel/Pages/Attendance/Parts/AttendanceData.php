<?php

namespace App\Livewire\TeacherPanel\Pages\Attendance\Parts;

use App\Enums\Teacher\TeacherPermissionEnum;
use App\Models\Attendance;
use App\Models\Branch;
use App\Models\Group;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class AttendanceData extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public string $search = '';
    public string $dateFilter = '';
    public string $branchFilter = '';
    public string $groupFilter = '';
    public string $statusFilter = '';

    public ?int $editingId = null;
    public string $editStatus = '';
    public string $editNotes = '';

    protected $listeners = ['refreshData' => '$refresh'];

    public function mount(): void
    {
        $user = Auth::guard('teacher')->user();

        abort_unless(
            $user->hasPermission(TeacherPermissionEnum::ATTENDANCE_VIEW),
            403
        );

        $this->dateFilter = now()->toDateString();
    }

    public function updatingSearch(): void { $this->resetPage(); }
    public function updatingDateFilter(): void { $this->resetPage(); }
    public function updatingBranchFilter(): void { $this->resetPage(); }
    public function updatingGroupFilter(): void { $this->resetPage(); }
    public function updatingStatusFilter(): void { $this->resetPage(); }

    public function clearFilters(): void
    {
        $this->search = '';
        $this->dateFilter = now()->toDateString();
        $this->branchFilter = '';
        $this->groupFilter = '';
        $this->statusFilter = '';
        $this->resetPage();
    }

    public function edit(int $id): void
    {
        $user = Auth::guard('teacher')->user();

        if (!$user->hasPermission(TeacherPermissionEnum::ATTENDANCE_MANAGE)) {
            $this->dispatch('show-error-message', message: 'ليس لديك صلاحية للتعديل.');
            return;
        }

        $attendance = Attendance::where('teacher_id', $user->getOwnerId())
            ->findOrFail($id);

        $this->editingId = $attendance->id;
        $this->editStatus = $attendance->status;
        $this->editNotes = $attendance->notes ?? '';
    }

    public function cancelEdit(): void
    {
        $this->editingId = null;
        $this->editStatus = '';
        $this->editNotes = '';
    }

    public function saveEdit(): void
    {
        $user = Auth::guard('teacher')->user();

        if (!$user->hasPermission(TeacherPermissionEnum::ATTENDANCE_MANAGE)) {
            $this->dispatch('show-error-message', message: 'ليس لديك صلاحية للتعديل.');
            return;
        }

        $this->validate([
            'editStatus' => 'required|in:present,late,absent,excused',
            'editNotes' => 'nullable|string|max:500',
        ]);

        $attendance = Attendance::where('teacher_id', $user->getOwnerId())
            ->findOrFail($this->editingId);

        $attendance->update([
            'status' => $this->editStatus,
            'notes' => $this->editNotes ?: null,
        ]);

        $this->editingId = null;
        $this->dispatch('show-success-message', message: 'تم تحديث الحضور بنجاح.');
    }

    public function render()
    {
        $user = Auth::guard('teacher')->user();
        $ownerId = $user->getOwnerId();
        $accessibleBranchIds = $user->getAccessibleBranchIds();

        $query = Attendance::query()
            ->with(['student', 'group', 'schedule.subject', 'branch', 'creator'])
            ->where('teacher_id', $ownerId);

        // Non-owner: restrict to accessible branches
        if (!$user->isOwner()) {
            empty($accessibleBranchIds)
                ? $query->whereRaw('1 = 0')
                : $query->whereIn('branch_id', $accessibleBranchIds);
        }

        $query->when($this->search !== '', fn($q) => $q->whereHas('student', function ($sub) {
            $sub->where('name', 'like', "%{$this->search}%")
                ->orWhere('code', 'like', "%{$this->search}%")
                ->orWhere('email', 'like', "%{$this->search}%");
        }));

        $query->when($this->dateFilter !== '', fn($q) => $q->where('attendance_date', $this->dateFilter));
        $query->when($this->branchFilter !== '', fn($q) => $q->where('branch_id', $this->branchFilter));
        $query->when($this->groupFilter !== '', fn($q) => $q->where('group_id', $this->groupFilter));
        $query->when($this->statusFilter !== '', fn($q) => $q->where('status', $this->statusFilter));

        $records = $query->orderByDesc('attendance_date')
            ->orderByDesc('check_in_time')
            ->paginate(15);

        return view('teacher-panel.pages.attendance.parts.attendance-data', [
            'records' => $records,
            'branches' => Branch::whereIn('id', $accessibleBranchIds)->orderBy('name')->get(),
            'groups' => Group::where('teacher_id', $ownerId)
                ->when(!$user->isOwner(), fn($q) => $q->whereIn('branch_id', $accessibleBranchIds))
                ->orderBy('name')
                ->get(),
            'canManage' => $user->hasPermission(TeacherPermissionEnum::ATTENDANCE_MANAGE),
            'currentTeacher' => $user,
        ]);
    }
}
