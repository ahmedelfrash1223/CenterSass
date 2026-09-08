<div>
    <!-- List Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <p class="text-muted mb-0 small">إدارة مجموعاتك ومواعيدها وطلابها</p>
        </div>
        <a href="{{ route('teacher.groups.create') }}" wire:navigate class="btn btn-primary d-flex align-items-center gap-1">
            <i class="ph-duotone ph-plus"></i>
            إضافة مجموعة
        </a>
    </div>

    @if(session('group-success'))
        <div class="alert alert-success d-flex align-items-center gap-2 mb-4">
            <i class="ph-duotone ph-check-circle fs-5"></i>
            <span>{{ session('group-success') }}</span>
        </div>
    @endif

    <!-- Search and Filters -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-2">
                <div class="col-md-5">
                    <div class="position-relative">
                        <i class="ph-duotone ph-magnifying-glass position-absolute top-50 translate-middle-y ms-3 text-muted"></i>
                        <input type="text" wire:model.live.debounce.400ms="search" class="form-control ps-5" placeholder="ابحث عن مجموعة...">
                    </div>
                </div>
                <div class="col-md-3">
                    <select wire:model.live="branchFilter" class="form-select">
                        <option value="">كل الفروع</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select wire:model.live="statusFilter" class="form-select">
                        <option value="">كل الحالات</option>
                        @foreach($statuses as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn btn-light border w-100" wire:click="$set('statusFilter', ''); $set('branchFilter', ''); $set('search', '');">
                        <i class="ph-duotone ph-funnel-simple"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Groups Table -->
    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                <tr>
                    <th class="ps-4">#</th>
                    <th>المجموعة</th>
                    <th>الفرع</th>
                    <th>الطلاب</th>
                    <th>الحالة</th>
                    <th>الفترة</th>
                    <th class="text-center">الإجراءات</th>
                </tr>
                </thead>
                <tbody>
                @forelse($groups as $group)
                    <tr wire:key="group-{{ $group->id }}">
                        <td class="ps-4">
                            <span class="text-muted fw-semibold">{{ $groups->firstItem() + $loop->index }}</span>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="avtar avtar-s bg-light-primary text-primary">
                                    <i class="ph-duotone ph-users-three fs-5"></i>
                                </div>
                                <span class="fw-semibold">{{ $group->name }}</span>
                            </div>
                        </td>
                        <td>{{ $group->branch?->name ?? '—' }}</td>
                        <td>
                            <i class="ph-duotone ph-users me-1 text-primary"></i>
                            {{ $group->students_count ?? 0 }}
                            @if($group->max_students > 0)
                                <small class="text-muted">/ {{ $group->max_students }}</small>
                            @endif
                        </td>
                        <td>
                            @php
                                $statusEnum = \App\Enums\Teacher\GroupStatusEnum::tryFrom($group->status);
                                $color = $statusEnum?->color() ?? 'secondary';
                                $label = $statusEnum?->label() ?? $group->status;
                            @endphp
                            <span class="badge bg-{{ $color }} bg-opacity-10 text-{{ $color }}">
                                    {{ $label }}
                                </span>
                        </td>
                        <td>
                            <small>
                                {{ $group->start_date?->format('Y-m-d') ?? 'غير محدد' }}
                                <br>
                                {{ $group->end_date?->format('Y-m-d') ?? 'غير محدد' }}
                            </small>
                        </td>
                        <td>
                            <div class="d-flex align-items-center justify-content-center gap-3">
                                @if($deleteId === $group->id)
                                    <button type="button" wire:click="delete" wire:loading.attr="disabled" wire:target="delete" class="border-0 bg-transparent p-0 text-success" title="تأكيد الحذف">
                                            <span wire:loading.remove wire:target="delete">
                                                <i class="ph-duotone ph-check-circle fs-4"></i>
                                            </span>
                                        <span wire:loading wire:target="delete" class="spinner-border spinner-border-sm"></span>
                                    </button>
                                    <button type="button" wire:click="cancelDelete" class="border-0 bg-transparent p-0 text-secondary" title="إلغاء">
                                        <i class="ph-duotone ph-x-circle fs-4"></i>
                                    </button>
                                @else
                                    <a href="{{ route('teacher.groups.show', $group) }}" wire:navigate class="border-0 bg-transparent p-0 text-info" title="عرض">
                                        <i class="ph-duotone ph-eye fs-4"></i>
                                    </a>
                                    <a href="{{ route('teacher.groups.edit', $group) }}" wire:navigate class="border-0 bg-transparent p-0 text-primary" title="تعديل">
                                        <i class="ph-duotone ph-pencil-simple fs-4"></i>
                                    </a>
                                    <button type="button" wire:click="confirmDelete({{ $group->id }})" class="border-0 bg-transparent p-0 text-danger" title="حذف">
                                        <i class="ph-duotone ph-trash fs-4"></i>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <div class="text-muted">
                                <i class="ph-duotone ph-users-three fs-1 d-block mb-2"></i>
                                <h6>{{ $search || $branchFilter || $statusFilter ? 'لا توجد نتائج' : 'لا توجد مجموعات حتى الآن' }}</h6>
                                <small>ابدأ بإضافة أول مجموعة.</small>
                            </div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        @if($groups->hasPages())
            <div class="card-footer bg-transparent border-top">
                {{ $groups->links() }}
            </div>
        @endif
    </div>
</div>
