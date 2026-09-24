<div>
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-transparent border-bottom">
            <h6 class="mb-0 fw-bold">
                <i class="ph-duotone ph-git-branch me-1"></i>
                فروعي
                <span class="badge bg-primary bg-opacity-10 text-primary ms-2">
                    {{ $staffInfo['my_branches']->count() }}
                </span>
            </h6>
        </div>
        <div class="card-body p-0">
            @if($staffInfo['my_branches']->count() > 0)
                <div class="list-group list-group-flush">
                    @foreach($staffInfo['my_branches'] as $branch)
                        <div class="list-group-item d-flex align-items-center gap-2"
                             wire:key="branch-{{ $branch->id }}">
                            <div class="stat-icon-box stat-icon-sm bg-primary bg-opacity-10 text-primary">
                                <i class="ph-duotone ph-git-branch"></i>
                            </div>
                            <div class="flex-grow-1">
                                <strong class="d-block small">{{ $branch->name }}</strong>
                                @if($branch->phone)
                                    <small class="text-muted">
                                        <i class="ph-duotone ph-phone me-1"></i>{{ $branch->phone }}
                                    </small>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-4">
                    <i class="ph-duotone ph-git-branch fs-1 text-muted d-block mb-2"></i>
                    <small class="text-muted">لم يتم إضافتك لأي فرع بعد</small>
                </div>
            @endif
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body text-center">
            <div class="stat-icon-box bg-success bg-opacity-10 text-success mx-auto mb-2">
                <i class="ph-duotone ph-calendar-check fs-3"></i>
            </div>
            <h3 class="mb-1 fw-bold">{{ $staffInfo['my_schedules_count'] }}</h3>
            <small class="text-muted">حصصي النهارده</small>
        </div>
    </div>
</div>
