<?php

namespace App\Livewire\TeacherPanel\Pages\Branch\Parts;

use App\Http\Requests\TeacherPanel\StoreBranchRequest;
use App\Models\Branch;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class BranchCreate extends Component
{
    public string $name = '';
    public string $address = '';
    public string $phone = '';
    public string $email = '';
    public int $is_active = 1;

    public bool $saving = false;
    public bool $savingAndNew = false;

    public function save(bool $createNew = false): void
    {
        $createNew ? $this->savingAndNew = true : $this->saving = true;

        $request = new StoreBranchRequest();
        $this->validate($request->rules(), $request->messages());

        Branch::create([
            'teacher_id' => Auth::guard('teacher')->id(),
            'name' => $this->name,
            'address' => $this->address,
            'phone' => $this->phone ?: null,
            'email' => $this->email ?: null,
            'is_active' => $this->is_active,
        ]);

        $this->saving = false;
        $this->savingAndNew = false;

        if ($createNew) {
            $this->reset(['name', 'address', 'phone', 'email', 'is_active']);
            session()->flash('branch-success', 'تم إنشاء الفرع بنجاح. يمكنك إضافة فرع آخر.');
            return;
        }

        session()->flash('branch-success', 'تم إنشاء الفرع بنجاح.');
        $this->redirectRoute('teacher.branches.index', navigate: true);
    }

    public function render()
    {
        return view('teacher-panel.pages.branch.parts.branch-create');
    }
}