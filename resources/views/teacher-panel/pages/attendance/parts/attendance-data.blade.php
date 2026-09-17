<div>
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <p class="text-muted mb-0 small">عرض وإدارة سجلات الحضور</p>
        <a href="{{ route('teacher.attendance.index') }}" wire:navigate class="btn btn-primary">
            <i class="ph-duotone ph-scan me-1"></i>
            صفحة الاسكان
        </a>
    </div>

    @livewire('teacher-panel.parts.flash-message', [
        'successKey' => 'attendance-success',
        'errorKey' => 'attendance-error',
    ])

    {{-- Filters --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-2">
                <div class="col-md-3">
                    <input type="text" wire:model.live.debounce.400ms="search"
                           class="form-control" placeholder="ابحث بالطالب...">
                </div>
                <div class="col-md-2">
                    <input type="date" wire:model.live="dateFilter" class="form-control">
                </div>
                <div class="col-md-2">
                    <select wire:model.live="branchFilter" class="form-select">
                        <option value="">كل الفروع</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select wire:model.live="groupFilter" class="form-select">
                        <option value="">كل المجموعات</option>
                        @foreach($groups as $group)
                            <option value="{{ $group->id }}">{{ $group->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select wire:model.live="statusFilter" class="form-select">
                        <option value="">كل الحالات</option>
                        <option value="present">حاضر</option>
                        <option value="late">متأخر</option>
                        <option value="absent">غائب</option>
                        <option value="excused">بعذر</option>
                    </select>
                </div>
                <div class="col-md-1">
                    <button type="button" wire:click="clearFilters" class="btn btn-light border w-100">
                        <i class="ph-duotone ph-funnel-simple"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                <tr>
                    <th class="ps-4">#</th>
                    <th>الطالب</th>
                    <th>المجموعة</th>
                    <th>الحصة</th>
                    <th>الفرع</th>
                    <th>التاريخ</th>
                    <th>الوقت</th>
                    <th>الحالة</th>
                    <th class="text-center">إجراءات</th>
                </tr>
                </thead>
                <tbody>
                @forelse($records as $record)
                    <tr wire:key="attendance-{{ $record->id }}">
                        <td class="ps-4"><span
                                class="text-muted fw-semibold">{{ $records->firstItem() + $loop->index }}</span></td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="student-avatar">{{ mb_substr($record->student?->name ?? '?', 0, 1) }}</div>
                                <div>
                                    <span class="fw-semibold d-block small">{{ $record->student?->name }}</span>
                                    <small class="text-muted">{{ $record->student?->code }}</small>
                                </div>
                            </div>
                        </td>
                        <td><small>{{ $record->group?->name }}</small></td>
                        <td>
                            <small class="d-block">{{ $record->schedule?->subject?->subject_label ?? '—' }}</small>
                            <small class="text-muted">
                                {{ $record->schedule?->start_time?->format('H:i') }}
                                - {{ $record->schedule?->end_time?->format('H:i') }}
                            </small>
                        </td>
                        <td><small>{{ $record->branch?->name }}</small></td>
                        <td><small>{{ $record->attendance_date?->format('Y-m-d') }}</small></td>
                        <td><small class="text-muted">{{ $record->check_in_time }}</small></td>
                        <td>
                            @if($editingId === $record->id)
                                <select wire:model="editStatus" class="form-select form-select-sm">
                                    <option value="present">حاضر</option>
                                    <option value="late">متأخر</option>
                                    <option value="absent">غائب</option>
                                    <option value="excused">بعذر</option>
                                </select>
                            @else
                                <span
                                    class="badge bg-{{ $record->status_color }} bg-opacity-10 text-{{ $record->status_color }}">
                                    {{ $record->status_label }}
                                </span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($editingId === $record->id)
                                <div class="d-flex justify-content-center gap-2">
                                    <button type="button" wire:click="saveEdit"
                                            class="btn btn-sm btn-success" title="حفظ">
                                        <i class="ph-duotone ph-check"></i>
                                    </button>
                                    <button type="button" wire:click="cancelEdit"
                                            class="btn btn-sm btn-secondary" title="إلغاء">
                                        <i class="ph-duotone ph-x"></i>
                                    </button>
                                </div>
                            @else
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="{{ route('teacher.attendance.show', $record->id) }}"
                                       wire:navigate class="btn btn-sm btn-light border" title="عرض">
                                        <i class="ph-duotone ph-eye"></i>
                                    </a>
                                    @if($canManage)
                                        <button type="button" wire:click="edit({{ $record->id }})"
                                                class="btn btn-sm btn-light border" title="تعديل">
                                            <i class="ph-duotone ph-pencil-simple"></i>
                                        </button>
                                    @endif
                                </div>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center py-5 text-muted">
                            <i class="ph-duotone ph-clipboard-text fs-1 d-block mb-2"></i>
                            <h6>لا توجد سجلات</h6>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        @if($records->hasPages())
            <div class="card-footer bg-transparent border-top">
                {{ $records->links() }}
            </div>
        @endif
    </div>
</div>
