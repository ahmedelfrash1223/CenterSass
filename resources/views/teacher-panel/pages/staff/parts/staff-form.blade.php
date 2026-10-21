<div>
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <h5 class="mb-4">{{ $editMode ? 'تعديل عضو الفريق' : 'إضافة عضو جديد' }}</h5>

            @if($editMode && isset($teacher))
                {{-- Info Strip --}}
                <div class="alert alert-light border mb-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center gap-2">
                                <div
                                    class="rounded-3 d-flex align-items-center justify-content-center bg-primary-subtle text-primary"
                                    style="width: 40px; height: 40px;">
                                    <i class="ph-duotone ph-user-plus"></i>
                                </div>
                                <div>
                                    <small class="text-body-secondary d-block">أضيف بواسطة</small>
                                    @if($teacher->creator)
                                        <span
                                            class="badge bg-{{ $teacher->creator->role->color() }}-subtle text-{{ $teacher->creator->role->color() }}-emphasis">
                                            <i class="ph-duotone {{ $teacher->creator->role->icon() }} me-1"></i>
                                            {{ $teacher->creator->name }}
                                        </span>
                                    @else
                                        <span class="text-body-secondary small">—</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center gap-2">
                                <div
                                    class="rounded-3 d-flex align-items-center justify-content-center bg-info-subtle text-info"
                                    style="width: 40px; height: 40px;">
                                    <i class="ph-duotone ph-calendar"></i>
                                </div>
                                <div>
                                    <small class="text-body-secondary d-block">تاريخ الإضافة</small>
                                    <span
                                        class="fw-semibold small">{{ $teacher->created_at?->format('Y-m-d h:i A') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif


            <div class="card border mb-3 {{ in_array('sectionBasic', $sectionsWithErrors) ? 'border-danger border-2' : '' }}">
                <div class="card-header bg-body d-flex align-items-center justify-content-between"
                     style="cursor: pointer;"
                     wire:click="toggleSection('sectionBasic')">
                    <div class="d-flex align-items-center gap-2">
                        <div
                            class="rounded-3 d-flex align-items-center justify-content-center bg-primary-subtle text-primary"
                            style="width: 40px; height: 40px;">
                            <i class="ph-duotone ph-user"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 fw-bold">البيانات الأساسية</h6>
                            <small class="text-body-secondary">الاسم، اسم المستخدم، البريد، كلمة المرور</small>
                        </div>
                    </div>
                    <i class="ph-duotone {{ $sectionBasic ? 'ph-caret-up' : 'ph-caret-down' }}"></i>
                </div>

                @if($sectionBasic)
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">الاسم <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                       wire:model="name" placeholder="أدخل الاسم">
                                @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">اسم المستخدم <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('username') is-invalid @enderror"
                                       wire:model="username" placeholder="أدخل اسم المستخدم">
                                @error('username')
                                <div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">البريد الإلكتروني <span class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                       wire:model="email" placeholder="أدخل البريد الإلكتروني">
                                @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">رقم الهاتف</label>
                                <input type="text" class="form-control @error('phone') is-invalid @enderror"
                                       wire:model="phone" placeholder="أدخل رقم الهاتف">
                                @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">نوع الحساب <span class="text-danger">*</span></label>
                                <select class="form-select @error('role') is-invalid @enderror" wire:model.live="role">
                                    @foreach($roles as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('role')
                                <div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    كلمة المرور
                                    @if($editMode)
                                        <small class="text-body-secondary">(اتركها فارغة إذا لم تريد التغيير)</small>
                                    @else
                                        <small class="text-body-secondary">(اتركها فارغة لاستخدام 12345678)</small>
                                    @endif
                                </label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror"
                                       wire:model="password" placeholder="أدخل كلمة المرور">
                                @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">تأكيد كلمة المرور</label>
                                <input type="password"
                                       class="form-control @error('password_confirmation') is-invalid @enderror"
                                       wire:model="password_confirmation" placeholder="أعد إدخال كلمة المرور">
                                @error('password_confirmation')
                                <div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">الحالة <span class="text-danger">*</span></label>
                                <div class="d-flex gap-2">
                                    <button type="button" wire:click="$set('is_active', 1)"
                                            class="status-option {{ $is_active == 1 ? 'active' : '' }}">
                                        <i class="ph-duotone ph-check-circle"></i>نشط
                                    </button>
                                    <button type="button" wire:click="$set('is_active', 0)"
                                            class="status-option {{ $is_active == 0 ? 'inactive' : '' }}">
                                        <i class="ph-duotone ph-x-circle"></i>غير نشط
                                    </button>
                                </div>
                                @error('is_active')
                                <div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                @endif
            </div>


            @if(!$editMode || (isset($teacher) && !$teacher->isOwner()))
                @if((int) $role === \App\Enums\Teacher\TeacherRoleEnum::STAFF->value)
                    <div class="card border mb-3 {{ in_array('sectionGrades', $sectionsWithErrors) ? 'border-danger border-2' : '' }}">
                        <div class="card-header bg-body d-flex align-items-center justify-content-between"
                             style="cursor: pointer;"
                             wire:click="toggleSection('sectionGrades')">
                            <div class="d-flex align-items-center gap-2">
                                <div
                                    class="rounded-3 d-flex align-items-center justify-content-center bg-success-subtle text-success"
                                    style="width: 40px; height: 40px;">
                                    <i class="ph-duotone ph-books"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-bold">الصفوف والمواد الدراسية</h6>
                                    <small class="text-body-secondary">حدد الصفوف اللي هيدرّسها والمواد لكل صف</small>
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                @if(count($assigned_grades) > 0)
                                    <span class="badge bg-success">{{ count($assigned_grades) }} صف</span>
                                @endif
                                @if(count($assigned_subjects) > 0)
                                    <span class="badge bg-primary">{{ count($assigned_subjects) }} مادة</span>
                                @endif
                                <i class="ph-duotone {{ $sectionGrades ? 'ph-caret-up' : 'ph-caret-down' }}"></i>
                            </div>
                        </div>

                        @if($sectionGrades)
                            <div class="card-body">

                                {{-- Grades --}}
                                <div class="mb-4">
                                    <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                                        <label class="form-label fw-semibold mb-0">
                                            <i class="ph-duotone ph-graduation-cap me-1"></i>
                                            الصفوف الدراسية
                                        </label>
                                        <div class="d-flex gap-2">
                                            <button type="button" wire:click="selectAllGrades"
                                                    class="btn btn-sm btn-outline-success">
                                                <i class="ph-duotone ph-check-square me-1"></i>تحديد الكل
                                            </button>
                                            <button type="button" wire:click="deselectAllGrades"
                                                    class="btn btn-sm btn-outline-danger">
                                                <i class="ph-duotone ph-x-square me-1"></i>إلغاء الكل
                                            </button>
                                        </div>
                                    </div>

                                    @error('assigned_grades')
                                        <div class="alert alert-danger small mb-2">{{ $message }}</div>
                                    @enderror

                                    @if($availableGrades->count() > 0)
                                        <div class="row g-2">
                                            @foreach($availableGrades as $grade)
                                                @php
                                                    $gradeLabel = $gradeOptions[$grade] ?? $grade;
                                                    $isSelected = in_array((string) $grade, $assigned_grades);
                                                @endphp
                                                <div class="col-md-4 col-lg-3">
                                                    <label
                                                        class="permission-item {{ $isSelected ? 'is-selected' : '' }}"
                                                        for="grade-{{ $grade }}">
                                                        <input type="checkbox"
                                                               wire:model.live="assigned_grades"
                                                               value="{{ $grade }}"
                                                               id="grade-{{ $grade }}"
                                                               class="permission-checkbox"
                                                            {{ $isSelected ? 'checked' : '' }}>
                                                        <div class="permission-content">
                                                            <span class="permission-icon">
                                                                <i class="ph-duotone ph-graduation-cap"></i>
                                                            </span>
                                                            <span class="permission-label">{{ $gradeLabel }}</span>
                                                            <span class="permission-check">
                                                                <i class="ph-duotone ph-check"></i>
                                                            </span>
                                                        </div>
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="alert alert-info small mb-0">
                                            <i class="ph-duotone ph-info me-1"></i>
                                            مفيش صفوف متاحة — لازم يكون عندك مواد مسجلة الأول.
                                        </div>
                                    @endif
                                </div>

                                {{-- Subjects --}}
                                @if(count($assigned_grades) > 0)
                                    <div>
                                        <div
                                            class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                                            <label class="form-label fw-semibold mb-0">
                                                <i class="ph-duotone ph-notebook me-1"></i>
                                                المواد الدراسية
                                                <small class="text-body-secondary">(حسب الصفوف المختارة)</small>
                                            </label>
                                            <div class="d-flex gap-2">
                                                <button type="button" wire:click="selectAllSubjects"
                                                        class="btn btn-sm btn-outline-success">
                                                    <i class="ph-duotone ph-check-square me-1"></i>تحديد الكل
                                                </button>
                                                <button type="button" wire:click="deselectAllSubjects"
                                                        class="btn btn-sm btn-outline-danger">
                                                    <i class="ph-duotone ph-x-square me-1"></i>إلغاء الكل
                                                </button>
                                            </div>
                                        </div>

                                        @error('assigned_subjects')
                                            <div class="alert alert-danger small mb-2">{{ $message }}</div>
                                        @enderror

                                        @if(count($availableSubjectsFiltered) > 0)
                                            @php
                                                $grouped = collect($availableSubjectsFiltered)->groupBy('grade_label');
                                            @endphp

                                            @foreach($grouped as $gradeLabel => $subjects)
                                                <div class="mb-3">
                                                    <div class="d-flex align-items-center gap-2 mb-2">
                                                        <span
                                                            class="badge bg-secondary-subtle text-secondary-emphasis px-3 py-2">
                                                            <i class="ph-duotone ph-graduation-cap me-1"></i>
                                                            {{ $gradeLabel }}
                                                        </span>
                                                        <small class="text-body-secondary">{{ $subjects->count() }}
                                                            مادة</small>
                                                    </div>
                                                    <div class="row g-2">
                                                        @foreach($subjects as $subject)
                                                            @php $isSel = in_array((string) $subject['id'], $assigned_subjects); @endphp
                                                            <div class="col-md-4 col-lg-3">
                                                                <label
                                                                    class="permission-item {{ $isSel ? 'is-selected' : '' }}"
                                                                    for="subject-{{ $subject['id'] }}">
                                                                    <input type="checkbox"
                                                                           wire:model.live="assigned_subjects"
                                                                           value="{{ $subject['id'] }}"
                                                                           id="subject-{{ $subject['id'] }}"
                                                                           class="permission-checkbox"
                                                                        {{ $isSel ? 'checked' : '' }}>
                                                                    <div class="permission-content">
                                                                        <span class="permission-icon">
                                                                            <i class="ph-duotone ph-notebook"></i>
                                                                        </span>
                                                                        <span
                                                                            class="permission-label">{{ $subject['subject_label'] }}</span>
                                                                        <span class="permission-check">
                                                                            <i class="ph-duotone ph-check"></i>
                                                                        </span>
                                                                    </div>
                                                                </label>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endforeach
                                        @else
                                            <div class="alert alert-warning small mb-0">
                                                <i class="ph-duotone ph-warning me-1"></i>
                                                مفيش مواد متاحة في الصفوف المختارة.
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                @endif
            @endif


            @if(!$editMode || (isset($teacher) && !$teacher->isOwner()))
                <div class="card border mb-3 {{ in_array('sectionBranches', $sectionsWithErrors) ? 'border-danger border-2' : '' }}">
                    <div class="card-header bg-body d-flex align-items-center justify-content-between"
                         style="cursor: pointer;"
                         wire:click="toggleSection('sectionBranches')">
                        <div class="d-flex align-items-center gap-2">
                            <div
                                class="rounded-3 d-flex align-items-center justify-content-center bg-info-subtle text-info"
                                style="width: 40px; height: 40px;">
                                <i class="ph-duotone ph-git-branch"></i>
                            </div>
                            <div>
                                <h6 class="mb-0 fw-bold">الفروع</h6>
                                <small class="text-body-secondary">الفروع المسموح لهذا العضو بالعمل فيها</small>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-info">{{ count($branch_ids) }} مختارة</span>
                            <i class="ph-duotone {{ $sectionBranches ? 'ph-caret-up' : 'ph-caret-down' }}"></i>
                        </div>
                    </div>

                    @if($sectionBranches)
                        <div class="card-body">
                            <div class="d-flex gap-2 mb-3">
                                <button type="button"
                                        wire:click="$set('branch_ids', @js($branches->pluck('id')->map(fn($id) => (string) $id)->toArray()))"
                                        class="btn btn-sm btn-outline-success">
                                    <i class="ph-duotone ph-check-square me-1"></i>تحديد الكل
                                </button>
                                <button type="button" wire:click="$set('branch_ids', [])"
                                        class="btn btn-sm btn-outline-danger">
                                    <i class="ph-duotone ph-x-square me-1"></i>إلغاء الكل
                                </button>
                            </div>

                            @error('branch_ids')
                                <div class="alert alert-danger small mb-2">{{ $message }}</div>
                            @enderror

                            @if($branches->count() > 0)
                                <div class="row g-2">
                                    @foreach($branches as $branch)
                                        @php $isSel = in_array((string) $branch->id, $branch_ids); @endphp
                                        <div class="col-md-4 col-lg-3">
                                            <label class="permission-item {{ $isSel ? 'is-selected' : '' }}"
                                                   for="branch-{{ $branch->id }}">
                                                <input type="checkbox"
                                                       wire:model.live="branch_ids"
                                                       value="{{ (string) $branch->id }}"
                                                       id="branch-{{ $branch->id }}"
                                                       class="permission-checkbox"
                                                    {{ $isSel ? 'checked' : '' }}>
                                                <div class="permission-content">
                                                    <span class="permission-icon">
                                                        <i class="ph-duotone ph-git-branch"></i>
                                                    </span>
                                                    <span class="permission-label">{{ $branch->name }}</span>
                                                    <span class="permission-check">
                                                        <i class="ph-duotone ph-check"></i>
                                                    </span>
                                                </div>
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="alert alert-info small mb-0">
                                    <i class="ph-duotone ph-info me-1"></i>
                                    لا توجد فروع متاحة لك.
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            @endif


            {{-- ✅ Payroll Settings --}}
            @if(!$editMode || (isset($teacher) && !$teacher->isOwner()))
                <div class="card border mb-3 {{ in_array('sectionPayroll', $sectionsWithErrors) ? 'border-danger border-2' : '' }}">
                    <div class="card-header bg-body d-flex align-items-center justify-content-between"
                         style="cursor: pointer;"
                         wire:click="toggleSection('sectionPayroll')">
                        <div class="d-flex align-items-center gap-2">
                            <div class="rounded-3 d-flex align-items-center justify-content-center bg-secondary-subtle text-secondary"
                                 style="width: 40px; height: 40px;">
                                <i class="ph-duotone ph-currency-dollar"></i>
                            </div>
                            <div>
                                <h6 class="mb-0 fw-bold">إعدادات الأجر</h6>
                                <small class="text-body-secondary">نوع الأجر والراتب والخصومات</small>
                            </div>
                        </div>
                        <i class="ph-duotone {{ $sectionPayroll ? 'ph-caret-up' : 'ph-caret-down' }}"></i>
                    </div>

                    @if($sectionPayroll)
                        <div class="card-body">
                            {{-- General Payroll --}}
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label">نوع الأجر <span class="text-danger">*</span></label>
                                    <select wire:model.live="payment_type"
                                            class="form-select @error('payment_type') is-invalid @enderror">
                                        @foreach(\App\Enums\Teacher\TeacherPaymentTypeEnum::options() as $value => $label)
                                            <option value="{{ $value }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    @error('payment_type')
                                    <div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">يوم الصرف في الشهر</label>
                                    <input type="number" min="1" max="31" wire:model="pay_day"
                                           class="form-control @error('pay_day') is-invalid @enderror">
                                    @error('pay_day')
                                    <div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                @if(in_array($payment_type, ['monthly', 'monthly_plus_per_session']))
                                    <div class="col-md-6">
                                        <label class="form-label">الراتب الشهري</label>
                                        <div class="input-group">
                                            <input type="number" step="0.01" min="0" wire:model="monthly_salary"
                                                   class="form-control @error('monthly_salary') is-invalid @enderror">
                                            <span class="input-group-text">ج.م</span>
                                        </div>
                                        @error('monthly_salary')
                                        <div class="text-danger small mt-1">{{ $message }}</div>@enderror
                                    </div>
                                @endif

                                @if(in_array($payment_type, ['per_session', 'monthly_plus_per_session']))
                                    <div class="col-md-6">
                                        <label class="form-label">سعر الحصة</label>
                                        <div class="input-group">
                                            <input type="number" step="0.01" min="0" wire:model="per_session_rate"
                                                   class="form-control @error('per_session_rate') is-invalid @enderror">
                                            <span class="input-group-text">ج.م</span>
                                        </div>
                                        @error('per_session_rate')
                                        <div class="text-danger small mt-1">{{ $message }}</div>@enderror
                                    </div>
                                @endif

                                @if($payment_type === 'hourly')
                                    <div class="col-md-6">
                                        <label class="form-label">سعر الساعة</label>
                                        <div class="input-group">
                                            <input type="number" step="0.01" min="0" wire:model="hourly_rate"
                                                   class="form-control @error('hourly_rate') is-invalid @enderror">
                                            <span class="input-group-text">ج.م</span>
                                        </div>
                                        @error('hourly_rate')
                                        <div class="text-danger small mt-1">{{ $message }}</div>@enderror
                                    </div>
                                @endif

                                <div class="col-md-6">
                                    <label class="form-label">خصم الحصة الملغية</label>
                                    <div class="input-group">
                                        <input type="number" step="0.01" min="0" wire:model="session_deduction_rate"
                                               class="form-control @error('session_deduction_rate') is-invalid @enderror">
                                        <span class="input-group-text">ج.م</span>
                                    </div>
                                    @error('session_deduction_rate')
                                    <div class="text-danger small mt-1">{{ $message }}</div>@enderror
                                </div>
                            </div>

                            {{-- Branch Payroll Settings --}}
                            @if(count($branchPayrollSettings) > 0)
                                <hr>
                                <h6 class="mb-3 fw-bold">
                                    <i class="ph-duotone ph-git-branch me-1"></i>
                                    إعدادات الفروع (اختياري)
                                </h6>
                                <small class="text-body-secondary d-block mb-3">لو العضو بيشتغل في أكتر من فرع بسعر
                                    مختلف</small>

                                @foreach($branchPayrollSettings as $branchId => $data)
                                    <div class="card border mb-3" wire:key="payroll-branch-{{ $branchId }}">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center justify-content-between mb-3">
                                                <h6 class="mb-0 fw-bold">{{ $data['branch_name'] }}</h6>
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox"
                                                           wire:model.live="branchPayrollSettings.{{ $branchId }}.enabled"
                                                           id="payroll-br-{{ $branchId }}">
                                                    <label class="form-check-label small"
                                                           for="payroll-br-{{ $branchId }}">
                                                        تفعيل إعدادات خاصة
                                                    </label>
                                                </div>
                                            </div>

                                            @if($data['enabled'])
                                                <div class="row g-3">
                                                    <div class="col-md-6">
                                                        <label class="form-label small">نوع الأجر</label>
                                                        <select
                                                            wire:model="branchPayrollSettings.{{ $branchId }}.payment_type"
                                                            class="form-select form-select-sm">
                                                            @foreach(\App\Enums\Teacher\TeacherPaymentTypeEnum::options() as $value => $label)
                                                                <option value="{{ $value }}">{{ $label }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label small">الراتب الشهري</label>
                                                        <input type="number" step="0.01"
                                                               wire:model="branchPayrollSettings.{{ $branchId }}.monthly_salary"
                                                               class="form-control form-control-sm">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label small">سعر الحصة</label>
                                                        <input type="number" step="0.01"
                                                               wire:model="branchPayrollSettings.{{ $branchId }}.per_session_rate"
                                                               class="form-control form-control-sm">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label small">سعر الساعة</label>
                                                        <input type="number" step="0.01"
                                                               wire:model="branchPayrollSettings.{{ $branchId }}.hourly_rate"
                                                               class="form-control form-control-sm">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label small">خصم الحصة الملغية</label>
                                                        <input type="number" step="0.01"
                                                               wire:model="branchPayrollSettings.{{ $branchId }}.session_deduction_rate"
                                                               class="form-control form-control-sm">
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    @endif
                </div>
            @endif


            <div class="card border mb-3 {{ in_array('sectionPermissions', $sectionsWithErrors) ? 'border-danger border-2' : '' }}">
                <div class="card-header bg-body d-flex align-items-center justify-content-between"
                     style="cursor: pointer;"
                     wire:click="toggleSection('sectionPermissions')">
                    <div class="d-flex align-items-center gap-2">
                        <div
                            class="rounded-3 d-flex align-items-center justify-content-center bg-warning-subtle text-warning"
                            style="width: 40px; height: 40px;">
                            <i class="ph-duotone ph-shield-check"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 fw-bold">الصلاحيات</h6>
                            <small class="text-body-secondary">حدد الصلاحيات المسموح لهذا العضو بالوصول إليها</small>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-warning">{{ $this->selectedCount }} مختارة</span>
                        <i class="ph-duotone {{ $sectionPermissions ? 'ph-caret-up' : 'ph-caret-down' }}"></i>
                    </div>
                </div>

                @if($sectionPermissions)
                    <div class="card-body">
                        <div class="d-flex gap-2 mb-3">
                            <button type="button" wire:click="selectAll" class="btn btn-sm btn-outline-success">
                                <i class="ph-duotone ph-check-square me-1"></i>تحديد الكل
                            </button>
                            <button type="button" wire:click="deselectAll" class="btn btn-sm btn-outline-danger">
                                <i class="ph-duotone ph-x-square me-1"></i>إلغاء الكل
                            </button>
                        </div>

                        @foreach($groupedPermissions as $groupName => $groupPermissions)
                            <div class="mb-3">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge bg-primary-subtle text-primary-emphasis px-3 py-2">
                                            {{ $groupName }}
                                        </span>
                                        <small class="text-body-secondary">{{ count($groupPermissions) }} صلاحية</small>
                                    </div>
                                    <button type="button" wire:click="toggleGroup('{{ $groupName }}')"
                                            class="btn btn-sm btn-link text-decoration-none p-0">
                                        تحديد الكل / إلغاء الكل
                                    </button>
                                </div>
                                <div class="row g-2">
                                    @foreach($groupPermissions as $permission)
                                        @php $isPerm = ($permissions[$permission->value] ?? false); @endphp
                                        <div class="col-md-4 col-lg-3">
                                            <label class="permission-item {{ $isPerm ? 'is-selected' : '' }}"
                                                   for="perm-{{ $permission->value }}">
                                                <input type="checkbox"
                                                       wire:click="togglePermission('{{ $permission->value }}')"
                                                       id="perm-{{ $permission->value }}"
                                                       class="permission-checkbox"
                                                    {{ $isPerm ? 'checked' : '' }}>
                                                <div class="permission-content">
                                                    <span class="permission-icon">
                                                        <i class="ph-duotone {{ $permission->icon() }}"></i>
                                                    </span>
                                                    <span class="permission-label">{{ $permission->label() }}</span>
                                                    <span class="permission-check">
                                                        <i class="ph-duotone ph-check"></i>
                                                    </span>
                                                </div>
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Form Actions --}}
            <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                @if($editMode)
                    <button type="button" class="btn btn-primary" wire:click="update" wire:loading.attr="disabled"
                            wire:target="update">
                        <span wire:loading.remove wire:target="update">
                            <i class="ph-duotone ph-floppy-disk me-1"></i>حفظ التعديلات
                        </span>
                        <span wire:loading wire:target="update">
                            <span class="spinner-border spinner-border-sm me-1"></span>جاري الحفظ...
                        </span>
                    </button>
                @else
                    <button type="button" class="btn btn-primary" wire:click="save(false)" wire:loading.attr="disabled"
                            wire:target="save(false)">
                        <span wire:loading.remove wire:target="save(false)">
                            <i class="ph-duotone ph-floppy-disk me-1"></i>حفظ
                        </span>
                        <span wire:loading wire:target="save(false)">
                            <span class="spinner-border spinner-border-sm me-1"></span>جاري الحفظ...
                        </span>
                    </button>
                    <button type="button" class="btn btn-success" wire:click="save(true)" wire:loading.attr="disabled"
                            wire:target="save(true)">
                        <span wire:loading.remove wire:target="save(true)">
                            <i class="ph-duotone ph-plus-circle me-1"></i>حفظ وإنشاء جديد
                        </span>
                        <span wire:loading wire:target="save(true)">
                            <span class="spinner-border spinner-border-sm me-1"></span>جاري الحفظ...
                        </span>
                    </button>
                @endif

                <a href="{{ route('teacher.staff.index') }}" wire:navigate class="btn btn-light border">
                    <i class="ph-duotone ph-x-circle me-1"></i>إلغاء
                </a>
            </div>
        </div>
    </div>
</div>
