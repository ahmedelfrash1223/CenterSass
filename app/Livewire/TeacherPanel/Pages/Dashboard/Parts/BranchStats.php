<?php

namespace App\Livewire\TeacherPanel\Pages\Dashboard\Parts;

use App\Models\Branch;
use App\Models\GroupSchedule;
use Livewire\Component;
use Livewire\WithPagination;

class BranchStats extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public int $perPage = 5;

    public function render()
    {
        $user = auth('teacher')->user();
        $ownerId = $user->getOwnerId();
        $today = now()->format('l');

        $branches = Branch::where('teacher_id', $ownerId)
            ->withCount([
                'students as students_count',
                'groups as groups_count',
            ])
            ->orderBy('name')
            ->paginate($this->perPage);

        $branches->getCollection()->transform(function ($branch) use ($today) {
            $branch->today_schedules_count = GroupSchedule::where('day', $today)
                ->whereHas('group', fn($q) => $q->where('branch_id', $branch->id))
                ->count();
            return $branch;
        });

        return view('teacher-panel.pages.dashboard.parts.branch-stats', [
            'branches' => $branches,
        ]);
    }
}
