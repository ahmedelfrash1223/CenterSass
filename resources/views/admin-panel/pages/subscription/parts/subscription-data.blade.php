<div>
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div><p class="text-muted mb-0 small">إدارة اشتراكات المدرسين والباقات</p></div>
        <a href="{{ route('admin.subscriptions.create') }}" wire:navigate class="btn btn-primary d-flex align-items-center gap-1"><i class="ph-duotone ph-plus"></i>إضافة اشتراك</a>
    </div>
    @if(session('subscription-success'))<div class="alert alert-success d-flex align-items-center gap-2 mb-4"><i class="ph-duotone ph-check-circle fs-5"></i><span>{{ session('subscription-success') }}</span></div>@endif
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-transparent border-bottom"><div class="position-relative col-md-5"><i class="ph-duotone ph-magnifying-glass position-absolute top-50 translate-middle-y ms-3 text-muted"></i><input type="text" wire:model.live.debounce.400ms="search" class="form-control ps-5" placeholder="ابحث عن المدرس أو الباقة أو الحالة..."></div></div>
        <div class="table-responsive"><table class="table table-hover align-middle mb-0"><thead><tr><th class="ps-4">#</th><th>المدرس</th><th>الباقة</th><th>الفترة</th><th>السعر</th><th>الحالة</th><th>الدفع</th><th class="text-center">الإجراءات</th></tr></thead><tbody>
        @forelse($subscriptions as $subscription)
            <tr wire:key="subscription-{{ $subscription->id }}"><td class="ps-4"><span class="text-muted fw-semibold">{{ $subscription->id }}</span></td>
                <td><a href="{{ route('admin.teacher.edit', $subscription->teacher_id) }}" wire:navigate class="text-decoration-none fw-semibold"><i class="ph-duotone ph-user me-1"></i>{{ $subscription->teacher?->name }}</a></td>
                <td><a href="{{ route('admin.package.edit', $subscription->package_id) }}" wire:navigate class="text-decoration-none"><i class="ph-duotone ph-package me-1"></i>{{ $subscription->package?->title }}</a></td>
                <td><small>{{ $subscription->starts_at?->format('Y-m-d') }}<br>{{ $subscription->ends_at?->format('Y-m-d') }}</small></td>
                <td><span class="fw-semibold">{{ number_format($subscription->price_after_discount, 2) }}</span> <small class="text-muted">ج.م</small></td>
                <td>@php($status = \App\Enums\Admin\SubscriptionStatusEnum::tryFrom((int) $subscription->status))<span class="badge bg-{{ match((int) $subscription->status) { 1 => 'success', 2 => 'secondary', 3 => 'danger', default => 'warning' } }} bg-opacity-10 text-{{ match((int) $subscription->status) { 1 => 'success', 2 => 'secondary', 3 => 'danger', default => 'warning' } }}">{{ $status?->label() }}</span></td>
                <td><span class="badge bg-{{ $subscription->is_paid ? 'success' : 'warning' }} bg-opacity-10 text-{{ $subscription->is_paid ? 'success' : 'warning' }}"><i class="ph-duotone {{ $subscription->is_paid ? 'ph-check-circle' : 'ph-clock' }} me-1"></i>{{ $subscription->is_paid ? 'مدفوع' : 'غير مدفوع' }}</span></td>
                <td><div class="d-flex align-items-center justify-content-center gap-3">@if($deleteId === $subscription->id)<button type="button" wire:click="delete" class="border-0 bg-transparent p-0 text-success" title="تأكيد الحذف" wire:loading.attr="disabled" wire:target="delete"><span wire:loading.remove wire:target="delete"><i class="ph-duotone ph-check-circle fs-4"></i></span><span wire:loading wire:target="delete" class="spinner-border spinner-border-sm"></span></button><button type="button" wire:click="cancelDelete" class="border-0 bg-transparent p-0 text-secondary" title="إلغاء"><i class="ph-duotone ph-x-circle fs-4"></i></button>@else<a wire:navigate href="{{ route('admin.subscriptions.edit', $subscription->id) }}" class="border-0 bg-transparent p-0 text-primary" title="تعديل"><i class="ph-duotone ph-pencil-simple fs-4"></i></a><button type="button" wire:click="confirmDelete({{ $subscription->id }})" class="border-0 bg-transparent p-0 text-danger" title="حذف"><i class="ph-duotone ph-trash fs-4"></i></button>@endif</div></td>
            </tr>
        @empty<tr><td colspan="8" class="text-center py-5"><div class="text-muted"><i class="ph-duotone ph-repeat fs-1 d-block mb-2"></i><h6>{{ $search ? 'لا توجد نتائج' : 'لا توجد اشتراكات' }}</h6></div></td></tr>@endforelse
        </tbody></table></div>
        @if($subscriptions->hasPages())<div class="card-footer bg-transparent">{{ $subscriptions->links() }}</div>@endif
    </div>
</div>