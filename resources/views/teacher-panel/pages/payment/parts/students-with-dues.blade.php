<div class="mt-4">


    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-body d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h6 class="mb-0 fw-bold">
                <i class="ph-duotone ph-warning-circle text-danger me-1"></i>
                مدفعوش حاجة
                <span class="badge bg-danger-subtle text-danger-emphasis ms-2">
                    {{ $unpaidStudents->total() }}
                </span>
            </h6>
            <input type="text" wire:model.live.debounce.300ms="searchUnpaid"
                   class="form-control form-control-sm" style="max-width: 200px;"
                   placeholder="بحث...">
        </div>

        <div class="card-body p-0">
            @if($unpaidStudents->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                        <tr>
                            <th class="ps-4" style="width: 50px;">#</th>
                            <th>الطالب</th>
                            <th>الكود</th>
                            <th>الفرع</th>
                            <th class="text-center" style="width: 120px;">إجراء</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($unpaidStudents as $student)
                            <tr wire:key="unpaid-{{ $student->id }}">
                                <td class="ps-4 text-body-secondary small">
                                    {{ $unpaidStudents->firstItem() + $loop->index }}
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-circle bg-danger-subtle text-danger d-flex align-items-center justify-content-center fw-bold flex-shrink-0"
                                             style="width: 34px; height: 34px; font-size: 14px;">
                                            {{ mb_substr($student->name, 0, 1) }}
                                        </div>
                                        <span class="fw-semibold">{{ $student->name }}</span>
                                    </div>
                                </td>
                                <td class="text-body-secondary small">{{ $student->code }}</td>
                                <td class="text-body-secondary small">{{ $student->branch?->name ?? '—' }}</td>
                                <td class="text-center">
                                    <button type="button"
                                            wire:click="selectStudent({{ $student->id }})"
                                            class="btn btn-danger btn-sm">
                                        دفع
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>

                @if($unpaidStudents->hasPages())
                    <div class="card-footer bg-body">{{ $unpaidStudents->links() }}</div>
                @endif
            @else
                <div class="text-center text-body-secondary py-5">
                    <i class="ph-duotone ph-check-circle text-success fs-1 d-block mb-2"></i>
                    <h6 class="fw-bold">مفيش طلاب مدفعوش حاجة</h6>
                    <p class="small mb-0">كل الطلاب دفعوا حاجة على الأقل الشهر دا.</p>
                </div>
            @endif
        </div>
    </div>


    <div class="card border-0 shadow-sm">
        <div class="card-header bg-body d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h6 class="mb-0 fw-bold">
                <i class="ph-duotone ph-clock text-warning me-1"></i>
                عليهم باقي
                <span class="badge bg-warning-subtle text-warning-emphasis ms-2">
                    {{ $partialStudents->total() }}
                </span>
            </h6>
            <input type="text" wire:model.live.debounce.300ms="searchPartial"
                   class="form-control form-control-sm" style="max-width: 200px;"
                   placeholder="بحث...">
        </div>

        <div class="card-body p-0">
            @if($partialStudents->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                        <tr>
                            <th class="ps-4" style="width: 50px;">#</th>
                            <th>الطالب</th>
                            <th>الكود</th>
                            <th>الفرع</th>
                            <th class="text-center" style="width: 120px;">إجراء</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($partialStudents as $student)
                            <tr wire:key="partial-{{ $student->id }}">
                                <td class="ps-4 text-body-secondary small">
                                    {{ $partialStudents->firstItem() + $loop->index }}
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-circle bg-warning-subtle text-warning d-flex align-items-center justify-content-center fw-bold flex-shrink-0"
                                             style="width: 34px; height: 34px; font-size: 14px;">
                                            {{ mb_substr($student->name, 0, 1) }}
                                        </div>
                                        <span class="fw-semibold">{{ $student->name }}</span>
                                    </div>
                                </td>
                                <td class="text-body-secondary small">{{ $student->code }}</td>
                                <td class="text-body-secondary small">{{ $student->branch?->name ?? '—' }}</td>
                                <td class="text-center">
                                    <button type="button"
                                            wire:click="selectStudent({{ $student->id }})"
                                            class="btn btn-warning btn-sm">
                                        دفع
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>

                @if($partialStudents->hasPages())
                    <div class="card-footer bg-body">{{ $partialStudents->links() }}</div>
                @endif
            @else
                <div class="text-center text-body-secondary py-5">
                    <i class="ph-duotone ph-check-circle text-success fs-1 d-block mb-2"></i>
                    <h6 class="fw-bold">مفيش طلاب عليهم باقي</h6>
                    <p class="small mb-0">كل الطلاب اللي دفعوا، دفعوا كامل الشهر دا.</p>
                </div>
            @endif
        </div>
    </div>

</div>
