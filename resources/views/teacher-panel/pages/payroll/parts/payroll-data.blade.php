<div>
    @livewire('teacher-panel.parts.flash-message', [
        'successKey' => 'payroll-success',
        'errorKey' => 'payroll-error',
    ])

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-2">
                <div class="col-md-2">
                    <input type="month" wire:model.live="monthFilter" class="form-control">
                </div>
                <div class="col-md-2">
                    <select wire:model.live="statusFilter" class="form-select">
                        <option value="">كل الحالات</option>
                        @foreach($statuses as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select wire:model.live="branchFilter" class="form-select">
                        <option value="">كل الفروع</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select wire:model.live="teacherFilter" class="form-select">
                        <option value="">كل المدرسين</option>
                        @foreach($teachers as $teacher)
                            <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="text" wire:model.live.debounce.400ms="search" class="form-control"
                        placeholder="ابحث...">
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-light border w-100" wire:click="clearFilters">
                        <i class="ph-duotone ph-funnel-simple me-1"></i>تفريغ
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">#</th>
                        <th>الرقم</th>
                        <th>المدرس</th>
                        <th>الفرع</th>
                        <th>الفترة</th>
                        <th class="text-center">الحصص</th>
                        <th class="text-center">الساعات</th>
                        <th class="text-center">الصافي</th>
                        <th class="text-center">المدفوع</th>
                        <th class="text-center">المتبقي</th>
                        <th class="text-center">الحالة</th>
                        <th class="text-center">إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payrolls as $payroll)
                        <tr wire:key="pr-{{ $payroll->id }}">
                            <td class="ps-4 text-body-secondary fw-semibold">
                                {{ $payrolls->firstItem() + $loop->index }}
                            </td>
                            <td><span class="fw-semibold">{{ $payroll->reference }}</span></td>
                            <td><span class="fw-semibold">{{ $payroll->teacher?->name }}</span></td>
                            <td>
                                @if($payroll->branch)
                                    <small>{{ $payroll->branch->name }}</small>
                                @else
                                    <span class="text-body-secondary">—</span>
                                @endif
                            </td>
                            <td><small>{{ $payroll->period_label }}</small></td>
                            <td class="text-center">
                                <span class="badge bg-primary text-white">
                                    {{ $payroll->payable_sessions }} / {{ $payroll->total_sessions }}
                                </span>
                            </td>
                            <td class="text-center">
                                <small>{{ number_format((float) $payroll->total_hours, 1) }} س</small>
                            </td>
                            <td class="text-center fw-bold text-success">
                                {{ number_format((float) $payroll->net_amount, 2) }}
                            </td>
                            <td class="text-center text-info">
                                {{ number_format((float) $payroll->paid_amount, 2) }}
                            </td>
                            <td class="text-center">
                                @php $remaining = $payroll->remainingAmount(); @endphp
                                @if($remaining > 0)
                                    <span class="text-danger fw-bold">{{ number_format($remaining, 2) }}</span>
                                @else
                                    <i class="ph-duotone ph-check-circle text-success fs-5"></i>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge bg-{{ $payroll->status->color() }} {{ $payroll->status->textColor() }}">
                                    {{ $payroll->status->label() }}
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-1">
                                    <a href="{{ route('teacher.payrolls.show', $payroll->id) }}" wire:navigate
                                        class="btn btn-sm btn-outline-primary">
                                        <i class="ph-duotone ph-eye"></i>
                                    </a>
                                    @if($payroll->teacher)
                                        <a href="{{ route('teacher.payrolls.settings', $payroll->teacher_id) }}" wire:navigate
                                            class="btn btn-sm btn-outline-secondary">
                                            <i class="ph-duotone ph-gear"></i>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="12" class="text-center py-5">
                                <i class="ph-duotone ph-user-circle-gear fs-1 d-block mb-2 text-body-secondary"></i>
                                <h6 class="text-body-secondary mb-1">لا توجد كشوف حسابات</h6>
                                <small class="text-body-secondary">
                                    اضغط <a href="{{ route('teacher.payrolls.generate') }}" wire:navigate>توليد كشوف
                                        جديدة</a>.
                                </small>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($payrolls->hasPages())
            <div class="card-footer bg-transparent border-top">{{ $payrolls->links() }}</div>
        @endif
    </div>
</div>
