<?php

namespace App\Livewire\AdminPanel\Pages\Dashboard\Parts;

use App\Enums\Admin\SubscriptionStatusEnum;
use App\Models\Admin;
use App\Models\Package;
use App\Models\Student;
use App\Models\Subscription;
use App\Models\Teacher;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;

class Data extends Component
{
    public int $totalTeachers = 0;
    public int $activeTeachers = 0;
    public int $inactiveTeachers = 0;
    public int $totalStudents = 0;
    public int $totalPackages = 0;
    public int $totalSubscriptions = 0;
    public int $activeSubscriptions = 0;
    public int $expiredSubscriptions = 0;
    public int $pendingSubscriptions = 0;
    public int $totalAdmins = 0;
    public int $newTeachersThisMonth = 0;
    public int $newSubscriptionsThisMonth = 0;
    public float $totalRevenue = 0;
    public float $monthlyRevenue = 0;
    public float $teachersGrowth = 0;
    public float $subscriptionsGrowth = 0;
    public float $revenueGrowth = 0;

    public function mount(): void
    {
        $this->loadStatistics();
    }

    protected function loadStatistics(): void
    {
        $statistics = Cache::remember('admin-dashboard-statistics', now()->addMinutes(5), function (): array {
            $startOfMonth = now()->startOfMonth();
            $startOfPreviousMonth = now()->subMonth()->startOfMonth();
            $endOfPreviousMonth = now()->startOfMonth();

            $currentTeachers = Teacher::count();
            $previousTeachers = Teacher::where('created_at', '<', $startOfPreviousMonth)->count();
            $currentSubscriptions = Subscription::count();
            $previousSubscriptions = Subscription::where('created_at', '<', $startOfPreviousMonth)->count();
            $currentRevenue = (float) Subscription::where('is_paid', true)
                ->where('created_at', '>=', $startOfMonth)
                ->sum('price_after_discount');
            $previousRevenue = (float) Subscription::where('is_paid', true)
                ->whereBetween('created_at', [$startOfPreviousMonth, $endOfPreviousMonth])
                ->sum('price_after_discount');

            return [
                'totalTeachers' => $currentTeachers,
                'activeTeachers' => Teacher::where('is_active', 1)->count(),
                'inactiveTeachers' => Teacher::where('is_active', 0)->count(),
                'totalStudents' => Student::count(),
                'totalPackages' => Package::count(),
                'totalSubscriptions' => $currentSubscriptions,
                'activeSubscriptions' => Subscription::where('status', SubscriptionStatusEnum::ACTIVE->value)->count(),
                'expiredSubscriptions' => Subscription::where('status', SubscriptionStatusEnum::EXPIRED->value)->count(),
                'pendingSubscriptions' => Subscription::where('status', SubscriptionStatusEnum::PENDING->value)->count(),
                'totalAdmins' => Admin::count(),
                'newTeachersThisMonth' => Teacher::where('created_at', '>=', $startOfMonth)->count(),
                'newSubscriptionsThisMonth' => Subscription::where('created_at', '>=', $startOfMonth)->count(),
                'totalRevenue' => (float) Subscription::where('is_paid', true)->sum('price_after_discount'),
                'monthlyRevenue' => $currentRevenue,
                'teachersGrowth' => $this->growth($currentTeachers, $previousTeachers),
                'subscriptionsGrowth' => $this->growth($currentSubscriptions, $previousSubscriptions),
                'revenueGrowth' => $this->growth($currentRevenue, $previousRevenue),
            ];
        });

        foreach ($statistics as $key => $value) {
            $this->{$key} = $value;
        }
    }

    protected function growth(float|int $current, float|int $previous): float
    {
        // Prevent division by zero
        if ($previous === 0 || $previous === 0.0) {
            return $current > 0 ? 100 : 0;
        }

        $change = (($current - $previous) / $previous) * 100;

        return round($change, 1);
    }

    public function render()
    {
        return view('admin-panel.pages.dashboard.parts.dashboard-stats');
    }
}
