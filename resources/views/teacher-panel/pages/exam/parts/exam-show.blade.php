<div>
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <div>
            <h4 class="mb-1">{{ $exam->title }}</h4>
            <p class="text-muted mb-0">{{ $exam->description }}</p>
        </div>

        <div class="d-flex gap-2">
            @if(auth('teacher')->user()->hasPermission(\App\Enums\Teacher\TeacherPermissionEnum::EXAM_UPDATE))
                <a wire:navigate href="{{ route('teacher.exams.edit', $exam) }}" class="btn btn-primary">
                    <i class="ph ph-pencil-simple"></i>
                    تعديل
                </a>
            @endif

            <a wire:navigate href="{{ route('teacher.exams.index') }}" class="btn btn-light">
                <i class="ph ph-arrow-right"></i>
                الامتحانات
            </a>
        </div>
    </div>

    {{-- Exam Info --}}
    <div class="row g-3 mb-3">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <small class="text-muted d-block mb-1">عدد الأسئلة</small>
                    <h4 class="mb-0">{{ $exam->questions->count() }}</h4>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <small class="text-muted d-block mb-1">إجمالي الدرجات</small>
                    <h4 class="mb-0">{{ number_format((float) $exam->total_marks, 2) }}</h4>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <small class="text-muted d-block mb-1">المدة</small>
                    <h4 class="mb-0">{{ $exam->duration ? $exam->duration . ' دقيقة' : 'غير محددة' }}</h4>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <small class="text-muted d-block mb-1">الحالة</small>
                    @php
                        $statusLabel = match($exam->status) {
                            'published' => 'منشور',
                            'closed' => 'مغلق',
                            default => 'مسودة',
                        };
                    @endphp
                    <span class="badge {{ $exam->status === 'published' ? 'bg-success' : ($exam->status === 'closed' ? 'bg-secondary' : 'bg-warning') }}">
                        {{ $statusLabel }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- Targets --}}
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-header bg-transparent">
            <h5 class="mb-0">المجموعات المستهدفة</h5>
        </div>

        <div class="card-body">
            <div class="row g-3">
                @foreach($exam->targets as $target)
                    <div class="col-md-4">
                        <div class="border rounded p-3 h-100">
                            <div class="fw-bold mb-2">{{ $target->group?->name }}</div>
                            <div class="small text-muted mb-1">الفرع: {{ $target->branch?->name }}</div>
                            <div class="small text-muted mb-1">الصف: {{ \App\Enums\Teacher\GradeEnum::tryFrom($target->grade)?->label() ?? $target->grade }}</div>
                            <div class="small text-muted">المادة: {{ $target->subject?->subject_label }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Instructions --}}
    @if($exam->instructions)
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-transparent">
                <h5 class="mb-0">تعليمات الامتحان</h5>
            </div>
            <div class="card-body">
                {!! nl2br(e($exam->instructions)) !!}
            </div>
        </div>
    @endif

    {{-- Questions --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-transparent">
            <h5 class="mb-0">الأسئلة</h5>
        </div>

        <div class="card-body">
            @foreach($exam->questions->sortBy('sort_order') as $index => $question)
                <div class="border rounded-3 p-3 mb-3" wire:key="show-question-{{ $question->id }}">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-primary">السؤال {{ $loop->iteration }}</span>
                            <span class="badge bg-light text-dark">
                                {{ \App\Enums\Teacher\ExamQuestionTypeEnum::tryFrom($question->type)?->label() ?? $question->type }}
                            </span>
                        </div>

                        <span class="text-muted">{{ $question->marks }} درجة</span>
                    </div>

                    <div class="fs-5 mb-2 exam-math-content">
                        {!! nl2br(e($question->question)) !!}
                    </div>

                    @if($question->description)
                        <div class="text-muted small mb-3">{{ $question->description }}</div>
                    @endif

                    @if($question->options->count())
                        <div class="d-flex flex-column gap-2">
                            @foreach($question->options->sortBy('sort_order') as $option)
                                <div class="border rounded p-3 d-flex align-items-center gap-2">
                                    @if($question->type === 'multiple_select')
                                        <i class="ph ph-square"></i>
                                    @else
                                        <i class="ph ph-circle"></i>
                                    @endif

                                    <span class="flex-grow-1">{{ $option->option_text }}</span>

                                    @if($option->is_correct)
                                        <span class="badge bg-success">الإجابة الصحيحة</span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @elseif($question->correct_answer)
                        <div class="alert alert-light mb-0">
                            <strong>الإجابة النموذجية:</strong>
                            <div class="mt-1">{{ $question->correct_answer }}</div>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    {{-- MathJax --}}
    @script
    <script>
        window.MathJax = {
            tex: {
                inlineMath: [['\\(', '\\)'], ['$', '$']],
                displayMath: [['\\[', '\\]'], ['$$', '$$']],
                packages: {'[+]': ['mhchem']}
            },
            loader: {
                load: ['[tex]/mhchem']
            }
        };

        if (!document.querySelector('script[data-classio-mathjax]')) {
            const script = document.createElement('script');
            script.src = 'https://cdn.jsdelivr.net/npm/mathjax@4/tex-mml-chtml.js';
            script.async = true;
            script.dataset.classioMathjax = 'true';
            document.head.appendChild(script);
        } else if (window.MathJax?.typesetPromise) {
            window.MathJax.typesetPromise();
        }
    </script>
    @endscript
</div>
