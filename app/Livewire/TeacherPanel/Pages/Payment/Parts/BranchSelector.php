<?php

namespace App\Livewire\TeacherPanel\Pages\Payment\Parts;

use App\Models\Branch;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class BranchSelector extends Component
{
    public string $branchId = '';

    public function mount(): void
    {
        $this->branchId = (string) session('teacher.payments.selected_branch', '');
    }

    public function updatedBranchId(): void
    {
        session(['teacher.payments.selected_branch' => $this->branchId]);

        $this->dispatch('branch-changed', branchId: $this->branchId);
    }

    public function render()
    {
        $user = Auth::guard('teacher')->user();
        $accessibleBranches = $user->getAccessibleBranchIds();

        $branchesQuery = Branch::query()->where('is_active', 1);

        if (! $user->isOwner()) {
            empty($accessibleBranches)
                ? $branchesQuery->whereRaw('1 = 0')
                : $branchesQuery->whereIn('id', $accessibleBranches);
        }

        return view('teacher-panel.pages.payment.parts.branch-selector', [
            'branches' => $branchesQuery->orderBy('name')->get(),
            'isOwner' => $user->isOwner(),
        ]);
    }
}
