<?php

namespace App\Livewire\TeacherPanel\Pages\Profile\Parts;

use App\Enums\Admin\SubscriptionRequestStatusEnum;
use App\Models\Package;
use App\Models\Subscription;
use App\Models\SubscriptionHistory;
use App\Models\SubscriptionRequest;
use App\Models\Teacher;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class SubscriptionInfo extends Component
{
    public ?Teacher $teacher = null;
    public ?Subscription $currentSubscription = null;
    public $packages;
    public $pendingRequest = null;
    public $subscriptionHistories = [];

    public ?int $selectedPackageId = null;
    public string $requestNotes = '';
    public bool $isSubmitting = false;

    // For cancel confirmation
    public bool $showCancelConfirm = false;

    public function mount(): void
    {
        $this->teacher = Auth::guard('teacher')->user();
        $this->loadData();
    }

    public function loadData(): void
    {
        // Load current subscription
        $this->currentSubscription = Subscription::where('teacher_id', $this->teacher->id)
            ->where('status', '1')
            ->with(['package', 'features'])
            ->first();

        // Load all packages
        $this->packages = Package::with(['features'])->get();

        // Check if there's a pending request
        $this->pendingRequest = SubscriptionRequest::where('teacher_id', $this->teacher->id)
            ->where('status', SubscriptionRequestStatusEnum::PENDING->value)
            ->with(['requestedPackage'])
            ->first();

        // Load subscription histories
        $this->subscriptionHistories = SubscriptionHistory::where('teacher_id', $this->teacher->id)
            ->with(['subscription', 'package'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
    }

    public function selectPackage(int $packageId): void
    {
        $this->selectedPackageId = $packageId;
        $this->requestNotes = '';
    }

    public function submitRequest(): void
    {
        $this->isSubmitting = true;

        // Validate
        if (!$this->selectedPackageId) {
            $this->addError('selectedPackageId', 'يرجى اختيار باقة.');
            $this->isSubmitting = false;
            return;
        }

        // Check if there's already a pending request
        if ($this->pendingRequest) {
            session()->flash('subscription-error', 'لديك طلب تغيير باقة قيد الانتظار بالفعل.');
            $this->isSubmitting = false;
            return;
        }

        // Create request
        SubscriptionRequest::create([
            'teacher_id' => $this->teacher->id,
            'current_subscription_id' => $this->currentSubscription?->id,
            'requested_package_id' => $this->selectedPackageId,
            'status' => SubscriptionRequestStatusEnum::PENDING->value,
            'notes' => $this->requestNotes,
        ]);

        $this->isSubmitting = false;
        $this->selectedPackageId = null;
        $this->requestNotes = '';

        // Refresh data
        $this->loadData();

        session()->flash('subscription-success', 'تم إرسال طلب تغيير الباقة بنجاح. سيتم مراجعته من قبل الإدارة.');
    }

    public function confirmCancelRequest(): void
    {
        $this->showCancelConfirm = true;
    }

    public function cancelCancelRequest(): void
    {
        $this->showCancelConfirm = false;
    }

    public function cancelRequest(): void
    {
        if ($this->pendingRequest) {
            $this->pendingRequest->update([
                'status' => SubscriptionRequestStatusEnum::CANCELLED->value,
            ]);

            $this->showCancelConfirm = false;
            $this->loadData();
            session()->flash('subscription-success', 'تم إلغاء طلب تغيير الباقة.');
        }
    }

    public function render()
    {
        return view('teacher-panel.pages.profile.parts.subscription-info');
    }
}
