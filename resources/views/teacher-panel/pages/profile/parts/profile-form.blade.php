<div class="profile-page" dir="rtl">

    @if(session()->has('profile-success'))
        <div class="alert alert-success d-flex align-items-center gap-2 border-0 shadow-sm mb-4">
            <i class="ph-duotone ph-check-circle fs-4"></i>
            <span>{{ session('profile-success') }}</span>
        </div>
    @endif

    @if(session()->has('password-success'))
        <div class="alert alert-success d-flex align-items-center gap-2 border-0 shadow-sm mb-4">
            <i class="ph-duotone ph-check-circle fs-4"></i>
            <span>{{ session('password-success') }}</span>
        </div>
    @endif

    <div class="card border-0 shadow-sm overflow-hidden">

        <!-- Profile Header -->
        <div class="card-header bg-primary text-white border-0 p-4">
            <div class="d-flex align-items-center justify-content-between gap-3 flex-wrap">
                <div class="d-flex align-items-center gap-3">
                    <!-- Avatar -->
                    <div class="position-relative flex-shrink-0">
                        <div class="avtar avtar-xl bg-white text-primary rounded-4 shadow-sm fw-bold fs-2">
                            {{ mb_substr($teacher->name, 0, 1) }}
                        </div>
                        <span
                            class="position-absolute bottom-0 start-0 translate-middle p-2 bg-success border border-3 border-white rounded-circle"></span>
                    </div>

                    <!-- Teacher Info -->
                    <div>
                        <h4 class="mb-1 text-white fw-bold d-flex align-items-center gap-2">
                            {{ $teacher->name }}
                            <span class="badge bg-white bg-opacity-25 text-white" style="font-size: 11px;">
                                <i class="ph-duotone {{ $teacher->role->icon() }} me-1"></i>
                                {{ $teacher->role->label() }}
                            </span>
                        </h4>
                        <div class="d-flex align-items-center gap-1 text-white opacity-75">
                            <i class="ph-duotone ph-envelope"></i>
                            <span>{{ $teacher->email }}</span>
                        </div>
                    </div>
                </div>

                <!-- Teacher Code -->
                <div class="text-start">
                    <small class="d-block text-white opacity-75 mb-1">
                        كود المدرس
                    </small>
                    <span class="badge bg-white text-primary fs-6 px-3 py-2">
                        {{ $teacher->code }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Account Info Strip -->
        <div class="bg-light border-bottom p-3">
            <div class="row g-2 text-center text-md-start">
                <div class="col-md-4">
                    <small class="text-muted d-block" style="font-size: 11px;">
                        <i class="ph-duotone ph-crown text-warning me-1"></i>
                        نوع الحساب
                    </small>
                    <span
                        class="badge bg-{{ $teacher->role->color() }} bg-opacity-10 text-{{ $teacher->role->color() }} mt-1">
                        {{ $teacher->role->label() }}
                    </span>
                </div>
                @if($teacher->owner)
                    <div class="col-md-4">
                        <small class="text-muted d-block" style="font-size: 11px;">
                            <i class="ph-duotone ph-user-check text-primary me-1"></i>
                            تابع لـ
                        </small>
                        <span class="small fw-semibold mt-1 d-inline-block">{{ $teacher->owner->name }}</span>
                    </div>
                @else
                    <div class="col-md-4">
                        <small class="text-muted d-block" style="font-size: 11px;">
                            <i class="ph-duotone ph-crown text-warning me-1"></i>
                            الحساب
                        </small>
                        <span class="small fw-semibold mt-1 d-inline-block">صاحب الحساب الرئيسي</span>
                    </div>
                @endif
                <div class="col-md-4">
                    <small class="text-muted d-block" style="font-size: 11px;">
                        <i class="ph-duotone ph-shield-check text-success me-1"></i>
                        الحالة
                    </small>
                    @if($teacher->is_active)
                        <span class="badge bg-success bg-opacity-10 text-success mt-1">
                            <i class="ph-duotone ph-check-circle me-1"></i>
                            نشط
                        </span>
                    @else
                        <span class="badge bg-danger bg-opacity-10 text-danger mt-1">
                            <i class="ph-duotone ph-x-circle me-1"></i>
                            غير نشط
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Body -->
        <div class="card-body p-0">

            <!-- Section Header -->
            <div class="p-4 border-bottom">
                <div class="d-flex align-items-center gap-3">
                    <div class="avtar avtar-s bg-light-primary text-primary rounded-3">
                        <i class="ph-duotone ph-user-circle fs-4"></i>
                    </div>
                    <div>
                        <h5 class="mb-1 fw-bold">المعلومات الشخصية</h5>
                        <p class="mb-0 text-muted small">قم بإدارة بيانات حسابك الشخصية</p>
                    </div>
                </div>
            </div>

            <!-- Name -->
            <div class="p-4 border-bottom">
                <div class="row align-items-start g-3">
                    <div class="col-auto">
                        <div class="avtar avtar-s bg-light-secondary text-secondary rounded-3">
                            <i class="ph-duotone ph-user fs-5"></i>
                        </div>
                    </div>
                    <div class="col">
                        <label class="text-muted small d-block mb-1">الاسم</label>

                        @if($editing === 'name')
                            <div>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                       wire:model="name" wire:keydown.enter="save" autofocus>
                                @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="d-flex align-items-center gap-2 mt-3">
                                    <button type="button" class="btn btn-success btn-sm d-flex align-items-center gap-1"
                                            wire:click="save" wire:loading.attr="disabled">
                                        <i class="ph-duotone ph-check"></i>
                                        حفظ
                                    </button>
                                    <button type="button"
                                            class="btn btn-light-danger btn-sm d-flex align-items-center gap-1"
                                            wire:click="cancelEditing">
                                        <i class="ph-duotone ph-x"></i>
                                        إلغاء
                                    </button>
                                </div>
                            </div>
                        @else
                            <div class="d-flex align-items-center gap-2">
                                <span class="fw-semibold text-body">{{ $teacher->name }}</span>
                                <button type="button" class="btn btn-sm btn-light-primary btn-icon"
                                        wire:click="startEditing('name')" title="تعديل الاسم">
                                    <i class="ph-duotone ph-pencil-simple"></i>
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Username -->
            <div class="p-4 border-bottom">
                <div class="row align-items-start g-3">
                    <div class="col-auto">
                        <div class="avtar avtar-s bg-light-secondary text-secondary rounded-3">
                            <i class="ph-duotone ph-identification-card fs-5"></i>
                        </div>
                    </div>
                    <div class="col">
                        <label class="text-muted small d-block mb-1">اسم المستخدم</label>

                        @if($editing === 'username')
                            <div>
                                <input type="text" class="form-control @error('username') is-invalid @enderror"
                                       wire:model="username" wire:keydown.enter="save" autofocus>
                                @error('username')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="d-flex align-items-center gap-2 mt-3">
                                    <button type="button" class="btn btn-success btn-sm d-flex align-items-center gap-1"
                                            wire:click="save" wire:loading.attr="disabled">
                                        <i class="ph-duotone ph-check"></i>
                                        حفظ
                                    </button>
                                    <button type="button"
                                            class="btn btn-light-danger btn-sm d-flex align-items-center gap-1"
                                            wire:click="cancelEditing">
                                        <i class="ph-duotone ph-x"></i>
                                        إلغاء
                                    </button>
                                </div>
                            </div>
                        @else
                            <div class="d-flex align-items-center gap-2">
                                <span class="fw-semibold text-body">{{ $teacher->username }}</span>
                                <button type="button" class="btn btn-sm btn-light-primary btn-icon"
                                        wire:click="startEditing('username')" title="تعديل اسم المستخدم">
                                    <i class="ph-duotone ph-pencil-simple"></i>
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Email -->
            <div class="p-4 border-bottom">
                <div class="row align-items-start g-3">
                    <div class="col-auto">
                        <div class="avtar avtar-s bg-light-secondary text-secondary rounded-3">
                            <i class="ph-duotone ph-envelope fs-5"></i>
                        </div>
                    </div>
                    <div class="col">
                        <label class="text-muted small d-block mb-1">البريد الإلكتروني</label>

                        @if($editing === 'email')
                            <div>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                       wire:model="email" wire:keydown.enter="save" autofocus>
                                @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="d-flex align-items-center gap-2 mt-3">
                                    <button type="button" class="btn btn-success btn-sm d-flex align-items-center gap-1"
                                            wire:click="save" wire:loading.attr="disabled">
                                        <i class="ph-duotone ph-check"></i>
                                        حفظ
                                    </button>
                                    <button type="button"
                                            class="btn btn-light-danger btn-sm d-flex align-items-center gap-1"
                                            wire:click="cancelEditing">
                                        <i class="ph-duotone ph-x"></i>
                                        إلغاء
                                    </button>
                                </div>
                            </div>
                        @else
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                <span class="fw-semibold text-body">{{ $teacher->email }}</span>
                                <button type="button" class="btn btn-sm btn-light-primary btn-icon"
                                        wire:click="startEditing('email')" title="تعديل البريد الإلكتروني">
                                    <i class="ph-duotone ph-pencil-simple"></i>
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Phone -->
            <div class="p-4 border-bottom">
                <div class="row align-items-start g-3">
                    <div class="col-auto">
                        <div class="avtar avtar-s bg-light-secondary text-secondary rounded-3">
                            <i class="ph-duotone ph-phone fs-5"></i>
                        </div>
                    </div>
                    <div class="col">
                        <label class="text-muted small d-block mb-1">رقم الهاتف</label>

                        @if($editing === 'phone')
                            <div>
                                <input type="text" class="form-control @error('phone') is-invalid @enderror"
                                       wire:model="phone" wire:keydown.enter="save" autofocus>
                                @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="d-flex align-items-center gap-2 mt-3">
                                    <button type="button" class="btn btn-success btn-sm d-flex align-items-center gap-1"
                                            wire:click="save" wire:loading.attr="disabled">
                                        <i class="ph-duotone ph-check"></i>
                                        حفظ
                                    </button>
                                    <button type="button"
                                            class="btn btn-light-danger btn-sm d-flex align-items-center gap-1"
                                            wire:click="cancelEditing">
                                        <i class="ph-duotone ph-x"></i>
                                        إلغاء
                                    </button>
                                </div>
                            </div>
                        @else
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                <span class="fw-semibold text-body">
                                    {{ $teacher->phone ?: 'غير محدد' }}
                                </span>
                                <button type="button" class="btn btn-sm btn-light-primary btn-icon"
                                        wire:click="startEditing('phone')" title="تعديل رقم الهاتف">
                                    <i class="ph-duotone ph-pencil-simple"></i>
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Teacher Code -->
            <div class="p-4 border-bottom">
                <div class="row align-items-start g-3">
                    <div class="col-auto">
                        <div class="avtar avtar-s bg-light-secondary text-secondary rounded-3">
                            <i class="ph-duotone ph-key fs-5"></i>
                        </div>
                    </div>
                    <div class="col">
                        <label class="text-muted small d-block mb-1">كود المدرس</label>
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <code class="px-3 py-2 bg-light rounded-2 text-body fw-semibold">
                                {{ $teacher->code }}
                            </code>
                            <span class="badge bg-light-secondary text-secondary">
                                <i class="ph-duotone ph-lock-key me-1"></i>
                                ثابت
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Change Password -->
            <div>
                <div class="p-4 border-bottom">
                    <div class="d-flex align-items-center gap-3">
                        <div class="avtar avtar-s bg-light-warning text-warning rounded-3">
                            <i class="ph-duotone ph-lock-key fs-4"></i>
                        </div>
                        <div>
                            <h5 class="mb-1 fw-bold">تغيير كلمة المرور</h5>
                            <p class="mb-0 text-muted small">قم بتحديث كلمة المرور الخاصة بحسابك</p>
                        </div>
                    </div>
                </div>

                <!-- Password Fields -->
                <div class="p-4">
                    <div class="row g-3">

                        <!-- Current Password -->
                        <div class="col-12">
                            <label class="form-label fw-semibold">كلمة المرور الحالية</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="ph-duotone ph-lock-key"></i>
                                </span>
                                <input type="password"
                                       class="form-control border-start-0 @error('current_password') is-invalid @enderror"
                                       wire:model="current_password" placeholder="أدخل كلمة المرور الحالية">
                            </div>
                            @error('current_password')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- New Password -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">كلمة المرور الجديدة</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="ph-duotone ph-key"></i>
                                </span>
                                <input type="password"
                                       class="form-control border-start-0 @error('new_password') is-invalid @enderror"
                                       wire:model="new_password" placeholder="أدخل كلمة المرور الجديدة">
                            </div>
                            @error('new_password')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Confirm Password -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">تأكيد كلمة المرور الجديدة</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="ph-duotone ph-check-circle"></i>
                                </span>
                                <input type="password"
                                       class="form-control border-start-0 @error('new_password_confirmation') is-invalid @enderror"
                                       wire:model="new_password_confirmation" wire:keydown.enter="changePassword"
                                       placeholder="أعد إدخال كلمة المرور الجديدة">
                            </div>
                            @error('new_password_confirmation')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Actions -->
                        <div class="col-12">
                            <div class="d-flex align-items-center gap-2">
                                <button type="button" class="btn btn-warning d-inline-flex align-items-center gap-2"
                                        wire:click="changePassword" wire:loading.attr="disabled">
                                    <i class="ph-duotone ph-lock-key"></i>
                                    <span wire:loading.remove wire:target="changePassword">
                                        تغيير كلمة المرور
                                    </span>
                                    <span wire:loading wire:target="changePassword">
                                        جاري التحديث...
                                    </span>
                                </button>
                                <button type="button"
                                        class="btn btn-light-secondary d-inline-flex align-items-center gap-2"
                                        wire:click="clearPasswordFields">
                                    <i class="ph-duotone ph-eraser"></i>
                                    مسح
                                </button>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div>

        <!-- Footer -->
        <div class="card-footer bg-light border-0 p-4">
            <a wire:navigate href="{{ route('teacher.dashboard') }}"
               class="btn btn-light-secondary d-inline-flex align-items-center gap-2">
                <i class="ph-duotone ph-arrow-right"></i>
                إلغاء والعودة للوحة التحكم
            </a>
        </div>

    </div>

</div>
