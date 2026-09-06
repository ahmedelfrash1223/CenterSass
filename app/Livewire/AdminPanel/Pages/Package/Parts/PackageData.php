<?php

namespace App\Livewire\AdminPanel\Pages\Package\Parts;

use App\Models\Package;
use Livewire\Component;
use Livewire\WithPagination;

class PackageData extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $deleteId = null;

    protected $listeners = [
        'refreshData' => '$refresh',
    ];

    public function updatingSearch()
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
        if (! $this->deleteId) {
            return;
        }

        $package = Package::findOrFail($this->deleteId);
        $package->delete();

        $this->deleteId = null;

        session()->flash('package-success', 'تم حذف الباقة بنجاح.');
    }

    public function render()
    {
        $packages = Package::with(['features', 'admin'])
            ->when($this->search !== '', function ($query) {
                $query->where('title', 'like', '%' . $this->search . '%');
            })
            ->latest()
            ->paginate(10);

        return view('admin-panel.pages.package.parts.package-data', [
            'packages' => $packages,
        ]);
    }
}
