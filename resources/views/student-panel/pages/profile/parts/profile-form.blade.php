<div class="student-profile-page">
    @foreach(['profile-success', 'password-success'] as $messageKey)
        @if(session()->has($messageKey))
            <div class="alert alert-success" role="status">{{ session($messageKey) }}</div>
        @endif
    @endforeach
    <div class="row g-4">
        <div class="col-xl-8">
            <section class="card student-profile-card">
                <div class="card-header d-flex align-items-center gap-3">
                    <span class="student-avatar student-avatar-large">
                        {{ mb_substr($student->name, 0, 1) }}
                    </span>
                    <div>
                        <h2 class="h5 mb-1">{{ $student->name }}</h2>
                        <p class="text-muted small mb-0">حساب الطالب</p>
                    </div>
                </div>
                <div class="card-body">
                    <h3 class="h6 mb-4">بيانات الحساب</h3>
                    <form wire:submit.prevent="save">
                        @foreach(['name' => 'الاسم', 'username' => 'اسم المستخدم', 'email' => 'البريد الإلكتروني', 'phone' => 'رقم الهاتف'] as $field => $label)
                            <div class="student-profile-row" wire:key="student-field-{{ $field }}">
                                <label for="profile-{{ $field }}" class="form-label mb-0">{{ $label }}</label>
                                @if($editing === $field)
                                    <div class="flex-grow-1">
                                        <input id="profile-{{ $field }}"
                                               type="{{ $field === 'email' ? 'email' : ($field === 'phone' ? 'tel' : 'text') }}"
                                               wire:model="{{ $field }}"
                                               class="form-control @error($field) is-invalid @enderror"
                                               maxlength="{{ $field === 'phone' ? '20' : '191' }}"
                                               aria-describedby="profile-error-{{ $field }}">
                                        <div id="profile-error-{{ $field }}">
                                            @error($field)
                                                <span class="invalid-feedback d-block">
                                                    {{ $message }}
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="d-flex gap-2 mt-2">
                                            <button type="submit" class="btn btn-primary btn-sm"
                                                    wire:loading.attr="disabled" wire:target="save">حفظ
                                            </button>
                                            <button type="button" class="btn btn-light btn-sm"
                                                    wire:click="cancelEditing">إلغاء
                                            </button>
                                        </div>
                                    </div>
                                @else
                                    <span class="flex-grow-1 text-break">{{ $student->{$field} ?: 'غير محدد' }}</span>
                                    <button type="button" wire:click="startEditing('{{ $field }}')"
                                            class="btn btn-light btn-sm" aria-label="تعديل {{ $label }}">
                                        <i class="ti ti-edit" aria-hidden="true"></i>
                                    </button>
                                @endif
                            </div>
                        @endforeach
                    </form>
                    <dl class="student-details mt-4 mb-0">
                        <div>
                            <dt>كود الطالب</dt>
                            <dd dir="ltr">{{ $student->code }}</dd>
                        </div>
                        <div>
                            <dt>الصف الدراسي</dt>
                            <dd>{{ $gradeLabel }}</dd>
                        </div>
                        <div>
                            <dt>الفرع</dt>
                            <dd>{{ $student->branch?->name ?? 'غير محدد' }}</dd>
                        </div>
                    </dl>
                    <p class="text-muted small mt-3 mb-0">لتغيير الصف الدراسي أو الفرع، تواصل مع إدارة المركز.</p>
                </div>
            </section>
            @if($student->profile)
                <section class="card">
                    <div class="card-header"><h2 class="h5 mb-0">معلومات الطالب وولي الأمر</h2></div>
                    <div class="card-body">
                        <dl class="student-details mb-0">
                            @foreach(['school_name' => 'المدرسة', 'guardian_name' => 'اسم ولي الأمر', 'guardian_phone' => 'هاتف ولي الأمر', 'guardian_relation' => 'صلة القرابة', 'city' => 'المدينة', 'address' => 'العنوان'] as $field => $label)
                                <div>
                                    <dt>{{ $label }}</dt>
                                    <dd>{{ $student->profile->{$field} ?: 'غير محدد' }}</dd>
                                </div>
                            @endforeach
                        </dl>
                    </div>
                </section>
            @endif
        </div>
        <div class="col-xl-4">
            <section class="card">
                <div class="card-header"><h2 class="h5 mb-0"><i class="ti ti-lock me-2" aria-hidden="true"></i> أمان
                        الحساب</h2></div>
                <div class="card-body">
                    <p class="text-muted small mb-4">اختر كلمة مرور قوية من 8 أحرف على الأقل.</p>
                    <form wire:submit.prevent="changePassword">
                        @foreach(['current_password' => 'كلمة المرور الحالية', 'new_password' => 'كلمة المرور الجديدة', 'new_password_confirmation' => 'تأكيد كلمة المرور الجديدة'] as $field => $label)
                            <div class="mb-3">
                                <label for="student-{{ $field }}" class="form-label">{{ $label }}</label>
                                <input id="student-{{ $field }}" type="password" wire:model="{{ $field }}" required
                                       maxlength="255"
                                       autocomplete="{{ $field === 'current_password' ? 'current-password' : 'new-password' }}"
                                       class="form-control @error($field) is-invalid @enderror"
                                       aria-describedby="password-error-{{ $field }}">
                                <div id="password-error-{{ $field }}">
                                    @error($field)
                                    <span class="invalid-feedback d-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        @endforeach
                        <button type="submit" class="btn btn-primary w-100" wire:loading.attr="disabled"
                                wire:target="changePassword">
                            <span wire:loading.remove wire:target="changePassword">تحديث كلمة المرور</span>
                            <span wire:loading wire:target="changePassword">جاري التحديث...</span>
                        </button>
                        <button type="button" class="btn btn-light w-100 mt-2" wire:click="clearPasswordFields">مسح
                            الحقول
                        </button>
                    </form>
                </div>
            </section>
        </div>
    </div>
</div>
