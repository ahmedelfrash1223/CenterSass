<div>
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <h5 class="mb-4">إضافة باقة جديدة</h5>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">اسم الباقة<span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('title') is-invalid @enderror" wire:model="title">
                    @error('title')
                    <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">السعر<span class="text-danger">*</span></label>
                    <input type="number" step="0.01" class="form-control @error('price') is-invalid @enderror"
                           wire:model="price">
                    @error('price')
                    <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">نوع الخصم<span class="text-danger">*</span></label>
                    <select class="form-select @error('discount_type') is-invalid @enderror" wire:model="discount_type">
                        @foreach($discountTypes as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('discount_type')
                    <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">قيمة الخصم<span class="text-danger">*</span></label>
                    <input type="number" step="0.01" class="form-control @error('discount_value') is-invalid @enderror"
                           wire:model="discount_value">
                    @error('discount_value')
                    <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">السعر بعد الخصم<span class="text-danger">*</span></label>
                    <input type="text" class="form-control"
                           value="{{ $price !== '' ? number_format(max(0, $discount_type == 1 ? ((float)$price - ((float)$price * (float)$discount_value / 100)) : ((float)$price - (float)$discount_value)), 2) : '0.00' }}"
                           disabled>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">المدة<span class="text-danger">*</span></label>
                    <input type="number" min="1" class="form-control @error('duration') is-invalid @enderror"
                           wire:model="duration">
                    @error('duration')
                    <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">نوع المدة<span class="text-danger">*</span></label>
                    <select class="form-select @error('duration_type') is-invalid @enderror" wire:model="duration_type">
                        @foreach($durationTypes as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('duration_type')
                    <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            <hr>

            <h6 class="mb-3">خصائص الباقة</h6>

            <div class="row">
                @foreach($featureTypes as $index => $feature)
                    <div class="col-md-6 mb-3">
                        <label class="form-label">{{ $feature->label() }} <small
                                class="text-muted">({{ $feature->unit() }})</small></label>
                        <input type="number" min="0"
                               class="form-control @error('features.'.$index.'.value') is-invalid @enderror"
                               wire:model="features.{{ $index }}.value">
                        @error('features.'.$index.'.value')
                        <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                @endforeach
            </div>

            @error('features')
            <div class="alert alert-danger">{{ $message }}</div>
            @enderror

            <div class="d-flex justify-content gap-2 mt-4">
                <button type="button" class="btn btn-primary"
                        wire:click="save(false)"
                        wire:loading.attr="disabled"
                        wire:target="save(false)">
                    <span wire:loading.remove wire:target="save(false)">
                        <i class="ph-duotone ph-floppy-disk me-1"></i>
                        حفظ
                    </span>
                    <span wire:loading wire:target="save(false)">
                        <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                        جاري الحفظ...
                    </span>
                </button>

                <button type="button" class="btn btn-success"
                        wire:click="save(true)"
                        wire:loading.attr="disabled"
                        wire:target="save(true)">
                    <span wire:loading.remove wire:target="save(true)">
                        <i class="ph-duotone ph-plus-circle me-1"></i>
                        حفظ وإنشاء جديد
                    </span>
                    <span wire:loading wire:target="save(true)">
                        <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                        جاري الحفظ...
                    </span>
                </button>

                <a href="{{ route('admin.package.index') }}" wire:navigate class="btn btn-light border">
                    <i class="ph-duotone ph-x-circle me-1"></i>
                    إلغاء
                </a>
            </div>
        </div>
    </div>
</div>
