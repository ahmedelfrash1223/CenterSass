<!-- resources/views/teacher-panel/pages/profile/parts/subscription-info.blade.php -->
<div>
    @if(session()->has('subscription-success'))
        <div class="alert alert-success d-flex align-items-center gap-2 border-0 shadow-sm mb-4">
            <i class="ph-duotone ph-check-circle fs-4"></i>
            <span>{{ session('subscription-success') }}</span>
        </div>
    @endif

    @if(session()->has('subscription-error'))
        <div class="alert alert-danger d-flex align-items-center gap-2 border-0 shadow-sm mb-4">
            <i class="ph-duotone ph-x-circle fs-4"></i>
            <span>{{ session('subscription-error') }}</span>
        </div>
    @endif

    <!-- Current Subscription -->
    <div class="card border-0 shadow-sm overflow-hidden mt-4">
        <div class="card-header bg-success text-white border-0 p-4">
            <div class="d-flex align-items-center gap-3">
                <div class="avtar avtar-s bg-white text-success rounded-3">
                    <i class="ph-duotone ph-check-circle fs-4"></i>
                </div>
                <div>
                    <h5 class="mb-0 text-white fw-bold">الاشتراك الحالي</h5>
                    <small class="text-white opacity-75">تفاصيل باقة الاشتراك الخاصة بك</small>
                </div>
            </div>
        </div>

        <div class="card-body p-0">
            @if($currentSubscription)
                <div class="p-4 border-bottom">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="text-muted small d-block mb-1">الباقة</label>
                            <h6 class="fw-bold mb-0">{{ $currentSubscription->package->title }}</h6>
                        </div>
                        <div class="col-md-3">
                            <label class="text-muted small d-block mb-1">السعر</label>
                            <span class="fw-bold">{{ number_format($currentSubscription->price, 2) }} ج.م</span>
                        </div>
                        <div class="col-md-3">
                            <label class="text-muted small d-block mb-1">المدة</label>
                            <span class="fw-bold">{{ $currentSubscription->package->duration }} {{ $currentSubscription->package->duration_type->label() }}</span>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small d-block mb-1">تاريخ البداية</label>
                            <span>{{ $currentSubscription->starts_at?->format('Y-m-d') ?? 'غير محدد' }}</span>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small d-block mb-1">تاريخ الانتهاء</label>
                            <span>{{ $currentSubscription->ends_at?->format('Y-m-d') ?? 'غير محدد' }}</span>
                        </div>
                        @if($currentSubscription->discount_value > 0)
                            <div class="col-12">
                                <span class="badge bg-warning bg-opacity-10 text-warning">
                                    <i class="ph-duotone ph-tag me-1"></i>
                                    خصم {{ $currentSubscription->discount_value }} {{ $currentSubscription->discount_type->label() }}
                                </span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Features -->
                @if($currentSubscription->features->count() > 0)
                    <div class="p-4 border-bottom">
                        <h6 class="fw-bold mb-3">خصائص الباقة</h6>
                        <div class="row g-2">
                            @foreach($currentSubscription->features as $feature)
                                @php
                                    $featureKey = $feature->feature_key instanceof \BackedEnum
                                        ? $feature->feature_key->value
                                        : $feature->feature_key;
                                    $featureEnum = \App\Enums\Admin\PackageFeatureKeyEnum::tryFrom($featureKey);
                                @endphp
                                <div class="col-md-4">
                                    <div class="d-flex align-items-center gap-2 p-2 bg-light rounded-2">
                                        <i class="ph-duotone ph-check-circle text-success"></i>
                                        <span>
                                            {{ $featureEnum?->label() ?? 'ميزة غير معروفة' }}:
                                            <strong>{{ $feature->value['value'] ?? 0 }}</strong>
                                            {{ $featureEnum?->unit() ?? '' }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            @else
                <div class="p-4 text-center">
                    <i class="ph-duotone ph-warning fs-1 d-block text-muted mb-2"></i>
                    <p class="text-muted mb-0">لا يوجد اشتراك نشط حالياً</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Pending Request Status -->
    @if($pendingRequest)
        <div class="card border-0 shadow-sm overflow-hidden mt-4 border-warning">
            <div class="card-header bg-warning bg-opacity-10 border-warning p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <i class="ph-duotone ph-clock fs-4 text-warning"></i>
                        <span class="fw-bold">طلب تغيير باقة قيد الانتظار</span>
                    </div>
                    <span class="badge bg-warning">قيد الانتظار</span>
                </div>
            </div>
            <div class="card-body">
                <p class="mb-1">الباقة المطلوبة: <strong>{{ $pendingRequest->requestedPackage->title }}</strong></p>
                <p class="text-muted small mb-2">تم الإرسال: {{ $pendingRequest->created_at->format('Y-m-d h:i A') }}</p>
                @if($pendingRequest->notes)
                    <p class="text-muted small mb-2">ملاحظات: {{ $pendingRequest->notes }}</p>
                @endif

                @if(!$showCancelConfirm)
                    <button type="button" class="btn btn-danger btn-sm" wire:click="confirmCancelRequest">
                        <i class="ph-duotone ph-x me-1"></i>
                        إلغاء الطلب
                    </button>
                @else
                    <div class="d-flex align-items-center gap-2 mt-2">
                        <span class="text-muted small">هل أنت متأكد من إلغاء الطلب؟</span>
                        <button type="button" class="btn btn-success btn-sm" wire:click="cancelRequest">
                            <i class="ph-duotone ph-check me-1"></i>
                            تأكيد الإلغاء
                        </button>
                        <button type="button" class="btn btn-secondary btn-sm" wire:click="cancelCancelRequest">
                            <i class="ph-duotone ph-x me-1"></i>
                            إلغاء
                        </button>
                    </div>
                @endif
            </div>
        </div>
    @endif

    <!-- Available Packages -->
    <div class="card border-0 shadow-sm overflow-hidden mt-4">
        <div class="card-header bg-primary text-white border-0 p-4">
            <div class="d-flex align-items-center gap-3">
                <div class="avtar avtar-s bg-white text-primary rounded-3">
                    <i class="ph-duotone ph-package fs-4"></i>
                </div>
                <div>
                    <h5 class="mb-0 text-white fw-bold">الباقات المتاحة</h5>
                    <small class="text-white opacity-75">اختر الباقة التي تناسبك</small>
                </div>
            </div>
        </div>

        <div class="card-body p-0">
            @if($packages->count() > 0)
                <div class="p-4">
                    <div class="row g-3">
                        @foreach($packages as $package)
                            <div class="col-md-6 col-lg-4">
                                <div class="card h-100 border-0 shadow-sm {{ $selectedPackageId == $package->id ? 'border-primary border-2' : '' }}"
                                     wire:click="selectPackage({{ $package->id }})"
                                     style="cursor: pointer; transition: all 0.3s;">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <h6 class="fw-bold mb-0">{{ $package->title }}</h6>
                                            @if($selectedPackageId == $package->id)
                                                <span class="badge bg-primary">
                                                    <i class="ph-duotone ph-check"></i>
                                                    مختار
                                                </span>
                                            @endif
                                        </div>

                                        <div class="mb-2">
                                            @if($package->discount_value > 0)
                                                <span class="text-decoration-line-through text-muted small">
                                                    {{ number_format($package->price, 2) }} ج.م
                                                </span>
                                                <span class="fw-bold text-success">
                                                    {{ number_format($package->price_after_discount, 2) }} ج.م
                                                </span>
                                                <span class="badge bg-danger bg-opacity-10 text-danger small">
                                                    -{{ $package->discount_value }}{{ $package->discount_type->label() == 'نسبة مئوية' ? '%' : ' ج.م' }}
                                                </span>
                                            @else
                                                <span class="fw-bold">{{ number_format($package->price, 2) }} ج.م</span>
                                            @endif
                                        </div>

                                        <div class="text-muted small mb-2">
                                            <i class="ph-duotone ph-clock me-1"></i>
                                            {{ $package->duration }} {{ $package->duration_type->label() }}
                                        </div>

                                        @if($package->features->count() > 0)
                                            <div class="mt-2">
                                                @foreach($package->features as $feature)
                                                    @php
                                                        $featureKey = $feature->package_feature_key instanceof \BackedEnum
                                                            ? $feature->package_feature_key->value
                                                            : $feature->package_feature_key;
                                                        $featureEnum = \App\Enums\Admin\PackageFeatureKeyEnum::tryFrom($featureKey);
                                                    @endphp
                                                    <div class="d-flex align-items-center gap-1 small">
                                                        <i class="ph-duotone ph-check-circle text-success fs-6"></i>
                                                        {{ $featureEnum?->label() ?? 'ميزة غير معروفة' }}:
                                                        {{ $feature->value['value'] ?? 0 }}
                                                        {{ $featureEnum?->unit() ?? '' }}
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Submit Request -->
                    @if($selectedPackageId)
                        <div class="mt-4 p-3 bg-light rounded-3">
                            <div class="row g-3">
                                <div class="col-md-8">
                                    <label class="form-label fw-semibold">ملاحظات (اختياري)</label>
                                    <textarea class="form-control" wire:model="requestNotes" rows="2" placeholder="أضف أي ملاحظات إضافية..."></textarea>
                                </div>
                                <div class="col-md-4 d-flex align-items-end">
                                    <button type="button" class="btn btn-success w-100" wire:click="submitRequest" wire:loading.attr="disabled">
                                        <span wire:loading.remove>
                                            <i class="ph-duotone ph-paper-plane me-1"></i>
                                            إرسال طلب تغيير الباقة
                                        </span>
                                        <span wire:loading>
                                            <span class="spinner-border spinner-border-sm me-1"></span>
                                            جاري الإرسال...
                                        </span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            @else
                <div class="p-4 text-center">
                    <i class="ph-duotone ph-package fs-1 d-block text-muted mb-2"></i>
                    <p class="text-muted mb-0">لا توجد باقات متاحة حالياً</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Subscription History -->
    @if($subscriptionHistories->count() > 0)
        <div class="card border-0 shadow-sm overflow-hidden mt-4">
            <div class="card-header bg-info text-white border-0 p-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="avtar avtar-s bg-white text-info rounded-3">
                        <i class="ph-duotone ph-clock-counter-clockwise fs-4"></i>
                    </div>
                    <div>
                        <h5 class="mb-0 text-white fw-bold">سجل الاشتراكات</h5>
                        <small class="text-white opacity-75">آخر 10 تغييرات على اشتراكك</small>
                    </div>
                </div>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                        <tr>
                            <th>التاريخ</th>
                            <th>الإجراء</th>
                            <th>الباقة</th>
                            <th>التفاصيل</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($subscriptionHistories as $history)
                            <tr>
                                <td class="text-muted small">
                                    {{ $history->created_at->format('Y-m-d h:i A') }}
                                </td>
                                <td>
                                        <span class="badge
                                            @switch($history->action_type)
                                                @case('created') bg-success @break
                                                @case('renewed') bg-primary @break
                                                @case('cancelled') bg-danger @break
                                                @case('expired') bg-warning @break
                                                @default bg-secondary
                                            @endswitch
                                            bg-opacity-10 text-{{
                                                match($history->action_type) {
                                                    'created' => 'success',
                                                    'renewed' => 'primary',
                                                    'cancelled' => 'danger',
                                                    'expired' => 'warning',
                                                    default => 'secondary'
                                                }
                                            }}
                                        ">
                                            @switch($history->action_type)
                                                @case('created') <i class="ph-duotone ph-plus-circle me-1"></i> إنشاء @break
                                                @case('renewed') <i class="ph-duotone ph-arrows-clockwise me-1"></i> تجديد @break
                                                @case('cancelled') <i class="ph-duotone ph-x-circle me-1"></i> إلغاء @break
                                                @case('expired') <i class="ph-duotone ph-clock me-1"></i> انتهاء @break
                                                @default {{ $history->action_type }}
                                            @endswitch
                                        </span>
                                </td>
                                <td>
                                    <span class="fw-semibold">{{ $history->package->title ?? 'غير معروف' }}</span>
                                </td>
                                <td class="text-muted small">
                                    @php
                                        // old_data و new_data مخزنين كـ json في قاعدة البيانات
                                        // الـ Model بيحولهم تلقائياً لـ array بسبب الـ cast
                                        $oldData = $history->old_data;
                                        $newData = $history->new_data;
                                    @endphp

                                    @if($oldData && $newData)
                                        @if(isset($oldData['ends_at']) && isset($newData['ends_at']))
                                            من {{ \Carbon\Carbon::parse($oldData['ends_at'])->format('Y-m-d') }}
                                            إلى {{ \Carbon\Carbon::parse($newData['ends_at'])->format('Y-m-d') }}
                                        @elseif(isset($oldData['status']) && isset($newData['status']))
                                            {{ $oldData['status'] }} → {{ $newData['status'] }}
                                        @elseif(isset($oldData['package_id']) && isset($newData['package_id']))
                                            تغيير الباقة
                                        @else
                                            تم التحديث
                                        @endif
                                    @else
                                        {{ $history->action_type == 'created' ? 'اشتراك جديد' : 'تم التحديث' }}
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
</div>
