<?php

namespace Database\Seeders;

use App\Enums\Admin\DiscountTypeEnum;
use App\Enums\Admin\DurationTypeEnum;
use App\Enums\Admin\PackageFeatureKeyEnum;
use App\Models\Admin;
use App\Models\Package;
use Illuminate\Database\Seeder;

class PackageSeeder extends Seeder
{
    public function run(): void
    {
        // Get first admin or create one if none exists
        $admin = Admin::first() ?? Admin::factory()->create();

        $freePackage = Package::create([
            'admin_id' => $admin->id,
            'title' => 'الباقة المجانية',
            'price' => 0.00,
            'discount_type' => DiscountTypeEnum::VALUE->value,
            'discount_value' => 0.00,
            'price_after_discount' => 0.00,
            'duration' => 30, // 30 days
            'duration_type' => DurationTypeEnum::DAY->value,
        ]);

        $freePackage->features()->createMany([
            [
                'package_feature_key' => PackageFeatureKeyEnum::MAX_STUDENTS->value,
                'value' => ['value' => 5],
            ],
            [
                'package_feature_key' => PackageFeatureKeyEnum::MAX_GROUPS->value,
                'value' => ['value' => 2],
            ],
            [
                'package_feature_key' => PackageFeatureKeyEnum::MAX_EMPLOYEES->value,
                'value' => ['value' => 1],
            ],
            [
                'package_feature_key' => PackageFeatureKeyEnum::MAX_BRANCHES->value,
                'value' => ['value' => 2],
            ],
        ]);


    }
}
