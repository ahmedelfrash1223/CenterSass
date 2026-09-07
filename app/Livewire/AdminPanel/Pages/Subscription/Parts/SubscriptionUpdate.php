<?php

namespace App\Livewire\AdminPanel\Pages\Subscription\Parts;

use App\Enums\Admin\DiscountTypeEnum;
use App\Enums\Admin\DurationTypeEnum;
use App\Enums\Admin\PaymentStatusEnum;
use App\Enums\Admin\SubscriptionStatusEnum;
use App\Http\Requests\AdminPanel\UpdateSubscriptionRequest;
use App\Models\Subscription;
use App\Models\Package;
use App\Models\Teacher;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class SubscriptionUpdate extends Component
{
    public Subscription $subscription;
    public $teacher_id = '';
    public $package_id = '';
    public $starts_at = '';
    public $ends_at = '';
    public $price = '0';
    public $discount_type = DiscountTypeEnum::PERCENTAGE->value;
    public $discount_value = '0';
    public $price_after_discount = '0';
    public $status = SubscriptionStatusEnum::ACTIVE->value;
    public $is_paid = 0;
    public $paid_at = '';
    public $parent_id = '';
    public array $features = [];

    public bool $updating = false;

    public function mount(Subscription $subscription): void
    {
        $this->subscription = $subscription->load(['features', 'teacher', 'package']);
        $this->teacher_id = $subscription->teacher_id;
        $this->package_id = $subscription->package_id;
        $this->starts_at = $subscription->starts_at?->format('Y-m-d\TH:i');
        $this->ends_at = $subscription->ends_at?->format('Y-m-d\TH:i');
        $this->price = (string) $subscription->price;
        $this->discount_type = $subscription->discount_type ?? DiscountTypeEnum::PERCENTAGE->value;
        $this->discount_value = (string) $subscription->discount_value;
        $this->price_after_discount = (string) $subscription->price_after_discount;
        $this->status = $subscription->status;
        $this->is_paid = $subscription->is_paid ? PaymentStatusEnum::PAID->value : PaymentStatusEnum::UNPAID->value;
        $this->paid_at = $subscription->paid_at?->format('Y-m-d\TH:i') ?? '';
        $this->parent_id = $subscription->parent_id ?? '';
        $this->features = $subscription->features->map(fn ($feature) => [
            'key' => $feature->feature_key,
            'value' => $feature->value['value'] ?? $feature->value,
        ])->values()->toArray();
    }

    public function updatedPackageId(): void
    {
        $package = Package::with('features')->find($this->package_id);
        if (! $package) {
            return;
        }

        $this->price = (string) $package->price;
        $this->discount_type = $package->discount_type?->value ?? DiscountTypeEnum::PERCENTAGE->value;
        $this->discount_value = (string) ($package->discount_value ?? 0);
        $this->features = $package->features->map(fn ($feature) => [
            'key' => $feature->package_feature_key->value,
            'value' => $feature->value['value'] ?? $feature->value,
        ])->values()->toArray();
        $this->calculatePrice();
        $this->calculateEndDate();
    }

    public function updatedStartsAt(): void
    {
        $this->calculateEndDate();
    }

    public function updatedDiscountType(): void
    {
        $this->calculatePrice();
    }

    public function updatedDiscountValue(): void
    {
        $this->calculatePrice();
    }

    public function updatedIsPaid(): void
    {
        if (! $this->is_paid) {
            $this->paid_at = '';
        } elseif ($this->paid_at === '') {
            $this->paid_at = now()->format('Y-m-d\TH:i');
        }
    }

    protected function calculatePrice(): void
    {
        $price = (float) $this->price;
        $discount = (float) $this->discount_value;
        $this->price_after_discount = (string) max(0, $this->discount_type == DiscountTypeEnum::PERCENTAGE->value
            ? $price - ($price * $discount / 100)
            : $price - $discount);
    }

    protected function calculateEndDate(): void
    {
        $package = Package::find($this->package_id);
        if (! $package || ! $this->starts_at) {
            return;
        }

        $start = Carbon::parse($this->starts_at);
        $this->ends_at = match ($package->duration_type->value) {
            DurationTypeEnum::DAY->value => $start->copy()->addDays($package->duration),
            DurationTypeEnum::WEEK->value => $start->copy()->addWeeks($package->duration),
            DurationTypeEnum::MONTH->value => $start->copy()->addMonths($package->duration),
            DurationTypeEnum::YEAR->value => $start->copy()->addYears($package->duration),
        };
    }

    public function update(): void
    {
        $this->updating = true;
        $request = new UpdateSubscriptionRequest();
        $request->merge(['subscription_id' => $this->subscription->id]);
        $this->validate($request->rules(), $request->messages());

        DB::transaction(function () {
            $this->subscription->update([
                'teacher_id' => $this->teacher_id,
                'package_id' => $this->package_id,
                'starts_at' => Carbon::parse($this->starts_at),
                'ends_at' => Carbon::parse($this->ends_at),
                'price' => $this->price,
                'discount_type' => $this->discount_type,
                'discount_value' => $this->discount_value,
                'price_after_discount' => $this->price_after_discount,
                'status' => $this->status,
                'is_paid' => $this->is_paid,
                'paid_at' => $this->is_paid ? ($this->paid_at ?: now()) : null,
                'parent_id' => $this->parent_id ?: null,
            ]);
            $this->subscription->features()->delete();
            foreach ($this->features as $feature) {
                $this->subscription->features()->create(['feature_key' => $feature['key'], 'value' => ['value' => $feature['value']]]);
            }
        });

        $this->updating = false;
        session()->flash('subscription-success', 'تم تحديث الاشتراك بنجاح.');
        $this->redirectRoute('admin.subscriptions.index', navigate: true);
    }

    public function render()
    {
        return view('admin-panel.pages.subscription.parts.subscription-update', [
            'teachers' => Teacher::orderBy('name')->get(),
            'packages' => Package::orderBy('title')->get(),
            'parents' => Subscription::with('teacher')->where('id', '!=', $this->subscription->id)->latest()->limit(50)->get(),
            'discountTypes' => DiscountTypeEnum::options(),
            'statuses' => SubscriptionStatusEnum::options(),
            'paymentStatuses' => [PaymentStatusEnum::UNPAID->value => PaymentStatusEnum::UNPAID->label(), PaymentStatusEnum::PAID->value => PaymentStatusEnum::PAID->label()],
        ]);
    }
}
