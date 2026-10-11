<div>
    {{-- Header --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div>
                    <h5 class="mb-2">{{ $assignment->title }}</h5>
                    <div class="d-flex flex-wrap gap-2">
                        <span
                            class="badge bg-{{ $assignment->type->color() }} bg-opacity-10 text-{{ $assignment->type->color() }}">
                            <i class="ph-duotone {{ $assignment->type->icon() }} me-1"></i>
                            {{ $assignment->type->label() }}
                        </span>
                        <span
                            class="badge bg-{{ $assignment->status->color() }} bg-opacity-10 text-{{ $assignment->status->color() }}">
                            {{ $assignment->status->label() }}
                        </span>
                        <span class="badge bg-info bg-opacity-10 text-info">
                            الدرجة: {{ $assignment->max_score }}
                        </span>
                        @if($assignment->group)
                            <span class="badge bg-primary bg-opacity-10 text-primary">
                                <i class="ph-duotone ph-users-three me-1"></i>{{ $assignment->group->name }}
                            </span>
                        @endif
                        @if($assignment->due_date)
                            <span class="badge bg-warning bg-opacity-10 text-warning">
                                <i class="ph-duotone ph-calendar me-1"></i>
                                {{ $assignment->due_date->format('Y-m-d') }}
                            </span>
                        @endif
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <a href="{{ route('teacher.assignments.index') }}" wire:navigate class="btn btn-light border">
                        <i class="ph-duotone ph-arrow-right me-1"></i>رجوع
                    </a>
                </div>
            </div>

            @if($assignment->description)
                <div class="alert alert-light border mt-3 mb-0 small">
                    {{ $assignment->description }}
                </div>
            @endif
        </div>
    </div>

    @livewire('teacher-panel.parts.flash-message', [
        'successKey' => 'assignment-success',
        'errorKey' => 'assignment-error',
    ])

    {{-- Stats --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center bg-primary-subtle text-primary"
                        style="width: 48px; height: 48px;">
                        <i class="ph-duotone ph-users-three fs-4"></i>
                    </div>
                    <div>
                        <div class="fs-4 fw-bold lh-1">{{ $totalStudents }}</div>
                        <div class="text-body-secondary small mt-1">عدد الطلاب</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center bg-success-subtle text-success"
                        style="width: 48px; height: 48px;">
                        <i class="ph-duotone ph-check-circle fs-4"></i>
                    </div>
                    <div>
                        <div class="fs-4 fw-bold lh-1 text-success">{{ $gradedCount }}</div>
                        <div class="text-body-secondary small mt-1">تم التقييم</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center bg-warning-subtle text-warning"
                        style="width: 48px; height: 48px;">
                        <i class="ph-duotone ph-chart-line fs-4"></i>
                    </div>
                    <div>
                        <div class="fs-4 fw-bold lh-1 text-warning">{{ $average ?? '—' }}</div>
                        <div class="text-body-secondary small mt-1">متوسط الدرجات</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Students Grading Table --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-body">
            <h6 class="mb-0 fw-bold">
                <i class="ph-duotone ph-list-checks me-1"></i>تقييم الطلاب
            </h6>
        </div>
        <div class="card-body p-0">
            @if($students->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4" style="width: 60px;">#</th>
                                <th>الطالب</th>
                                <th class="text-center">الدرجة</th>
                                <th class="text-center">التقييم</th>
                                <th>ملاحظات</th>
                                <th class="text-center" style="width: 180px;">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($students as $student)
                                @php
                                    $row = $grades[$student->id] ?? ['score' => '', 'grade' => '', 'notes' => ''];
                                    $isEditing = $editingStudentId === $student->id;
                                    $gradeEnum = $row['grade'] ? \App\Enums\Teacher\AssignmentGradeEnum::tryFrom($row['grade']) : null;
                                @endphp
                                <tr wire:key="student-{{ $student->id }}">
                                    <td class="ps-4 text-muted fw-semibold">{{ $loop->iteration }}</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="student-avatar">
                                                {{ mb_substr($student->name, 0, 1) }}
                                            </div>
                                            <div>
                                                <div class="fw-semibold">{{ $student->name }}</div>
                                                <small class="text-muted">{{ $student->code }}</small>
                                            </div>
                                        </div>
                                    </td>

                                    @if($isEditing && $canGrade)
                                        <td class="text-center" style="max-width: 120px;">
                                            <input type="number" step="0.5" min="0" max="{{ $assignment->max_score }}"
                                                wire:model="grades.{{ $student->id }}.score"
                                                wire:change="autoGrade({{ $student->id }})"
                                                class="form-control form-control-sm text-center">
                                        </td>
                                        <td class="text-center" style="max-width: 160px;">
                                            <select wire:model="grades.{{ $student->id }}.grade" class="form-select form-select-sm">
                                                <option value="">—</option>
                                                @foreach($gradeOptions as $value => $label)
                                                    <option value="{{ $value }}">{{ $label }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <input type="text" wire:model="grades.{{ $student->id }}.notes"
                                                class="form-control form-control-sm" placeholder="ملاحظات...">
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-2">
                                                <button type="button" wire:click="saveGrade({{ $student->id }})"
                                                    class="btn btn-sm btn-success" wire:loading.attr="disabled">
                                                    <i class="ph-duotone ph-check"></i>
                                                </button>
                                                <button type="button" wire:click="cancelEdit"
                                                    class="btn btn-sm btn-outline-secondary">
                                                    <i class="ph-duotone ph-x"></i>
                                                </button>
                                            </div>
                                        </td>
                                    @else
                                        <td class="text-center">
                                            @if($row['score'] !== '')
                                                <span class="badge bg-info bg-opacity-10 text-info fs-6">
                                                    {{ $row['score'] }} / {{ $assignment->max_score }}
                                                </span>
                                            @else
                                                <span class="text-muted small">لم يتم التقييم</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($gradeEnum)
                                                <span
                                                    class="badge bg-{{ $gradeEnum->color() }} bg-opacity-10 text-{{ $gradeEnum->color() }}">
                                                    {{ $gradeEnum->label() }}
                                                </span>
                                            @else
                                                <span class="text-muted small">—</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($row['notes'])
                                                <small class="text-muted">{{ $row['notes'] }}</small>
                                            @else
                                                <span class="text-muted small">—</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($canGrade)
                                                <button type="button" wire:click="editStudent({{ $student->id }})"
                                                    class="btn btn-sm btn-outline-primary">
                                                    <i class="ph-duotone ph-pencil-simple me-1"></i>
                                                    {{ $row['score'] !== '' ? 'تعديل' : 'تقييم' }}
                                                </button>
                                            @else
                                                <span class="text-muted small">—</span>
                                            @endif
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="ph-duotone ph-users-three fs-1 d-block mb-2 text-muted"></i>
                    <h6 class="text-muted mb-1">لا يوجد طلاب في هذه المجموعة</h6>
                    <small class="text-muted">أضف طلاب للمجموعة الأول عشان تقدر تقيّمهم.</small>
                </div>
            @endif
        </div>
    </div>
</div>
