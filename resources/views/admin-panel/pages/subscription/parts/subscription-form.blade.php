<div>
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <h5 class="mb-4">{{ $editMode ?? false ? 'تعديل الاشتراك' : 'إضافة اشتراك جديد' }}</h5>
            <!-- Subscription Details -->
            <div class="row">
                <div class="col-md-6 mb-3"><label class="form-label">المدرس <span
                            class="text-danger">*</span></label><select
                        class="form-select @error('teacher_id') is-invalid @enderror"
                        wire:model.live="teacher_id"
                    >
                        <option value="">اختر المدرس</option>
                        @foreach ($teachers as $teacher)
                            <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                        @endforeach
                    </select>
                    @error('teacher_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror @if ($existingSubscriptionId ?? null)
                            <div class="mt-2"><a
                                    href="{{ route('admin.subscriptions.show', $existingSubscriptionId) }}"
                                    wire:navigate
                                    class="text-danger small"
                                ><i class="ph-duotone ph-arrow-square-out me-1"></i>عرض الاشتراك الحالي وتجديده</a>
                            </div>
                        @endif
                    </div>
                    <div class="col-md-6 mb-3"><label class="form-label">الباقة <span
                                class="text-danger">*</span></label><select
                            class="form-select @error('package_id') is-invalid @enderror"
                            wire:model.live="package_id"
                        >
                            <option value="">اختر الباقة</option>
                            @foreach ($packages as $package)
                                <option value="{{ $package->id }}">{{ $package->title }} -
                                    {{ number_format($package->price, 2) }}</option>
                            @endforeach
                        </select>
                        @error('package_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3"><label class="form-label">تاريخ البداية <span
                                class="text-danger">*</span></label><input
                            type="datetime-local"
                            class="form-control @error('starts_at') is-invalid @enderror"
                            wire:model.live="starts_at"
                        >
                        @error('starts_at')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3"><label class="form-label">تاريخ النهاية <span
                                class="text-danger">*</span></label><input
                            type="datetime-local"
                            class="form-control @error('ends_at') is-invalid @enderror"
                            wire:model="ends_at"
                        >
                        @error('ends_at')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-3"><label class="form-label">السعر</label><input
                            type="number"
                            step="0.01"
                            class="form-control"
                            wire:model="price"
                            readonly
                        ></div>
                    <div class="col-md-4 mb-3"><label class="form-label">نوع الخصم</label><select
                            class="form-select"
                            wire:model.live="discount_type"
                        >
                            @foreach ($discountTypes as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 mb-3"><label class="form-label">قيمة الخصم</label><input
                            type="number"
                            step="0.01"
                            min="0"
                            class="form-control"
                            wire:model.live="discount_value"
                        ></div>
                    <div class="col-md-4 mb-3"><label class="form-label">بعد الخصم</label><input
                            type="number"
                            step="0.01"
                            class="form-control"
                            wire:model="price_after_discount"
                            readonly
                        ></div>
                    <div class="col-md-4 mb-3"><label class="form-label">الحالة</label><select
                            class="form-select"
                            wire:model="status"
                        >
                            @foreach ($statuses as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 mb-3"><label class="form-label">حالة الدفع</label><select
                            class="form-select"
                            wire:model.live="is_paid"
                        >
                            <option value="0">غير مدفوع</option>
                            <option value="1">مدفوع</option>
                        </select></div>
                    <div class="col-md-6 mb-3"><label class="form-label">تاريخ الدفع</label><input
                            type="datetime-local"
                            class="form-control"
                            wire:model="paid_at"
                            @disabled(!$is_paid)
                        ></div>
                    <div class="col-md-6 mb-3"><label class="form-label">الاشتراك الأساسي</label><select
                            class="form-select"
                            wire:model="parent_id"
                        >
                            <option value="">اشتراك جديد</option>
                            @foreach ($parents as $parent)
                                <option value="{{ $parent->id }}">#{{ $parent->id }} - {{ $parent->teacher?->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <!-- Package-derived Features -->
                @if (count($features))
                    <hr>
                    <h6 class="mb-3">خصائص الاشتراك</h6>
                    <div class="row">
                        @foreach ($features as $index => $feature)
                            <div class="col-md-6 mb-3"><label
                                    class="form-label">{{ \App\Enums\Admin\PackageFeatureKeyEnum::tryFrom((int) $feature['key'])?->label() ?? 'ميزة غير معروفة' }}</label><input
                                    type="number"
                                    min="0"
                                    class="form-control @error('features.' . $index . '.value') is-invalid @enderror"
                                    wire:model="features.{{ $index }}.value"
                                >
                                @error('features.' . $index . '.value')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        @endforeach
                    </div>
                @endif
                <!-- Form Actions -->
                <div class="d-flex justify-content-end gap-2 mt-4">
                    @if ($editMode ?? false)
                        <button
                            type="button"
                            class="btn btn-primary"
                            wire:click="update"
                            wire:loading.attr="disabled"
                            wire:target="update"
                        ><span
                                wire:loading.remove
                                wire:target="update"
                            ><i class="ph-duotone ph-floppy-disk me-1"></i>حفظ التعديلات</span><span
                                wire:loading
                                wire:target="update"
                            ><span class="spinner-border spinner-border-sm me-1"></span>جاري
                                الحفظ...</span></button>
                    @else
                        <button
                            type="button"
                            class="btn btn-primary"
                            wire:click="save(false)"
                            wire:loading.attr="disabled"
                            wire:target="save(false)"
                        ><span
                                wire:loading.remove
                                wire:target="save(false)"
                            ><i class="ph-duotone ph-floppy-disk me-1"></i>حفظ</span><span
                                wire:loading
                                wire:target="save(false)"
                            ><span class="spinner-border spinner-border-sm me-1"></span>جاري الحفظ...</span></button>
                        <button
                            type="button"
                            class="btn btn-success"
                            wire:click="save(true)"
                            wire:loading.attr="disabled"
                            wire:target="save(true)"
                        ><span
                                wire:loading.remove
                                wire:target="save(true)"
                            ><i class="ph-duotone ph-plus-circle me-1"></i>حفظ وإنشاء
                                جديد</span><span
                                wire:loading
                                wire:target="save(true)"
                            ><span class="spinner-border spinner-border-sm me-1"></span>جاري الحفظ...</span></button>
                    @endif
                    <a
                        href="{{ route('admin.subscriptions.index') }}"
                        wire:navigate
                        class="btn btn-light border"
                    ><i class="ph-duotone ph-x-circle me-1"></i>إلغاء</a>
                </div>
            </div>
        </div>
    </div>
