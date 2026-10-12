<div>
    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <h5 class="mb-4">تذكرة دعم جديدة</h5>

            <div class="row">
                <div class="col-md-12 mb-3">
                    <label class="form-label">عنوان التذكرة <span class="text-danger">*</span></label>
                    <input type="text" wire:model="subject" class="form-control @error('subject') is-invalid @enderror"
                        placeholder="مثال: مشكلة في تسجيل الحضور">
                    @error('subject')
                    <div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">التصنيف <span class="text-danger">*</span></label>
                    <select wire:model="category" class="form-select @error('category') is-invalid @enderror">
                        <option value="">— اختر التصنيف —</option>
                        @foreach($categories as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('category')
                    <div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">الأولوية <span class="text-danger">*</span></label>
                    <select wire:model="priority" class="form-select @error('priority') is-invalid @enderror">
                        @foreach($priorities as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('priority')
                    <div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-12 mb-3">
                    <label class="form-label">تفاصيل المشكلة <span class="text-danger">*</span></label>
                    <textarea rows="6" wire:model="message" class="form-control @error('message') is-invalid @enderror"
                        placeholder="اشرح المشكلة بالتفصيل..."></textarea>
                    @error('message')
                    <div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                <button type="button" class="btn btn-primary" wire:click="save" wire:loading.attr="disabled"
                    wire:target="save">
                    <span wire:loading.remove wire:target="save">
                        <i class="ph-duotone ph-paper-plane-tilt me-1"></i>إرسال التذكرة
                    </span>
                    <span wire:loading wire:target="save">
                        <span class="spinner-border spinner-border-sm me-1"></span>جاري الإرسال...
                    </span>
                </button>
                <a href="{{ route('teacher.support.index') }}" wire:navigate class="btn btn-outline-secondary">
                    <i class="ph-duotone ph-x-circle me-1"></i>إلغاء
                </a>
            </div>
        </div>
    </div>
</div>
