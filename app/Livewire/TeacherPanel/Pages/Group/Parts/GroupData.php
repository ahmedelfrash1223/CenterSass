<?php

namespace App\Livewire\TeacherPanel\Pages\Group\Parts;

use App\Enums\Teacher\GroupStatusEnum;
use App\Models\Group;
use App\Models\Branch;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class GroupData extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public string $search = '';
    public string $branchFilter = '';
    public string $statusFilter = '';
    public ?int $deleteId = null;
    public bool $showDeleteConfirm = false;

    protected $listeners = [
        'refreshData' => '$refresh',
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatedBranchFilter(): void
    {
        $this->resetPage();
    }

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    public function confirmDelete(int $id): void
    {
        $this->deleteId = $id;
        $this->showDeleteConfirm = true;
    }

    public function cancelDelete(): void
    {
        $this->deleteId = null;
        $this->showDeleteConfirm = false;
    }

    public function delete(): void
    {
        if (!$this->deleteId) {
            return;
        }

        $group = Group::where('teacher_id', Auth::guard('teacher')->id())
            ->findOrFail($this->deleteId);

        $group->schedules()->delete();
        $group->students()->detach();
        $group->delete();

        $this->deleteId = null;
        $this->showDeleteConfirm = false;

        session()->flash('group-success', 'تم حذف المجموعة بنجاح.');
        $this->dispatch('refreshData');
    }

    public function render()
    {
        $teacherId = Auth::guard('teacher')->id();

        $groups = Group::with(['branch'])
            ->withCount([
                'students as students_count' => function ($query) {
                    $query->where('group_student.status', 'active');
                }
            ])
            ->where('teacher_id', $teacherId)
            ->when($this->search !== '', function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', "%{$this->search}%")
                        ->orWhere('description', 'like', "%{$this->search}%");
                });
            })
            ->when($this->branchFilter !== '', function ($query) {
                $query->where('branch_id', $this->branchFilter);
            })
            ->when($this->statusFilter !== '', function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->latest()
            ->paginate(10);

        return view('teacher-panel.pages.group.parts.group-data', [
            'groups' => $groups,
            'branches' => Branch::where('teacher_id', $teacherId)->orderBy('name')->get(),
            'statuses' => GroupStatusEnum::options(),
        ]);
    }
}
