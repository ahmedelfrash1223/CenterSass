<div>
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <p class="text-body-secondary mb-0 small">تواصل مع فريق الدعم الفني</p>

        @if($canCreate)
            <a href="{{ route('teacher.support.create') }}" wire:navigate
                class="btn btn-primary d-flex align-items-center gap-1">
                <i class="ph-duotone ph-plus-circle"></i>
                تذكرة جديدة
            </a>
        @endif
    </div>

    @livewire('teacher-panel.parts.flash-message', [
        'successKey' => 'support-success',
        'errorKey' => 'support-error',
    ])

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-2">
                <div class="col-md-6">
                    <div class="position-relative">
                        <i
                            class="ph-duotone ph-magnifying-glass position-absolute top-50 translate-middle-y ms-3 text-body-secondary"></i>
                        <input type="text" wire:model.live.debounce.400ms="search" class="form-control ps-5"
                            placeholder="ابحث بالعنوان أو رقم التذكرة...">
                    </div>
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
                        <th>الموضوع</th>
                        <th>التصنيف</th>
                        <th>الأولوية</th>
                        <th>الحالة</th>
                        <th>الرسائل</th>
                        <th>آخر تحديث</th>
                        <th class="text-center">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tickets as $ticket)
                        <tr wire:key="tkt-{{ $ticket->id }}">
                            <td class="ps-4 text-body-secondary fw-semibold">
                                {{ $tickets->firstItem() + $loop->index }}
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-3 d-flex align-items-center justify-content-center bg-primary-subtle text-primary flex-shrink-0"
                                        style="width: 36px; height: 36px;">
                                        <i class="ph-duotone ph-ticket fs-5"></i>
                                    </div>
                                    <div>
                                        <div class="fw-semibold">{{ $ticket->subject }}</div>
                                        <small class="text-body-secondary">{{ $ticket->reference }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-secondary text-white">
                                    <i class="ph-duotone {{ $ticket->category->icon() }} me-1"></i>
                                    {{ $ticket->category->label() }}
                                </span>
                            </td>
                            <td>
                                <span
                                    class="badge bg-{{ $ticket->priority->color() }} {{ $ticket->priority->textColor() }}">
                                    {{ $ticket->priority->label() }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-{{ $ticket->status->color() }} {{ $ticket->status->textColor() }}">
                                    <i class="ph-duotone {{ $ticket->status->icon() }} me-1"></i>
                                    {{ $ticket->status->label() }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-primary text-white">
                                    {{ $ticket->messages_count }}
                                </span>
                            </td>
                            <td>
                                <small class="text-body-secondary">
                                    {{ $ticket->last_reply_at?->diffForHumans() ?? '—' }}
                                </small>
                            </td>
                            <td>
                                <div class="d-flex align-items-center justify-content-center gap-3">
                                    @if($deleteId === $ticket->id)
                                        <button type="button" wire:click="delete"
                                            class="border-0 bg-transparent p-0 text-success" title="تأكيد">
                                            <span wire:loading.remove wire:target="delete">
                                                <i class="ph-duotone ph-check-circle fs-4"></i>
                                            </span>
                                            <span wire:loading wire:target="delete"
                                                class="spinner-border spinner-border-sm"></span>
                                        </button>
                                        <button type="button" wire:click="cancelDelete"
                                            class="border-0 bg-transparent p-0 text-secondary" title="إلغاء">
                                            <i class="ph-duotone ph-x-circle fs-4"></i>
                                        </button>
                                    @else
                                        @if($canView)
                                            <a wire:navigate href="{{ route('teacher.support.show', $ticket->id) }}"
                                                class="border-0 bg-transparent p-0 text-info" title="عرض">
                                                <i class="ph-duotone ph-chat-circle fs-4"></i>
                                            </a>
                                        @endif

                                        @if($canClose && !$ticket->isClosed())
                                            <button type="button" wire:click="confirmDelete({{ $ticket->id }})"
                                                class="border-0 bg-transparent p-0 text-danger" title="إغلاق">
                                                <i class="ph-duotone ph-lock fs-4"></i>
                                            </button>
                                        @endif
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <i class="ph-duotone ph-lifebuoy fs-1 d-block mb-2 text-body-secondary"></i>
                                <h6 class="text-body-secondary mb-1">لا توجد تذاكر</h6>
                                <small class="text-body-secondary">ابدأ بإنشاء تذكرة دعم جديدة.</small>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($tickets->hasPages())
            <div class="card-footer bg-transparent border-top">
                {{ $tickets->links() }}
            </div>
        @endif
    </div>
</div>
