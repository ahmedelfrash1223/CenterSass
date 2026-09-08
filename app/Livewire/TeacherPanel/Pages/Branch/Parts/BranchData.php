<?php

namespace App\Livewire\TeacherPanel\Pages\Branch\Parts;

use App\Models\Branch;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class BranchData extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public string $search = '';
    public string $status = '';
    public ?int $deleteId = null;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatus(): void
    {
        $this->resetPage();
    }

    public function confirmDelete(int $id): void
    {
        $this->deleteId = $id;
    }

    public function cancelDelete(): void
    {
        $this->deleteId = null;
    }

    public function delete(): void
    {
        if (! $this->deleteId) {
            return;
        }

        Branch::where('teacher_id', Auth::guard('teacher')->id())
            ->findOrFail($this->deleteId)
            ->delete();

        $this->deleteId = null;
        session()->flash('branch-success', 'تم حذف الفرع بنجاح.');
    }

    public function render()
    {
        $branches = Branch::query()
            ->where('teacher_id', Auth::guard('teacher')->id())
            ->when($this->search !== '', function ($query) {
                $query->where(function ($query) {
                    $query->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('address', 'like', '%' . $this->search . '%')
                        ->orWhere('phone', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->status !== '', fn ($query) => $query->where('is_active', $this->status))
            ->latest()
            ->paginate(10);

        return view('teacher-panel.pages.branch.parts.branch-data', compact('branches'));
    }
}