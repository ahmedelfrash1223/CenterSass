<div>
    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">

            <h5 class="mb-4">طلب كروت جديدة</h5>

            {{-- Filters --}}
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <label class="form-label small fw-semibold">الفرع <span class="text-danger">*</span></label>
                    <select wire:model.live="branchFilter" class="form-select">
                        <option value="">— اختر الفرع —</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label small fw-semibold">المرحلة الدراسية</label>
                    <select wire:model.live="gradeFilter" class="form-select" @if(!$branchFilter) disabled @endif>
                        <option value="">كل المراحل</option>
                        @foreach($grades as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label small fw-semibold">بحث باسم المجموعة</label>
                    <div class="position-relative">
                        <i class="ph-duotone ph-magnifying-glass position-absolute top-50 translate-middle-y ms-3 text-body-secondary"></i>
                        <input type="text" wire:model.live.debounce.400ms="groupSearch"
                               class="form-control ps-5" placeholder="اكتب اسم المجموعة..."
                               @if(!$branchFilter) disabled @endif>
                    </div>
                </div>
            </div>

            @if(!$branchFilter)
                <div class="alert alert-info d-flex align-items-center gap-2 mb-0">
                    <i class="ph-duotone ph-info fs-5"></i>
                    <span>اختر الفرع الأول لعرض المجموعات المتاحة.</span>
                </div>
            @elseif($groups->isEmpty())
                <div class="alert alert-warning d-flex align-items-center gap-2 mb-0">
                    <i class="ph-duotone ph-warning fs-5"></i>
                    <span>لا توجد مجموعات مطابقة للفلاتر المحددة.</span>
                </div>
            @else
                {{-- Selected info --}}
                @if(count($selectedStudentIds) > 0)
                    <div class="alert alert-info d-flex align-items-center justify-content-between gap-2 mb-3">
                        <div class="d-flex align-items-center gap-2">
                            <i class="ph-duotone ph-check-circle fs-5"></i>
                            <span>تم اختيار <strong>{{ count($selectedStudentIds) }}</strong> طالب</span>
                        </div>
                        <button type="button" wire:click="clearAll" class="btn btn-sm btn-outline-secondary">
                            <i class="ph-duotone ph-x me-1"></i>إلغاء الكل
                        </button>
                    </div>
                @endif

                {{-- Groups --}}
                @foreach($groups as $data)
                    @php
                        $group = $data['group'];
                        $students = $data['students'];
                        $isOpen = $this->openGroups[$group->id] ?? false;
                        $groupStudentIds = $students->pluck('id')->toArray();
                        $selectedInGroup = count(array_intersect($groupStudentIds, $selectedStudentIds));
                        $allSelected = count($groupStudentIds) > 0 && $selectedInGroup === count($groupStudentIds);
                    @endphp

                    <div class="card border mb-3" wire:key="grp-{{ $group->id }}">
                        <div class="card-header bg-body d-flex align-items-center justify-content-between flex-wrap gap-2">
                            <div class="d-flex align-items-center gap-2 flex-grow-1"
                                 style="cursor: pointer;"
                                 wire:click="toggleGroup({{ $group->id }})">
                                <i class="ph-duotone {{ $isOpen ? 'ph-caret-down' : 'ph-caret-left' }} fs-5"></i>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-3 d-flex align-items-center justify-content-center bg-primary-subtle text-primary"
                                         style="width: 36px; height: 36px;">
                                        <i class="ph-duotone ph-users-three"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 fw-bold">{{ $group->name }}</h6>
                                        <small class="text-body-secondary">
                                            {{ $group->branch?->name ?? '—' }}
                                            · {{ $students->count() }} طالب
                                            @if($selectedInGroup > 0)
                                                · <span class="text-success fw-bold">تم اختيار {{ $selectedInGroup }}</span>
                                            @endif
                                        </small>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex gap-2">
                                @if($allSelected)
                                    <button type="button"
                                            wire:click="deselectWholeGroup({{ $group->id }})"
                                            class="btn btn-sm btn-outline-danger">
                                        <i class="ph-duotone ph-x-square me-1"></i>إلغاء المجموعة
                                    </button>
                                @else
                                    <button type="button"
                                            wire:click="selectWholeGroup({{ $group->id }})"
                                            class="btn btn-sm btn-outline-primary">
                                        <i class="ph-duotone ph-check-square me-1"></i>اختر المجموعة كاملة
                                    </button>
                                @endif
                            </div>
                        </div>

                        @if($isOpen)
                            <div class="card-body p-0">
                                @if($students->isEmpty())
                                    <div class="p-3 text-center text-body-secondary small">
                                        لا يوجد طلاب في هذه المجموعة.
                                    </div>
                                @else
                                    <div class="table-responsive">
                                        <table class="table table-sm table-hover align-middle mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th style="width: 50px;"></th>
                                                    <th>الطالب</th>
                                                    <th>الكود</th>
                                                    <th>المرحلة</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($students as $student)
                                                    @php $isSelected = in_array($student->id, $selectedStudentIds); @endphp
                                                    <tr wire:key="std-{{ $student->id }}" class="{{ $isSelected ? 'table-primary' : '' }}">
                                                        <td>
                                                            <input type="checkbox"
                                                                   class="form-check-input"
                                                                   wire:click="toggleStudent({{ $student->id }})"
                                                                   @if($isSelected) checked @endif>
                                                        </td>
                                                        <td>
                                                            <span class="fw-semibold">{{ $student->name }}</span>
                                                        </td>
                                                        <td>
                                                            <small class="text-body-secondary">{{ $student->code }}</small>
                                                        </td>
                                                        <td>
                                                            @if($student->grade)
                                                                <span class="badge bg-info-subtle text-info-emphasis">
                                                                    {{ \App\Enums\Teacher\GradeEnum::tryFrom($student->grade)?->label() ?? $student->grade }}
                                                                </span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                @endforeach
            @endif

            {{-- Notes --}}
            @if($branchFilter && $groups->isNotEmpty())
                <div class="mt-4">
                    <label class="form-label small fw-semibold">ملاحظات (اختياري)</label>
                    <textarea wire:model="notes" rows="3" class="form-control"
                              placeholder="أي تفاصيل إضافية للأدمن..."></textarea>
                </div>

                {{-- Confirm --}}
                @if($confirming)
                    <div class="alert alert-warning d-flex align-items-center gap-3 mt-4">
                        <i class="ph-duotone ph-warning-circle fs-3"></i>
                        <div class="flex-grow-1">
                            <strong>تأكيد إرسال الطلب؟</strong>
                            <p class="small mb-0">هيتم إرسال طلب {{ count($selectedStudentIds) }} كارت للأدمن.</p>
                        </div>
                        <button type="button" wire:click="save" class="btn btn-warning" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="save">تأكيد</span>
                            <span wire:loading wire:target="save">
                                <span class="spinner-border spinner-border-sm"></span>
                            </span>
                        </button>
                        <button type="button" wire:click="cancelConfirm" class="btn btn-outline-secondary">
                            إلغاء
                        </button>
                    </div>
                @endif

                {{-- Actions --}}
                <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                    @if(!$confirming)
                        <button type="button" wire:click="confirm"
                                class="btn btn-primary"
                                @if(empty($selectedStudentIds)) disabled @endif>
                            <i class="ph-duotone ph-paper-plane-tilt me-1"></i>
                            إرسال الطلب ({{ count($selectedStudentIds) }})
                        </button>
                    @endif
                    <a href="{{ route('teacher.card-requests.index') }}" wire:navigate class="btn btn-outline-secondary">
                        <i class="ph-duotone ph-x-circle me-1"></i>إلغاء
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
