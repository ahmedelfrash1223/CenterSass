<div>
    <div class="card border-0 shadow-sm"><div class="card-body">
        <h5 class="mb-4">{{ $editMode ? 'تعديل المادة' : 'إضافة مادة جديدة' }}</h5>
        <!-- Subject Details -->
        <div class="row">
            <div class="col-md-6 mb-3"><label class="form-label">المرحلة <span class="text-danger">*</span></label><select wire:model="grade" class="form-select @error('grade') is-invalid @enderror"><option value="">اختر المرحلة</option>@foreach($grades as $value => $label)<option value="{{ $value }}">{{ $label }}</option>@endforeach</select>@error('grade')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
            <div class="col-md-6 mb-3"><label class="form-label">المادة <span class="text-danger">*</span></label><select wire:model="subject" class="form-select @error('subject') is-invalid @enderror"><option value="">اختر المادة</option>@foreach($subjectOptions as $value => $label)<option value="{{ $value }}">{{ $label }}</option>@endforeach</select>@error('subject')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
            <div class="col-md-6 mb-3"><label class="form-label">الحالة <span class="text-danger">*</span></label><select wire:model="is_active" class="form-select @error('is_active') is-invalid @enderror"><option value="1">نشط</option><option value="0">غير نشط</option></select>@error('is_active')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
        </div>
        <!-- Form Actions -->
        <div class="d-flex justify-content-end gap-2 mt-4">
            @if($editMode)
                <button type="button" wire:click="update" wire:loading.attr="disabled" wire:target="update" class="btn btn-primary"><span wire:loading.remove wire:target="update"><i class="ph-duotone ph-floppy-disk me-1"></i>حفظ التعديلات</span><span wire:loading wire:target="update"><span class="spinner-border spinner-border-sm me-1"></span>جاري الحفظ...</span></button>
            @else
                <button type="button" wire:click="save(false)" wire:loading.attr="disabled" wire:target="save(false)" class="btn btn-primary"><span wire:loading.remove wire:target="save(false)"><i class="ph-duotone ph-floppy-disk me-1"></i>حفظ</span><span wire:loading wire:target="save(false)"><span class="spinner-border spinner-border-sm me-1"></span>جاري الحفظ...</span></button>
                <button type="button" wire:click="save(true)" wire:loading.attr="disabled" wire:target="save(true)" class="btn btn-success"><span wire:loading.remove wire:target="save(true)"><i class="ph-duotone ph-plus-circle me-1"></i>حفظ وإنشاء جديد</span><span wire:loading wire:target="save(true)"><span class="spinner-border spinner-border-sm me-1"></span>جاري الحفظ...</span></button>
            @endif
            <a href="{{ route('teacher.subjects.index') }}" wire:navigate class="btn btn-light border"><i class="ph-duotone ph-x-circle me-1"></i>إلغاء</a>
        </div>
    </div></div>
</div>