<div>
    @livewire('teacher-panel.parts.flash-message', [
        'successKey' => 'payroll-success',
        'errorKey'   => 'payroll-error',
    ])

    <div class="row g-4">
        <div class="col-md-5">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-body">
                    <h6 class="mb-0 fw-bold"><i class="ph-duotone ph-calendar me-1"></i>بيانات التوليد</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">الشهر <span class="text-danger">*</span></label>
                        <input type="month" wire:model="month" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">الفرع @if(!auth('teacher')->user()->isOwner())<span class="text-danger">*</span>@endif</label>
                        <select wire:model.live="branchId" class="form-select">
                            @if(auth('teacher')->user()->isOwner())
                                <option value="">كل الفروع</option>
                            @else
                                <option value="">اختر فرعًا</option>
                            @endif
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                            @endforeach
                        </select>
                        <small class="text-body-secondary">لو اخترت فرع، الحصص هتتحسب من الفرع ده بس</small>
                    </div>

                    <button type="button" wire:click="generate" class="btn btn-primary btn-lg w-100"
                            wire:loading.attr="disabled" wire:target="generate">
                        <span wire:loading.remove wire:target="generate">
                            <i class="ph-duotone ph-check me-1"></i>توليد الكشوف
                        </span>
                        <span wire:loading wire:target="generate">
                            <span class="spinner-border spinner-border-sm me-1"></span>جاري التوليد...
                        </span>
                    </button>

                    @if(!empty($results))
                        <div class="alert alert-info mt-3 mb-0">
                            <strong>تم توليد {{ $results['created'] }} كشف.</strong>
                            @if($results['skipped'] > 0)
                                <div class="small mt-1">
                                    تم تخطي {{ $results['skipped'] }} مدرس
                                    @if(!empty($results['skipped_teachers']))
                                        : {{ implode(', ', array_slice($results['skipped_teachers'], 0, 3)) }}
                                    @endif
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-7">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-body d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold">
                        <i class="ph-duotone ph-users-four me-1"></i>
                        المدرسين ({{ count($selectedTeacherIds) }} مختار)
                    </h6>
                    <div class="d-flex gap-2">
                        <button type="button" wire:click="selectAllTeachers" class="btn btn-sm btn-outline-primary">
                            تحديد الكل
                        </button>
                        <button type="button" wire:click="clearTeachers" class="btn btn-sm btn-outline-secondary">
                            إلغاء الكل
                        </button>
                    </div>
                </div>
                <div class="card-body p-0" style="max-height: 600px; overflow-y: auto;">
                    @forelse($teachers as $teacher)
                        <div class="form-check p-3 border-bottom" wire:key="tch-{{ $teacher->id }}">
                            <input type="checkbox" wire:model.live="selectedTeacherIds"
                                   value="{{ $teacher->id }}" class="form-check-input" id="tch-{{ $teacher->id }}">
                            <label class="form-check-label w-100" for="tch-{{ $teacher->id }}">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center fw-bold flex-shrink-0"
                                         style="width: 36px; height: 36px; font-size: 14px;">
                                        {{ mb_substr($teacher->name, 0, 1) }}
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="fw-semibold">{{ $teacher->name }}</div>
                                        <small class="text-body-secondary">{{ $teacher->role?->label() ?? 'مدرس' }}</small>
                                    </div>
                                </div>
                            </label>
                        </div>
                    @empty
                        <div class="text-center py-5 text-body-secondary">
                            <i class="ph-duotone ph-user-minus fs-1 d-block mb-2"></i>
                            <h6>لا يوجد مدرسين</h6>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
