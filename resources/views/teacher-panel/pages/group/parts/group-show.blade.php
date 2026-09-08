<div>
    @if(session()->has('success'))
        <div class="alert alert-success d-flex align-items-center gap-2 mb-4">
            <i class="ph-duotone ph-check-circle fs-5"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session()->has('error'))
        <div class="alert alert-danger d-flex align-items-center gap-2 mb-4">
            <i class="ph-duotone ph-x-circle fs-5"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <!-- Group Header -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h5 class="mb-1">
                        <i class="ph-duotone ph-users-three me-2"></i>
                        {{ $group->name }}
                    </h5>
                    <span class="text-muted">{{ $group->branch?->name }}</span>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('teacher.groups.edit', $group) }}" wire:navigate class="btn btn-primary">
                        <i class="ph-duotone ph-pencil-simple me-1"></i>
                        تعديل
                    </a>
                    <a href="{{ route('teacher.groups.index') }}" wire:navigate class="btn btn-light border">
                        <i class="ph-duotone ph-arrow-right me-1"></i>
                        رجوع
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Group Details -->
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="mb-4">بيانات المجموعة</h6>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <small class="text-muted d-block">الاسم</small>
                            {{ $group->name }}
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted d-block">الفرع</small>
                            {{ $group->branch?->name ?? '—' }}
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted d-block">الحالة</small>
                            @php
                                $statusEnum = \App\Enums\Teacher\GroupStatusEnum::tryFrom($group->status);
                                $color = $statusEnum?->color() ?? 'secondary';
                                $label = $statusEnum?->label() ?? $group->status;
                            @endphp
                            <span class="badge bg-{{ $color }} bg-opacity-10 text-{{ $color }}">
                                {{ $label }}
                            </span>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted d-block">الطلاب</small>
                            {{ $activeStudentsCount ?? $group->students->count() }}
                            @if($group->max_students > 0)
                                <span class="text-muted">/ {{ $group->max_students }}</span>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted d-block">الفترة</small>
                            {{ $group->start_date?->format('Y-m-d') ?? 'غير محدد' }}
                            إلى
                            {{ $group->end_date?->format('Y-m-d') ?? 'غير محدد' }}
                        </div>
                        <div class="col-12">
                            <small class="text-muted d-block">الوصف</small>
                            {{ $group->description ?: 'لا يوجد وصف.' }}
                        </div>

                        <!-- Schedules -->
                        @if($group->schedules->count() > 0)
                            <div class="col-12">
                                <small class="text-muted d-block mb-2">المواعيد والمواد</small>
                                <div class="row g-2">
                                    @foreach($group->schedules as $schedule)
                                        <div class="col-md-6">
                                            <div class="p-2 bg-light rounded-2">
                                                <span class="fw-semibold">{{ $schedule->day }}</span>
                                                <span class="text-muted mx-1">|</span>
                                                <span>{{ $schedule->start_time }} - {{ $schedule->end_time }}</span>
                                                <br>
                                                <small class="text-muted">
                                                    {{ $schedule->subject->grade_label }} - {{ $schedule->subject->subject_label }}
                                                </small>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Students Section -->
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h6 class="mb-0">
                            <i class="ph-duotone ph-student me-1"></i>
                            طلاب المجموعة ({{ $activeStudentsCount ?? $group->students->count() }})
                        </h6>
                        <button type="button" class="btn btn-sm btn-primary" wire:click="toggleAddStudent">
                            <i class="ph-duotone ph-user-plus me-1"></i>
                            {{ $showAddStudent ? 'إلغاء' : 'إضافة طالب' }}
                        </button>
                    </div>

                    <!-- Add Student Form -->
                    @if($showAddStudent)
                        <div class="p-3 bg-light rounded-3 mb-3">
                            <div class="row g-2">
                                <div class="col-12">
                                    <input type="text" wire:model.live.debounce.300ms="searchStudent" class="form-control form-control-sm" placeholder="ابحث عن طالب...">
                                </div>
                                <div class="col-8">
                                    <select wire:model="selectedStudentId" class="form-select form-select-sm">
                                        <option value="">اختر طالب</option>
                                        @foreach($availableStudents as $student)
                                            <option value="{{ $student->id }}">
                                                {{ $student->name }} ({{ $student->email }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-4">
                                    <button type="button" class="btn btn-success btn-sm w-100" wire:click="addStudent" wire:loading.attr="disabled" {{ !$selectedStudentId ? 'disabled' : '' }}>
                                        <span wire:loading.remove>
                                            <i class="ph-duotone ph-check"></i>
                                            إضافة
                                        </span>
                                        <span wire:loading>
                                            <span class="spinner-border spinner-border-sm"></span>
                                        </span>
                                    </button>
                                </div>
                            </div>
                            @if($searchStudent && $availableStudents->count() == 0)
                                <small class="text-muted">لا يوجد طلاب متاحين للإضافة</small>
                            @endif
                        </div>
                    @endif

                    <!-- Students List -->
                    <div class="table-responsive">
                        <table class="table table-sm align-middle mb-0">
                            <thead>
                            <tr>
                                <th>الاسم</th>
                                <th>البريد</th>
                                <th class="text-center">الإجراءات</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($group->students as $student)
                                <tr>
                                    <td>
                                        <span class="fw-semibold">{{ $student->name }}</span>
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $student->email }}</small>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-danger" wire:click="removeStudent({{ $student->id }})" wire:confirm="هل أنت متأكد من إزالة هذا الطالب؟">
                                            <i class="ph-duotone ph-user-minus"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-3">
                                        <i class="ph-duotone ph-users fs-3 d-block mb-1"></i>
                                        لا يوجد طلاب في هذه المجموعة
                                    </td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
