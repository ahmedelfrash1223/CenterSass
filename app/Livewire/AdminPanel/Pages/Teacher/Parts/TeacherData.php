<?php

namespace App\Livewire\AdminPanel\Pages\Teacher\Parts;

use App\Enums\Teacher\TeacherRoleEnum;
use App\Models\Teacher;
use Livewire\Component;
use Livewire\WithPagination;

class TeacherData extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $roleFilter = '';
    public $statusFilter = '';
    public $ownerFilter = '';
    public $deleteId = null;

    protected $listeners = [
        'refreshData' => '$refresh',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingRoleFilter()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingOwnerFilter()
    {
        $this->resetPage();
    }

    public function confirmDelete($id)
    {
        $this->deleteId = $id;
    }

    public function cancelDelete()
    {
        $this->deleteId = null;
    }

    public function delete()
    {
        if (!$this->deleteId) {
            return;
        }

        $teacher = Teacher::findOrFail($this->deleteId);

        if ($teacher->isOwner() && $teacher->staff()->count() > 0) {
            session()->flash('teacher-error', 'لا يمكن حذف المدرس المسئول لأنه يمتلك مدرسين أو مساعدين تابعين له.');
            $this->deleteId = null;
            return;
        }

        $teacher->delete();

        $this->deleteId = null;

        session()->flash('teacher-success', 'تم حذف المدرس بنجاح.');
    }

    public function render()
    {
        $teachers = Teacher::query()
            ->with(['owner'])
            ->when($this->search !== '', function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%')
                        ->orWhere('username', 'like', '%' . $this->search . '%')
                        ->orWhere('code', 'like', '%' . $this->search . '%')
                        ->orWhere('phone', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->roleFilter !== '', function ($query) {
                $query->where('role', $this->roleFilter);
            })
            ->when($this->statusFilter !== '', function ($query) {
                $query->where('is_active', $this->statusFilter);
            })
            ->when($this->ownerFilter !== '', function ($query) {
                if ($this->ownerFilter === 'owners_only') {
                    $query->whereNull('owner_id');
                } elseif ($this->ownerFilter === 'staff_only') {
                    $query->whereNotNull('owner_id');
                } else {
                    $query->where('owner_id', $this->ownerFilter);
                }
            })
            ->latest()
            ->paginate(10);

        return view('admin-panel.pages.teacher.parts.teacher-data', [
            'teachers' => $teachers,
            'roles' => TeacherRoleEnum::options(),
            'owners' => Teacher::where('role', TeacherRoleEnum::OWNER->value)
                ->orderBy('name')
                ->get(),
        ]);
    }
}
