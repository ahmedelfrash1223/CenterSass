<div>
    @php
        $status = \App\Enums\Admin\SubscriptionStatusEnum::tryFrom((int) $subscription->status);
        $durationDays =
            $subscription->starts_at && $subscription->ends_at
                ? $subscription->starts_at->diffInDays($subscription->ends_at)
                : 0;
        $renewalCount = $subscription->histories->where('action_type', 'renewed')->count();
    @endphp

    @if (session('subscription-success'))
        <div class="alert alert-success d-flex align-items-center gap-2 mb-4">
            <i class="ph-duotone ph-check-circle fs-5"></i>
            <span>{{ session('subscription-success') }}</span>
        </div>
    @endif

    @error('renewal')
        <div class="alert alert-danger d-flex align-items-center gap-2 mb-4">
            <i class="ph-duotone ph-warning-circle fs-5"></i>
            <span>{{ $message }}</span>
        </div>
    @enderror

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div>
                    <h5 class="mb-1"><i class="ph-duotone ph-repeat me-2"></i>اشتراك #{{ $subscription->id }}</h5>
                    <span class="text-muted">{{ $subscription->teacher?->name }} -
                        {{ $subscription->package?->title }}</span>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    @if ($subscription->is_paid)
                        <button
                            type="button"
                            wire:click="renew"
                            @disabled($renewalDisabled)
                            wire:loading.attr="disabled"
                            wire:target="renew"
                            class="btn btn-success"
                        >
                            <span
                                wire:loading.remove
                                wire:target="renew"
                            ><i
                                    class="ph-duotone ph-arrow-clockwise me-1"></i>{{ $renewalDisabled ? 'انتهت مهلة التجديد' : 'تجديد' }}</span>
                            <span
                                wire:loading
                                wire:target="renew"
                            ><span class="spinner-border spinner-border-sm me-1"></span>جاري التجديد...</span>
                        </button>
                    @endif
                    <a
                        href="{{ route('admin.subscriptions.edit', $subscription) }}"
                        wire:navigate
                        class="btn btn-primary"
                    ><i class="ph-duotone ph-pencil-simple me-1"></i>تعديل</a>
                    <a
                        href="{{ route('admin.subscriptions.index') }}"
                        wire:navigate
                        class="btn btn-light border"
                    ><i class="ph-duotone ph-arrow-right me-1"></i>رجوع</a>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="mb-4">بيانات الاشتراك</h6>
                    <div class="row g-3">
                        <div class="col-md-6"><small class="text-muted d-block">المدرس</small><a
                                href="{{ route('admin.teacher.show', $subscription->teacher_id) }}"
                                wire:navigate
                            >{{ $subscription->teacher?->name }}</a></div>
                        <div class="col-md-6"><small class="text-muted d-block">الباقة</small><a
                                href="{{ route('admin.package.show', $subscription->package_id) }}"
                                wire:navigate
                            >{{ $subscription->package?->title }}</a></div>
                        <div class="col-md-6"><small
                                class="text-muted d-block">الفترة</small>{{ $subscription->starts_at?->format('Y-m-d H:i') }}
                            إلى {{ $subscription->ends_at?->format('Y-m-d H:i') }}</div>
                        <div class="col-md-6"><small class="text-muted d-block">إجمالي المدة</small>{{ $durationDays }}
                            يوم
                        </div>
                        <div class="col-md-6"><small class="text-muted d-block">الحالة</small><span
                                class="badge bg-{{ match ((int) $subscription->status) {1 => 'success',2 => 'secondary',3 => 'danger',default => 'warning'} }} bg-opacity-10 text-{{ match ((int) $subscription->status) {1 => 'success',2 => 'secondary',3 => 'danger',default => 'warning'} }}"
                            >{{ $status?->label() }}</span>
                        </div>
                        <div class="col-md-6"><small class="text-muted d-block">الدفع</small><span
                                class="badge bg-{{ $subscription->is_paid ? 'success' : 'warning' }} bg-opacity-10 text-{{ $subscription->is_paid ? 'success' : 'warning' }}"
                            >{{ $subscription->is_paid ? 'مدفوع' : 'غير مدفوع' }}</span>
                        </div>
                        <div class="col-md-4"><small
                                class="text-muted d-block">السعر</small>{{ number_format($subscription->price, 2) }}
                            ج.م
                        </div>
                        <div class="col-md-4"><small
                                class="text-muted d-block">الخصم</small>{{ $subscription->discount_value }}
                            {{ \App\Enums\Admin\DiscountTypeEnum::tryFrom((int) $subscription->discount_type)?->label() }}
                        </div>
                        <div class="col-md-4"><small class="text-muted d-block">بعد الخصم</small><strong
                                class="text-success"
                            >{{ number_format($subscription->price_after_discount, 2) }}
                                ج.م</strong></div>
                        <div class="col-md-6"><small class="text-muted d-block">تاريخ
                                الدفع</small>{{ $subscription->paid_at?->format('Y-m-d H:i') ?? 'غير متاح' }}</div>
                        <div class="col-md-6"><small class="text-muted d-block">عدد
                                التجديدات</small>{{ $renewalCount }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm mt-4">
                <div class="card-body">
                    <h6 class="mb-4">خصائص الاشتراك</h6>
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <tbody>
                                @forelse($subscription->features as $feature)
                                    <tr>
                                        <td>
                                            {{ \App\Enums\Admin\PackageFeatureKeyEnum::tryFrom($feature->feature_key)?->label() ?? 'ميزة غير معروفة' }}
                                        </td>
                                        <td class="text-end">
                                            {{ $feature->value['value'] ?? $feature->value }}
                                            {{ \App\Enums\Admin\PackageFeatureKeyEnum::tryFrom($feature->feature_key)?->unit() }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td
                                            colspan="2"
                                            class="text-muted text-center"
                                        >لا توجد خصائص</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <h6 class="mb-3">الاشتراك الأساسي</h6>
                    @if ($subscription->parent)
                        <a
                            href="{{ route('admin.subscriptions.show', $subscription->parent) }}"
                            wire:navigate
                        >اشتراك
                            #{{ $subscription->parent->id }}</a>
                        <div class="small text-muted">{{ $subscription->parent->package?->title }}</div>
                    @else
                        <span class="text-muted">اشتراك أصلي</span>
                    @endif
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="mb-3">سجل الاشتراك</h6>
                    <div class="list-group list-group-flush">
                        @forelse($subscription->histories as $history)
                            <div class="list-group-item px-0">
                                <div class="d-flex justify-content-between">
                                    <strong>{{ $history->action_type }}</strong><small
                                        class="text-muted">{{ $history->created_at?->format('Y-m-d H:i') }}</small>
                                </div>
                                <small class="text-muted">بواسطة {{ $history->admin?->name ?? 'النظام' }}</small>
                            </div>
                        @empty
                            <span class="text-muted">لا يوجد سجل تغييرات</span>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-end gap-2 mt-4">
        @if ($confirmingDelete)
            <button
                type="button"
                wire:click="delete"
                wire:loading.attr="disabled"
                wire:target="delete"
                class="btn btn-danger"
            ><span
                    wire:loading.remove
                    wire:target="delete"
                ><i class="ph-duotone ph-trash me-1"></i>تأكيد الحذف</span><span
                    wire:loading
                    wire:target="delete"
                ><span class="spinner-border spinner-border-sm me-1"></span>جاري
                    الحذف...</span></button>
            <button
                type="button"
                wire:click="cancelDelete"
                class="btn btn-light border"
            >إلغاء</button>
        @else
            <button
                type="button"
                wire:click="confirmDelete"
                class="btn btn-outline-danger"
            ><i class="ph-duotone ph-trash me-1"></i>حذف
            </button>
        @endif
    </div>
</div>
