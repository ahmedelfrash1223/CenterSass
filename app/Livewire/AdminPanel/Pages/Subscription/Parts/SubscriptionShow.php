<?php

namespace App\Livewire\AdminPanel\Pages\Subscription\Parts;

use App\Models\Subscription;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class SubscriptionShow extends Component
{
    public Subscription $subscription;
    public bool $confirmingDelete = false;
    public bool $renewing = false;

    public function mount(Subscription $subscription): void
    {
        $this->subscription = $subscription->load(['teacher', 'package', 'parent', 'renewals.teacher', 'features']);
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
        $this->renewing = true;
        $package = $this->subscription->package;
        $start = $this->subscription->ends_at?->copy() ?? now();
        $end = match ($package->duration_type->value) {
            1 => $start->copy()->addDays($package->duration),
            2 => $start->copy()->addWeeks($package->duration),
            3 => $start->copy()->addMonths($package->duration),
            4 => $start->copy()->addYears($package->duration),
        };

        $renewal = DB::transaction(function () use ($package, $start, $end) {
            $renewal = Subscription::create([
                'teacher_id' => $this->subscription->teacher_id,
                'package_id' => $package->id,
                'starts_at' => $start,
                'ends_at' => $end,
                'price' => $package->price,
                'discount_type' => $package->discount_type?->value,
                'discount_value' => $package->discount_value ?? 0,
                'price_after_discount' => $package->price_after_discount,
                'status' => 1,
                'is_paid' => false,
                'parent_id' => $this->subscription->id,
            ]);
            foreach ($package->features as $feature) {
                $renewal->features()->create([
                    'feature_key' => $feature->package_feature_key->value,
                    'value' => $feature->value,
                ]);
            }
            return $renewal;
        });

        $this->redirectRoute('admin.subscriptions.show', $renewal->id, navigate: true);
    }

    public function render()
    {
        return view('admin-panel.pages.subscription.parts.subscription-show');
    }
}