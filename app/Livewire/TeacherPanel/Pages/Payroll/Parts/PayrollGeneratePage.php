<?php

namespace App\Livewire\TeacherPanel\Pages\Payroll\Parts;

use App\Enums\Teacher\TeacherPermissionEnum;
use App\Models\Branch;
use App\Models\Teacher;
use App\Services\Teacher\TeacherPayrollService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class PayrollGeneratePage extends Component
{
    public string $month = '';

    public string $branchId = '';

    public array $selectedTeacherIds = [];

    public array $results = [];

    public function mount(): void
    {
        $user = Auth::guard('teacher')->user();

        abort_unless(
            $user->hasPermission(TeacherPermissionEnum::PAYROLL_GENERATE),
            403
        );

        $this->month = now()->format('Y-m');
    }

    public function selectAllTeachers(): void
    {
        $this->selectedTeacherIds = $this->getAvailableTeachers()
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->toArray();
    }

    public function clearTeachers(): void
    {
        $this->selectedTeacherIds = [];
    }

    public function generate(TeacherPayrollService $service): void
    {
        $user = Auth::guard('teacher')->user();

        abort_unless($user->hasPermission(TeacherPermissionEnum::PAYROLL_GENERATE), 403);

        if (empty($this->selectedTeacherIds)) {
            $this->dispatch('show-error-message', message: 'اختر مدرس واحد على الأقل.');

            return;
        }

        $date = Carbon::createFromFormat('Y-m', $this->month);
        $periodStart = $date->copy()->startOfMonth();
        $periodEnd = $date->copy()->endOfMonth();

        $branchId = $this->branchId !== '' ? (int) $this->branchId : null;

        $this->results = ['created' => 0, 'skipped' => 0, 'skipped_teachers' => []];

        foreach ($this->selectedTeacherIds as $teacherId) {
            $teacher = Teacher::find($teacherId);
            if (! $teacher) {
                continue;
            }

            if (! $user->isOwner() && $teacher->getOwnerId() !== $user->getOwnerId()) {
                continue;
            }

            $payroll = $service->generateForTeacher(
                teacher: $teacher,
                periodStart: $periodStart,
                periodEnd: $periodEnd,
                branchId: $branchId,
                generatedBy: $user,
            );

            if ($payroll) {
                $this->results['created']++;
            } else {
                $this->results['skipped']++;
                $this->results['skipped_teachers'][] = $teacher->name;
            }
        }

        $this->dispatch('show-success-message', message: "تم توليد {$this->results['created']} كشف. تم تخطي {$this->results['skipped']}."
        );
    }

    protected function getAvailableTeachers()
    {
        $user = Auth::guard('teacher')->user();
        $ownerId = $user->getOwnerId();

        $query = Teacher::where(function ($q) use ($ownerId) {
            $q->where('id', $ownerId)->orWhere('owner_id', $ownerId);
        })->where('is_active', 1);

        if (! $user->isOwner()) {
            $accessibleBranches = $user->getAccessibleBranchIds();
            $query->whereHas('branches', fn ($b) => $b->whereIn('branches.id', $accessibleBranches));
        }

        return $query->orderBy('name')->get();
    }

    public function render()
    {
        $user = Auth::guard('teacher')->user();
        $accessibleBranches = $user->getAccessibleBranchIds();

        return view('teacher-panel.pages.payroll.parts.payroll-generate-page', [
            'teachers' => $this->getAvailableTeachers(),
            'branches' => Branch::whereIn('id', $accessibleBranches)->orderBy('name')->get(),
        ]);
    }
}
