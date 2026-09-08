<?php

namespace App\Livewire\AdminPanel\Pages\SubscriptionRequest\Parts;

use App\Enums\Admin\SubscriptionRequestStatusEnum;
use App\Enums\Admin\SubscriptionStatusEnum;
use App\Models\Subscription;
use App\Models\SubscriptionHistory;
use App\Models\SubscriptionRequest;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class RequestData extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $statusFilter = '1';
    public $deleteId = null;

    // For approve/reject confirmation
    public $showApproveConfirm = false;
    public $showRejectConfirm = false;
    public $selectedRequestId = null;
    public $rejectReason = '';

    protected $listeners = [
        'refreshData' => '$refresh',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function confirmApprove($id)
    {
        $this->selectedRequestId = $id;
        $this->showApproveConfirm = true;
        $this->rejectReason = '';
    }

    public function cancelApprove()
    {
        $this->showApproveConfirm = false;
        $this->selectedRequestId = null;
    }

    public function confirmReject($id)
    {
        $this->selectedRequestId = $id;
        $this->showRejectConfirm = true;
        $this->rejectReason = '';
    }

    public function cancelReject()
    {
        $this->showRejectConfirm = false;
        $this->selectedRequestId = null;
        $this->rejectReason = '';
    }

    public function approve()
    {
        if (!$this->selectedRequestId) {
            return;
        }

        $request = SubscriptionRequest::with(['teacher', 'requestedPackage', 'currentSubscription'])
            ->findOrFail($this->selectedRequestId);

        // Check if request is still pending
        if ($request->status !== SubscriptionRequestStatusEnum::PENDING->value) {
            session()->flash('request-error', 'هذا الطلب تم معالجته مسبقاً.');
            $this->showApproveConfirm = false;
            $this->selectedRequestId = null;
            return;
        }

        // Get current subscription or create new one
        $currentSubscription = $request->currentSubscription;

        if ($currentSubscription) {
            // Update existing subscription
            $oldData = [
                'package_id' => $currentSubscription->package_id,
                'status' => $currentSubscription->status,
                'ends_at' => $currentSubscription->ends_at?->toDateTimeString(),
            ];

            $currentSubscription->update([
                'package_id' => $request->requested_package_id,
                'status' => SubscriptionStatusEnum::ACTIVE->value,
                'ends_at' => now()->addDays($request->requestedPackage->duration),
            ]);

            // Create history record
            SubscriptionHistory::create([
                'subscription_id' => $currentSubscription->id,
                'teacher_id' => $request->teacher_id,
                'package_id' => $request->requested_package_id,
                'action_type' => 'upgraded',
                'old_data' => $oldData,
                'new_data' => [
                    'package_id' => $request->requested_package_id,
                    'status' => SubscriptionStatusEnum::ACTIVE->value,
                    'ends_at' => now()->addDays($request->requestedPackage->duration)->toDateTimeString(),
                ],
                'performed_by' => Auth::guard('admin')->id(),
            ]);

        } else {
            // Create new subscription
            $subscription = Subscription::create([
                'teacher_id' => $request->teacher_id,
                'package_id' => $request->requested_package_id,
                'starts_at' => now(),
                'ends_at' => now()->addDays($request->requestedPackage->duration),
                'price' => $request->requestedPackage->price,
                'discount_type' => $request->requestedPackage->discount_type,
                'discount_value' => $request->requestedPackage->discount_value,
                'price_after_discount' => $request->requestedPackage->price_after_discount,
                'status' => SubscriptionStatusEnum::ACTIVE->value,
                'is_paid' => false,
            ]);

            // Create history record
            SubscriptionHistory::create([
                'subscription_id' => $subscription->id,
                'teacher_id' => $request->teacher_id,
                'package_id' => $request->requested_package_id,
                'action_type' => 'created',
                'new_data' => [
                    'package_id' => $request->requested_package_id,
                    'status' => SubscriptionStatusEnum::ACTIVE->value,
                ],
                'performed_by' => Auth::guard('admin')->id(),
            ]);
        }

        // Update request status
        $request->update([
            'status' => SubscriptionRequestStatusEnum::APPROVED->value,
            'approved_at' => now(),
            'processed_by' => Auth::guard('admin')->id(),
        ]);

        $this->showApproveConfirm = false;
        $this->selectedRequestId = null;

        session()->flash('request-success', 'تم قبول طلب تغيير الباقة بنجاح.');
        $this->dispatch('refreshData');
    }

    public function reject()
    {
        if (!$this->selectedRequestId) {
            return;
        }

        $request = SubscriptionRequest::findOrFail($this->selectedRequestId);

        // Check if request is still pending
        if ($request->status !== SubscriptionRequestStatusEnum::PENDING->value) {
            session()->flash('request-error', 'هذا الطلب تم معالجته مسبقاً.');
            $this->showRejectConfirm = false;
            $this->selectedRequestId = null;
            $this->rejectReason = '';
            return;
        }

        // Update request status
        $request->update([
            'status' => SubscriptionRequestStatusEnum::REJECTED->value,
            'rejected_at' => now(),
            'processed_by' => Auth::guard('admin')->id(),
            'notes' => $this->rejectReason ?: $request->notes,
        ]);

        $this->showRejectConfirm = false;
        $this->selectedRequestId = null;
        $this->rejectReason = '';

        session()->flash('request-success', 'تم رفض طلب تغيير الباقة.');
        $this->dispatch('refreshData');
    }

    public function render()
    {
        $requests = SubscriptionRequest::with(['teacher', 'requestedPackage', 'currentSubscription', 'processedBy'])
            ->when($this->search !== '', function ($query) {
                $query->whereHas('teacher', function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%');
                })->orWhereHas('requestedPackage', function ($q) {
                    $q->where('title', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->statusFilter !== '', function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin-panel.pages.subscription-request.parts.request-data', [
            'requests' => $requests,
            'statusOptions' => SubscriptionRequestStatusEnum::options(),
        ]);
    }
}
