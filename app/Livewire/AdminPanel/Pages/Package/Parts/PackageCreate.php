<?php

namespace App\Livewire\AdminPanel\Pages\Package\Parts;

use App\Enums\Admin\DiscountTypeEnum;
use App\Enums\Admin\DurationTypeEnum;
use App\Enums\Admin\PackageFeatureKeyEnum;
use App\Http\Requests\AdminPanel\StorePackageRequest;
use App\Models\Package;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\On;

class PackageCreate extends Component
{
    public string $title = '';
    public string $price = '';
    public int $discount_type = DiscountTypeEnum::PERCENTAGE->value;
    public string $discount_value = '0';
    public string $duration = '';
    public int $duration_type = DurationTypeEnum::MONTH->value;
    public array $features = [];

    public bool $saving = false;
    public bool $savingAndNew = false;

    public function mount(): void
    {
        $this->features = collect(PackageFeatureKeyEnum::cases())->map(fn($feature) => [
            'key' => $feature->value,
            'value' => 0
        ])->toArray();
    }

    public function save(bool $createNew = false): void
    {
        if ($createNew) {
            $this->savingAndNew = true;
        } else {
            $this->saving = true;
        }

        $request = new StorePackageRequest();
        $validated = $this->validate($request->rules(), $request->messages());

        $price = (float)$this->price;
        $discount = (float)$this->discount_value;
        $priceAfterDiscount = $this->discount_type === DiscountTypeEnum::PERCENTAGE->value
            ? $price - ($price * $discount / 100)
            : $price - $discount;
        $priceAfterDiscount = max(0, $priceAfterDiscount);

        $package = Package::create([
            'admin_id' => Auth::guard('admin')->id(),
            'title' => $this->title,
            'price' => $price,
            'discount_type' => $this->discount_type,
            'discount_value' => $discount,
            'price_after_discount' => $priceAfterDiscount,
            'duration' => (int)$this->duration,
            'duration_type' => $this->duration_type,
        ]);

        foreach ($this->features as $feature) {
            $package->features()->create([
                'package_feature_key' => $feature['key'],
                'value' => ['value' => $feature['value']]
            ]);
        }

        $this->saving = false;
        $this->savingAndNew = false;

        if ($createNew) {
            $this->redirectRoute('admin.package.create', navigate: true);
            return;
        }

        session()->flash('success', 'تم إنشاء الباقة بنجاح');
        $this->redirectRoute('admin.package.index', navigate: true);
    }

    public function render()
    {
        return view('admin-panel.pages.package.parts.package-create', [
            'discountTypes' => DiscountTypeEnum::options(),
            'durationTypes' => DurationTypeEnum::options(),
            'featureTypes' => PackageFeatureKeyEnum::cases()
        ]);
    }
}
