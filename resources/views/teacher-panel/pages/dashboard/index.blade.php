@section('home-active', 'active')
@section('title', 'لوحة التحكم')
@extends('teacher-panel.master')

@php
    use App\Enums\Teacher\TeacherPermissionEnum as Perm;

    $homeUser = auth('teacher')->user();
    $homeNow = now();
    // Same rule as teacher-home.js (which refreshes it on the client in the app timezone)
    $greeting = $homeNow->hour < 12 ? 'صباح الخير' : 'مساء الخير';
    $canTakeAttendance = $homeUser?->hasPermission(Perm::ATTENDANCE_VIEW);
    $canAddStudent = $homeUser?->hasPermission(Perm::STUDENT_CREATE);
@endphp

@section('main')

    <div class="pc-container">
        <div class="pc-content nb-home" data-nb-home data-nb-tz="{{ config('app.timezone') }}">

            <!-- Page head: greeting + today + quick actions -->
            <section class="nb-home__head" aria-labelledby="nb-home-greeting">
                <div class="nb-home__hello">
                    <h2 class="nb-home__greeting" id="nb-home-greeting">
                        <span data-nb-greeting>{{ $greeting }}</span>، أ. {{ $homeUser?->name }}
                    </h2>
                    <p class="nb-home__date">
                        <i class="ph-duotone ph-calendar-blank" aria-hidden="true"></i>
                        <time datetime="{{ $homeNow->toDateString() }}">{{ $homeNow->locale('ar')->translatedFormat('l، j F Y') }}</time>
                    </p>
                </div>

                @if ($canTakeAttendance || $canAddStudent)
                    <div class="nb-home__actions">
                        @if ($canTakeAttendance)
                            <a href="{{ route('teacher.attendance.index') }}" wire:navigate class="nb-btn nb-btn--primary">
                                <i class="ph-duotone ph-scan" aria-hidden="true"></i>
                                تسجيل حضور
                            </a>
                        @endif
                        @if ($canAddStudent)
                            <a href="{{ route('teacher.students.create') }}" wire:navigate class="nb-btn nb-btn--ghost">
                                <i class="ph-duotone ph-user-plus" aria-hidden="true"></i>
                                إضافة طالب
                            </a>
                        @endif
                    </div>
                @endif
            </section>

            @livewire('teacher-panel.pages.dashboard.parts.data')
        </div>
    </div>

@endsection
