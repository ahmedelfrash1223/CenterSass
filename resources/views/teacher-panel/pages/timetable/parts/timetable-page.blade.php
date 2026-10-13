<div>

    @if(!$isRegularTeacher)
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold text-body-secondary">
                            <i class="ph-duotone ph-git-branch me-1"></i>الفرع
                        </label>
                        <select wire:model.live="branchFilter" class="form-select">
                            <option value="">كل الفروع</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>
    @endif


    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-transparent border-bottom">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div>
                    <h6 class="mb-1 fw-bold">
                        <i class="ph-duotone ph-calendar-week me-1"></i>
                        @if($isRegularTeacher)
                            جدول حصصي
                        @else
                            حصص الأسبوع
                        @endif
                    </h6>
                    <small class="text-body-secondary">
                        <i class="ph-duotone ph-info me-1"></i>
                        اختر يوم من الأزرار لعرض الحصص
                    </small>
                </div>

                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-primary-subtle text-primary-emphasis">
                        {{ $schedules->count() }} حصة
                    </span>

                    @if(!$isToday)
                        <button type="button" wire:click="goToToday" class="btn btn-sm btn-outline-primary">
                            <i class="ph-duotone ph-arrow-counter-clockwise me-1"></i>
                            اليوم الحالي
                        </button>
                    @endif
                </div>
            </div>
        </div>

        {{-- Day Tabs --}}
        <div class="card-body pb-0">
            <div class="d-flex gap-2 flex-wrap justify-content-center">
                @foreach($days as $dayEn => $dayAr)
                    @php
                        $isSelected = $selectedDay === $dayEn;
                        $isTodayDay = $today === $dayEn;
                        $dayCount = count($timetable[$dayEn] ?? []);
                    @endphp

                    <button type="button" wire:click="selectDay('{{ $dayEn }}')"
                        class="btn {{ $isSelected ? 'btn-primary' : 'btn-outline-secondary' }} position-relative">
                        {{ $dayAr }}
                        @if($dayCount > 0)
                            <span
                                class="badge bg-{{ $isSelected ? 'light' : 'primary' }} text-{{ $isSelected ? 'primary' : 'white' }} ms-1">
                                {{ $dayCount }}
                            </span>
                        @endif
                        @if($isTodayDay)
                            <span
                                class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-2 border-white rounded-circle"
                                title="النهارده"></span>
                        @endif
                    </button>
                @endforeach
            </div>
        </div>

        {{-- Schedules Grid --}}
        <div class="card-body">
            @if($schedules->count() > 0)
                <div class="row g-3">
                    @foreach($schedules as $schedule)
                        @php
                            $status = $this->getScheduleStatus($schedule, $isToday);
                            $statusColor = $this->getScheduleStatusColor($status);
                            $statusLabel = $this->getScheduleStatusLabel($status);
                            $statusIcon = $this->getScheduleStatusIcon($status);
                        @endphp

                        <div class="col-md-6 col-xl-4">
                            <div class="card border-2 border-{{ $statusColor }} bg-{{ $statusColor }}-subtle h-100 position-relative"
                                wire:key="schedule-{{ $schedule['id'] }}">

                                <div class="card-body">
                                    <div class="d-flex align-items-center justify-content-between mb-2 flex-wrap gap-2">
                                        <span class="badge bg-{{ $statusColor }} text-white">
                                            <i class="ph-duotone {{ $statusIcon }} me-1"></i>
                                            {{ $statusLabel }}
                                        </span>
                                        <small class="text-body-secondary">
                                            <i class="ph-duotone ph-clock me-1"></i>
                                            {{ $schedule['start_time'] }}
                                            -
                                            {{ $schedule['end_time'] }}
                                        </small>
                                    </div>

                                    <h6 class="fw-bold mb-2">{{ $schedule['subject_label'] }}</h6>

                                    <div class="d-flex flex-column gap-1 mb-2">
                                        <small class="text-body-secondary d-flex align-items-center gap-1">
                                            <i class="ph-duotone ph-users-three"></i>
                                            <span>{{ $schedule['group_name'] }}</span>
                                        </small>
                                        <small class="text-body-secondary d-flex align-items-center gap-1">
                                            <i class="ph-duotone ph-git-branch"></i>
                                            <span>{{ $schedule['branch_name'] }}</span>
                                        </small>
                                        <small class="text-body-secondary d-flex align-items-center gap-1">
                                            <i class="ph-duotone ph-chalkboard-teacher"></i>
                                            <span>{{ $schedule['teacher_name'] }}</span>
                                        </small>
                                        <small class="text-body-secondary d-flex align-items-center gap-1">
                                            <i class="ph-duotone ph-student"></i>
                                            <span>{{ $schedule['students_count'] }} طالب</span>
                                        </small>
                                    </div>

                                    @if($canViewGroups)
                                        <a href="{{ route('teacher.groups.show', $schedule['group_id']) }}" wire:navigate
                                            class="btn btn-sm {{ $status === 'running' ? 'btn-danger' : 'btn-outline-primary' }} w-100">
                                            <i class="ph-duotone ph-arrow-left me-1"></i>
                                            عرض المجموعة
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-5">
                    <i class="ph-duotone ph-calendar-x fs-1 d-block text-body-secondary mb-2"></i>
                    <h6 class="text-body-secondary mb-1">لا توجد حصص في اليوم دا</h6>
                    <small class="text-body-secondary">اختر يوم تاني من فوق</small>
                </div>
            @endif
        </div>

        {{-- Legend --}}
        <div class="card-footer bg-transparent border-top">
            <div class="d-flex gap-3 flex-wrap small text-body-secondary">
                <span><span class="badge bg-danger me-1">&nbsp;</span> جارية الآن</span>
                <span><span class="badge bg-primary me-1">&nbsp;</span> لم تبدأ</span>
                <span><span class="badge bg-secondary me-1">&nbsp;</span> خلصت</span>
            </div>
        </div>
    </div>


    <div class="card border-0 shadow-sm">
        <div class="card-header bg-transparent border-bottom">
            <h6 class="mb-0 fw-bold">
                <i class="ph-duotone ph-magnifying-glass me-1"></i>
                عرض جدول مجموعة محددة
            </h6>
        </div>
        <div class="card-body">

            {{-- Filters --}}
            <div class="row g-3 align-items-end mb-4">
                <div class="col-md-4">
                    <label class="form-label small fw-semibold text-body-secondary">
                        الفرع <span class="text-danger">*</span>
                    </label>
                    <select wire:model.live="viewBranchId" class="form-select">
                        <option value="">— اختر الفرع —</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label small fw-semibold text-body-secondary">
                        المرحلة الدراسية <span class="text-danger">*</span>
                    </label>
                    <select wire:model.live="viewGrade" class="form-select" @if(!$viewBranchId) disabled @endif>
                        <option value="">— اختر المرحلة —</option>
                        @foreach($gradesForView as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label small fw-semibold text-body-secondary">
                        المجموعة <span class="text-danger">*</span>
                    </label>
                    <select wire:model.live="viewGroupId" class="form-select" @if(!$viewGrade) disabled @endif>
                        <option value="">— اختر المجموعة —</option>
                        @foreach($groupsForView as $group)
                            <option value="{{ $group->id }}">{{ $group->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Result --}}
            @if(!$selectedGroupInfo)
                <div class="text-center py-5 text-body-secondary">
                    <i class="ph-duotone ph-hand-pointing fs-1 d-block mb-2"></i>
                    <h6 class="fw-bold">اختر الفرع والمرحلة والمجموعة</h6>
                    <p class="small mb-0">هيتم عرض جدول الأسبوع الكامل للمجموعة.</p>
                </div>
            @else
                {{-- Group Info --}}
                <div class="card border-primary border-2 mb-4 bg-primary-subtle bg-opacity-10">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <div>
                                <h6 class="fw-bold mb-1">{{ $selectedGroupInfo['name'] }}</h6>
                                <div class="d-flex gap-2 flex-wrap small text-body-secondary">
                                    <span>
                                        <i class="ph-duotone ph-student me-1"></i>
                                        {{ $selectedGroupInfo['grade_label'] }}
                                    </span>
                                    <span>·</span>
                                    <span>
                                        <i class="ph-duotone ph-git-branch me-1"></i>
                                        {{ $selectedGroupInfo['branch_name'] }}
                                    </span>
                                    <span>·</span>
                                    <span>
                                        <i class="ph-duotone ph-users-three me-1"></i>
                                        {{ $selectedGroupInfo['students_count'] }} طالب
                                    </span>
                                </div>
                            </div>

                            <div class="d-flex gap-2">
                                <a href="{{ route('teacher.groups.show', $selectedGroupInfo['id']) }}" wire:navigate
                                    class="btn btn-sm btn-primary">
                                    <i class="ph-duotone ph-arrow-left me-1"></i>عرض المجموعة
                                </a>
                                <button type="button" wire:click="clearSelectedGroup"
                                    class="btn btn-sm btn-outline-secondary">
                                    <i class="ph-duotone ph-x"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-2 flex-wrap justify-content-center mb-3">
                    @foreach($days as $dayEn => $dayAr)
                        @php
                            $isSelected = $selectedDay === $dayEn;
                            $dayCount = count($selectedGroupTimetable[$dayEn] ?? []);
                        @endphp
                        <button type="button" wire:click="selectDay('{{ $dayEn }}')"
                            class="btn btn-sm {{ $isSelected ? 'btn-primary' : 'btn-outline-secondary' }}">
                            {{ $dayAr }}
                            @if($dayCount > 0)
                                <span
                                    class="badge bg-{{ $isSelected ? 'light' : 'primary' }} text-{{ $isSelected ? 'primary' : 'white' }} ms-1">
                                    {{ $dayCount }}
                                </span>
                            @endif
                        </button>
                    @endforeach
                </div>

                {{-- Schedules Grid --}}
                @if($selectedGroupSchedules->count() > 0)
                    <div class="row g-3">
                        @foreach($selectedGroupSchedules as $schedule)
                            <div class="col-md-6 col-xl-4" wire:key="grp-sch-{{ $schedule['id'] }}">
                                <div class="card border h-100">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center justify-content-between mb-2 flex-wrap gap-2">
                                            <span class="badge bg-primary text-white">
                                                <i class="ph-duotone ph-clock me-1"></i>
                                                {{ $schedule['start_time'] }} - {{ $schedule['end_time'] }}
                                            </span>
                                        </div>

                                        <h6 class="fw-bold mb-2">{{ $schedule['subject_label'] }}</h6>

                                        <small class="text-body-secondary d-flex align-items-center gap-1">
                                            <i class="ph-duotone ph-chalkboard-teacher"></i>
                                            <span>{{ $schedule['teacher_name'] }}</span>
                                        </small>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="ph-duotone ph-calendar-x fs-1 d-block text-body-secondary mb-2"></i>
                        <h6 class="text-body-secondary mb-1">لا توجد حصص في اليوم دا</h6>
                        <small class="text-body-secondary">اختر يوم تاني من فوق</small>
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>
