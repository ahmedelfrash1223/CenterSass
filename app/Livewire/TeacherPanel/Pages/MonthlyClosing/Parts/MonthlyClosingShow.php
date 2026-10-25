<?php

namespace App\Livewire\TeacherPanel\Pages\MonthlyClosing\Parts;

use App\Enums\Teacher\TeacherPermissionEnum;
use App\Models\MonthlyClosing;
use App\Services\Teacher\MonthlyClosingService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class MonthlyClosingShow extends Component
{
    public MonthlyClosing $closing;

    public ?string $confirmingAction = null;

    public function mount(MonthlyClosing $closing): void
    {
        $user = Auth::guard('teacher')->user();

        abort_unless(
            $user->hasPermission(TeacherPermissionEnum::MONTHLY_CLOSING_VIEW),
            403
        );

        abort_unless($closing->teacher_id === $user->getOwnerId(), 404);

        $this->closing = $closing->load(['branch', 'teacher', 'closedBy', 'items']);
    }


    public function askConfirm(string $action): void
    {
        $this->confirmingAction = $action;
    }

    public function cancelConfirm(): void
    {
        $this->confirmingAction = null;
    }

    public function confirmAction(MonthlyClosingService $service): void
    {
        $action = $this->confirmingAction;
        $this->confirmingAction = null;

        match ($action) {
            'reopen' => $this->reopen($service),
            'delete' => $this->delete($service),
            default => null,
        };
    }


    public function reopen(MonthlyClosingService $service): void
    {
        $user = Auth::guard('teacher')->user();

        if (! $user->hasPermission(TeacherPermissionEnum::MONTHLY_CLOSING_MANAGE)) {
            $this->dispatch('show-error-message', message: 'ليس لديك صلاحية لإعادة فتح الشهر.');

            return;
        }

        try {
            $service->reopen($user, $this->closing);

            $this->dispatch('show-success-message', message: 'تم إعادة فتح الشهر.');

            $this->redirect(route('teacher.monthly-closing.index'), navigate: true);
        } catch (\Throwable $e) {
            $this->dispatch('show-error-message', message: $e->getMessage());
        }
    }

    public function delete(MonthlyClosingService $service): void
    {
        $user = Auth::guard('teacher')->user();

        if (! $user->hasPermission(TeacherPermissionEnum::MONTHLY_CLOSING_DELETE)) {
            $this->dispatch('show-error-message', message: 'ليس لديك صلاحية لحذف التقفيل.');

            return;
        }

        try {
            $service->delete($user, $this->closing);

            session()->flash('monthly-closing-success', 'تم حذف التقفيل.');

            $this->redirect(route('teacher.monthly-closing.index'), navigate: true);
        } catch (\Throwable $e) {
            $this->dispatch('show-error-message', message: $e->getMessage());
        }
    }

    // ────────────────────────────────────────
    // Confirm message
    // ────────────────────────────────────────
    public function confirmMessage(): ?array
    {
        if (! $this->confirmingAction) {
            return null;
        }

        return match ($this->confirmingAction) {
            'reopen' => [
                'icon'        => 'ph-lock-open',
                'title'       => 'تأكيد إعادة فتح الشهر',
                'description' => 'سيتم إلغاء التقفيل والسماح بتعديل البيانات مرة أخرى.',
                'btn_label'   => 'نعم، أعد الفتح',
                'btn_class'   => 'btn-warning',
            ],
            'delete' => [
                'icon'        => 'ph-warning-circle',
                'title'       => 'تأكيد حذف التقفيل',
                'description' => 'سيتم حذف التقفيل نهائياً. هذا الإجراء لا يمكن التراجع عنه.',
                'btn_label'   => 'نعم، احذف',
                'btn_class'   => 'btn-danger',
            ],
            default => null,
        };
    }

    public function render()
    {
        $user = Auth::guard('teacher')->user();

        $revenueItems = $this->closing->items->where('type', 'revenue')->values();
        $expenseItems = $this->closing->items->where('type', 'expense')->values();

        return view('teacher-panel.pages.monthly-closing.parts.monthly-closing-show', [
            'revenueItems' => $revenueItems,
            'expenseItems' => $expenseItems,
            'canManage'    => $user->hasPermission(TeacherPermissionEnum::MONTHLY_CLOSING_MANAGE),
            'canDelete'    => $user->hasPermission(TeacherPermissionEnum::MONTHLY_CLOSING_DELETE),
            'canExport'    => $user->hasPermission(TeacherPermissionEnum::MONTHLY_CLOSING_EXPORT),
            'confirm'      => $this->confirmMessage(),
        ]);
    }
}
