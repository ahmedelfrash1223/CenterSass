<?php

namespace App\Livewire\AdminPanel\Pages\Package\Parts;

use App\Models\Package;
use Livewire\Component;

class PackageShow extends Component
{
    public Package $package;
    public bool $confirmingDelete = false;

    public function mount(Package $package): void
    {
        $this->package = $package->load(['features', 'admin']);
    }

    public function confirmDelete(): void
    {
        $this->confirmingDelete = true;
    }

    public function cancelDelete(): void
    {
        $this->confirmingDelete = false;
    }

    public function delete(): void
    {
        $this->package->delete();
        session()->flash('package-success', 'تم حذف الباقة بنجاح.');
        $this->redirectRoute('admin.package.index', navigate: true);
    }

    public function render()
    {
        return view('admin-panel.pages.package.parts.package-show');
    }
}