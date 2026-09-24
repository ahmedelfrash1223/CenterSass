<?php

namespace App\Livewire\TeacherPanel\Pages\Branch\Parts;

use App\Enums\Admin\PackageFeatureKeyEnum;
use App\Enums\Teacher\TeacherPermissionEnum;
use App\Http\Requests\TeacherPanel\StoreBranchRequest;
use App\Livewire\TeacherPanel\Concerns\Shared\HasSubscriptionLimitCheck;
use App\Models\Branch;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class BranchCreate extends Component
{
    use HasSubscriptionLimitCheck;

    public string $name = '';
    public string $address = '';
    public string $phone = '';
    public string $email = '';
    public int $is_active = 1;

    public function mount(): void
    {
        $user = Auth::guard('teacher')->user();

        abort_unless(
            $user->hasPermission(TeacherPermissionEnum::BRANCH_CREATE),
            403
        );

        // Redirect if subscription limit already reached
        $count = Branch::where('teacher_id', $user->getOwnerId())->count();

        $this->checkLimitOrRedirect(
            PackageFeatureKeyEnum::MAX_BRANCHES->value,
            $count,
            'branch-error',
            'teacher.branches.index'
        );
    }

    public function save(bool $createNew = false): void
    {
        $user = Auth::guard('teacher')->user();
        $ownerId = $user->getOwnerId();

        // Guard against exceeding limit on direct submit
        $count = Branch::where('teacher_id', $ownerId)->count();

        if (!$this->checkLimitOrFail(PackageFeatureKeyEnum::MAX_BRANCHES->value, $count)) {
            return;
        }

        $request = new StoreBranchRequest();
        $this->validate($request->rules(), $request->messages());

        Branch::create([
            'teacher_id' => $ownerId,
            'created_by' => $user->id,
            'name' => $this->name,
            'address' => $this->address,
            'phone' => $this->phone ?: null,
            'email' => $this->email ?: null,
            'is_active' => (int) $this->is_active,
        ]);

        if ($createNew) {
            $this->reset(['name', 'address', 'phone', 'email']);
            $this->is_active = 1;

            // If new count hits limit, bounce back with message
            $newCount = Branch::where('teacher_id', $ownerId)->count();

            if (!$this->checkLimitOrRedirect(
                PackageFeatureKeyEnum::MAX_BRANCHES->value,
                $newCount,
                'branch-error',
                'teacher.branches.index'
            )) {
                session()->flash('branch-success', 'تم إنشاء الفرع بنجاح.');
                return;
            }

            $this->dispatch('show-success-message', message: 'تم إنشاء الفرع بنجاح. يمكنك إضافة فرع آخر.');
            return;
        }

        session()->flash('branch-success', 'تم إنشاء الفرع بنجاح.');
        $this->redirect(route('teacher.branches.index'), navigate: true);
    }

    public function render()
    {
        $user = Auth::guard('teacher')->user();
        $ownerId = $user->getOwnerId();

        $count = Branch::where('teacher_id', $ownerId)->count();
        $limit = $this->getLimitData(PackageFeatureKeyEnum::MAX_BRANCHES->value, $count);

        return view('teacher-panel.pages.branch.parts.branch-create', [
            'limit' => $limit,
        ]);
    }
}
