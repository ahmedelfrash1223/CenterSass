<div>
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div>
                    <h5 class="mb-1"><i class="ph-duotone ph-chalkboard-teacher me-2"></i>{{ $teacher->name }}</h5><span
                        class="text-muted"
                    >{{ $teacher->code }}</span>
                </div>
                <div class="d-flex gap-2"><a
                        href="{{ route('admin.teacher.edit', $teacher) }}"
                        wire:navigate
                        class="btn btn-primary"
                    ><i class="ph-duotone ph-pencil-simple me-1"></i>تعديل</a><a
                        href="{{ route('admin.teacher.index') }}"
                        wire:navigate
                        class="btn btn-light border"
                    ><i class="ph-duotone ph-arrow-right me-1"></i>رجوع</a></div>
            </div>
        </div>
    </div>
    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h6 class="mb-4">بيانات المدرس</h6>
                    <div class="row g-3">
                        <div class="col-sm-6"><small class="text-muted d-block">الاسم</small>{{ $teacher->name }}</div>
                        <div class="col-sm-6"><small class="text-muted d-block">اسم
                                المستخدم</small>{{ $teacher->username }}</div>
                        <div class="col-sm-6"><small class="text-muted d-block">البريد الإلكتروني</small><a
                                href="mailto:{{ $teacher->email }}"
                            >{{ $teacher->email }}</a></div>
                        <div class="col-sm-6"><small
                                class="text-muted d-block">الهاتف</small>{{ $teacher->phone ?? 'غير محدد' }}</div>
                        <div class="col-sm-6"><small class="text-muted d-block">الكود</small>{{ $teacher->code }}</div>
                        <div class="col-sm-6"><small class="text-muted d-block">الحالة</small>
                            @if ($teacher->is_active)
                            <span class="badge bg-success bg-opacity-10 text-success">نشط</span>@else<span
                                    class="badge bg-danger bg-opacity-10 text-danger"
                                >غير نشط</span>
                            @endif
                        </div>
                        <div class="col-sm-6"><small class="text-muted d-block">تاريخ
                                الإنشاء</small>{{ $teacher->created_at?->format('Y-m-d H:i') }}</div>
                        <div class="col-sm-6"><small class="text-muted d-block">آخر
                                تحديث</small>{{ $teacher->updated_at?->format('Y-m-d H:i') }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h6 class="mb-4">الطلاب ({{ $teacher->students->count() }})</h6>
                    <div class="table-responsive">
                        <table class="table table-sm align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>الاسم</th>
                                    <th>البريد</th>
                                    <th>الحالة</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($teacher->students as $student)
                                    <tr>
                                        <td>{{ $student->name }}</td>
                                        <td>{{ $student->email }}</td>
                                        <td>{{ $student->is_active ? 'نشط' : 'غير نشط' }}</td>
                                </tr>@empty<tr>
                                        <td
                                            colspan="3"
                                            class="text-muted"
                                        >لا يوجد طلاب</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="d-flex justify-content-end gap-2 mt-4">
        @if ($confirmingDelete)
            <button
                type="button"
                wire:click="delete"
                wire:loading.attr="disabled"
                wire:target="delete"
                class="btn btn-danger"
            ><span
                    wire:loading.remove
                    wire:target="delete"
                ><i class="ph-duotone ph-trash me-1"></i>تأكيد الحذف</span><span
                    wire:loading
                    wire:target="delete"
                ><span class="spinner-border spinner-border-sm me-1"></span>جاري
                    الحذف...</span></button><button
                type="button"
                wire:click="cancelDelete"
                class="btn btn-light border"
        >إلغاء</button>@else<button
                type="button"
                wire:click="confirmDelete"
                class="btn btn-outline-danger"
            ><i class="ph-duotone ph-trash me-1"></i>حذف</button>
        @endif
    </div>
</div>
