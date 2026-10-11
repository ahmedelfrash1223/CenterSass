<div>
    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">

            {{-- Header --}}
            <div class="d-flex align-items-center gap-2 mb-4">
                <div>
                    <h5 class="mb-0 fw-bold">{{ $editMode ?? false ? 'تعديل المادة' : 'إضافة مادة جديدة' }}</h5>
                    <small class="text-body-secondary">اختر المواد والمراحل الدراسية</small>
                </div>
            </div>

            {{-- Main Selection Area --}}
            <div class="bg-body-secondary rounded-3 p-1 border">
                <div class="row g-0 align-items-stretch">

                    {{-- Subjects --}}
                    <div class="col-lg-6">
                        <div class="bg-body rounded-3 h-100 d-flex flex-column overflow-hidden">
                            <div class="d-flex align-items-center justify-content-between p-3 border-bottom">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-3 d-flex align-items-center justify-content-center bg-success-subtle text-success"
                                        style="width: 36px; height: 36px;">
                                        <i class="ph-duotone ph-books fs-5"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 fw-bold">المواد الدراسية</h6>
                                        <small class="text-body-secondary">اختر مادة أو أكثر</small>
                                    </div>
                                </div>
                                @if(count($subjects) > 0)
                                    <div
                                        class="d-flex align-items-center gap-1 badge bg-success-subtle text-success-emphasis rounded-pill">
                                        <i class="ph-duotone ph-check-circle"></i>
                                        <span>{{ count($subjects) }}</span>
                                        <button type="button" wire:click="$set('subjects', [])"
                                            class="btn btn-sm btn-link p-0 ms-1 text-success-emphasis text-decoration-none lh-1">
                                            <i class="ph-duotone ph-x"></i>
                                        </button>
                                    </div>
                                @endif
                            </div>

                            <div class="p-3 flex-grow-1">
                                <div class="row g-1">
                                    @foreach($subjectOptions as $value => $label)
                                        @php $isSelected = in_array($value, $subjects); @endphp
                                        <div class="col-6">
                                            <input type="checkbox" wire:model.live="subjects" value="{{ $value }}"
                                                id="subject-{{ $value }}" class="btn-check">
                                            <label for="subject-{{ $value }}"
                                                class="btn w-100 text-start d-flex align-items-center justify-content-between gap-1
                                                                  {{ $isSelected ? 'btn-success' : 'btn-outline-secondary' }}">
                                                <span class="small fw-semibold">{{ $label }}</span>
                                                @if($isSelected)
                                                    <i class="ph-duotone ph-check"></i>
                                                @endif
                                            </label>
                                        </div>
                                    @endforeach
                                </div>

                                @error('subjects')
                                    <div class="alert alert-danger d-flex align-items-center gap-2 mt-2 mb-0 py-2 small">
                                        <i class="ph-duotone ph-warning-circle"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Divider --}}
                    <div class="col-lg-1 d-none d-lg-flex align-items-center justify-content-center">
                        <div class="d-flex flex-column align-items-center gap-3 h-100 py-4">
                            <div class="border-start flex-grow-1"></div>
                            <div class="rounded-circle d-flex align-items-center justify-content-center bg-body border text-body-secondary"
                                style="width: 32px; height: 32px;">
                                <i class="ph-duotone ph-x"></i>
                            </div>
                            <div class="border-start flex-grow-1"></div>
                        </div>
                    </div>

                    {{-- Grades --}}
                    <div class="col-lg-5">
                        <div class="bg-body rounded-3 h-100 d-flex flex-column overflow-hidden">
                            <div class="d-flex align-items-center justify-content-between p-3 border-bottom">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-3 d-flex align-items-center justify-content-center bg-primary-subtle text-primary"
                                        style="width: 36px; height: 36px;">
                                        <i class="ph-duotone ph-student fs-5"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 fw-bold">المراحل الدراسية</h6>
                                        <small class="text-body-secondary">اختر مرحلة أو أكثر</small>
                                    </div>
                                </div>
                                @if(count($grades) > 0)
                                    <div
                                        class="d-flex align-items-center gap-1 badge bg-primary-subtle text-primary-emphasis rounded-pill">
                                        <i class="ph-duotone ph-check-circle"></i>
                                        <span>{{ count($grades) }}</span>
                                        <button type="button" wire:click="$set('grades', [])"
                                            class="btn btn-sm btn-link p-0 ms-1 text-primary-emphasis text-decoration-none lh-1">
                                            <i class="ph-duotone ph-x"></i>
                                        </button>
                                    </div>
                                @endif
                            </div>

                            <div class="p-3 flex-grow-1">
                                <div class="row g-1">
                                    @foreach($gradeOptions as $value => $label)
                                        @php $isSelected = in_array($value, $grades); @endphp
                                        <div class="col-6">
                                            <input type="checkbox" wire:model.live="grades" value="{{ $value }}"
                                                id="grade-{{ $value }}" class="btn-check">
                                            <label for="grade-{{ $value }}"
                                                class="btn w-100 text-start d-flex align-items-center justify-content-between gap-1
                                                                  {{ $isSelected ? 'btn-primary' : 'btn-outline-secondary' }}">
                                                <span class="small fw-semibold">{{ $label }}</span>
                                                @if($isSelected)
                                                    <i class="ph-duotone ph-check"></i>
                                                @endif
                                            </label>
                                        </div>
                                    @endforeach
                                </div>

                                @error('grades')
                                    <div class="alert alert-danger d-flex align-items-center gap-2 mt-2 mb-0 py-2 small">
                                        <i class="ph-duotone ph-warning-circle"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Bottom Bar --}}
            <div class="bg-body-secondary rounded-3 p-3 mt-3 border">
                <div class="row g-3 align-items-center">

                    {{-- Status --}}
                    <div class="col-md-5">
                        <div class="d-flex align-items-center gap-3 flex-wrap">
                            <label class="small fw-bold text-body mb-0 d-flex align-items-center gap-1">
                                <i class="ph-duotone ph-toggle-left"></i>
                                الحالة
                            </label>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">الحالة <span class="text-danger">*</span></label>
                                <div class="d-flex gap-2">
                                    <button type="button" wire:click="$set('is_active', 1)"
                                        class="status-option {{ $is_active == 1 ? 'active' : '' }}">
                                        <i class="ph-duotone ph-check-circle"></i>
                                        نشط
                                    </button>
                                    <button type="button" wire:click="$set('is_active', 0)"
                                        class="status-option {{ $is_active == 0 ? 'inactive' : '' }}">
                                        <i class="ph-duotone ph-x-circle"></i>
                                        غير نشط
                                    </button>
                                </div>
                                @error('is_active')
                                <div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>

                    {{-- Summary --}}
                    <div class="col-md-7">
                        @if(count($grades) > 0 && count($subjects) > 0)
                            <div
                                class="d-flex align-items-center justify-content-center gap-3 p-2 bg-body border rounded-3">
                                <div class="text-center lh-1">
                                    <div class="fs-4 fw-bold text-success">{{ count($subjects) }}</div>
                                    <small class="text-body-secondary text-uppercase" style="font-size: 10px;">مواد</small>
                                </div>
                                <span class="fs-5 fw-bold text-body-secondary opacity-50">×</span>
                                <div class="text-center lh-1">
                                    <div class="fs-4 fw-bold text-primary">{{ count($grades) }}</div>
                                    <small class="text-body-secondary text-uppercase" style="font-size: 10px;">مراحل</small>
                                </div>
                                <span class="fs-5 fw-bold text-body-secondary opacity-50">=</span>
                                <div class="text-center lh-1">
                                    <div class="fs-4 fw-bold text-warning">{{ count($grades) * count($subjects) }}</div>
                                    <small class="text-body-secondary text-uppercase"
                                        style="font-size: 10px;">إجمالي</small>
                                </div>
                            </div>
                        @else
                            <div class="d-flex align-items-center justify-content-center gap-2 p-3 bg-body border border-dashed rounded-3 text-body-secondary small fw-semibold"
                                style="border-style: dashed !important;">
                                <i class="ph-duotone ph-info fs-6"></i>
                                اختر المواد والمراحل لعرض الملخص
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Form Actions --}}
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
                    <button type="button" wire:click="save(false)" wire:loading.attr="disabled" wire:target="save(false)"
                        class="btn btn-primary">
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
                <a href="{{ route('teacher.subjects.index') }}" wire:navigate class="btn btn-outline-secondary">
                    <i class="ph-duotone ph-x-circle me-1"></i>إلغاء
                </a>
            </div>
        </div>
    </div>
</div>
