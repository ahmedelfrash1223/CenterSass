<div>
    {{-- Header --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div>
                    <h5 class="mb-2">{{ $cardRequest->reference }}</h5>
                    <div class="d-flex flex-wrap gap-2">
                        <span
                            class="badge bg-{{ $cardRequest->status->color() }} {{ $cardRequest->status->color() === 'warning' ? 'text-dark' : 'text-white' }}">
                            <i class="ph-duotone {{ $cardRequest->status->icon() }} me-1"></i>
                            {{ $cardRequest->status->label() }}
                        </span>
                        <span class="badge bg-primary text-white">
                            <i class="ph-duotone ph-identification-card me-1"></i>
                            {{ $cardRequest->cards_count }} كارت
                        </span>
                        <span class="badge bg-secondary text-white">
                            <i class="ph-duotone ph-calendar me-1"></i>
                            {{ $cardRequest->created_at?->format('Y-m-d h:i A') }}
                        </span>
                    </div>
                </div>

                <a href="{{ route('teacher.card-requests.index') }}" wire:navigate class="btn btn-outline-secondary">
                    <i class="ph-duotone ph-arrow-right me-1"></i>رجوع
                </a>
            </div>

            @if($cardRequest->notes)
                <div class="alert alert-light border mt-3 mb-0 small">
                    <strong>ملاحظات المدرس:</strong> {{ $cardRequest->notes }}
                </div>
            @endif

            @if($cardRequest->admin_notes)
                <div class="alert alert-info border mt-3 mb-0 small">
                    <strong>ملاحظات الأدمن:</strong> {{ $cardRequest->admin_notes }}
                </div>
            @endif
        </div>
    </div>

    {{-- Students Table --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-body">
            <h6 class="mb-0 fw-bold">
                <i class="ph-duotone ph-users-three me-1"></i>
                الطلاب ({{ $cardRequest->items->count() }})
            </h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">#</th>
                            <th>الطالب</th>
                            <th>الكود</th>
                            <th>المرحلة</th>
                            <th>الفرع</th>
                            <th>المجموعة</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($cardRequest->items as $item)
                            <tr wire:key="item-{{ $item->id }}">
                                <td class="ps-4 text-body-secondary fw-semibold">{{ $loop->iteration }}</td>
                                <td>
                                    <span class="fw-semibold">{{ $item->student_name }}</span>
                                </td>
                                <td>
                                    <small class="text-body-secondary">{{ $item->student_code ?? '—' }}</small>
                                </td>
                                <td>
                                    @if($item->grade)
                                        <span class="badge bg-info text-white">
                                            <i class="ph-duotone ph-student me-1"></i>
                                            {{ \App\Enums\Teacher\GradeEnum::tryFrom($item->grade)?->label() ?? $item->grade }}
                                        </span>
                                    @else
                                        <span class="text-body-secondary small">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if($item->branch)
                                        <small>{{ $item->branch->name }}</small>
                                    @else
                                        <span class="text-body-secondary small">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if($item->group)
                                        <small>{{ $item->group->name }}</small>
                                    @else
                                        <span class="text-body-secondary small">—</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
