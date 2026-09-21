<div>
    @if(!$invoice->isCancelled())

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <div class="d-flex flex-wrap gap-2 align-items-center">
                    <h6 class="mb-0 fw-bold me-2">
                        <i class="ph-duotone ph-gear me-1"></i>
                        إجراءات الفاتورة
                    </h6>

                    <div class="ms-auto d-flex flex-wrap gap-2">
                        @if($canEdit)
                            <button type="button"
                                    wire:click="{{ $showEditPanel ? 'closeEditPanel' : 'openEditPanel' }}"
                                    class="btn {{ $showEditPanel ? 'btn-primary' : 'btn-outline-primary' }}">
                                <i class="ph-duotone ph-pencil-simple me-1"></i>
                                تعديل البنود
                            </button>
                        @endif

                        @if($canCancel)
                            <button type="button"
                                    wire:click="{{ $showCancelPanel ? 'closeCancelPanel' : 'openCancelPanel' }}"
                                    class="btn {{ $showCancelPanel ? 'btn-warning' : 'btn-outline-warning' }}">
                                <i class="ph-duotone ph-x-circle me-1"></i>
                                إلغاء الفاتورة
                            </button>
                        @endif

                        @if($canDelete)
                            <button type="button"
                                    wire:click="{{ $showDeletePanel ? 'closeDeletePanel' : 'openDeletePanel' }}"
                                    class="btn {{ $showDeletePanel ? 'btn-danger' : 'btn-outline-danger' }}">
                                <i class="ph-duotone ph-trash me-1"></i>
                                حذف
                            </button>
                        @endif
                    </div>
                </div>

                @if((float) $invoice->paid_amount > 0 && ($canEdit || $canDelete))
                    <div class="alert alert-warning small mt-3 mb-0 d-flex align-items-center gap-2">
                        <i class="ph-duotone ph-warning"></i>
                        <span>
                            الفاتورة عليها دفعات — التعديل أو الحذف هيأثر على السجل المالي.
                        </span>
                    </div>
                @endif
            </div>
        </div>

        {{-- Inline Panel: Cancel --}}
        @if($showCancelPanel)
            <div class="card border-warning border-2 mb-4" wire:key="cancel-panel">
                <div class="card-header bg-warning-subtle">
                    <h6 class="mb-0 fw-bold text-warning-emphasis">
                        <i class="ph-duotone ph-x-circle me-1"></i>
                        إلغاء الفاتورة
                    </h6>
                </div>
                <div class="card-body">
                    <p class="text-body-secondary small mb-3">
                        هل أنت متأكد من إلغاء الفاتورة
                        <strong>{{ $invoice->invoice_number }}</strong>؟
                        <br>
                        الإلغاء مش هيحذف البيانات — بس هيغيّر حالة الفاتورة لـ "ملغية".
                    </p>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            سبب الإلغاء <span class="text-danger">*</span>
                        </label>
                        <textarea wire:model="cancellation_reason"
                                  class="form-control @error('cancellation_reason') is-invalid @enderror"
                                  rows="3"
                                  placeholder="مثال: تم توليدها بالخطأ، الطالب انسحب..."
                                  maxlength="500"></textarea>
                        @error('cancellation_reason')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex gap-2">
                        <button type="button"
                                wire:click="cancelInvoice"
                                wire:loading.attr="disabled"
                                class="btn btn-warning">
                            <span wire:loading.remove wire:target="cancelInvoice">
                                <i class="ph-duotone ph-check me-1"></i>
                                تأكيد الإلغاء
                            </span>
                            <span wire:loading wire:target="cancelInvoice">
                                <span class="spinner-border spinner-border-sm me-1"></span>
                                جاري...
                            </span>
                        </button>
                        <button type="button"
                                wire:click="closeCancelPanel"
                                class="btn btn-outline-secondary">
                            رجوع
                        </button>
                    </div>
                </div>
            </div>
        @endif

        {{-- Inline Panel: Delete --}}
        @if($showDeletePanel)
            <div class="card border-danger border-2 mb-4" wire:key="delete-panel">
                <div class="card-header bg-danger-subtle">
                    <h6 class="mb-0 fw-bold text-danger-emphasis">
                        <i class="ph-duotone ph-trash me-1"></i>
                        حذف الفاتورة نهائيًا
                    </h6>
                </div>
                <div class="card-body">

                    @if(!$deleteConfirmed)

                        {{-- الخطوة 1: طلب التأكيد --}}
                        <div class="alert alert-danger d-flex gap-2 mb-3">
                            <i class="ph-duotone ph-warning fs-4"></i>
                            <div>
                                <strong>تحذير!</strong>
                                <p class="small mb-0 mt-1">
                                    حذف الفاتورة لا يمكن التراجع عنه، وهيتحذف معاها كل البنود والدفعات المرتبطة.
                                </p>
                            </div>
                        </div>

                        @if((float) $invoice->paid_amount > 0)
                            <div class="alert alert-warning small d-flex gap-2 mb-3">
                                <i class="ph-duotone ph-info"></i>
                                <span>
                                    الفاتورة عليها دفعات بقيمة
                                    <strong>{{ number_format((float) $invoice->paid_amount, 2) }} ج.م</strong>
                                    — هتتحذف مع الفاتورة.
                                </span>
                            </div>
                        @endif

                        <p class="mb-3">
                            هل أنت متأكد من حذف الفاتورة
                            <strong>{{ $invoice->invoice_number }}</strong>
                            الخاصة بالطالب
                            <strong>{{ $invoice->student?->name }}</strong>؟
                        </p>

                        <div class="d-flex gap-2">
                            <button type="button"
                                    wire:click="requestDeleteConfirm"
                                    class="btn btn-danger">
                                <i class="ph-duotone ph-trash me-1"></i>
                                نعم، احذف
                            </button>
                            <button type="button"
                                    wire:click="closeDeletePanel"
                                    class="btn btn-outline-secondary">
                                رجوع
                            </button>
                        </div>

                    @else

                        {{-- الخطوة 2: تأكيد نهائي --}}
                        <div class="alert alert-danger d-flex gap-3 mb-3">
                            <i class="ph-duotone ph-warning-circle fs-2"></i>
                            <div>
                                <strong class="d-block mb-1">تأكيد أخير</strong>
                                <p class="small mb-0">
                                    اضغط "تأكيد الحذف" عشان تمسح الفاتورة نهائيًا. مفيش رجوع بعد كده.
                                </p>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="button"
                                    wire:click="deleteInvoice"
                                    wire:loading.attr="disabled"
                                    class="btn btn-danger btn-lg">
                                <span wire:loading.remove wire:target="deleteInvoice">
                                    <i class="ph-duotone ph-check me-1"></i>
                                    تأكيد الحذف النهائي
                                </span>
                                <span wire:loading wire:target="deleteInvoice">
                                    <span class="spinner-border spinner-border-sm me-1"></span>
                                    جاري الحذف...
                                </span>
                            </button>
                            <button type="button"
                                    wire:click="cancelDeleteConfirm"
                                    class="btn btn-outline-secondary">
                                تراجع
                            </button>
                        </div>

                    @endif

                </div>
            </div>
        @endif

        {{-- Inline Panel: Edit Items --}}
        @if($showEditPanel)
            <div class="card border-primary border-2 mb-4" wire:key="edit-panel">
                <div class="card-header bg-primary-subtle d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold text-primary-emphasis">
                        <i class="ph-duotone ph-pencil-simple me-1"></i>
                        تعديل بنود الفاتورة
                    </h6>
                    <button type="button"
                            wire:click="closeEditPanel"
                            class="btn btn-sm btn-outline-secondary">
                        <i class="ph-duotone ph-x"></i>
                    </button>
                </div>
                <div class="card-body">
                    <p class="text-body-secondary small mb-3">
                        الفاتورة: <strong>{{ $invoice->invoice_number }}</strong>
                        — يمكنك تعديل المبلغ والخصم لكل بند.
                    </p>

                    @if((float) $invoice->paid_amount > 0)
                        <div class="alert alert-warning small d-flex gap-2 mb-3">
                            <i class="ph-duotone ph-info"></i>
                            <span>
                                الفاتورة عليها دفعات — التعديل هيأثر على المبالغ المحسوبة.
                                المدفوع: <strong>{{ number_format((float) $invoice->paid_amount, 2) }} ج.م</strong>
                            </span>
                        </div>
                    @endif

                    @foreach($editItems as $index => $item)
                        <div class="card border mb-3" wire:key="edit-item-{{ $item['id'] }}">
                            <div class="card-body">
                                <h6 class="fw-bold mb-3">{{ $item['description'] }}</h6>

                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label small fw-semibold">المبلغ الإجمالي</label>
                                        <input type="number"
                                               step="0.01"
                                               min="0"
                                               wire:model.live="editItems.{{ $index }}.total"
                                               class="form-control form-control-sm">
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label small fw-semibold">نوع الخصم</label>
                                        <select wire:model.live="editItems.{{ $index }}.discount_type"
                                                class="form-select form-select-sm">
                                            <option value="fixed">مبلغ ثابت</option>
                                            <option value="percentage">نسبة %</option>
                                        </select>
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label small fw-semibold">
                                            قيمة الخصم
                                            @if($item['discount_type'] === 'percentage')
                                                (%)
                                            @else
                                                (ج.م)
                                            @endif
                                        </label>
                                        <input type="number"
                                               step="0.01"
                                               min="0"
                                               @if($item['discount_type'] === 'percentage') max="100" @endif
                                               wire:model.live="editItems.{{ $index }}.discount_value"
                                               class="form-control form-control-sm">
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label small fw-semibold">سبب الخصم</label>
                                        <input type="text"
                                               wire:model="editItems.{{ $index }}.discount_reason"
                                               class="form-control form-control-sm"
                                               placeholder="اختياري"
                                               maxlength="255">
                                    </div>
                                </div>

                                <div class="mt-3 d-flex justify-content-between align-items-center">
                                    @php
                                        $total = (float) $item['total'];
                                        $value = (float) $item['discount_value'];
                                        $discount = $item['discount_type'] === 'percentage'
                                            ? round($total * ($value / 100), 2)
                                            : $value;
                                        $discount = min($discount, $total);
                                    @endphp
                                    <small class="text-body-secondary">
                                        بعد الخصم:
                                        <strong class="text-success">{{ number_format($total - $discount, 2) }} ج.م</strong>
                                    </small>

                                    <button type="button"
                                            wire:click="updateItem({{ $index }})"
                                            wire:loading.attr="disabled"
                                            class="btn btn-sm btn-primary">
                                        <span wire:loading.remove wire:target="updateItem">
                                            <i class="ph-duotone ph-check me-1"></i>حفظ البند
                                        </span>
                                        <span wire:loading wire:target="updateItem">
                                            <span class="spinner-border spinner-border-sm"></span>
                                        </span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="card-footer bg-body">
                    <button type="button"
                            wire:click="closeEditPanel"
                            class="btn btn-outline-secondary">
                        إغلاق
                    </button>
                </div>
            </div>
        @endif

    @else

        <div class="alert alert-secondary d-flex align-items-start gap-3 mb-4">
            <i class="ph-duotone ph-x-circle fs-3"></i>
            <div>
                <strong>الفاتورة ملغية</strong>
                @if($invoice->cancellation_reason)
                    <p class="small mb-0 mt-1">السبب: {{ $invoice->cancellation_reason }}</p>
                @endif
                @if($invoice->cancelled_at)
                    <small class="text-body-secondary">
                        بتاريخ: {{ $invoice->cancelled_at->format('Y-m-d H:i A') }}
                    </small>
                @endif
            </div>
        </div>

    @endif
</div>
