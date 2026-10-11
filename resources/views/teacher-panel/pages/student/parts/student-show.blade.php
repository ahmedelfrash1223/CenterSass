<div>
    {{-- Student Header --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">

                {{-- Left: Avatar + Info --}}
                <div class="d-flex align-items-center gap-3">
                    <div class="avtar avtar-l bg-primary bg-opacity-10 text-primary">
                        <span class="fs-2 fw-bold">{{ mb_substr($student->name, 0, 1) }}</span>
                    </div>
                    <div>
                        <h5 class="mb-1 d-flex align-items-center gap-2 flex-wrap">
                            {{ $student->name }}
                            @if($student->is_active)
                                <span class="badge bg-success bg-opacity-10 text-success">
                                    <i class="ph-duotone ph-check-circle me-1"></i>نشط
                                </span>
                            @else
                                <span class="badge bg-danger bg-opacity-10 text-danger">
                                    <i class="ph-duotone ph-x-circle me-1"></i>غير نشط
                                </span>
                            @endif
                        </h5>
                        <span class="text-muted small">
                            <i class="ph-duotone ph-identification-card me-1"></i>{{ $student->code }}
                        </span>
                    </div>
                </div>

                {{-- Right: Actions --}}
                <div class="d-flex gap-2">
                    @if($canUpdate)
                        <a href="{{ route('teacher.students.edit', $student) }}" wire:navigate class="btn btn-primary">
                            <i class="ph-duotone ph-pencil-simple me-1"></i>تعديل
                        </a>
                    @endif
                    <a href="{{ route('teacher.students.index') }}" wire:navigate class="btn btn-light border">
                        <i class="ph-duotone ph-arrow-right me-1"></i>رجوع
                    </a>
                </div>

            </div>
        </div>
    </div>

    {{-- ID Card Section --}}
    <div class="card border-0 shadow-sm mb-4 id-card-section">
        <div class="card-header bg-transparent border-bottom d-flex justify-content-between align-items-center">
            <h6 class="mb-0 fw-bold">
                <i class="ph-duotone ph-identification-card me-1"></i>
                كود التعريف
            </h6>
        </div>
        <div class="card-body p-4">
            <x-card-code :barcode="$student->barcode_value" :qr="$student->qr_value" :barcodeHeight="65"
                title="الباركود" />
        </div>
    </div>

    <div class="row g-4">
        {{-- Student Details --}}
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-bottom">
                    <h6 class="mb-0 fw-bold">
                        <i class="ph-duotone ph-info me-1"></i>بيانات الطالب
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <small class="text-muted d-block mb-1">الاسم</small>
                            <span class="fw-semibold">{{ $student->name }}</span>
                        </div>
                        <div class="col-sm-6">
                            <small class="text-muted d-block mb-1">الهاتف</small>
                            @if($student->phone)
                                <a href="tel:{{ $student->phone }}" class="text-decoration-none small">
                                    {{ $student->phone }}
                                </a>
                            @else
                                <span class="text-muted">غير محدد</span>
                            @endif
                        </div>
                        <div class="col-sm-6">
                            <small class="text-muted d-block mb-1">المرحلة الدراسية</small>
                            @if($gradeLabel)
                                <span class="badge bg-info bg-opacity-10 text-info">{{ $gradeLabel }}</span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </div>
                        <div class="col-sm-6">
                            <small class="text-muted d-block mb-1">الفرع</small>
                            @if($student->branch)
                                <span class="badge bg-primary bg-opacity-10 text-primary">
                                    <i class="ph-duotone ph-git-branch me-1"></i>{{ $student->branch->name }}
                                </span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </div>
                        <div class="col-sm-6">
                            <small class="text-muted d-block mb-1">أضيف بواسطة</small>
                            @if($student->creator)
                                <span
                                    class="badge bg-{{ $student->creator->role->color() }} bg-opacity-10 text-{{ $student->creator->role->color() }}">
                                    <i class="ph-duotone {{ $student->creator->role->icon() }} me-1"></i>
                                    {{ $student->creator->name }}
                                </span>
                            @else
                                <span class="text-muted small">—</span>
                            @endif
                        </div>
                        <div class="col-sm-6">
                            <small class="text-muted d-block mb-1">تاريخ الإنشاء</small>
                            <span class="small">
                                <i class="ph-duotone ph-calendar me-1 text-muted"></i>
                                {{ $student->created_at?->format('Y-m-d h:i A') }}
                            </span>
                        </div>
                        <div class="col-sm-6">
                            <small class="text-muted d-block mb-1">آخر تحديث</small>
                            <span class="small">
                                <i class="ph-duotone ph-clock me-1 text-muted"></i>
                                {{ $student->updated_at?->format('Y-m-d h:i A') }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Statistics --}}
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-bottom">
                    <h6 class="mb-0 fw-bold">
                        <i class="ph-duotone ph-chart-bar me-1"></i>الإحصائيات
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="stat-card stat-primary">
                                <div class="stat-icon"><i class="ph-duotone ph-users-three"></i></div>
                                <div>
                                    <span class="stat-num">{{ $student->groups->count() }}</span>
                                    <span class="stat-label">المجموعات</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="stat-card stat-success">
                                <div class="stat-icon"><i class="ph-duotone ph-check-circle"></i></div>
                                <div>
                                    <span class="stat-num">{{ $student->is_active ? 'نشط' : 'غير نشط' }}</span>
                                    <span class="stat-label">الحالة</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Groups --}}
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-bottom d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold">
                        <i class="ph-duotone ph-users-three me-1"></i>
                        المجموعات
                    </h6>
                    <span class="badge bg-primary bg-opacity-10 text-primary">
                        {{ $student->groups->count() }}
                    </span>
                </div>
                <div class="card-body p-0">
                    @if($student->groups->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th class="ps-4" style="width: 60px;">#</th>
                                        <th>المجموعة</th>
                                        <th>المرحلة</th>
                                        <th>تاريخ الانضمام</th>
                                        <th>الحالة</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($student->groups as $index => $group)
                                        <tr wire:key="group-{{ $group->id }}">
                                            <td class="ps-4">
                                                <span class="text-muted fw-semibold">{{ $index + 1 }}</span>
                                            </td>
                                            <td><span class="fw-semibold">{{ $group->name }}</span></td>
                                            <td>
                                                @if($group->grade)
                                                    <span class="badge bg-info bg-opacity-10 text-info">
                                                        {{ \App\Enums\Teacher\GradeEnum::tryFrom($group->grade)?->label() ?? $group->grade }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="text-muted small">
                                                    <i class="ph-duotone ph-calendar me-1"></i>
                                                    {{ $group->pivot->joined_at ? \Carbon\Carbon::parse($group->pivot->joined_at)->format('Y-m-d') : '—' }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($group->pivot->status === 'active')
                                                    <span class="badge bg-success bg-opacity-10 text-success">نشط</span>
                                                @else
                                                    <span class="badge bg-secondary bg-opacity-10 text-secondary">
                                                        {{ $group->pivot->status }}
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="ph-duotone ph-users-three fs-1 d-block mb-2 text-muted"></i>
                            <p class="text-muted mb-0">الطالب غير مضاف لأي مجموعة حالياً</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
