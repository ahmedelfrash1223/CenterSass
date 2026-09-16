<?php

namespace App\Livewire\TeacherPanel\Pages\Branch\Parts;

use App\Enums\Teacher\TeacherPermissionEnum;
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

    public function mount(Branch $branch): void
    {
        $user = Auth::guard('teacher')->user();

        abort_unless(
            $user->hasPermission(TeacherPermissionEnum::BRANCH_UPDATE),
            403
        );

        // Branch must belong to the same owner account
        abort_unless(
            $branch->teacher_id === $user->getOwnerId(),
            404
        );

        $branch->load('creator');

        $this->branch = $branch;
        $this->name = $branch->name;
        $this->address = $branch->address;
        $this->phone = $branch->phone ?? '';
        $this->email = $branch->email ?? '';
        $this->is_active = (int)$branch->is_active;
    }

    public function update(): void
    {
        $user = Auth::guard('teacher')->user();

        abort_unless(
            $user->hasPermission(TeacherPermissionEnum::BRANCH_UPDATE),
            403
        );

        $request = new UpdateBranchRequest();
        $request->merge(['branch_id' => $this->branch->id]);
        $this->validate($request->rules(), $request->messages());

        $this->branch->update([
            'name' => $this->name,
            'address' => $this->address,
            'phone' => $this->phone ?: null,
            'email' => $this->email ?: null,
            'is_active' => (int)$this->is_active,
        ]);

        session()->flash('branch-success', 'تم تحديث الفرع بنجاح.');
        $this->redirect(route('teacher.branches.index'), navigate: true);
    }

    public function render()
    {
        return view('teacher-panel.pages.branch.parts.branch-update');
    }
}
