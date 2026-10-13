<?php

namespace App\Livewire\TeacherPanel\Pages\CardRequest\Parts;

use App\Enums\Teacher\RevenueSourceEnum;
use App\Enums\Teacher\TeacherPermissionEnum;
use App\Models\CardRequest;
use App\Services\Teacher\RevenueService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class CardRequestShow extends Component
{
    public CardRequest $cardRequest;

    public array $paidItemIds = [];

    public function mount(CardRequest $cardRequest): void
    {
        $user = Auth::guard('teacher')->user();

        abort_unless(
            $user->hasPermission(TeacherPermissionEnum::CARD_REQUEST_VIEW),
            403
        );

        abort_unless($cardRequest->teacher_id === $user->getOwnerId(), 404);

        if (! $user->isOwner()) {
            $accessibleBranches = $user->getAccessibleBranchIds();
            $hasAccess = $cardRequest->items()
                ->whereIn('branch_id', $accessibleBranches)
                ->exists();

            abort_unless($hasAccess, 404);
        }

        $this->cardRequest = $cardRequest->load(['items.branch', 'items.group', 'creator']);
    }

    public function togglePaid(int $itemId): void
    {
        if (in_array($itemId, $this->paidItemIds)) {
            $this->paidItemIds = array_values(array_diff($this->paidItemIds, [$itemId]));
        } else {
            $this->paidItemIds[] = $itemId;
        }
    }

    public function selectAllUnpaid(): void
    {
        $this->paidItemIds = $this->cardRequest->items()
            ->where('is_paid', false)
            ->pluck('id')
            ->toArray();
    }

    public function clearSelection(): void
    {
        $this->paidItemIds = [];
    }

    public function markAsPaid(RevenueService $revenueService): void
    {
        $user = Auth::guard('teacher')->user();

        if (empty($this->paidItemIds)) {
            $this->dispatch('show-error-message', message: 'اختر طالب واحد على الأقل.');

            return;
        }

        $items = $this->cardRequest->items()
            ->whereIn('id', $this->paidItemIds)
            ->where('is_paid', false)
            ->get();

        if ($items->isEmpty()) {
            $this->dispatch('show-error-message', message: 'لا توجد بنود صالحة.');

            return;
        }

        DB::transaction(function () use ($user, $items, $revenueService) {
            $totalPaid = 0.0;

            foreach ($items as $item) {
                $revenue = $revenueService->record(
                    user: $user,
                    source: RevenueSourceEnum::CARD_REQUEST,
                    amount: (float) $item->card_price,
                    sourceId: $this->cardRequest->id,
                    branchId: $item->branch_id,
                    studentId: $item->student_id,
                    description: "دفع كارت - {$item->student_name}",
                    meta: [
                        'card_request_reference' => $this->cardRequest->reference,
                        'item_id' => $item->id,
                    ],
                    occurredAt: now(),
                );

                $item->update([
                    'is_paid' => true,
                    'paid_at' => now(),
                    'paid_by' => $user->id,
                    'revenue_id' => $revenue->id,
                ]);

                $totalPaid += (float) $item->card_price;
            }

            $this->cardRequest->refresh();
            $paidItems = $this->cardRequest->items()->where('is_paid', true)->get();

            $this->cardRequest->update([
                'paid_amount' => $paidItems->sum('card_price'),
                'paid_cards_count' => $paidItems->count(),
            ]);
        });

        $this->cardRequest->refresh()->load(['items.branch', 'items.group']);
        $this->paidItemIds = [];

        $this->dispatch('revenue-recorded');
        $this->dispatch('show-success-message', message: 'تم تسجيل الدفع بنجاح.');
    }

    public function render()
    {
        $user = Auth::guard('teacher')->user();

        return view('teacher-panel.pages.card-request.parts.card-request-show', [
            'canMarkPaid' => $user->hasPermission(TeacherPermissionEnum::CARD_REQUEST_UPDATE),
        ]);
    }
}
