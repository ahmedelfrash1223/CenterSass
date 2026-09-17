@section('title', 'تفاصيل الحضور')
@section('attendance-active', 'active')
@extends('teacher-panel.master')
@section('main')
    <div class="pc-container">
        <div class="pc-content">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="mb-0">تفاصيل الحضور</h4>
                <a href="{{ route('teacher.attendance.records') }}" wire:navigate class="btn btn-light border">
                    <i class="ph-duotone ph-arrow-right me-1"></i>رجوع
                </a>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <small class="text-muted d-block">الطالب</small>
                            <strong>{{ $attendance->student?->name }}</strong>
                            <small class="text-muted d-block">{{ $attendance->student?->code }}</small>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted d-block">الحصة</small>
                            <strong>{{ $attendance->schedule?->subject?->subject_label ?? '—' }}</strong>
                            <small class="text-muted d-block">
                                {{ $attendance->schedule?->start_time?->format('H:i') }} -
                                {{ $attendance->schedule?->end_time?->format('H:i') }}
                            </small>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted d-block">المجموعة</small>
                            <strong>{{ $attendance->group?->name ?? '—' }}</strong>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted d-block">الفرع</small>
                            <strong>{{ $attendance->branch?->name ?? '—' }}</strong>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted d-block">التاريخ</small>
                            <strong>{{ $attendance->attendance_date?->format('Y-m-d') }}</strong>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted d-block">وقت التسجيل</small>
                            <strong>{{ $attendance->check_in_time }}</strong>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted d-block">الحالة</small>
                            <span class="badge bg-{{ $attendance->status_color }} bg-opacity-10 text-{{ $attendance->status_color }}">
                                {{ $attendance->status_label }}
                            </span>
                        </div>
                        @if($attendance->notes)
                            <div class="col-12">
                                <small class="text-muted d-block">ملاحظات</small>
                                <p class="mb-0">{{ $attendance->notes }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
