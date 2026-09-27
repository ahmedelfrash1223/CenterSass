<div>
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <p class="text-muted mb-0 small">إدارة الامتحانات والأسئلة والمجموعات المستهدفة</p>
        </div>
        @if($canCreate)
            <a href="{{ route('teacher.exams.create') }}" wire:navigate
               class="btn btn-primary d-flex align-items-center gap-1">
                <i class="ph-duotone ph-plus"></i> إضافة امتحان
            </a>
        @endif
    </div>

    @livewire('teacher-panel.parts.flash-message', [
        'successKey' => 'exam-success',
        'errorKey'   => 'exam-error',
    ])

    {{-- Filters --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-2">
                <div class="col-md-5">
                    <div class="position-relative">
                        <i class="ph-duotone ph-magnifying-glass position-absolute top-50 translate-middle-y ms-3 text-muted"></i>
                        <input type="text" wire:model.live.debounce.400ms="search"
                               class="form-control ps-5" placeholder="ابحث عن امتحان...">
                    </div>
                </div>
                <div class="col-md-3">
                    <select wire:model.live="branchFilter" class="form-select">
                        <option value="">كل الفروع</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select wire:model.live="statusFilter" class="form-select">
                        <option value="">كل الحالات</option>
                        @foreach($statuses as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn btn-light border w-100" wire:click="clearFilters">
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
                    <th>الامتحان</th>
                    <th>الأسئلة</th>
                    <th>الدرجة</th>
                    <th>المدة</th>
                    <th>الحالة</th>
                    <th class="text-center">الإجراءات</th>
                </tr>
                </thead>
                <tbody>
                @forelse($exams as $exam)
                    <tr wire:key="exam-{{ $exam->id }}">
                        <td class="ps-4">
                            <span class="text-muted fw-semibold">{{ $exams->firstItem() + $loop->index }}</span>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="avtar avtar-s bg-light-primary text-primary">
                                    <i class="ph-duotone ph-exam fs-5"></i>
                                </div>
                                <span class="fw-semibold">{{ $exam->title }}</span>
                            </div>
                        </td>
                        <td>
                            <i class="ph-duotone ph-question me-1 text-primary"></i>
                            {{ $exam->questions_count }}
                        </td>
                        <td>
                            <span class="badge bg-success bg-opacity-10 text-success">
                                {{ number_format((float) $exam->total_marks, 2) }}
                            </span>
                        </td>
                        <td>
                            @if($exam->duration)
                                <span class="text-muted small">
                                    <i class="ph-duotone ph-clock me-1"></i>{{ $exam->duration }} دقيقة
                                </span>
                            @else
                                <span class="text-muted small">—</span>
                            @endif
                        </td>
                        <td>
                            @php
                                $statusColor = match($exam->status) {
                                    'published' => 'success',
                                    'closed'    => 'secondary',
                                    default     => 'warning',
                                };
                                $statusLabel = match($exam->status) {
                                    'published' => 'منشور',
                                    'closed'    => 'مغلق',
                                    default     => 'مسودة',
                                };
                            @endphp
                            <span class="badge bg-{{ $statusColor }} bg-opacity-10 text-{{ $statusColor }}">
                                {{ $statusLabel }}
                            </span>
                        </td>
                        <td>
                            <div class="d-flex align-items-center justify-content-center gap-3">
                                @if($deleteId === $exam->id)
                                    <button type="button" wire:click="delete"
                                            wire:loading.attr="disabled" wire:target="delete"
                                            class="border-0 bg-transparent p-0 text-success" title="تأكيد الحذف">
                                        <span wire:loading.remove wire:target="delete">
                                            <i class="ph-duotone ph-check-circle fs-4"></i>
                                        </span>
                                        <span wire:loading wire:target="delete"
                                              class="spinner-border spinner-border-sm"></span>
                                    </button>
                                    <button type="button" wire:click="cancelDelete"
                                            class="border-0 bg-transparent p-0 text-secondary" title="إلغاء">
                                        <i class="ph-duotone ph-x-circle fs-4"></i>
                                    </button>
                                @else
                                    <a href="{{ route('teacher.exams.show', $exam) }}" wire:navigate
                                       class="border-0 bg-transparent p-0 text-info" title="عرض">
                                        <i class="ph-duotone ph-eye fs-4"></i>
                                    </a>

                                    @if($canUpdate)
                                        <a href="{{ route('teacher.exams.edit', $exam) }}" wire:navigate
                                           class="border-0 bg-transparent p-0 text-primary" title="تعديل">
                                            <i class="ph-duotone ph-pencil-simple fs-4"></i>
                                        </a>
                                    @endif

                                    @if($canDelete)
                                        <button type="button" wire:click="confirmDelete({{ $exam->id }})"
                                                class="border-0 bg-transparent p-0 text-danger" title="حذف">
                                            <i class="ph-duotone ph-trash fs-4"></i>
                                        </button>
                                    @endif
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <div class="text-muted">
                                <i class="ph-duotone ph-exam fs-1 d-block mb-2"></i>
                                <h6>{{ $search || $statusFilter || $branchFilter ? 'لا توجد نتائج' : 'لا توجد امتحانات حتى الآن' }}</h6>
                                <small>ابدأ بإضافة أول امتحان.</small>
                            </div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        @if($exams->hasPages())
            <div class="card-footer bg-transparent border-top">
                {{ $exams->links() }}
            </div>
        @endif
    </div>
</div>
