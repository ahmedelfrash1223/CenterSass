<div>
    {{-- Staff Card --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="d-flex align-items-center gap-3 mb-3">
                <div class="rounded-3 d-flex align-items-center justify-content-center bg-primary-subtle text-primary"
                     style="width: 48px; height: 48px;">
                    <i class="ph-duotone ph-users-four fs-4"></i>
                </div>
                <div class="flex-grow-1">
                    <h3 class="mb-0 fw-bold">{{ $ownerStats['staff_count'] }}</h3>
                    <small class="text-body-secondary">أعضاء الفريق</small>
                </div>
            </div>
            <div class="d-flex justify-content-between pt-3 border-top">
                <small class="text-body-secondary">
                    <i class="ph-duotone ph-check-circle text-success me-1"></i>
                    نشط: <strong>{{ $ownerStats['active_staff_count'] }}</strong>
                </small>
                <a href="{{ route('teacher.staff.index') }}" wire:navigate
                   class="text-primary small text-decoration-none">
                    عرض الكل <i class="ph-duotone ph-arrow-left"></i>
                </a>
            </div>
        </div>
    </div>

    {{-- Subscription Card --}}
    @if($ownerStats['subscription'])
        @php
            $sub = $ownerStats['subscription'];
            $daysLeft = $sub->ends_at ? (int) now()->diffInDays($sub->ends_at, false) : null;
        @endphp

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="d-flex align-items-center gap-2">
                        <i class="ph-duotone ph-crown text-warning fs-4"></i>
                        <h6 class="mb-0 fw-bold">اشتراكك</h6>
                    </div>
                    <span class="badge bg-success text-white">
                        <i class="ph-duotone ph-check-circle me-1"></i>نشط
                    </span>
                </div>

                {{-- الباقة --}}
                <div class="mb-2">
                    <small class="text-body-secondary d-block">الباقة</small>
                    <strong>{{ $sub->package?->name ?? 'غير محددة' }}</strong>
                </div>

                {{-- تاريخ الانتهاء --}}
                @if($sub->ends_at)
                    <div class="mb-3">
                        <small class="text-body-secondary d-block">تاريخ الانتهاء</small>
                        <strong>{{ $sub->ends_at->format('Y-m-d') }}</strong>
                        <small class="text-body-secondary d-block mt-1">
                            @if($daysLeft > 0)
                                ({{ $daysLeft }} يوم متبقي)
                            @elseif($daysLeft === 0)
                                (ينتهي اليوم)
                            @else
                                (انتهى منذ {{ abs($daysLeft) }} يوم)
                            @endif
                        </small>
                    </div>
                @endif

                <a href="{{ route('teacher.profile.subscription') }}"
                   wire:navigate
                   class="btn btn-sm btn-primary w-100">
                    <i class="ph-duotone ph-crown me-1"></i>
                    إدارة الاشتراك
                </a>
            </div>
        </div>
    @endif
</div>
