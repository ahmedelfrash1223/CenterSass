<div>
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <h5 class="mb-4">إضافة حجز جديد</h5>

            {{-- =============================================
                 Section: البيانات الأساسية
                 ============================================= --}}
            <div class="card border mb-3">
                <div class="card-header bg-body d-flex align-items-center justify-content-between"
                     style="cursor: pointer;"
                     wire:click="toggleSection('sectionBasic')">
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-3 d-flex align-items-center justify-content-center bg-primary-subtle text-primary"
                             style="width: 40px; height: 40px;">
                            <i class="ph-duotone ph-user"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 fw-bold">البيانات الأساسية</h6>
                            <small class="text-body-secondary">الاسم، الهاتف، المرحلة، الفرع</small>
                        </div>
                    </div>
                    <i class="ph-duotone {{ $sectionBasic ? 'ph-caret-up' : 'ph-caret-down' }}"></i>
                </div>

                @if($sectionBasic)
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">الاسم <span class="text-danger">*</span></label>
                                <input type="text" wire:model="name"
                                       class="form-control @error('name') is-invalid @enderror"
                                       placeholder="أدخل الاسم">
                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">رقم الهاتف</label>
                                <input type="text" wire:model="phone"
                                       class="form-control @error('phone') is-invalid @enderror"
                                       placeholder="01xxxxxxxxx">
                                @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">المرحلة الدراسية <span class="text-danger">*</span></label>
                                <select wire:model.live="grade"
                                        class="form-select @error('grade') is-invalid @enderror">
                                    <option value="">اختر المرحلة</option>
                                    @foreach($grades as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('grade')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">الفرع <span class="text-danger">*</span></label>
                                <select wire:model.live="branch_id"
                                        class="form-select @error('branch_id') is-invalid @enderror">
                                    <option value="">اختر الفرع</option>
                                    @foreach($branches as $branch)
                                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                    @endforeach
                                </select>
                                @error('branch_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            {{-- =============================================
                 Section: بيانات إضافية
                 ============================================= --}}
            <div class="card border mb-3">
                <div class="card-header bg-body d-flex align-items-center justify-content-between"
                     style="cursor: pointer;"
                     wire:click="toggleSection('sectionProfile')">
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-3 d-flex align-items-center justify-content-center bg-success-subtle text-success"
                             style="width: 40px; height: 40px;">
                            <i class="ph-duotone ph-identification-card"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 fw-bold">بيانات إضافية</h6>
                            <small class="text-body-secondary">ولي الأمر، المدرسة، العنوان</small>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        @if($guardian_name || $school_name)
                            <span class="badge bg-success text-white">
                                <i class="ph-duotone ph-check me-1"></i>مكتمل جزئيًا
                            </span>
                        @endif
                        <i class="ph-duotone {{ $sectionProfile ? 'ph-caret-up' : 'ph-caret-down' }}"></i>
                    </div>
                </div>

                @if($sectionProfile)
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">اسم ولي الأمر</label>
                                <input type="text" wire:model="guardian_name" class="form-control"
                                       placeholder="أدخل اسم ولي الأمر">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">رقم ولي الأمر</label>
                                <input type="text" wire:model="guardian_phone" class="form-control"
                                       placeholder="01xxxxxxxxx">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">صلة القرابة</label>
                                <select wire:model="guardian_relation" class="form-select">
                                    <option value="">اختر صلة القرابة</option>
                                    <option value="father">الأب</option>
                                    <option value="mother">الأم</option>
                                    <option value="brother">الأخ</option>
                                    <option value="sister">الأخت</option>
                                    <option value="uncle">العم / الخال</option>
                                    <option value="aunt">العمة / الخالة</option>
                                    <option value="grandfather">الجد</option>
                                    <option value="grandmother">الجدة</option>
                                    <option value="other">أخرى</option>
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">اسم المدرسة</label>
                                <input type="text" wire:model="school_name" class="form-control"
                                       placeholder="أدخل اسم المدرسة">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">المدينة</label>
                                <input type="text" wire:model="city" class="form-control"
                                       placeholder="أدخل المدينة">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">العنوان</label>
                                <input type="text" wire:model="address" class="form-control"
                                       placeholder="أدخل العنوان الكامل">
                            </div>

                            <div class="col-12 mb-3">
                                <label class="form-label">ملاحظات</label>
                                <textarea rows="3" wire:model="notes" class="form-control"
                                          placeholder="ملاحظات إضافية"></textarea>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            {{-- =============================================
                 Section: المجموعات
                 ============================================= --}}
            <div class="card border mb-3">
                <div class="card-header bg-body d-flex align-items-center justify-content-between"
                     style="cursor: pointer;"
                     wire:click="toggleSection('sectionGroups')">
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-3 d-flex align-items-center justify-content-center bg-warning-subtle text-warning"
                             style="width: 40px; height: 40px;">
                            <i class="ph-duotone ph-users-three"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 fw-bold">المجموعات</h6>
                            <small class="text-body-secondary">اختيار المجموعات المطلوبة للحجز</small>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-warning text-dark">{{ count($group_ids) }} مختارة</span>
                        <i class="ph-duotone {{ $sectionGroups ? 'ph-caret-up' : 'ph-caret-down' }}"></i>
                    </div>
                </div>

                @if($sectionGroups)
                    <div class="card-body">
                        @if(!$grade || !$branch_id)
                            <div class="alert alert-info d-flex align-items-center gap-2 mb-0">
                                <i class="ph-duotone ph-info fs-5"></i>
                                <span>
                                    لازم تختار
                                    <strong>المرحلة الدراسية</strong>
                                    و
                                    <strong>الفرع</strong>
                                    الأول عشان تظهر المجموعات المتاحة.
                                </span>
                            </div>
                        @else
                            <div class="d-flex justify-content-end gap-2 mb-3">
                                <button type="button"
                                        wire:click="$set('group_ids', @js($groups->where('is_full', false)->pluck('id')->map(fn($id) => (string)$id)->toArray()))"
                                        class="btn btn-sm btn-outline-success">
                                    <i class="ph-duotone ph-check-square me-1"></i>تحديد الكل
                                </button>
                                <button type="button" wire:click="$set('group_ids', [])"
                                        class="btn btn-sm btn-outline-danger">
                                    <i class="ph-duotone ph-x-square me-1"></i>إلغاء الكل
                                </button>
                            </div>

                            @error('group_ids')
                            <div class="alert alert-danger d-flex align-items-center gap-2 mb-3">
                                <i class="ph-duotone ph-warning-circle fs-5"></i>
                                <span>{{ $message }}</span>
                            </div>
                            @enderror

                            @if($groups->count() > 0)
                                <div class="row g-2">
                                    @foreach($groups as $group)
                                        @php
                                            $isSelected = in_array((string)$group->id, $group_ids);
                                        @endphp
                                        <div class="col-md-4 col-lg-3">
                                            <label class="permission-item {{ $isSelected ? 'is-selected' : '' }}"
                                                   for="grp-{{ $group->id }}">
                                                <input type="checkbox"
                                                       wire:model.live="group_ids"
                                                       value="{{ (string) $group->id }}"
                                                       id="grp-{{ $group->id }}"
                                                       class="permission-checkbox"
                                                    {{ $isSelected ? 'checked' : '' }}>
                                                <div class="permission-content">
                                                    <span class="permission-icon">
                                                        <i class="ph-duotone ph-users-three"></i>
                                                    </span>
                                                    <span class="permission-label">
                                                        {{ $group->name }}
                                                    </span>
                                                    <span class="permission-check">
                                                        <i class="ph-duotone ph-check"></i>
                                                    </span>
                                                </div>
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="alert alert-warning d-flex align-items-center gap-2 mb-0">
                                    <i class="ph-duotone ph-warning fs-5"></i>
                                    <span>لا توجد مجموعات متاحة للفرع والمرحلة المختارين.</span>
                                </div>
                            @endif
                        @endif
                    </div>
                @endif
            </div>

            {{-- Form Actions --}}
            <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                <button type="button" class="btn btn-primary" wire:click="save(false)"
                        wire:loading.attr="disabled" wire:target="save(false)">
                    <span wire:loading.remove wire:target="save(false)">
                        <i class="ph-duotone ph-floppy-disk me-1"></i>حفظ الحجز
                    </span>
                    <span wire:loading wire:target="save(false)">
                        <span class="spinner-border spinner-border-sm me-1"></span>جاري الحفظ...
                    </span>
                </button>
                <button type="button" class="btn btn-success" wire:click="save(true)"
                        wire:loading.attr="disabled" wire:target="save(true)">
                    <span wire:loading.remove wire:target="save(true)">
                        <i class="ph-duotone ph-plus-circle me-1"></i>حفظ وإنشاء جديد
                    </span>
                    <span wire:loading wire:target="save(true)">
                        <span class="spinner-border spinner-border-sm me-1"></span>جاري الحفظ...
                    </span>
                </button>

                <a href="{{ route('teacher.bookings.index') }}" wire:navigate class="btn btn-light border">
                    <i class="ph-duotone ph-x-circle me-1"></i>إلغاء
                </a>
            </div>
        </div>
    </div>
</div>
