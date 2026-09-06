<div>
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <h5 class="mb-4">تعديل المدرس</h5>

            <div class="row">
                <!-- Name -->
                <div class="col-md-6 mb-3">
                    <label class="form-label">اسم المدرس <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" wire:model="name" placeholder="أدخل اسم المدرس">
                    @error('name')
                    <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <!-- Username -->
                <div class="col-md-6 mb-3">
                    <label class="form-label">اسم المستخدم <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('username') is-invalid @enderror" wire:model="username" placeholder="أدخل اسم المستخدم">
                    @error('username')
                    <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <!-- Email -->
                <div class="col-md-6 mb-3">
                    <label class="form-label">البريد الإلكتروني <span class="text-danger">*</span></label>
                    <input type="email" class="form-control @error('email') is-invalid @enderror" wire:model="email" placeholder="أدخل البريد الإلكتروني">
                    @error('email')
                    <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <!-- Phone -->
                <div class="col-md-6 mb-3">
                    <label class="form-label">رقم الهاتف</label>
                    <input type="text" class="form-control @error('phone') is-invalid @enderror" wire:model="phone" placeholder="أدخل رقم الهاتف">
                    @error('phone')
                    <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <!-- Password -->
                <div class="col-md-6 mb-3">
                    <label class="form-label">كلمة المرور <small class="text-muted">(اتركها فارغة إذا لم تريد التغيير)</small></label>
                    <input type="password" class="form-control @error('password') is-invalid @enderror" wire:model="password" placeholder="أدخل كلمة المرور الجديدة">
                    @error('password')
                    <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <!-- Password Confirmation -->
                <div class="col-md-6 mb-3">
                    <label class="form-label">تأكيد كلمة المرور</label>
                    <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror" wire:model="password_confirmation" placeholder="أعد إدخال كلمة المرور">
                    @error('password_confirmation')
                    <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <!-- Status -->
                <div class="col-md-6 mb-3">
                    <label class="form-label">الحالة <span class="text-danger">*</span></label>
                    <select class="form-select @error('is_active') is-invalid @enderror" wire:model="is_active">
                        <option value="1">نشط</option>
                        <option value="0">غير نشط</option>
                    </select>
                    @error('is_active')
                    <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-4">
                <button type="button" class="btn btn-primary"
                        wire:click="update"
                        wire:loading.attr="disabled"
                        wire:target="update">
                    <span wire:loading.remove wire:target="update">
                        <i class="ph-duotone ph-floppy-disk me-1"></i>
                        حفظ التعديلات
                    </span>
                    <span wire:loading wire:target="update">
                        <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                        جاري الحفظ...
                    </span>
                </button>

                <a href="{{ route('admin.teacher.index') }}" wire:navigate class="btn btn-light border">
                    <i class="ph-duotone ph-x-circle me-1"></i>
                    إلغاء
                </a>
            </div>
        </div>
    </div>
</div>
