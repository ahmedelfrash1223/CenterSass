<div class="card border-0 shadow-sm mt-4">
    <div class="card-header bg-transparent border-bottom d-flex justify-content-between align-items-center">
        <h6 class="mb-0 fw-bold">
            <i class="ph-duotone ph-buildings me-1"></i>
            إحصائيات الفروع
        </h6>
        <a href="{{ route('teacher.branches.index') }}" wire:navigate class="btn btn-sm btn-light border">
            عرض الفروع <i class="ph-duotone ph-arrow-left ms-1"></i>
        </a>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                <tr>
                    <th class="ps-4">الفرع</th>
                    <th class="text-center">الطلاب</th>
                    <th class="text-center">المجموعات</th>
                    <th class="text-center">حصص النهارده</th>
                    <th class="text-center">الحالة</th>
                </tr>
                </thead>
                <tbody>
                @forelse($branches as $branch)
                    <tr wire:key="branch-{{ $branch->id }}">
                        <td class="ps-4">
                            <div class="d-flex align-items-center gap-2">
                                <div class="stat-icon-box stat-icon-sm bg-primary bg-opacity-10 text-primary">
                                    <i class="ph-duotone ph-git-branch"></i>
                                </div>
                                <div>
                                    <strong class="d-block small">{{ $branch->name }}</strong>
                                    @if($branch->phone)
                                        <small class="text-muted">
                                            <i class="ph-duotone ph-phone me-1"></i>{{ $branch->phone }}
                                        </small>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-primary bg-opacity-10 text-primary">
                                {{ $branch->students_count }}
                            </span>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-success bg-opacity-10 text-success">
                                {{ $branch->groups_count }}
                            </span>
                        </td>
                        <td class="text-center">
                            @if($branch->today_schedules_count > 0)
                                <span class="badge bg-warning bg-opacity-10 text-warning">
                                    <i class="ph-duotone ph-broadcast me-1"></i>
                                    {{ $branch->today_schedules_count }}
                                </span>
                            @else
                                <span class="text-muted small">—</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($branch->is_active)
                                <span class="badge bg-success bg-opacity-10 text-success">نشط</span>
                            @else
                                <span class="badge bg-danger bg-opacity-10 text-danger">غير نشط</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">
                            <i class="ph-duotone ph-git-branch fs-1 d-block mb-2"></i>
                            لا توجد فروع
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($branches->hasPages())
        <div class="card-footer bg-body">{{ $branches->links() }}</div>
    @endif
</div>
