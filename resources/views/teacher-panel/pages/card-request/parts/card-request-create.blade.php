<div>
    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">

            <h5 class="mb-4">طلب كروت جديدة</h5>

            {{-- Filters --}}
            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">الفرع <span class="text-danger">*</span></label>
                    <select wire:model.live="branchFilter" class="form-select">
                        <option value="">— اختر الفرع —</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label small fw-semibold">المرحلة الدراسية</label>
                    <select wire:model.live="gradeFilter" class="form-select" @if(!$branchFilter) disabled @endif>
                        <option value="">كل المراحل</option>
                        @foreach($grades as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label small fw-semibold">بحث باسم المجموعة</label>
                    <div class="position-relative">
                        <i class="ph-duotone ph-magnifying-glass position-absolute top-50 translate-middle-y ms-3 text-body-secondary"></i>
                        <input type="text" wire:model.live.debounce.400ms="groupSearch"
                               class="form-control ps-5" placeholder="اكتب اسم المجموعة..."
                               @if(!$branchFilter) disabled @endif>
                    </div>
                </div>

                <div class="col-md-3">
                    <label class="form-label small fw-semibold">سعر الكارت <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input type="number" step="0.01" min="0" wire:model.live="unit_price"
                               class="form-control" placeholder="0.00">
                        <span class="input-group-text">ج.م</span>
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
                <div class="row g-4">

                    <div class="col-lg-5">
                        <div class="card border h-100">
                            <div class="card-header bg-body">
                                <h6 class="mb-0 fw-bold">
                                    <i class="ph-duotone ph-users-three me-1"></i>
                                    المجموعات ({{ count($selectedGroupIds) }} مختارة)
                                </h6>
                            </div>
                            <div class="card-body p-0" style="max-height: 500px; overflow-y: auto;">
                                @foreach($groups as $data)
                                    @php
                                        $group = $data['group'];
                                        $isSelected = in_array($group->id, $selectedGroupIds);
                                    @endphp
                                    <div wire:key="grp-{{ $group->id }}" class="border-bottom">
                                        <label class="d-flex align-items-center gap-2 p-3 mb-0 w-100"
                                               style="cursor: pointer;">
                                            <input type="checkbox"
                                                   class="form-check-input mt-0"
                                                   wire:click="toggleGroup({{ $group->id }})"
                                                   @if($isSelected) checked @endif>
                                            <div class="flex-grow-1">
                                                <div class="fw-semibold">{{ $group->name }}</div>
                                                <small class="text-body-secondary">
                                                    {{ $data['students']->count() }} طالب
                                                </small>
                                            </div>
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-7">
                        <div class="card border h-100">
                            <div class="card-header bg-body d-flex justify-content-between align-items-center">
                                <h6 class="mb-0 fw-bold">
                                    <i class="ph-duotone ph-student me-1"></i>
                                    الطلاب ({{ count($selectedStudentIds) }} مختار)
                                </h6>
                                @if(count($selectedGroupIds) > 0)
                                    <div class="d-flex gap-2">
                                        <button type="button" wire:click="selectAllStudents"
                                                class="btn btn-sm btn-outline-primary">
                                            تحديد الكل
                                        </button>
                                        <button type="button" wire:click="deselectAllStudents"
                                                class="btn btn-sm btn-outline-secondary">
                                            إلغاء الكل
                                        </button>
                                    </div>
                                @endif
                            </div>
                            <div class="card-body p-0" style="max-height: 500px; overflow-y: auto;">
                                @if(empty($selectedGroupIds))
                                    <div class="text-center py-5 text-body-secondary">
                                        <i class="ph-duotone ph-hand-pointing fs-1 d-block mb-2"></i>
                                        <h6 class="fw-bold">اختر مجموعة</h6>
                                        <p class="small mb-0">اختر مجموعة أو أكثر لعرض الطلاب.</p>
                                    </div>
                                @elseif($allStudents->isEmpty())
                                    <div class="text-center py-5 text-body-secondary">
                                        <i class="ph-duotone ph-user-minus fs-1 d-block mb-2"></i>
                                        <p class="small mb-0">لا يوجد طلاب في المجموعات المختارة.</p>
                                    </div>
                                @else
                                    <div class="table-responsive">
                                        <table class="table table-sm table-hover mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th style="width: 40px;"></th>
                                                    <th>الطالب</th>
                                                    <th>الكود</th>
                                                    <th>الفرع</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($allStudents as $student)
                                                    @php
                                                        $isSelected = in_array($student->id, $selectedStudentIds);
                                                        $hasPrev = in_array($student->id, $studentsWithExistingCards);
                                                    @endphp
                                                    <tr wire:key="std-{{ $student->id }}"
                                                        class="{{ $isSelected ? 'table-primary' : '' }}">
                                                        <td>
                                                            <input type="checkbox"
                                                                   class="form-check-input"
                                                                   wire:click="toggleStudent({{ $student->id }})"
                                                                   @if($isSelected) checked @endif>
                                                        </td>
                                                        <td>
                                                            <span class="fw-semibold">{{ $student->name }}</span>
                                                            @if($hasPrev)
                                                                <span class="badge bg-warning text-dark ms-1" title="عنده كارت سابق">
                                                                    <i class="ph-duotone ph-warning"></i>
                                                                </span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <small class="text-body-secondary">{{ $student->code }}</small>
                                                        </td>
                                                        <td>
                                                            <small>{{ $student->branch?->name ?? '—' }}</small>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <label class="form-label small fw-semibold">ملاحظات (اختياري)</label>
                    <textarea wire:model="notes" rows="3" class="form-control"
                              placeholder="أي تفاصيل إضافية للأدمن..."></textarea>
                </div>

                @if(count($selectedStudentIds) > 0 && (float) $unit_price > 0)
                    <div class="alert alert-primary mt-3 mb-0">
                        <div class="d-flex justify-content-between">
                            <span>عدد الكروت:</span>
                            <strong>{{ count($selectedStudentIds) }}</strong>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>سعر الكارت:</span>
                            <strong>{{ number_format((float) $unit_price, 2) }} ج.م</strong>
                        </div>
                        <hr class="my-2">
                        <div class="d-flex justify-content-between">
                            <span class="fw-bold">الإجمالي:</span>
                            <strong class="text-success fs-5">
                                {{ number_format((float) $unit_price * count($selectedStudentIds), 2) }} ج.م
                            </strong>
                        </div>
                    </div>
                @endif

                {{-- Confirm --}}
                @if($confirming)
                    <div class="alert alert-warning mt-3">
                        @if(!empty($studentsWithExistingCards))
                            <div class="alert alert-danger mb-3">
                                <div class="d-flex align-items-start gap-2">
                                    <i class="ph-duotone ph-warning-circle fs-3"></i>
                                    <div class="flex-grow-1">
                                        <strong>تنبيه: طلاب عندهم كارت قبل كده</strong>
                                        <p class="small mb-0 mt-1">
                                            في {{ count($studentsWithExistingCards) }} طالب طلبوا كروت قبل كده.
                                            لو أكدت، هيتعملهم طلب تاني.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="d-flex align-items-center gap-3">
                            <i class="ph-duotone ph-warning-circle fs-3"></i>
                            <div class="flex-grow-1">
                                <strong>تأكيد إرسال الطلب؟</strong>
                                <p class="small mb-0">
                                    هيتم إرسال طلب {{ count($selectedStudentIds) }} كارت بسعر
                                    {{ number_format((float) $unit_price, 2) }} ج.م/كارت، الإجمالي:
                                    <strong>{{ number_format((float) $unit_price * count($selectedStudentIds), 2) }} ج.م</strong>
                                </p>
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
                    </div>
                @endif

                {{-- Actions --}}
                <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                    @if(!$confirming)
                        <button type="button" wire:click="confirm"
                                class="btn btn-primary"
                                @if(empty($selectedStudentIds) || (float) $unit_price <= 0) disabled @endif>
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
