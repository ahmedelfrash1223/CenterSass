<?php

namespace App\Livewire\AdminPanel\Pages\Admin\Parts;

use App\Models\Admin;
use Livewire\Component;
use Livewire\WithPagination;

class AdminData extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $deleteId = null;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function confirmDelete($id): void
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

        Admin::findOrFail($this->deleteId)->delete();
        $this->deleteId = null;

        session()->flash('admin-success', 'تم حذف المدير بنجاح.');
    }

    public function render()
    {
        $admins = Admin::query()
            ->when($this->search !== '', function ($query) {
                $query->where(function ($query) {
                    $query->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%')
                        ->orWhere('username', 'like', '%' . $this->search . '%');
                });
            })
            ->latest()
            ->paginate(10);

        return view('admin-panel.pages.admin.parts.admin-data', compact('admins'));
    }
}