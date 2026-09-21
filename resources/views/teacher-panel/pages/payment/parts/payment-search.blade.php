<div>
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-lg-7">
                    <label class="form-label small fw-semibold text-body-secondary">
                        البحث عن طالب
                    </label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="ph-duotone ph-magnifying-glass"></i>
                        </span>
                        <input type="text"
                               wire:model.live.debounce.300ms="search"
                               class="form-control"
                               placeholder="اسم، بريد، كود، أو باركود...">
                    </div>
                </div>

                <div class="col-lg-5">
                    <label class="form-label small fw-semibold text-body-secondary">
                        أو امسح الباركود
                    </label>
                    <input type="text"
                           id="payment-scan-input"
                           class="form-control text-center"
                           placeholder="امسح الكارت..."
                           autocomplete="off"
                           autocorrect="off"
                           autocapitalize="off"
                           spellcheck="false"
                           dir="ltr">
                </div>
            </div>

            @if(strlen(trim($search)) >= 2)
                <div class="mt-3 d-flex flex-column gap-2">
                    @if($students->count() > 0)
                        @foreach($students as $student)
                            {{-- ✅ الحل: btn-outline-secondary بدل btn-light --}}
                            <button type="button"
                                    wire:click="selectStudent({{ $student->id }})"
                                    class="btn btn-outline-secondary text-start d-flex align-items-center gap-3 p-3 w-100">
                                <div
                                    class="rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center fw-bold flex-shrink-0"
                                    style="width: 40px; height: 40px;">
                                    {{ mb_substr($student->name, 0, 1) }}
                                </div>
                                <div class="flex-grow-1">
                                    <div class="fw-semibold">{{ $student->name }}</div>
                                    <div class="text-body-secondary small">
                                        {{ $student->code }}
                                        @if($student->branch)
                                            · {{ $student->branch->name }}
                                        @endif
                                    </div>
                                </div>
                                <i class="ph-duotone ph-arrow-left text-primary"></i>
                            </button>
                        @endforeach
                    @else
                        <div class="text-center text-body-secondary py-4">
                            <i class="ph-duotone ph-magnifying-glass fs-2 d-block mb-2"></i>
                            <div class="small">لا توجد نتائج مطابقة</div>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>

    @script
    <script>
        let submitTimer = null;
        const getScanInput = () => document.getElementById('payment-scan-input');

        function submitBuffer() {
            const input = getScanInput();
            if (!input) return;
            const value = input.value.trim();
            if (!value) return;
            input.value = '';
            $wire.handleScan(value);
        }

        document.addEventListener('input', function (e) {
            if (e.target?.id !== 'payment-scan-input') return;
            clearTimeout(submitTimer);
            submitTimer = setTimeout(submitBuffer, 400);
        });

        document.addEventListener('keydown', function (e) {
            if (e.target?.id === 'payment-scan-input' && e.key === 'Enter') {
                e.preventDefault();
                clearTimeout(submitTimer);
                submitBuffer();
            }
        });

        function focusScanInput() {
            const input = getScanInput();
            if (!input || document.activeElement === input) return;
            const active = document.activeElement;
            if (active && ['INPUT', 'TEXTAREA', 'SELECT'].includes(active.tagName) && active !== input) return;
            input.focus();
        }

        $wire.on('focus-scan-input', () => setTimeout(focusScanInput, 100));
        setTimeout(focusScanInput, 300);
    </script>
    @endscript
</div>
