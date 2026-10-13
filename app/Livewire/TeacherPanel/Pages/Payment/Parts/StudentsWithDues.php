<?php

namespace App\Livewire\TeacherPanel\Pages\Payment\Parts;

use App\Services\Teacher\PaymentQueryService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class StudentsWithDues extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public string $month = '';

    public string $branchId = '';

    public string $searchUnpaid = '';

    public string $searchPartial = '';

    public function mount(): void
    {
        $this->month = session('teacher.payments.selected_month', now()->format('Y-m'));
        $this->branchId = (string) session('teacher.payments.selected_branch', '');
    }

    #[On('month-changed')]
    public function onMonthChanged(string $month): void
    {
        $this->month = $month;
        $this->resetPage('unpaidPage');
        $this->resetPage('partialPage');
    }

    #[On('branch-changed')]
    public function onBranchChanged(string $branchId): void
    {
        $this->branchId = $branchId;
        $this->resetPage('unpaidPage');
        $this->resetPage('partialPage');
    }

    #[On('payment-recorded')]
    public function refreshList(): void
    {
        // re-render
    }

    public function updatingSearchUnpaid(): void
    {
        $this->resetPage('unpaidPage');
    }

    public function updatingSearchPartial(): void
    {
        $this->resetPage('partialPage');
    }

    public function selectStudent(int $studentId): void
    {
        $this->dispatch('student-selected', studentId: $studentId)
            ->to('teacher-panel.pages.payment.parts.student-payment-card');

        $this->dispatch('scroll-to-payment-form');
    }

    public function render()
    {
        $user = Auth::guard('teacher')->user();
        $service = app(PaymentQueryService::class);

        $branchFilter = $this->branchId !== '' ? (int) $this->branchId : null;

        $unpaidQuery = $service->studentsUnpaid($user, $this->month);
        if ($branchFilter) {
            $unpaidQuery->where('branch_id', $branchFilter);
        }
        if ($this->searchUnpaid !== '') {
            $unpaidQuery->where(function ($q) {
                $q->where('name', 'like', "%{$this->searchUnpaid}%")
                    ->orWhere('code', 'like', "%{$this->searchUnpaid}%");
            });
        }
        $unpaidStudents = $unpaidQuery->paginate(10, ['*'], 'unpaidPage');

        $partialQuery = $service->studentsPartial($user, $this->month);
        if ($branchFilter) {
            $partialQuery->where('branch_id', $branchFilter);
        }
        if ($this->searchPartial !== '') {
            $partialQuery->where(function ($q) {
                $q->where('name', 'like', "%{$this->searchPartial}%")
                    ->orWhere('code', 'like', "%{$this->searchPartial}%");
            });
        }
        $partialStudents = $partialQuery->paginate(10, ['*'], 'partialPage');

        return view('teacher-panel.pages.payment.parts.students-with-dues', [
            'unpaidStudents' => $unpaidStudents,
            'partialStudents' => $partialStudents,
        ]);
    }
}
