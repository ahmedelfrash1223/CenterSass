<?php

namespace App\Livewire\AdminPanel\Pages\Package\Parts;

use App\Enums\Admin\DiscountTypeEnum;
use App\Enums\Admin\DurationTypeEnum;
use App\Enums\Admin\PackageFeatureKeyEnum;
use App\Http\Requests\AdminPanel\StorePackageRequest;
use App\Models\Package;
use Livewire\Component;

class PackageUpdate extends Component
{
    public Package $package;
    public string $title = '';
    public string $price = '';
    public int $discount_type = 1;
    public string $discount_value = '0';
    public string $duration = '';
    public int $duration_type = 3;
    public array $features = [];

    public bool $updating = false;

    public function mount(Package $package): void
    {
        $this->package = $package->load('features');
        $this->title = $package->title;
        $this->price = (string)$package->price;
        $this->discount_type = $package->discount_type->value;
        $this->discount_value = (string)$package->discount_value;
        $this->duration = (string)$package->duration;
        $this->duration_type = $package->duration_type->value;
        $this->features = collect(PackageFeatureKeyEnum::cases())->map(function ($feature) use ($package) {
            $item = $package->features->firstWhere('package_feature_key', $feature->value);
            return ['key' => $feature->value, 'value' => $item?->value['value'] ?? 0];
        })->toArray();
    }

    public function update(): void
    {
        $this->updating = true;

        $request = new StorePackageRequest();
        $this->validate($request->rules(), $request->messages());

        $price = (float)$this->price;
        $discount = (float)$this->discount_value;
        $priceAfterDiscount = $this->discount_type === DiscountTypeEnum::PERCENTAGE->value
            ? $price - ($price * $discount / 100)
            : $price - $discount;
        $priceAfterDiscount = max(0, $priceAfterDiscount);

        $this->package->update([
            'title' => $this->title,
            'price' => $price,
            'discount_type' => $this->discount_type,
            'discount_value' => $discount,
            'price_after_discount' => $priceAfterDiscount,
            'duration' => (int)$this->duration,
            'duration_type' => $this->duration_type
        ]);

        $this->package->features()->delete();
        foreach ($this->features as $feature) {
            $this->package->features()->create([
                'package_feature_key' => $feature['key'],
                'value' => ['value' => $feature['value']]
            ]);
        }

        $this->updating = false;

        session()->flash('package-success', 'تم تحديث الباقة بنجاح');
        $this->redirectRoute('admin.package.index', navigate: true);
    }

    public function render()
    {
        return view('admin-panel.pages.package.parts.package-update', [
            'discountTypes' => DiscountTypeEnum::options(),
            'durationTypes' => DurationTypeEnum::options(),
            'featureTypes' => PackageFeatureKeyEnum::cases(),
        ]);
    }
}
