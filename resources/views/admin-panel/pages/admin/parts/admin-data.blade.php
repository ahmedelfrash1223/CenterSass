<div>
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div><p class="text-muted mb-0 small">إدارة المديرين والحسابات والصلاحيات</p></div>
        <a href="{{ route('admin.admins.create') }}" wire:navigate
           class="btn btn-primary d-flex align-items-center gap-1">
            <i class="ph-duotone ph-user-plus"></i> إضافة مدير
        </a>
    </div>

    @if(session('admin-success'))
        <div class="alert alert-success d-flex align-items-center gap-2 mb-4">
            <i class="ph-duotone ph-check-circle fs-5"></i><span>{{ session('admin-success') }}</span>
        </div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-transparent border-bottom">
            <div class="row align-items-center">
                <div class="col-md-5">
                    <div class="position-relative">
                        <i class="ph-duotone ph-magnifying-glass position-absolute top-50 translate-middle-y ms-3 text-muted"></i>
                        <input type="text" wire:model.live.debounce.400ms="search" class="form-control ps-5"
                               placeholder="ابحث عن مدير (الاسم، البريد، اسم المستخدم)...">
                    </div>
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                <tr>
                    <th class="ps-4">#</th>
                    <th>الكود</th>
                    <th>المدير</th>
                    <th>اسم المستخدم</th>
                    <th>البريد الإلكتروني</th>
                    <th>الحالة</th>
                    <th class="text-center">الإجراءات</th>
                </tr>
                </thead>
                <tbody>
                @forelse($admins as $admin)
                    <tr wire:key="admin-{{ $admin->id }}">
                        <td class="ps-4"><span class="text-muted fw-semibold">{{ $admin->id }}</span></td>
                        <td><span class="badge bg-light-primary text-primary">{{ $admin->code }}</span></td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="avtar avtar-s bg-light-primary text-primary"><i
                                        class="ph-duotone ph-user fs-5"></i></div>
                                <span class="fw-semibold">{{ $admin->name }}</span></div>
                        </td>
                        <td>{{ $admin->username }}</td>
                        <td><a href="mailto:{{ $admin->email }}" class="text-decoration-none">{{ $admin->email }}</a>
                        </td>
                        <td>@if($admin->is_active)
                                <span class="badge bg-success bg-opacity-10 text-success"><i
                                        class="ph-duotone ph-check-circle me-1"></i>نشط</span>
                            @else
                                <span class="badge bg-danger bg-opacity-10 text-danger"><i
                                        class="ph-duotone ph-x-circle me-1"></i>غير نشط</span>
                            @endif</td>
                        <td>
                            <div class="d-flex align-items-center justify-content-center gap-3">
                                @if($deleteId === $admin->id)
                                    <button type="button" wire:click="delete"
                                            class="border-0 bg-transparent p-0 text-success" title="تأكيد الحذف"
                                            wire:loading.attr="disabled" wire:target="delete"><span wire:loading.remove
                                                                                                    wire:target="delete"><i
                                                class="ph-duotone ph-check-circle fs-4"></i></span><span wire:loading
                                                                                                         wire:target="delete"
                                                                                                         class="spinner-border spinner-border-sm"></span>
                                    </button>
                                    <button type="button" wire:click="cancelDelete"
                                            class="border-0 bg-transparent p-0 text-secondary" title="إلغاء"><i
                                            class="ph-duotone ph-x-circle fs-4"></i></button>
                                @else
                                    <a wire:navigate href="{{ route('admin.admins.edit', $admin->id) }}"
                                       class="border-0 bg-transparent p-0 text-primary" title="تعديل"><i
                                            class="ph-duotone ph-pencil-simple fs-4"></i></a>
                                    <button type="button" wire:click="confirmDelete({{ $admin->id }})"
                                            class="border-0 bg-transparent p-0 text-danger" title="حذف"><i
                                            class="ph-duotone ph-trash fs-4"></i></button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <div class="text-muted"><i class="ph-duotone ph-users fs-1 d-block mb-2"></i>
                                <h6>{{ $search ? 'لا توجد نتائج' : 'لا يوجد مديرون' }}</h6></div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
        @if($admins->hasPages())
            <div class="card-footer bg-transparent">{{ $admins->links() }}</div>
        @endif
    </div>
</div>
