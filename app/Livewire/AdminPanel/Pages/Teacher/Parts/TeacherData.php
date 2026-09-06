<?php

namespace App\Livewire\AdminPanel\Pages\Teacher\Parts;

use App\Models\Teacher;
use Livewire\Component;
use Livewire\WithPagination;

class TeacherData extends Component
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

        $teacher = Teacher::findOrFail($this->deleteId);
        $teacher->delete();

        $this->deleteId = null;

        session()->flash('teacher-success', 'تم حذف المدرس بنجاح.');
    }

    public function render()
    {
        $teachers = Teacher::query()
            ->when($this->search !== '', function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%')
                    ->orWhere('username', 'like', '%' . $this->search . '%')
                    ->orWhere('code', 'like', '%' . $this->search . '%');
            })
            ->latest()
            ->paginate(10);

        return view('admin-panel.pages.teacher.parts.teacher-data', [
            'teachers' => $teachers,
        ]);
    }
}
