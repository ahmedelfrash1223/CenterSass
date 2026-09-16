<div>
    <!-- List Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <p class="text-muted mb-0 small">إدارة المراحل والمواد الدراسية التي تقوم بتدريسها</p>
        </div>
        @if($canCreate)
            <a href="{{ route('teacher.subjects.create') }}" wire:navigate
               class="btn btn-primary d-flex align-items-center gap-1">
                <i class="ph-duotone ph-plus"></i>
                إضافة مادة
            </a>
        @endif
    </div>

    <!-- Feedback -->
    @livewire('teacher-panel.parts.flash-message', [
        'successKey' => 'subject-success',
        'errorKey' => 'subject-error',
        'warningKey' => 'subject-warning',
    ])

    <!-- Search Bar -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="position-relative col-md-6">
                <i class="ph-duotone ph-magnifying-glass position-absolute top-50 translate-middle-y ms-3 text-muted"></i>
                <input type="text" wire:model.live.debounce.400ms="search" class="form-control ps-5"
                       placeholder="ابحث عن مرحلة أو مادة...">
            </div>
        </div>
    </div>

    <!-- Grouped Subjects by Grade -->
    @forelse($groupedSubjects as $group)
        <div class="card border-0 shadow-sm mb-3 grade-group" wire:key="grade-group-{{ $group['grade'] }}">
            <!-- Grade Header -->
            <div class="grade-header">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <!-- Left: Icon + Info -->
                    <div class="d-flex align-items-center gap-3">
                        <div
                            class="grade-icon bg-{{ $group['grade_color'] }} bg-opacity-10 text-{{ $group['grade_color'] }}">
                            <i class="ph-duotone ph-student"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 fw-bold">{{ $group['grade_label'] }}</h6>
                            <small class="text-muted">
                                {{ $group['count'] }} مادة
                                @if($group['active_count'] > 0)
                                    · {{ $group['active_count'] }} نشطة
                                @endif
                            </small>
                        </div>
                    </div>

                    <!-- Right: Badge + Delete All -->
                    <div class="d-flex align-items-center gap-2">
                        <span
                            class="badge bg-{{ $group['grade_color'] }} bg-opacity-10 text-{{ $group['grade_color'] }} grade-count-badge">
                            <i class="ph-duotone ph-books me-1"></i>
                            {{ $group['count'] }}
                        </span>

                        <!-- Delete All Button / Confirmation -->
                        @if($canDelete)
                            @if($deleteAllGrade === $group['grade'])
                                <!-- Confirmation State -->
                                <div class="delete-all-confirm">
                                    <span class="confirm-text">حذف الكل؟</span>
                                    <button type="button" wire:click="deleteAll" wire:loading.attr="disabled"
                                            wire:target="deleteAll" class="btn-confirm-yes" title="تأكيد الحذف">
                                        <span wire:loading.remove wire:target="deleteAll">
                                            <i class="ph-duotone ph-check"></i>
                                        </span>
                                        <span wire:loading wire:target="deleteAll"
                                              class="spinner-border spinner-border-sm"></span>
                                    </button>
                                    <button type="button" wire:click="cancelDeleteAll" class="btn-confirm-no"
                                            title="إلغاء">
                                        <i class="ph-duotone ph-x"></i>
                                    </button>
                                </div>
                            @else
                                <!-- Delete All Button -->
                                <button type="button" wire:click="confirmDeleteAll('{{ $group['grade'] }}')"
                                        class="btn-delete-all" title="حذف كل المواد">
                                    <i class="ph-duotone ph-trash"></i>
                                    <span class="d-none d-md-inline">حذف الكل</span>
                                </button>
                            @endif
                        @else
                            <!-- Disabled Delete All Button -->
                            <span class="text-muted opacity-50 d-inline-flex align-items-center gap-1"
                                  title="ليس لديك صلاحية" style="cursor: not-allowed;">
                                <i class="ph-duotone ph-trash"></i>
                                <span class="d-none d-md-inline">حذف الكل</span>
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Subjects Chips -->
            <div class="grade-body">
                <div class="subjects-chips">
                    @foreach($group['subjects'] as $subject)
                        <div class="subject-chip {{ $subject['is_active'] ? 'is-active' : 'is-inactive' }}"
                             wire:key="subject-{{ $subject['id'] }}">
                            <!-- Subject Info -->
                            <div class="chip-info">
                                <span
                                    class="chip-status {{ $subject['is_active'] ? 'status-active' : 'status-inactive' }}"></span>
                                <span class="chip-name">{{ $subject['subject_label'] }}</span>
                            </div>

                            <!-- Creator Badge -->
                            <!-- Creator Badge -->
                            @if(!empty($subject['creator_name']))
                                <div class="chip-creator" title="أضيف بواسطة {{ $subject['creator_name'] }}">
                                    <span
                                        class="chip-creator-badge badge bg-{{ $subject['creator_role_color'] ?? 'secondary' }} bg-opacity-10 text-{{ $subject['creator_role_color'] ?? 'secondary' }}">
                                        <i class="ph-duotone {{ $subject['creator_role_icon'] ?? 'ph-user' }}"></i>
                                        {{ $subject['creator_name'] }}
                                    </span>
                                </div>
                            @endif

                            <!-- Actions -->
                            <div class="chip-actions">
                                @if($deleteId === $subject['id'])
                                    <!-- Confirm Delete -->
                                    <button type="button" wire:click="delete" wire:loading.attr="disabled"
                                            wire:target="delete" class="chip-btn chip-btn-confirm" title="تأكيد الحذف">
                                        <span wire:loading.remove wire:target="delete">
                                            <i class="ph-duotone ph-check"></i>
                                        </span>
                                        <span wire:loading wire:target="delete"
                                              class="spinner-border spinner-border-sm"></span>
                                    </button>
                                    <button type="button" wire:click="cancelDelete" class="chip-btn chip-btn-cancel"
                                            title="إلغاء">
                                        <i class="ph-duotone ph-x"></i>
                                    </button>
                                @else
                                    <!-- Edit -->
                                    @if($canUpdate)
                                        <a href="{{ route('teacher.subjects.edit', $subject['id']) }}" wire:navigate
                                           class="chip-btn chip-btn-edit" title="تعديل">
                                            <i class="ph-duotone ph-pencil-simple"></i>
                                        </a>
                                    @else
                                        <span class="chip-btn chip-btn-disabled" title="ليس لديك صلاحية"
                                              style="cursor: not-allowed; opacity: 0.5;">
                                            <i class="ph-duotone ph-pencil-simple"></i>
                                        </span>
                                    @endif

                                    <!-- Delete -->
                                    @if($canDelete)
                                        <button type="button"
                                                wire:click="confirmDelete({{ $subject['id'] }}, '{{ $group['grade'] }}')"
                                                class="chip-btn chip-btn-delete" title="حذف">
                                            <i class="ph-duotone ph-trash"></i>
                                        </button>
                                    @else
                                        <span class="chip-btn chip-btn-disabled" title="ليس لديك صلاحية"
                                              style="cursor: not-allowed; opacity: 0.5;">
                                            <i class="ph-duotone ph-trash"></i>
                                        </span>
                                    @endif
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @empty
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">
                <div class="text-muted">
                    <i class="ph-duotone ph-books fs-1 d-block mb-3"></i>
                    <h6 class="mb-1">{{ $search ? 'لا توجد نتائج' : 'لا توجد مواد مضافة' }}</h6>
                    <small>{{ $search ? 'لم يتم العثور على مادة مطابقة.' : 'ابدأ بإضافة أول مادة دراسية.' }}</small>
                </div>
            </div>
        </div>
    @endforelse

    <!-- Pagination -->
    @if($subjects->hasPages())
        <div class="mt-3">
            {{ $subjects->links() }}
        </div>
    @endif
</div>
