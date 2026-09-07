@php
    $pageType = $pageType ?? 'index';
    $badge = match ($pageType) {
        'create' => ['color' => 'success', 'icon' => 'ph-plus-circle', 'text' => 'جديد'],
        'edit' => ['color' => 'warning', 'icon' => 'ph-pencil-simple', 'text' => 'تعديل'],
        'show' => ['color' => 'info', 'icon' => 'ph-eye', 'text' => 'عرض'],
        default => ['color' => 'primary', 'icon' => 'ph-list', 'text' => 'إدارة'],
    };
@endphp

<!-- Page Header -->
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center g-3">
            <!-- Breadcrumb Navigation -->
            <div class="col-md-12">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-2">
                        <li class="breadcrumb-item"><a
                                href="{{ route('admin.dashboard') }}"
                                wire:navigate
                                class="text-decoration-none"
                            ><i class="ph-duotone ph-house me-1"></i>الرئيسية</a></li>
                        @if ($pageType !== 'index')
                            <li class="breadcrumb-item"><a
                                    href="{{ route($indexRoute) }}"
                                    wire:navigate
                                    class="text-decoration-none"
                                ><i class="ph-duotone {{ $icon }} me-1"></i>{{ $moduleLabel }}</a></li>
                            <li
                                class="breadcrumb-item active"
                                aria-current="page"
                            >{{ $pageLabel }}</li>
                        @else
                            <li
                                class="breadcrumb-item active"
                                aria-current="page"
                            ><i class="ph-duotone {{ $icon }} me-1"></i>{{ $moduleLabel }}</li>
                        @endif
                    </ol>
                </nav>
            </div>
            <!-- Page Title and Actions -->
            <div class="col-md-12">
                <div class="d-flex align-items-center justify-content-between gap-3 flex-wrap">
                    <div class="d-flex align-items-center gap-3">
                        <div class="page-header-icon-wrapper">
                            <div class="page-header-icon"><i class="ph-duotone {{ $icon }} fs-1"></i></div>
                        </div>
                        <div>
                            <h1 class="page-header-title mb-0">{{ $title }} <span
                                    class="badge bg-{{ $badge['color'] }} bg-opacity-10 text-{{ $badge['color'] }} ms-2 fs-6 fw-normal"
                                ><i class="ph-duotone {{ $badge['icon'] }} me-1"></i>{{ $badge['text'] }}</span>
                            </h1>
                            <p class="text-muted small mb-0 mt-1"><i
                                    class="ph-duotone ph-dot-outline me-1"></i>{{ $description }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
