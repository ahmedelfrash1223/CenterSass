<div>
    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <!-- Header -->
            <div class="d-flex align-items-center gap-2 mb-4">
                <div>
                    <h5 class="mb-0 fw-bold">{{ $editMode ?? false ? 'تعديل المادة' : 'إضافة مادة جديدة' }}</h5>
                    <small class="text-muted">اختر المواد والمراحل الدراسية</small>
                </div>
            </div>

            <!-- Main Selection Area -->
            <div class="selection-area">
                <div class="row g-0 align-items-stretch">
                    <!-- Subjects (Left) -->
                    <div class="col-lg-6">
                        <div class="selection-panel panel-subjects">
                            <div class="panel-header">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="panel-icon panel-icon-success">
                                        <i class="ph-duotone ph-books"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 fw-bold">المواد الدراسية</h6>
                                        <small class="text-muted">اختر مادة أو أكثر</small>
                                    </div>
                                </div>
                                @if(count($subjects) > 0)
                                    <div class="selected-badge selected-badge-success">
                                        <i class="ph-duotone ph-check-circle"></i>
                                        <span>{{ count($subjects) }}</span>
                                        <button type="button" wire:click="$set('subjects', [])" class="badge-clear">
                                            <i class="ph-duotone ph-x"></i>
                                        </button>
                                    </div>
                                @endif
                            </div>

                            <div class="panel-body">
                                <div class="options-grid">
                                    @foreach($subjectOptions as $value => $label)
                                        <label
                                            class="option-chip option-chip-success {{ in_array($value, $subjects) ? 'is-selected' : '' }}"
                                            for="subject-{{ $value }}">
                                            <input
                                                type="checkbox"
                                                wire:model.live="subjects"
                                                value="{{ $value }}"
                                                id="subject-{{ $value }}"
                                            >
                                            <span class="chip-label">{{ $label }}</span>
                                            <span class="chip-check">
                                                <i class="ph-duotone ph-check"></i>
                                            </span>
                                        </label>
                                    @endforeach
                                </div>
                                @error('subjects')
                                <div class="error-msg">
                                    <i class="ph-duotone ph-warning-circle"></i>
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Center Divider -->
                    <div class="col-lg-1 d-none d-lg-flex">
                        <div class="center-divider">
                            <div class="divider-line"></div>
                            <div class="divider-icon">
                                <i class="ph-duotone ph-x"></i>
                            </div>
                            <div class="divider-line"></div>
                        </div>
                    </div>

                    <!-- Grades (Right) -->
                    <div class="col-lg-5">
                        <div class="selection-panel panel-grades">
                            <div class="panel-header">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="panel-icon panel-icon-primary">
                                        <i class="ph-duotone ph-student"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 fw-bold">المراحل الدراسية</h6>
                                        <small class="text-muted">اختر مرحلة أو أكثر</small>
                                    </div>
                                </div>
                                @if(count($grades) > 0)
                                    <div class="selected-badge selected-badge-primary">
                                        <i class="ph-duotone ph-check-circle"></i>
                                        <span>{{ count($grades) }}</span>
                                        <button type="button" wire:click="$set('grades', [])" class="badge-clear">
                                            <i class="ph-duotone ph-x"></i>
                                        </button>
                                    </div>
                                @endif
                            </div>

                            <div class="panel-body">
                                <div class="options-grid options-grid-compact">
                                    @foreach($gradeOptions as $value => $label)
                                        <label
                                            class="option-chip option-chip-primary {{ in_array($value, $grades) ? 'is-selected' : '' }}"
                                            for="grade-{{ $value }}">
                                            <input
                                                type="checkbox"
                                                wire:model.live="grades"
                                                value="{{ $value }}"
                                                id="grade-{{ $value }}"
                                            >
                                            <span class="chip-label">{{ $label }}</span>
                                            <span class="chip-check">
                                                <i class="ph-duotone ph-check"></i>
                                            </span>
                                        </label>
                                    @endforeach
                                </div>
                                @error('grades')
                                <div class="error-msg">
                                    <i class="ph-duotone ph-warning-circle"></i>
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bottom Bar -->
            <div class="bottom-bar">
                <div class="row g-3 align-items-center">
                    <!-- Status -->
                    <div class="col-md-5">
                        <div class="status-group">
                            <label class="status-label">
                                <i class="ph-duotone ph-toggle-left"></i>
                                الحالة
                            </label>
                            <div class="status-toggle">
                                <button type="button"
                                        wire:click="$set('is_active', 1)"
                                        class="status-btn {{ $is_active == 1 ? 'is-active' : '' }}">
                                    <i class="ph-duotone ph-check-circle"></i>
                                    نشط
                                </button>
                                <button type="button"
                                        wire:click="$set('is_active', 0)"
                                        class="status-btn {{ $is_active == 0 ? 'is-inactive' : '' }}">
                                    <i class="ph-duotone ph-x-circle"></i>
                                    غير نشط
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Summary -->
                    <div class="col-md-7">
                        @if(count($grades) > 0 && count($subjects) > 0)
                            <div class="summary-box">
                                <div class="summary-item">
                                    <span class="summary-num text-success">{{ count($subjects) }}</span>
                                    <span class="summary-label">مواد</span>
                                </div>
                                <span class="summary-op">×</span>
                                <div class="summary-item">
                                    <span class="summary-num text-primary">{{ count($grades) }}</span>
                                    <span class="summary-label">مراحل</span>
                                </div>
                                <span class="summary-op">=</span>
                                <div class="summary-item summary-item-total">
                                    <span class="summary-num">{{ count($grades) * count($subjects) }}</span>
                                    <span class="summary-label">إجمالي</span>
                                </div>
                            </div>
                        @else
                            <div class="summary-empty">
                                <i class="ph-duotone ph-info"></i>
                                اختر المواد والمراحل لعرض الملخص
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                @if($editMode ?? false)
                    <button type="button" wire:click="update" wire:loading.attr="disabled" wire:target="update"
                            class="btn btn-primary">
                        <span wire:loading.remove wire:target="update">
                            <i class="ph-duotone ph-floppy-disk me-1"></i>حفظ التعديلات
                        </span>
                        <span wire:loading wire:target="update">
                            <span class="spinner-border spinner-border-sm me-1"></span>جاري الحفظ...
                        </span>
                    </button>
                @else
                    <button type="button" wire:click="save(false)" wire:loading.attr="disabled"
                            wire:target="save(false)" class="btn btn-primary">
                        <span wire:loading.remove wire:target="save(false)">
                            <i class="ph-duotone ph-floppy-disk me-1"></i>حفظ
                        </span>
                        <span wire:loading wire:target="save(false)">
                            <span class="spinner-border spinner-border-sm me-1"></span>جاري الحفظ...
                        </span>
                    </button>
                    <button type="button" wire:click="save(true)" wire:loading.attr="disabled" wire:target="save(true)"
                            class="btn btn-success">
                        <span wire:loading.remove wire:target="save(true)">
                            <i class="ph-duotone ph-plus-circle me-1"></i>حفظ وإنشاء جديد
                        </span>
                        <span wire:loading wire:target="save(true)">
                            <span class="spinner-border spinner-border-sm me-1"></span>جاري الحفظ...
                        </span>
                    </button>
                @endif
                <a href="{{ route('teacher.subjects.index') }}" wire:navigate class="btn btn-light border">
                    <i class="ph-duotone ph-x-circle me-1"></i>إلغاء
                </a>
            </div>
        </div>
    </div>
</div>
