<div>
    <p class="text-body-secondary mb-4 small">إدارة طلبات الكروت المرسلة من جميع المدرسين</p>

    @livewire('admin-panel.parts.flash-message', [
        'successKey' => 'card-request-success',
        'errorKey' => 'card-request-error',
    ])

    {{-- Filters --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-2">
                <div class="col-md-5">
                    <input type="text" wire:model.live.debounce.400ms="search" class="form-control"
                        placeholder="ابحث برقم الطلب...">
                </div>
                <div class="col-md-3">
                    <select wire:model.live="statusFilter" class="form-select">
                        <option value="">كل الحالات</option>
                        @foreach($statuses as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select wire:model.live="teacherFilter" class="form-select">
                        <option value="">كل المدرسين</option>
                        @foreach($teachers as $teacher)
                            <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn btn-light border w-100" wire:click="clearFilters" title="تفريغ">
                        <i class="ph-duotone ph-funnel-simple"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">#</th>
                        <th>رقم الطلب</th>
                        <th>المدرس</th>
                        <th>الفرع</th>
                        <th>عدد الكروت</th>
                        <th>الإجمالي</th>
                        <th>الحالة</th>
                        <th>التاريخ</th>
                        <th class="text-center">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requests as $request)
                        @php
                            $branches = $request->items
                                ->pluck('branch.name')
                                ->filter()
                                ->unique()
                                ->values();
                        @endphp
                        <tr wire:key="card-request-{{ $request->id }}">
                            <td class="ps-4 text-body-secondary fw-semibold">
                                {{ $requests->firstItem() + $loop->index }}
                            </td>
                            <td><span class="fw-semibold">{{ $request->reference }}</span></td>
                            <td>
                                <div class="fw-semibold">{{ $request->teacher?->name ?? 'غير معروف' }}</div>
                                <small class="text-body-secondary">بواسطة {{ $request->creator?->name ?? '—' }}</small>
                            </td>
                            <td>
                                @forelse($branches as $branchName)
                                    <span class="badge bg-secondary-subtle text-secondary-emphasis me-1">
                                        <i class="ph-duotone ph-buildings me-1"></i>{{ $branchName }}
                                    </span>
                                @empty
                                    <span class="text-body-secondary small">—</span>
                                @endforelse
                            </td>
                            <td>
                                <span class="badge bg-primary text-white">{{ $request->cards_count }} كارت</span>
                            </td>
                            <td>{{ number_format((float) $request->total_price, 2) }} ج.م</td>
                            <td>
                                <span class="badge bg-{{ $request->status->color() }} text-white">
                                    <i class="ph-duotone {{ $request->status->icon() }} me-1"></i>
                                    {{ $request->status->label() }}
                                </span>
                            </td>
                            <td>
                                <small
                                    class="text-body-secondary">{{ $request->created_at?->format('Y-m-d h:i A') }}</small>
                            </td>
                            <td>
                                <div class="d-flex align-items-center justify-content-center gap-3">
                                    @if($cancelId === $request->id)
                                        <button type="button" wire:click="cancelRequest"
                                            class="border-0 bg-transparent p-0 text-success" title="تأكيد">
                                            <i class="ph-duotone ph-check-circle fs-4"></i>
                                        </button>
                                        <button type="button" wire:click="cancelConfirmation"
                                            class="border-0 bg-transparent p-0 text-secondary" title="تراجع">
                                            <i class="ph-duotone ph-x-circle fs-4"></i>
                                        </button>
                                    @else
                                        <a wire:navigate href="{{ route('admin.card-requests.show', $request->id) }}"
                                            class="border-0 bg-transparent p-0 text-info" title="عرض">
                                            <i class="ph-duotone ph-eye fs-4"></i>
                                        </a>
                                        @if($request->canBeCancelled())
                                            <button type="button" wire:click="confirmCancel({{ $request->id }})"
                                                class="border-0 bg-transparent p-0 text-danger" title="إلغاء">
                                                <i class="ph-duotone ph-x-circle fs-4"></i>
                                            </button>
                                        @endif
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-5">
                                <i class="ph-duotone ph-identification-card fs-1 d-block mb-2 text-body-secondary"></i>
                                <h6 class="text-body-secondary mb-1">لا توجد طلبات</h6>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($requests->hasPages())
            <div class="card-footer bg-transparent border-top">
                {{ $requests->links() }}
            </div>
        @endif
    </div>
</div>
