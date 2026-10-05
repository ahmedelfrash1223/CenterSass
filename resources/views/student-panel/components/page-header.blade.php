<div class="page-header">
    <div class="page-block">
    <nav aria-label="مسار التنقل">
        <ol class="breadcrumb mb-3">
            <li class="breadcrumb-item"><a href="{{ route('student.dashboard') }}" wire:navigate>الرئيسية</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $title }}</li>
        </ol>
    </nav>
    <h1 class="student-page-title">{{ $title }}</h1>
    <p class="text-muted">{{ $description }}</p>
    </div>
</div>
