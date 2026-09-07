<div>
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <h5 class="mb-4">{{ $editMode ? 'تعديل المدرس' : 'إضافة مدرس جديد' }}</h5>
            <div class="row">
                <div class="col-md-6 mb-3"><label class="form-label">اسم المدرس <span class="text-danger">*</span></label><input type="text" class="form-control @error('name') is-invalid @enderror" wire:model="name" placeholder="أدخل اسم المدرس">@error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                <div class="col-md-6 mb-3"><label class="form-label">اسم المستخدم <span class="text-danger">*</span></label><input type="text" class="form-control @error('username') is-invalid @enderror" wire:model="username" placeholder="أدخل اسم المستخدم">@error('username')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                <div class="col-md-6 mb-3"><label class="form-label">البريد الإلكتروني <span class="text-danger">*</span></label><input type="email" class="form-control @error('email') is-invalid @enderror" wire:model="email" placeholder="أدخل البريد الإلكتروني">@error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                <div class="col-md-6 mb-3"><label class="form-label">رقم الهاتف</label><input type="text" class="form-control @error('phone') is-invalid @enderror" wire:model="phone" placeholder="أدخل رقم الهاتف">@error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                <div class="col-md-6 mb-3"><label class="form-label">كلمة المرور @if($editMode)<small class="text-muted">(اتركها فارغة إذا لم تريد التغيير)</small>@else<span class="text-danger">*</span>@endif</label><input type="password" class="form-control @error('password') is-invalid @enderror" wire:model="password" placeholder="أدخل كلمة المرور">@error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                <div class="col-md-6 mb-3"><label class="form-label">تأكيد كلمة المرور @if(!$editMode)<span class="text-danger">*</span>@endif</label><input type="password" class="form-control @error('password_confirmation') is-invalid @enderror" wire:model="password_confirmation" placeholder="أعد إدخال كلمة المرور">@error('password_confirmation')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                <div class="col-md-6 mb-3"><label class="form-label">الحالة <span class="text-danger">*</span></label><select class="form-select @error('is_active') is-invalid @enderror" wire:model="is_active"><option value="1">نشط</option><option value="0">غير نشط</option></select>@error('is_active')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
            </div>
            <div class="d-flex justify-content-end gap-2 mt-4">
                @if($editMode)
                    <button type="button" class="btn btn-primary" wire:click="update" wire:loading.attr="disabled" wire:target="update"><span wire:loading.remove wire:target="update"><i class="ph-duotone ph-floppy-disk me-1"></i>حفظ التعديلات</span><span wire:loading wire:target="update"><span class="spinner-border spinner-border-sm me-1"></span>جاري الحفظ...</span></button>
                @else
                    <button type="button" class="btn btn-primary" wire:click="save" wire:loading.attr="disabled" wire:target="save"><span wire:loading.remove wire:target="save"><i class="ph-duotone ph-floppy-disk me-1"></i>حفظ</span><span wire:loading wire:target="save"><span class="spinner-border spinner-border-sm me-1"></span>جاري الحفظ...</span></button>
                    <button type="button" class="btn btn-success" wire:click="save(true)" wire:loading.attr="disabled" wire:target="save(true)"><span wire:loading.remove wire:target="save(true)"><i class="ph-duotone ph-plus-circle me-1"></i>حفظ وإنشاء جديد</span><span wire:loading wire:target="save(true)"><span class="spinner-border spinner-border-sm me-1"></span>جاري الحفظ...</span></button>
                @endif
                <a href="{{ route('admin.teacher.index') }}" wire:navigate class="btn btn-light border"><i class="ph-duotone ph-x-circle me-1"></i>إلغاء</a>
            </div>
        </div>
    </div>
</div>
