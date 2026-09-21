<?php

namespace App\Livewire\TeacherPanel\Pages\Payment\Parts;

use App\Enums\Teacher\TeacherPermissionEnum;
use App\Services\Teacher\PaymentQueryService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class PaymentSearch extends Component
{
    public string $search = '';

    public function mount(): void
    {
        abort_unless(
            Auth::guard('teacher')->user()->hasPermission(TeacherPermissionEnum::PAYMENT_VIEW),
            403
        );
    }

    public function selectStudent(int $studentId): void
    {
        $this->search = '';

        $this->dispatch('student-selected', studentId: $studentId)
            ->to('teacher-panel.pages.payment.parts.student-payment-card');

        $this->dispatch('scroll-to-payment-form');
    }

    public function handleScan(string $value): void
    {
        $value = trim($value);

        if ($value === '') {
            return;
        }

        $user = Auth::guard('teacher')->user();
        $student = app(PaymentQueryService::class)
            ->searchStudents($user, $value)
            ->first();

        if ($student) {
            $this->selectStudent($student->id);
        } else {
            $this->dispatch('show-error-message', message: 'الطالب غير موجود.');
        }

        $this->dispatch('focus-scan-input');
    }

    public function render()
    {
        $user = Auth::guard('teacher')->user();

        $students = collect();
        if (strlen(trim($this->search)) >= 2) {
            $students = app(PaymentQueryService::class)
                ->searchStudents($user, $this->search)
                ->limit(8)
                ->get();
        }

        return view('teacher-panel.pages.payment.parts.payment-search', [
            'students' => $students,
        ]);
    }
}
