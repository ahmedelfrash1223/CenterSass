<?php

namespace App\Livewire\TeacherPanel\Pages\Payroll\Parts;

use App\Enums\Teacher\TeacherPaymentTypeEnum;
use App\Enums\Teacher\TeacherPermissionEnum;
use App\Enums\Teacher\TeacherRoleEnum;
use App\Models\Branch;
use App\Models\Teacher;
use App\Models\TeacherPayroll;
use App\Models\TeacherPayrollSetting;
use App\Services\Teacher\TeacherPayrollService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class PayrollGeneratePage extends Component
{
    public string $month = '';

    public string $branchId = '';

    public string $roleFilter = 'staff';

    public array $selectedTeacherIds = [];

    public array $results = [];

    // Inline Settings
    public ?int $openSettingsTeacherId = null;

    public ?int $settingsTeacherId = null;

    public string $settingsTeacherName = '';

    public string $settingsPaymentType = 'monthly';

    public string $settingsMonthlySalary = '0';

    public string $settingsPerSessionRate = '0';

    public string $settingsHourlyRate = '0';

    public string $settingsSessionDeductionRate = '0';

    public function mount(): void
    {
        $user = Auth::guard('teacher')->user();

        abort_unless($user->hasPermission(TeacherPermissionEnum::PAYROLL_GENERATE), 403);

        $this->month = now()->format('Y-m');
    }

    public function updatedMonth(): void
    {
        $this->selectedTeacherIds = [];
        $this->results = [];
        $this->closeSettings();
    }

    public function updatedBranchId(): void
    {
        $this->selectedTeacherIds = [];
        $this->results = [];
        $this->closeSettings();
    }

    public function updatedRoleFilter(): void
    {
        $this->selectedTeacherIds = [];
        $this->results = [];
        $this->closeSettings();
    }

    public function selectAllTeachers(): void
    {
        $this->selectedTeacherIds = $this->availableTeachers
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->toArray();
    }

    public function clearTeachers(): void
    {
        $this->selectedTeacherIds = [];
    }

    // ============================================
    // Settings (Inline)
    // ============================================

    public function openSettingsFor(int $teacherId): void
    {
        $user = Auth::guard('teacher')->user();
        abort_unless($user->hasPermission(TeacherPermissionEnum::PAYROLL_MANAGE), 403);

        $teacher = Teacher::findOrFail($teacherId);
        abort_unless($teacher->getOwnerId() === $user->getOwnerId(), 404);

        $this->openSettingsTeacherId = $teacherId;
        $this->settingsTeacherId = $teacherId;
        $this->settingsTeacherName = $teacher->name;

        $setting = TeacherPayrollSetting::where('teacher_id', $teacherId)->first();

        if ($setting) {
            $this->settingsPaymentType = $setting->payment_type->value;
            $this->settingsMonthlySalary = (string) $setting->monthly_salary;
            $this->settingsPerSessionRate = (string) $setting->per_session_rate;
            $this->settingsHourlyRate = (string) $setting->hourly_rate;
            $this->settingsSessionDeductionRate = (string) $setting->session_deduction_rate;
        } else {
            $this->settingsPaymentType = 'monthly';
            $this->settingsMonthlySalary = '0';
            $this->settingsPerSessionRate = '0';
            $this->settingsHourlyRate = '0';
            $this->settingsSessionDeductionRate = '0';
        }
    }

    public function closeSettings(): void
    {
        $this->openSettingsTeacherId = null;
        $this->settingsTeacherId = null;
        $this->settingsTeacherName = '';
    }

    public function saveSettings(): void
    {
        $user = Auth::guard('teacher')->user();
        abort_unless($user->hasPermission(TeacherPermissionEnum::PAYROLL_MANAGE), 403);

        if ($this->settingsTeacherId === null) {
            return;
        }

        $this->validate([
            'settingsPaymentType' => 'required|string',
            'settingsMonthlySalary' => 'numeric|min:0',
            'settingsPerSessionRate' => 'numeric|min:0',
            'settingsHourlyRate' => 'numeric|min:0',
            'settingsSessionDeductionRate' => 'numeric|min:0',
        ]);

        TeacherPayrollSetting::updateOrCreate(
            ['teacher_id' => $this->settingsTeacherId],
            [
                'payment_type' => $this->settingsPaymentType,
                'monthly_salary' => (float) $this->settingsMonthlySalary,
                'per_session_rate' => (float) $this->settingsPerSessionRate,
                'hourly_rate' => (float) $this->settingsHourlyRate,
                'session_deduction_rate' => (float) $this->settingsSessionDeductionRate,
                'is_active' => true,
            ]
        );

        $this->dispatch('show-success-message', message: 'تم حفظ إعدادات الأجر.');
        $this->closeSettings();
    }

    // ============================================
    // Generate
    // ============================================

    public function generate(TeacherPayrollService $service)
    {
        $user = Auth::guard('teacher')->user();

        abort_unless($user->hasPermission(TeacherPermissionEnum::PAYROLL_GENERATE), 403);

        if ($this->branchId === '') {
            $this->dispatch('show-error-message', message: 'اختر الفرع أولاً.');

            return;
        }

        if (! in_array((int) $this->branchId, $user->getAccessibleBranchIds(), true)) {
            $this->dispatch('show-error-message', message: 'الفرع المختار غير متاح لك.');

            return;
        }

        if (empty($this->selectedTeacherIds)) {
            $this->dispatch('show-error-message', message: 'اختر واحد على الأقل.');

            return;
        }

        $date = Carbon::createFromFormat('Y-m', $this->month);
        $periodStart = $date->copy()->startOfMonth();
        $periodEnd = $date->copy()->endOfMonth();

        $branchId = (int) $this->branchId;

        $created = 0;
        $skipped = 0;
        $noSettingsTeachers = [];
        $firstPayrollId = null;

        foreach ($this->selectedTeacherIds as $teacherId) {
            $teacher = Teacher::find($teacherId);
            if (! $teacher) {
                continue;
            }
            if ($teacher->isOwner()) {
                continue;
            }
            if (! $user->isOwner() && $teacher->getOwnerId() !== $user->getOwnerId()) {
                continue;
            }

            $hasSettings = TeacherPayrollSetting::where('teacher_id', $teacher->id)
                ->where('is_active', true)
                ->exists();

            if (! $hasSettings) {
                $noSettingsTeachers[] = $teacher->name;
                $skipped++;

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
                $created++;
                if ($firstPayrollId === null) {
                    $firstPayrollId = $payroll->id;
                }
            } else {
                $skipped++;
            }
        }

        if (! empty($noSettingsTeachers)) {
            $names = implode('، ', array_slice($noSettingsTeachers, 0, 3));
            if (count($noSettingsTeachers) > 3) {
                $names .= ' وأخرين';
            }
            $this->dispatch('show-error-message', message: "فيه موظفين مش عندهم إعدادات أجر: {$names}. ضيف إعداداتهم الأول."
            );
        }

        if ($created === 0) {
            if (empty($noSettingsTeachers)) {
                $this->dispatch('show-error-message', message: 'مفيش كشوف اتولدت — تحقق من وجود حصص في الشهر والفرع.');
            }

            return;
        }

        session()->flash('payroll-success', "تم توليد {$created} كشف. تم تخطي {$skipped}.");

        if ($created === 1 && $firstPayrollId) {
            $this->redirect(route('teacher.payrolls.show', $firstPayrollId), navigate: true);
        } else {
            $this->redirect(route('teacher.payrolls.review'), navigate: true);
        }
    }

    // ============================================
    // Available Teachers
    // ============================================

    public function getAvailableTeachersProperty()
    {
        $user = Auth::guard('teacher')->user();
        $ownerId = $user->getOwnerId();

        if ($this->month === '' || $this->branchId === '') {
            return collect();
        }

        $date = Carbon::createFromFormat('Y-m', $this->month);
        $periodStart = $date->copy()->startOfMonth();
        $periodEnd = $date->copy()->endOfMonth();

        $query = Teacher::where(function ($q) use ($ownerId) {
            $q->where('id', $ownerId)->orWhere('owner_id', $ownerId);
        })
            ->where('is_active', 1)
            ->where('id', '!=', $ownerId);

        if ($this->roleFilter === 'staff') {
            $query->where('role', TeacherRoleEnum::STAFF->value)
                ->whereHas('groupSchedules', fn ($q) => $q->whereHas('group', fn ($g) => $g->where('branch_id', (int) $this->branchId)));
        } else {
            $query->where('role', TeacherRoleEnum::ASSISTANT->value);
        }

        $query->whereHas('branches', fn ($b) => $b->where('branches.id', (int) $this->branchId));

        if (! $user->isOwner()) {
            $accessibleBranches = $user->getAccessibleBranchIds();
            $query->whereHas('branches', fn ($b) => $b->whereIn('branches.id', $accessibleBranches));
        }

        $existing = TeacherPayroll::query()
            ->where('branch_id', (int) $this->branchId)
            ->whereDate('period_start', $periodStart->toDateString())
            ->whereDate('period_end', $periodEnd->toDateString())
            ->pluck('teacher_id')
            ->toArray();

        if (! empty($existing)) {
            $query->whereNotIn('id', $existing);
        }

        return $query->orderBy('name')->get();
    }

    public function render()
    {
        $user = Auth::guard('teacher')->user();
        $accessibleBranches = $user->getAccessibleBranchIds();

        $teachers = $this->availableTeachers;
        $teacherIds = $teachers->pluck('id')->toArray();
        $withSettings = TeacherPayrollSetting::whereIn('teacher_id', $teacherIds)
            ->where('is_active', true)
            ->pluck('teacher_id')
            ->toArray();

        return view('teacher-panel.pages.payroll.parts.payroll-generate-page', [
            'teachers' => $teachers,
            'withSettings' => $withSettings,
            'branches' => Branch::whereIn('id', $accessibleBranches)->orderBy('name')->get(),
            'paymentTypes' => TeacherPaymentTypeEnum::options(),
        ]);
    }
}
