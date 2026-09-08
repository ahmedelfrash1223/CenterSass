<?php

namespace App\Livewire\TeacherPanel\Pages\Branch\Parts;

use App\Http\Requests\TeacherPanel\UpdateBranchRequest;
use App\Models\Branch;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class BranchUpdate extends Component
{
    public Branch $branch;
    public string $name = '';
    public string $address = '';
    public string $phone = '';
    public string $email = '';
    public int $is_active = 1;

    public bool $updating = false;

    public function mount(Branch $branch): void
    {
        abort_unless($branch->teacher_id === Auth::guard('teacher')->id(), 404);

        $this->branch = $branch;
        $this->name = $branch->name;
        $this->address = $branch->address;
        $this->phone = $branch->phone ?? '';
        $this->email = $branch->email ?? '';
        $this->is_active = (int) $branch->is_active;
    }

    public function update(): void
    {
        $this->updating = true;

        $request = new UpdateBranchRequest();
        $request->merge(['branch_id' => $this->branch->id]);
        $this->validate($request->rules(), $request->messages());

        $this->branch->update([
            'name' => $this->name,
            'address' => $this->address,
            'phone' => $this->phone ?: null,
            'email' => $this->email ?: null,
            'is_active' => $this->is_active,
        ]);

        $this->updating = false;
        session()->flash('branch-success', 'تم تحديث الفرع بنجاح.');
        $this->redirectRoute('teacher.branches.index', navigate: true);
    }

    public function render()
    {
        return view('teacher-panel.pages.branch.parts.branch-update');
    }
}