<?php

namespace App\Livewire\AdminPanel\Pages\Subscription\Parts;

use App\Enums\Admin\DiscountTypeEnum;
use App\Enums\Admin\DurationTypeEnum;
use App\Enums\Admin\PaymentStatusEnum;
use App\Enums\Admin\SubscriptionStatusEnum;
use App\Http\Requests\AdminPanel\StoreSubscriptionRequest;
use App\Models\Subscription;
use App\Models\Package;
use App\Models\Teacher;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class SubscriptionCreate extends Component
{
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

    public bool $saving = false;
    public bool $savingAndNew = false;

    public function mount(): void
    {
        $this->starts_at = now()->format('Y-m-d\TH:i');
        $this->loadPackage();
    }

    public function updatedPackageId(): void
    {
        $this->loadPackage();
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

    protected function loadPackage(): void
    {
        $package = $this->package_id ? Package::with('features')->find($this->package_id) : null;
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
        $package = $this->package_id ? Package::find($this->package_id) : null;
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

    public function save(bool $createNew = false): void
    {
        $createNew ? $this->savingAndNew = true : $this->saving = true;
        $request = new StoreSubscriptionRequest();
        $this->validate($request->rules(), $request->messages());

        DB::transaction(function () {
            $subscription = Subscription::create($this->payload());
            $this->syncFeatures($subscription);
        });

        $this->saving = false;
        $this->savingAndNew = false;
        if ($createNew) {
            $this->reset(['teacher_id', 'package_id', 'ends_at', 'price', 'discount_value', 'price_after_discount', 'paid_at', 'parent_id', 'features']);
            $this->starts_at = now()->format('Y-m-d\TH:i');
            session()->flash('subscription-success', 'تم إنشاء الاشتراك بنجاح. يمكنك إضافة اشتراك آخر.');
            return;
        }

        session()->flash('subscription-success', 'تم إنشاء الاشتراك بنجاح.');
        $this->redirectRoute('admin.subscriptions.index', navigate: true);
    }

    protected function payload(): array
    {
        return [
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
        ];
    }

    protected function syncFeatures(Subscription $subscription): void
    {
        foreach ($this->features as $feature) {
            $subscription->features()->create(['feature_key' => $feature['key'], 'value' => ['value' => $feature['value']]]);
        }
    }

    public function render()
    {
        return view('admin-panel.pages.subscription.parts.subscription-create', [
            'teachers' => Teacher::orderBy('name')->get(),
            'packages' => Package::orderBy('title')->get(),
            'parents' => Subscription::with('teacher')->latest()->limit(50)->get(),
            'discountTypes' => DiscountTypeEnum::options(),
            'statuses' => SubscriptionStatusEnum::options(),
            'paymentStatuses' => [PaymentStatusEnum::UNPAID->value => PaymentStatusEnum::UNPAID->label(), PaymentStatusEnum::PAID->value => PaymentStatusEnum::PAID->label()],
        ]);
    }
}
