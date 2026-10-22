<?php

namespace App\Livewire\AdminPanel\Pages\CardRequest\Parts;

use App\Enums\Teacher\CardRequestStatusEnum;
use App\Http\Requests\AdminPanel\ReviewCardRequestRequest;
use App\Models\CardRequest;
use Illuminate\Support\Facades\Validator;
use Livewire\Component;

class CardRequestShow extends Component
{
    public CardRequest $cardRequest;

    public string $adminNotes = '';

    public ?string $confirmingAction = null;

    public function mount(CardRequest $cardRequest): void
    {
        $this->cardRequest = $cardRequest->load([
            'items.branch',
            'items.group',
            'creator',
            'teacher',
        ]);
        $this->adminNotes = $this->cardRequest->admin_notes ?? '';
    }

    // ────────────────────────────────────────
    // Confirmation helpers
    // ────────────────────────────────────────
    public function askConfirm(string $action): void
    {
        $this->confirmingAction = $action;
    }

    public function cancelConfirm(): void
    {
        $this->confirmingAction = null;
    }

    public function confirmAction(): void
    {
        $action = $this->confirmingAction;
        $this->confirmingAction = null;

        match ($action) {
            'approve' => $this->review('approve'),
            'reject' => $this->review('reject'),
            'start' => $this->startProcessing(),
            'complete' => $this->markAsCompleted(),
            'ship' => $this->markAsShipped(),
            'deliver' => $this->markAsDelivered(),
            'cancel' => $this->cancelRequest(),
            default => null,
        };
    }

    protected function buildConfirmMessage(): ?array
    {
        if (! $this->confirmingAction) {
            return null;
        }

        return match ($this->confirmingAction) {
            'approve' => [
                'icon' => 'ph-check-circle',
                'title' => 'تأكيد قبول الطلب',
                'description' => 'سيتم تحويل حالة الطلب من "في انتظار المراجعة" إلى "تم القبول".',
                'btn_label' => 'نعم، اقبل الطلب',
                'btn_class' => 'btn-success',
            ],
            'reject' => [
                'icon' => 'ph-x-circle',
                'title' => 'تأكيد رفض الطلب',
                'description' => 'سيتم رفض الطلب ولن يتم تنفيذه. تأكد من كتابة سبب الرفض في الملاحظات.',
                'btn_label' => 'نعم، ارفض الطلب',
                'btn_class' => 'btn-danger',
            ],
            'start' => [
                'icon' => 'ph-play-circle',
                'title' => 'تأكيد بدء التنفيذ',
                'description' => 'سيتم تحويل حالة الطلب إلى "جاري العمل" ويمكنك بعدها تصدير ملفات الطباعة.',
                'btn_label' => 'نعم، ابدأ التنفيذ',
                'btn_class' => 'btn-primary',
            ],
            'complete' => [
                'icon' => 'ph-check-square',
                'title' => 'تأكيد إنهاء الطلب',
                'description' => 'سيتم تحويل حالة الطلب إلى "تم الانتهاء" تمهيداً للشحن.',
                'btn_label' => 'نعم، أنهِ الطلب',
                'btn_class' => 'btn-success',
            ],
            'ship' => [
                'icon' => 'ph-truck',
                'title' => 'تأكيد شحن الطلب',
                'description' => 'سيتم تحويل حالة الطلب إلى "تم الشحن" وسيتمكن المدرس من رؤية تحديث الحالة.',
                'btn_label' => 'نعم، اشحن الطلب',
                'btn_class' => 'btn-info',
            ],
            'deliver' => [
                'icon' => 'ph-package',
                'title' => 'تأكيد تسليم الطلب',
                'description' => 'سيتم تحويل حالة الطلب إلى "اكتمل". هذا هو الإجراء النهائي ولا يمكن التراجع عنه.',
                'btn_label' => 'نعم، تم التسليم',
                'btn_class' => 'btn-success',
            ],
            'cancel' => [
                'icon' => 'ph-warning-circle',
                'title' => 'تأكيد إلغاء الطلب',
                'description' => 'سيتم إلغاء الطلب نهائياً. هذا الإجراء لا يمكن التراجع عنه.',
                'btn_label' => 'نعم، ألغِ الطلب',
                'btn_class' => 'btn-danger',
            ],
            default => null,
        };
    }

    // ────────────────────────────────────────
    // Review (approve / reject)
    // ────────────────────────────────────────
    public function review(string $action): void
    {
        $this->cardRequest->refresh();

        if (! $this->cardRequest->isPending()) {
            $this->dispatch('show-error-message', message: 'لا يمكن مراجعة الطلب في حالته الحالية.');

            return;
        }

        $request = new ReviewCardRequestRequest;
        $validated = Validator::make(
            ['action' => $action, 'admin_notes' => $this->adminNotes],
            $request->rules(),
            $request->messages(),
        )->validate();

        $status = $validated['action'] === 'approve'
            ? CardRequestStatusEnum::APPROVED
            : CardRequestStatusEnum::REJECTED;

        $this->cardRequest->update([
            'status' => $status->value,
            'admin_notes' => $validated['admin_notes'] ?? null,
            'approved_at' => $status === CardRequestStatusEnum::APPROVED ? now() : null,
            'rejected_at' => $status === CardRequestStatusEnum::REJECTED ? now() : null,
        ]);

        $this->cardRequest->refresh();
        $this->dispatch('show-success-message', message: $status === CardRequestStatusEnum::APPROVED
            ? 'تم قبول الطلب.'
            : 'تم رفض الطلب.');
    }

    // ────────────────────────────────────────
    // APPROVED → IN_PROGRESS
    // ────────────────────────────────────────
    public function startProcessing(): void
    {
        $this->cardRequest->refresh();

        if ($this->cardRequest->status !== CardRequestStatusEnum::APPROVED) {
            $this->dispatch('show-error-message', message: 'لا يمكن بدء التنفيذ في حالته الحالية.');

            return;
        }

        $this->cardRequest->update([
            'status' => CardRequestStatusEnum::IN_PROGRESS->value,
        ]);

        $this->cardRequest->refresh();
        $this->dispatch('show-success-message', message: 'تم بدء تنفيذ الطلب.');
    }

    // ────────────────────────────────────────
    // IN_PROGRESS → COMPLETED
    // ────────────────────────────────────────
    public function markAsCompleted(): void
    {
        $this->cardRequest->refresh();

        if ($this->cardRequest->status !== CardRequestStatusEnum::IN_PROGRESS) {
            $this->dispatch('show-error-message', message: 'لا يمكن إنهاء الطلب في حالته الحالية.');

            return;
        }

        $this->cardRequest->update([
            'status' => CardRequestStatusEnum::COMPLETED->value,
            'completed_at' => now(),
        ]);

        $this->cardRequest->refresh();
        $this->dispatch('show-success-message', message: 'تم إنهاء الطلب.');
    }

    // ────────────────────────────────────────
    // COMPLETED → SHIPPED
    // ────────────────────────────────────────
    public function markAsShipped(): void
    {
        $this->cardRequest->refresh();

        if ($this->cardRequest->status !== CardRequestStatusEnum::COMPLETED) {
            $this->dispatch('show-error-message', message: 'لا يمكن شحن الطلب في حالته الحالية.');

            return;
        }

        $this->cardRequest->update([
            'status' => CardRequestStatusEnum::SHIPPED->value,
            'shipped_at' => now(),
        ]);

        $this->cardRequest->refresh();
        $this->dispatch('show-success-message', message: 'تم شحن الطلب.');
    }

    // ────────────────────────────────────────
    // SHIPPED → DELIVERED
    // ────────────────────────────────────────
    public function markAsDelivered(): void
    {
        $this->cardRequest->refresh();

        if ($this->cardRequest->status !== CardRequestStatusEnum::SHIPPED) {
            $this->dispatch('show-error-message', message: 'لا يمكن تسليم الطلب في حالته الحالية.');

            return;
        }

        $this->cardRequest->update([
            'status' => CardRequestStatusEnum::DELIVERED->value,
            'delivered_at' => now(),
        ]);

        $this->cardRequest->refresh();
        $this->dispatch('show-success-message', message: 'تم تسليم الطلب.');
    }

    // ────────────────────────────────────────
    // Cancel
    // ────────────────────────────────────────
    public function cancelRequest(): void
    {
        $this->cardRequest->refresh();

        if (! $this->cardRequest->canBeCancelled()) {
            $this->dispatch('show-error-message', message: 'لا يمكن إلغاء الطلب في حالته الحالية.');

            return;
        }

        $this->cardRequest->update(['status' => CardRequestStatusEnum::CANCELLED->value]);
        $this->cardRequest->refresh();
        $this->dispatch('show-success-message', message: 'تم إلغاء الطلب بنجاح.');
    }

    public function render()
    {
        $status = $this->cardRequest->status;

        return view('admin-panel.pages.card-request.parts.card-request-show', [
            'confirm' => $this->buildConfirmMessage(),
            'canReview' => $status === CardRequestStatusEnum::PENDING,
            'canStartProcessing' => $status === CardRequestStatusEnum::APPROVED,
            'canComplete' => $status === CardRequestStatusEnum::IN_PROGRESS,
            'canShip' => $status === CardRequestStatusEnum::COMPLETED,
            'canDeliver' => $status === CardRequestStatusEnum::SHIPPED,
            'canCancel' => $this->cardRequest->canBeCancelled(),
            'canExport' => $status === CardRequestStatusEnum::IN_PROGRESS,
        ]);
    }
}
