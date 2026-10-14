<?php

namespace App\Livewire\TeacherPanel\Pages\Payroll\Parts;

use App\Enums\Teacher\TeacherPaymentTypeEnum;
use App\Enums\Teacher\TeacherPermissionEnum;
use App\Models\Branch;
use App\Models\Teacher;
use App\Models\TeacherBranchPayrollSetting;
use App\Models\TeacherPayrollSetting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class PayrollSettings extends Component
{
    public Teacher $teacher;

    public string $payment_type = 'monthly';

    public string $monthly_salary = '0';

    public string $per_session_rate = '0';

    public string $hourly_rate = '0';

    public string $session_deduction_rate = '0';

    public int $pay_day = 1;

    public array $branchSettings = [];

    public function mount(Teacher $teacher): void
    {
        $user = Auth::guard('teacher')->user();

        abort_unless($user->hasPermission(TeacherPermissionEnum::PAYROLL_MANAGE), 403);
        abort_unless($teacher->getOwnerId() === $user->getOwnerId(), 404);

        $this->teacher = $teacher;

        $setting = TeacherPayrollSetting::where('teacher_id', $teacher->id)->first();

        if ($setting) {
            $this->payment_type = $setting->payment_type->value;
            $this->monthly_salary = (string) $setting->monthly_salary;
            $this->per_session_rate = (string) $setting->per_session_rate;
            $this->hourly_rate = (string) $setting->hourly_rate;
            $this->session_deduction_rate = (string) $setting->session_deduction_rate;
            $this->pay_day = (int) ($setting->pay_day ?? 1);
        }

        $this->loadBranchSettings();
    }

    public function loadBranchSettings(): void
    {
        $branches = Branch::where('teacher_id', $this->teacher->getOwnerId())
            ->orderBy('name')
            ->get();

        $this->branchSettings = [];
        foreach ($branches as $branch) {
            $existing = TeacherBranchPayrollSetting::where('teacher_id', $this->teacher->id)
                ->where('branch_id', $branch->id)
                ->first();

            $this->branchSettings[$branch->id] = [
                'branch_name' => $branch->name,
                'enabled' => (bool) $existing,
                'payment_type' => $existing?->payment_type?->value ?? $this->payment_type,
                'monthly_salary' => (string) ($existing?->monthly_salary ?? 0),
                'per_session_rate' => (string) ($existing?->per_session_rate ?? 0),
                'hourly_rate' => (string) ($existing?->hourly_rate ?? 0),
                'session_deduction_rate' => (string) ($existing?->session_deduction_rate ?? 0),
            ];
        }
    }

    public function saveGeneral(): void
    {
        $user = Auth::guard('teacher')->user();
        abort_unless($user->hasPermission(TeacherPermissionEnum::PAYROLL_MANAGE), 403);

        $this->validate([
            'payment_type' => 'required|string',
            'monthly_salary' => 'numeric|min:0',
            'per_session_rate' => 'numeric|min:0',
            'hourly_rate' => 'numeric|min:0',
            'session_deduction_rate' => 'numeric|min:0',
            'pay_day' => 'integer|min:1|max:31',
        ]);

        TeacherPayrollSetting::updateOrCreate(
            ['teacher_id' => $this->teacher->id],
            [
                'payment_type' => $this->payment_type,
                'monthly_salary' => (float) $this->monthly_salary,
                'per_session_rate' => (float) $this->per_session_rate,
                'hourly_rate' => (float) $this->hourly_rate,
                'session_deduction_rate' => (float) $this->session_deduction_rate,
                'pay_day' => $this->pay_day,
                'is_active' => true,
            ]
        );

        $this->dispatch('show-success-message', message: 'تم حفظ الإعدادات العامة.');
    }

    public function saveBranch(int $branchId): void
    {
        $user = Auth::guard('teacher')->user();
        abort_unless($user->hasPermission(TeacherPermissionEnum::PAYROLL_MANAGE), 403);

        $data = $this->branchSettings[$branchId] ?? null;
        if (! $data) {
            return;
        }

        DB::transaction(function () use ($branchId, $data) {
            if (! empty($data['enabled'])) {
                TeacherBranchPayrollSetting::updateOrCreate(
                    ['teacher_id' => $this->teacher->id, 'branch_id' => $branchId],
                    [
                        'payment_type' => $data['payment_type'],
                        'monthly_salary' => (float) $data['monthly_salary'],
                        'per_session_rate' => (float) $data['per_session_rate'],
                        'hourly_rate' => (float) $data['hourly_rate'],
                        'session_deduction_rate' => (float) $data['session_deduction_rate'],
                        'is_active' => true,
                    ]
                );
            } else {
                TeacherBranchPayrollSetting::where('teacher_id', $this->teacher->id)
                    ->where('branch_id', $branchId)
                    ->delete();
            }
        });

        $this->dispatch('show-success-message', message: 'تم حفظ إعدادات الفرع.');
    }

    public function render()
    {
        return view('teacher-panel.pages.payroll.parts.payroll-settings', [
            'paymentTypes' => TeacherPaymentTypeEnum::options(),
        ]);
    }
}
