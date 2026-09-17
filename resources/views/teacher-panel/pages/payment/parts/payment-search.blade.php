<div>
    <div class="card border shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-end">

                <div class="col-lg-7">
                    <label class="form-label small fw-semibold text-muted">
                        <i class="ph-duotone ph-magnifying-glass me-1"></i>البحث عن طالب
                    </label>
                    <div class="input-group input-group-lg">
                        <span class="input-group-text bg-transparent border-end-0">
                            <i class="ph-duotone ph-magnifying-glass text-muted"></i>
                        </span>
                        <input type="text"
                               wire:model.live.debounce.300ms="search"
                               class="form-control border-start-0 ps-0"
                               placeholder="اسم، بريد، كود، أو باركود...">
                    </div>
                </div>

                <div class="col-lg-5">
                    <label class="form-label small fw-semibold text-muted">
                        <i class="ph-duotone ph-barcode me-1"></i>أو امسح الباركود
                    </label>
                    <input type="text"
                           id="payment-scan-input"
                           class="form-control form-control-lg text-center font-monospace"
                           placeholder="امسح الكارت..."
                           autocomplete="off"
                           autocorrect="off"
                           autocapitalize="off"
                           spellcheck="false"
                           dir="ltr"
                           style="letter-spacing: 1px;">
                </div>

            </div>

            @if(strlen(trim($search)) >= 2)
                <div class="mt-3 pt-3 border-top">
                    @if($students->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($students as $student)
                                <button type="button"
                                        wire:click="selectStudent({{ $student->id }})"
                                        class="list-group-item list-group-item-action d-flex align-items-center gap-3 py-3">
                                    <div
                                        class="avatar-sm rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center fw-bold"
                                        style="width: 44px; height: 44px;">
                                        {{ mb_substr($student->name, 0, 1) }}
                                    </div>
                                    <div class="flex-grow-1 text-start">
                                        <div class="fw-semibold">{{ $student->name }}</div>
                                        <small class="text-muted">
                                            <i class="ph-duotone ph-identification-card me-1"></i>{{ $student->code }}
                                            @if($student->branch)
                                                <span class="mx-1">•</span>
                                                <i class="ph-duotone ph-git-branch me-1"></i>{{ $student->branch->name }}
                                            @endif
                                        </small>
                                    </div>
                                    <i class="ph-duotone ph-arrow-left text-primary"></i>
                                </button>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4 text-muted">
                            <i class="ph-duotone ph-magnifying-glass fs-1 d-block mb-2"></i>
                            <p class="mb-0 small">لا توجد نتائج مطابقة</p>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>

    @script
    <script>
        let submitTimer = null;

        function getScanInput() {
            return document.getElementById('payment-scan-input');
        }

        function submitBuffer() {
            const input = getScanInput();
            if (!input) return;
            const value = input.value.trim();
            if (!value) return;
            input.value = '';
            $wire.handleScan(value);
        }

        document.addEventListener('input', function (e) {
            if (!e.target || e.target.id !== 'payment-scan-input') return;
            clearTimeout(submitTimer);
            submitTimer = setTimeout(submitBuffer, 400);
        });

        document.addEventListener('keydown', function (e) {
            if (e.target && e.target.id === 'payment-scan-input' && e.key === 'Enter') {
                e.preventDefault();
                clearTimeout(submitTimer);
                submitBuffer();
            }
        });

        function focusScanInput() {
            const input = getScanInput();
            if (!input) return;
            if (document.activeElement === input) return;
            const active = document.activeElement;
            if (active && ['INPUT', 'TEXTAREA', 'SELECT'].includes(active.tagName) && active !== input) return;
            input.focus();
        }

        $wire.on('focus-scan-input', () => setTimeout(focusScanInput, 100));
        setTimeout(focusScanInput, 300);
    </script>
    @endscript
</div>
