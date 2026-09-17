<div>
    {{-- Mode Tabs --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-2">
            <ul class="nav nav-pills nav-fill gap-2" role="tablist">
                <li class="nav-item">
                    <button type="button" wire:click="switchMode('usb')"
                            class="nav-link w-100 {{ $mode === 'usb' ? 'active' : '' }}">
                        <i class="ph-duotone ph-barcode me-1"></i> اسكانر USB
                    </button>
                </li>
                <li class="nav-item">
                    <button type="button" wire:click="switchMode('camera')"
                            class="nav-link w-100 {{ $mode === 'camera' ? 'active' : '' }}">
                        <i class="ph-duotone ph-camera me-1"></i> كاميرا
                    </button>
                </li>
                <li class="nav-item">
                    <button type="button" wire:click="switchMode('manual')"
                            class="nav-link w-100 {{ $mode === 'manual' ? 'active' : '' }}">
                        <i class="ph-duotone ph-hand-pointing me-1"></i> يدوي
                    </button>
                </li>
            </ul>
        </div>
    </div>

    {{-- Main Scanner Area --}}
    <div class="card border-0 shadow-sm mb-4 scan-card">
        <div class="card-body p-4">

            {{-- USB Scanner Mode --}}
            @if($mode === 'usb')
                <div class="text-center py-5">
                    <div class="usb-icon-wrapper mx-auto mb-4">
                        <i class="ph-duotone ph-barcode fs-1"></i>
                    </div>
                    <h5 class="mb-2">امسح الكارت باستخدام الاسكانر</h5>
                    <p class="text-muted mb-4">سيتم التسجيل تلقائياً بمجرد قراءة الكارت</p>

                    <div class="mx-auto" style="max-width: 420px;">
                        <input type="text"
                               id="usb-scan-input"
                               class="form-control form-control-lg text-center"
                               placeholder="في انتظار قراءة الكارت..."
                               autocomplete="off"
                               autocorrect="off"
                               autocapitalize="off"
                               spellcheck="false"
                               dir="ltr"
                               style="font-family: 'Consolas', 'Courier New', monospace; direction: ltr; text-align: center; letter-spacing: 1.5px;">
                    </div>
                    <small class="text-muted d-block mt-3">
                        <i class="ph-duotone ph-info me-1"></i>
                        يعمل مع أي لغة كيبورد — لا حاجة للنقر على أي مكان
                    </small>
                </div>
            @endif

            {{-- Camera Mode --}}
            @if($mode === 'camera')
                <div class="text-center">
                    <div id="qr-reader" class="qr-reader-box mx-auto"></div>
                    <p class="text-muted mt-3 mb-0">
                        <i class="ph-duotone ph-info me-1"></i>
                        وجّه كاميرا الموبايل نحو الباركود أو الـ QR Code
                    </p>
                </div>
            @endif

            {{-- Manual Mode --}}
            @if($mode === 'manual')
                <div class="manual-mode-wrapper">
                    {{-- Group Select --}}
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">المجموعة</label>
                            <select wire:model.live="manualGroupId" class="form-select form-select-lg">
                                <option value="">اختر المجموعة</option>
                                @foreach($groups as $group)
                                    <option value="{{ $group->id }}">{{ $group->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        @if($manualGroupId && $manualStudents->count() > 0)
                            <div class="col-md-6 d-flex align-items-end">
                                <div class="d-flex gap-2 w-100">
                                    <button type="button"
                                            wire:click="selectAllManual"
                                            class="btn btn-outline-success flex-fill">
                                        <i class="ph-duotone ph-check-square me-1"></i>
                                        تحديد غير المسجلين
                                    </button>
                                    <button type="button"
                                            wire:click="deselectAllManual"
                                            class="btn btn-outline-danger flex-fill"
                                        {{ empty($manualStudentIds) ? 'disabled' : '' }}>
                                        <i class="ph-duotone ph-x-square me-1"></i>
                                        إلغاء الكل
                                    </button>
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- Students List --}}
                    @if($manualGroupId)
                        @if($manualStudents->count() > 0)
                            @php
                                $unregisteredCount = $manualStudents->count() - count($alreadyRegisteredIds);
                            @endphp

                            {{-- Info Bar --}}
                            <div class="manual-info-bar mb-3">
                                <div class="d-flex align-items-center gap-3 flex-wrap">
                                    <div class="info-chip info-chip-primary">
                                        <i class="ph-duotone ph-users"></i>
                                        <span>{{ $manualStudents->count() }} طالب</span>
                                    </div>
                                    <div class="info-chip info-chip-success">
                                        <i class="ph-duotone ph-check-circle"></i>
                                        <span>{{ $unregisteredCount }} متاح</span>
                                    </div>
                                    <div class="info-chip info-chip-warning">
                                        <i class="ph-duotone ph-clock-counter-clockwise"></i>
                                        <span>{{ count($alreadyRegisteredIds) }} مسجل</span>
                                    </div>
                                    <div class="info-chip info-chip-info">
                                        <i class="ph-duotone ph-check-square"></i>
                                        <span>{{ count($manualStudentIds) }} مختار</span>
                                    </div>
                                </div>
                            </div>

                            {{-- Students Grid --}}
                            <div class="manual-students-grid">
                                <div class="row g-2">
                                    @foreach($manualStudents as $student)
                                        @php
                                            $isSelected = in_array((string) $student->id, $manualStudentIds);
                                            $isRegistered = in_array((string) $student->id, $alreadyRegisteredIds);
                                        @endphp
                                        <div class="col-md-6 col-lg-4" wire:key="manual-student-{{ $student->id }}">
                                            <div
                                                class="manual-student-card {{ $isSelected ? 'is-selected' : '' }} {{ $isRegistered ? 'is-registered' : '' }}"
                                                wire:click="toggleManualStudent({{ $student->id }})"
                                                role="button">

                                                <div
                                                    class="manual-student-avatar {{ $isRegistered ? 'bg-success' : '' }}">
                                                    @if($isRegistered)
                                                        <i class="ph-duotone ph-check-circle"></i>
                                                    @else
                                                        {{ mb_substr($student->name, 0, 1) }}
                                                    @endif
                                                </div>

                                                <div class="manual-student-info">
                                                    <span class="fw-semibold d-block">{{ $student->name }}</span>
                                                    <small class="text-muted">{{ $student->code }}</small>
                                                </div>

                                                <div class="manual-student-check">
                                                    @if($isRegistered)
                                                        <span class="badge bg-success">مسجل</span>
                                                    @elseif($isSelected)
                                                        <i class="ph-duotone ph-check-circle text-primary"></i>
                                                    @else
                                                        <i class="ph-duotone ph-circle text-muted"></i>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Submit Bar --}}
                            <div class="manual-submit-bar mt-4">
                                <button type="button"
                                        wire:click="registerManual"
                                        class="btn btn-success btn-lg w-100"
                                    {{ empty($manualStudentIds) ? 'disabled' : '' }}>
                                    <i class="ph-duotone ph-check-circle me-2"></i>
                                    تسجيل حضور {{ count($manualStudentIds) }} طالب
                                </button>
                            </div>
                        @else
                            <div class="alert alert-info d-flex align-items-center gap-2">
                                <i class="ph-duotone ph-info fs-5"></i>
                                <span>لا يوجد طلاب في هذه المجموعة.</span>
                            </div>
                        @endif
                    @else
                        <div class="alert alert-info d-flex align-items-center gap-2">
                            <i class="ph-duotone ph-info fs-5"></i>
                            <span>اختر مجموعة لعرض طلابها.</span>
                        </div>
                    @endif
                </div>
            @endif

            {{-- Live Scan Result --}}
            @if($lastResult)
                <div class="mt-4">
                    @if($lastResult['success'])
                        <div class="scan-result-card scan-result-success">
                            <div class="d-flex align-items-center gap-3 flex-wrap">
                                <div class="scan-result-icon bg-success bg-opacity-10 text-success">
                                    <i class="ph-duotone ph-check-circle fs-1"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h5 class="mb-1 fw-bold text-success">{{ $lastResult['message'] }}</h5>
                                    <div class="d-flex gap-3 flex-wrap small">
                                        @if(!empty($lastResult['student_name']))
                                            <span><i class="ph-duotone ph-user me-1"></i>{{ $lastResult['student_name'] }}</span>
                                        @endif
                                        @if(!empty($lastResult['branch']))
                                            <span><i class="ph-duotone ph-git-branch me-1"></i>{{ $lastResult['branch'] }}</span>
                                        @endif
                                        @if(!empty($lastResult['time']))
                                            <span><i
                                                    class="ph-duotone ph-clock me-1"></i>{{ $lastResult['time'] }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            @if(!empty($lastResult['registered']))
                                <hr class="my-3">
                                <h6 class="fw-bold small mb-2">الحصص المسجلة:</h6>
                                <div class="row g-2">
                                    @foreach($lastResult['registered'] as $row)
                                        <div class="col-md-6">
                                            <div class="p-2 bg-white rounded-2 border">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span class="fw-semibold small">{{ $row['subject'] }}</span>
                                                    <span class="badge bg-success bg-opacity-10 text-success">
                                                        {{ $row['status'] === 'late' ? 'متأخر' : 'حاضر' }}
                                                    </span>
                                                </div>
                                                <small class="text-muted d-block"><i
                                                        class="ph-duotone ph-users-three me-1"></i>{{ $row['group'] }}
                                                </small>
                                                <small class="text-muted"><i
                                                        class="ph-duotone ph-clock me-1"></i>{{ $row['start'] }}
                                                    - {{ $row['end'] }}</small>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            @if(!empty($lastResult['skipped']))
                                <hr class="my-3">
                                <h6 class="fw-bold small mb-2 text-muted">تم تخطيه (مسجل بالفعل):</h6>
                                <div class="row g-2">
                                    @foreach($lastResult['skipped'] as $row)
                                        <div class="col-md-6">
                                            <div class="p-2 bg-white rounded-2 border border-secondary-subtle">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span
                                                        class="fw-semibold small text-muted">{{ $row['subject'] }}</span>
                                                    <span
                                                        class="badge bg-secondary bg-opacity-10 text-secondary">مسجل</span>
                                                </div>
                                                <small class="text-muted">{{ $row['group'] }}
                                                    - {{ $row['start'] }}</small>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="scan-result-card scan-result-error">
                            <div class="d-flex align-items-center gap-3 flex-wrap">
                                <div class="scan-result-icon bg-danger bg-opacity-10 text-danger">
                                    <i class="ph-duotone ph-x-circle fs-1"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h5 class="mb-1 fw-bold text-danger">{{ $lastResult['message'] }}</h5>
                                    @if(!empty($lastResult['student_name']))
                                        <small class="text-muted d-block"><i
                                                class="ph-duotone ph-user me-1"></i>{{ $lastResult['student_name'] }}
                                        </small>
                                    @endif
                                    @if(!empty($lastResult['scanned']))
                                        <small class="text-muted d-block mt-1">
                                            <i class="ph-duotone ph-info me-1"></i>
                                            القيمة المقروءة: <code>{{ $lastResult['scanned'] }}</code>
                                        </small>
                                    @endif
                                    @if(!empty($lastResult['today_schedules']))
                                        <small class="text-muted d-block mt-2">حصص اليوم:</small>
                                        <ul class="mb-0 small">
                                            @foreach($lastResult['today_schedules'] as $s)
                                                <li>{{ $s['start'] }} - {{ $s['end'] }} | {{ $s['subject'] }}
                                                    ({{ $s['group'] }})
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            @endif

        </div>
    </div>

    {{-- Recent Scans --}}
    @if($recentScans->count() > 0)
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-bottom d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold">
                    <i class="ph-duotone ph-clock-counter-clockwise me-1"></i> آخر العمليات
                </h6>
                <span class="badge bg-primary bg-opacity-10 text-primary">{{ $recentScans->count() }}</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                        <tr>
                            <th class="ps-4">الطالب</th>
                            <th>المجموعة</th>
                            <th>الحصة</th>
                            <th>الوقت</th>
                            <th>الحالة</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($recentScans as $record)
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center gap-2">
                                        <div
                                            class="student-avatar">{{ mb_substr($record->student?->name ?? '?', 0, 1) }}</div>
                                        <div>
                                            <span class="fw-semibold d-block small">{{ $record->student?->name }}</span>
                                            <small class="text-muted">{{ $record->student?->code }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td><small>{{ $record->group?->name }}</small></td>
                                <td><small>{{ $record->schedule?->subject?->subject_label ?? '—' }}</small></td>
                                <td><small class="text-muted">{{ $record->check_in_time }}</small></td>
                                <td>
                                    <span
                                        class="badge bg-{{ $record->status_color }} bg-opacity-10 text-{{ $record->status_color }}">
                                        {{ $record->status_label }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    {{-- Scripts --}}
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>

    @script
    <script>
        let submitTimer = null;
        const SUBMIT_DELAY_MS = 400;

        function getScanInput() {
            return document.getElementById('usb-scan-input');
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
            if (!e.target || e.target.id !== 'usb-scan-input') return;

            clearTimeout(submitTimer);
            submitTimer = setTimeout(submitBuffer, SUBMIT_DELAY_MS);
        });

        document.addEventListener('keydown', function (e) {
            if (e.target && e.target.id === 'usb-scan-input' && e.key === 'Enter') {
                e.preventDefault();
                clearTimeout(submitTimer);
                submitBuffer();
            }
        });

        function focusInput() {
            const input = getScanInput();
            if (!input) return;
            if (document.activeElement === input) return;

            const active = document.activeElement;
            if (active && active.tagName === 'TEXTAREA') return;
            if (active && active.tagName === 'INPUT' && active !== input) return;
            if (active && active.tagName === 'SELECT') return;

            input.focus();
        }

        function scheduleFocus() {
            setTimeout(focusInput, 50);
            setTimeout(focusInput, 200);
        }

        $wire.on('focus-usb-input', () => scheduleFocus());

        document.addEventListener('click', function (e) {
            if (e.target.closest('input, textarea, select, button, a')) return;
            scheduleFocus();
        });

        scheduleFocus();

        /* Camera scanner */
        let html5QrCode = null;
        let cameraRunning = false;

        function initCamera() {
            const target = document.getElementById('qr-reader');

            if (!target) {
                if (html5QrCode && cameraRunning) {
                    html5QrCode.stop().then(() => {
                        html5QrCode.clear();
                        html5QrCode = null;
                        cameraRunning = false;
                    }).catch(() => {
                    });
                }
                return;
            }

            if (cameraRunning) return;

            html5QrCode = new Html5Qrcode("qr-reader");

            html5QrCode.start(
                {facingMode: "environment"},
                {fps: 10, qrbox: {width: 300, height: 200}, aspectRatio: 1.7},
                onScanSuccess,
                () => {
                }
            ).then(() => {
                cameraRunning = true;
            }).catch(err => {
                target.innerHTML = `
                    <div class="alert alert-warning mb-0 text-start">
                        <i class="ph-duotone ph-warning me-1"></i>
                        تعذر تشغيل الكاميرا: ${err.message || err}
                    </div>`;
            });
        }

        function onScanSuccess(decodedText) {
            if (window._lastScan === decodedText) return;
            window._lastScan = decodedText;
            setTimeout(() => {
                window._lastScan = null;
            }, 2000);
            $wire.handleScan(decodedText);
        }

        const cameraWatchdog = setInterval(() => {
            const target = document.getElementById('qr-reader');
            if (target && !cameraRunning) initCamera();
        }, 1000);

        window.addEventListener('beforeunload', () => clearInterval(cameraWatchdog));
    </script>
    @endscript
</div>
