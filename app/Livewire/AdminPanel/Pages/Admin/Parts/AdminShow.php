<?php

namespace App\Livewire\AdminPanel\Pages\Admin\Parts;

use App\Models\Admin;
use Livewire\Component;

class AdminShow extends Component
{
    public Admin $admin;
    public bool $confirmingDelete = false;

    public function mount(Admin $admin): void
    {
        $this->admin = $admin->load('themeSettings');
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
        $this->admin->delete();
        session()->flash('admin-success', 'تم حذف المدير بنجاح.');
        $this->redirectRoute('admin.admins.index', navigate: true);
    }

    public function render()
    {
        return view('admin-panel.pages.admin.parts.admin-show');
    }
}