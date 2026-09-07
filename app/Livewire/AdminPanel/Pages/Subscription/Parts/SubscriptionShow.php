<?php

namespace App\Livewire\AdminPanel\Pages\Subscription\Parts;

use App\Enums\Admin\DurationTypeEnum;
use App\Models\Subscription;
use App\Models\SubscriptionHistory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class SubscriptionShow extends Component
{
    public Subscription $subscription;
    public bool $confirmingDelete = false;
    public bool $renewing = false;
    public bool $renewalDisabled = false;

    public function mount(Subscription $subscription): void
    {
        $this->subscription = $subscription->load(['teacher', 'package', 'parent.package', 'renewals.teacher', 'features', 'histories.admin']);
        $this->renewalDisabled = $this->isRenewalExpired();
    }

    public function confirmDelete(): void
    {
        $this->confirmingDelete = true;
    }

    public function cancelDelete(): void
    {
        $this->confirmingDelete = false;
    }

    public function delete(): void
    {
        $this->subscription->delete();
        session()->flash('subscription-success', 'تم حذف الاشتراك بنجاح.');
        $this->redirectRoute('admin.subscriptions.index', navigate: true);
    }

    public function renew(): void
    {
        if (! $this->subscription->is_paid) {
            $this->addError('renewal', 'لا يمكن تجديد اشتراك غير مدفوع.');
            return;
        }

        if ($this->isRenewalExpired()) {
            $this->addError('renewal', 'انتهت مدة السماح لتجديد هذا الاشتراك.');
            return;
        }

        $this->renewing = true;
        $package = $this->subscription->package;
        $start = $this->subscription->ends_at?->copy() ?? now();
        $end = match ($package->duration_type->value) {
            DurationTypeEnum::DAY->value => $start->copy()->addDays($package->duration),
            DurationTypeEnum::WEEK->value => $start->copy()->addWeeks($package->duration),
            DurationTypeEnum::MONTH->value => $start->copy()->addMonths($package->duration),
            DurationTypeEnum::YEAR->value => $start->copy()->addYears($package->duration),
        };

        $newData = DB::transaction(function () use ($end) {
            $oldData = $this->subscription->fresh()->toArray();
            $this->subscription->update([
                'ends_at' => $end,
                'status' => 1,
            ]);

            $newData = $this->subscription->fresh()->toArray();
            SubscriptionHistory::create([
                'subscription_id' => $this->subscription->id,
                'teacher_id' => $this->subscription->teacher_id,
                'package_id' => $this->subscription->package_id,
                'action_type' => 'renewed',
                'old_data' => $oldData,
                'new_data' => $newData,
                'performed_by' => Auth::guard('admin')->id(),
            ]);

            return $newData;
        });

        $this->renewing = false;
        $newEndDate = date('Y-m-d H:i', strtotime($newData['ends_at']));
        session()->flash('subscription-success', "تم تجديد الاشتراك بنجاح. التاريخ الجديد: {$newEndDate}");
        $this->redirectRoute('admin.subscriptions.show', $this->subscription->id, navigate: true);
    }

    protected function isRenewalExpired(): bool
    {
        return $this->subscription->ends_at
            && $this->subscription->ends_at->lt(now()->subDays(config('subscriptions.renewal_expiry_days', 30)));
    }

    public function render()
    {
        return view('admin-panel.pages.subscription.parts.subscription-show');
    }
}