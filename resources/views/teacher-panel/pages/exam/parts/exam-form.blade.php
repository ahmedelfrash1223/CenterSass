<div>
    {{-- ============ Header ============ --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <div>
            <h4 class="mb-1">
                {{ $editMode ? 'تعديل الامتحان' : 'إضافة امتحان جديد' }}
            </h4>
            <p class="text-muted mb-0">
                {{ $editMode
                    ? 'تعديل بيانات الامتحان والأسئلة والمجموعات المستهدفة.'
                    : 'أنشئ امتحانًا وحدد الصف والمادة والمجموعات والأسئلة.' }}
            </p>
        </div>

        <div class="d-flex gap-2">
            @if($editMode)
                <a wire:navigate href="{{ route('teacher.exams.show', $exam) }}" class="btn btn-light">
                    <i class="ph ph-eye"></i> عرض
                </a>
            @endif

            <a wire:navigate href="{{ route('teacher.exams.index') }}" class="btn btn-light">
                <i class="ph ph-arrow-right"></i> الامتحانات
            </a>
        </div>
    </div>

    @if (session('exam-success'))
        <div class="alert alert-success">{{ session('exam-success') }}</div>
    @endif

    {{-- ============ Basic ============ --}}
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-header bg-transparent d-flex justify-content-between align-items-center"
             wire:click="toggleSection('sectionBasic')" style="cursor:pointer;">
            <div>
                <h5 class="mb-1">بيانات الامتحان</h5>
                <small class="text-muted">الاسم والوصف والتعليمات والمدة.</small>
            </div>
            <i class="ph {{ $sectionBasic ? 'ph-caret-up' : 'ph-caret-down' }}"></i>
        </div>

        @if($sectionBasic)
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label">اسم الامتحان <span class="text-danger">*</span></label>
                        <input type="text" wire:model="title" class="form-control"
                               placeholder="مثال: امتحان الوحدة الأولى">
                        @error('title') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">المدة بالدقائق</label>
                        <input type="number" wire:model="duration" class="form-control" min="1" placeholder="60">
                        @error('duration') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label">وصف الامتحان</label>
                        <textarea wire:model="description" class="form-control" rows="3"
                                  placeholder="وصف مختصر للامتحان..."></textarea>
                    </div>

                    <div class="col-12">
                        <label class="form-label">تعليمات الطلاب</label>
                        <textarea wire:model="instructions" class="form-control" rows="3"
                                  placeholder="تعليمات حل الامتحان..."></textarea>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">يبدأ في</label>
                        <input type="datetime-local" wire:model="starts_at" class="form-control">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">ينتهي في</label>
                        <input type="datetime-local" wire:model="ends_at" class="form-control">
                        @error('ends_at') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>

                    @if($editMode)
                        <div class="col-md-4">
                            <label class="form-label">الحالة</label>
                            <select wire:model="status" class="form-select">
                                <option value="draft">مسودة</option>
                                <option value="published">منشور</option>
                                <option value="closed">مغلق</option>
                            </select>
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>

    {{-- ============ Targets ============ --}}
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-header bg-transparent d-flex justify-content-between align-items-center"
             wire:click="toggleSection('sectionTargets')" style="cursor:pointer;">
            <div>
                <h5 class="mb-1">تحديد المستهدفين</h5>
                <small class="text-muted">حدد الفرع والصف والمادة ثم اختر مجموعة أو أكثر.</small>
            </div>
            <i class="ph {{ $sectionTargets ? 'ph-caret-up' : 'ph-caret-down' }}"></i>
        </div>

        @if($sectionTargets)
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">الفرع <span class="text-danger">*</span></label>
                        <select wire:model.live="targetBranchId" class="form-select">
                            <option value="0">اختر الفرع</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                            @endforeach
                        </select>
                        @error('targetBranchId') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">الصف <span class="text-danger">*</span></label>
                        <select wire:model.live="targetGrade" class="form-select">
                            <option value="">اختر الصف</option>
                            @foreach($grades as $grade)
                                <option value="{{ $grade['value'] }}">{{ $grade['label'] }}</option>
                            @endforeach
                        </select>
                        @error('targetGrade') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">المادة <span class="text-danger">*</span></label>
                        <select wire:model.live="targetSubjectId" class="form-select">
                            <option value="0">اختر المادة</option>
                            @foreach($subjects as $subject)
                                @if(!$targetGrade || $subject['grade'] == $targetGrade)
                                    <option value="{{ $subject['id'] }}">
                                        {{ $subject['subject_label'] }} - {{ $subject['grade_label'] }}
                                    </option>
                                @endif
                            @endforeach
                        </select>
                        @error('targetSubjectId') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>
                </div>

                @if(count($availableGroups))
                    <div class="border rounded p-3 mt-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h6 class="mb-1">المجموعات</h6>
                                <small class="text-muted">يمكن إرسال نفس الامتحان لأكثر من مجموعة.</small>
                            </div>
                            <div class="d-flex gap-2">
                                <button type="button" wire:click="selectAllGroups" class="btn btn-sm btn-light">
                                    تحديد الكل
                                </button>
                                <button type="button" wire:click="deselectAllGroups" class="btn btn-sm btn-light">
                                    إلغاء الكل
                                </button>
                            </div>
                        </div>

                        <div class="row g-2">
                            @foreach($availableGroups as $group)
                                <div class="col-md-4">
                                    <label class="border rounded p-3 d-flex align-items-center gap-2 h-100"
                                           style="cursor:pointer;">
                                        <input type="checkbox"
                                               wire:click="toggleGroup({{ $group['id'] }})"
                                               @checked(in_array($group['id'], $selectedGroupIds))
                                               class="form-check-input">
                                        <span>{{ $group['name'] }}</span>
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @elseif($targetBranchId && $targetGrade && $targetSubjectId)
                    <div class="alert alert-warning mt-4 mb-0">لا توجد مجموعات متاحة لهذا التحديد.</div>
                @endif

                @error('selectedGroupIds') <div class="text-danger small mt-2">{{ $message }}</div> @enderror
            </div>
        @endif
    </div>

    {{-- ============ Questions ============ --}}
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-header bg-transparent d-flex justify-content-between align-items-center"
             wire:click="toggleSection('sectionQuestions')" style="cursor:pointer;">
            <div>
                <h5 class="mb-1">أسئلة الامتحان</h5>
                <small class="text-muted">أضف الأسئلة وحدد النوع والدرجة والإجابة الصحيحة.</small>
            </div>
            <i class="ph {{ $sectionQuestions ? 'ph-caret-up' : 'ph-caret-down' }}"></i>
        </div>

        @if($sectionQuestions)
            <div class="card-body">
                @foreach($questions as $index => $question)
                    <div class="border rounded-3 p-3 mb-3" wire:key="exam-question-{{ $index }}">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-primary">السؤال {{ $index + 1 }}</span>
                                <select wire:model.live="questions.{{ $index }}.type"
                                        wire:change="changeQuestionType({{ $index }}, $event.target.value)"
                                        class="form-select form-select-sm">
                                    @foreach($questionTypes as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="button" wire:click="removeQuestion({{ $index }})"
                                    class="btn btn-link text-danger p-0">
                                <i class="ph ph-trash fs-5"></i>
                            </button>
                        </div>

                        {{-- Question text --}}
                        <div class="mb-3">
                            <label class="form-label">نص السؤال <span class="text-danger">*</span></label>

                            <div class="d-flex flex-wrap gap-1 mb-2 math-toolbar">
                                <button type="button" class="btn btn-light btn-sm" data-math="x^{2}">x²</button>
                                <button type="button" class="btn btn-light btn-sm" data-math="x_{1}">x₁</button>
                                <button type="button" class="btn btn-light btn-sm" data-math="\sqrt{x}">√x</button>
                                <button type="button" class="btn btn-light btn-sm" data-math="\frac{a}{b}">a/b</button>
                                <button type="button" class="btn btn-light btn-sm" data-math="\ce{H2O}">H₂O</button>
                                <button type="button" class="btn btn-light btn-sm"
                                        data-math="\ce{2H2 + O2 -> 2H2O}">⚗ معادلة</button>
                            </div>

                            <textarea wire:model="questions.{{ $index }}.question"
                                      class="form-control exam-question-editor" rows="4"
                                      placeholder="اكتب السؤال هنا..."></textarea>
                            <small class="text-muted">يمكنك استخدام الرموز الرياضية أو LaTeX.</small>
                            @error("questions.$index.question")
                            <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Question Image --}}
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">
                                <i class="ph ph-image me-1"></i> صورة السؤال (اختياري)
                            </label>

                            @if(!empty($question['media_url']))
                                <div class="d-flex align-items-center gap-3 mb-2">
                                    <img src="{{ $question['media_url'] }}" class="rounded border"
                                         style="max-height: 120px;">
                                    <button type="button" wire:click="removeQuestionImage({{ $index }})"
                                            class="btn btn-sm btn-outline-danger">
                                        <i class="ph ph-trash me-1"></i> إزالة
                                    </button>
                                </div>
                            @endif

                            <input type="file" accept="image/*"
                                   wire:change="uploadQuestionImage({{ $index }}, $event.target.files[0])"
                                   class="form-control form-control-sm"
                                @disabled($this->isUploading("question.{$index}"))>

                            @if($this->isUploading("question.{$index}"))
                                <div class="d-flex align-items-center gap-2 mt-2 text-primary small">
                                    <span class="spinner-border spinner-border-sm"></span>
                                    جاري رفع الصورة...
                                </div>
                            @endif
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-3">
                                <label class="form-label">الدرجة</label>
                                <input type="number" step="0.25" min="0.25"
                                       wire:model="questions.{{ $index }}.marks" class="form-control">
                                @error("questions.$index.marks")
                                <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-9">
                                <label class="form-label">وصف السؤال</label>
                                <input type="text" wire:model="questions.{{ $index }}.description"
                                       class="form-control" placeholder="وصف اختياري...">
                            </div>
                        </div>

                        @if(in_array($question['type'], ['true_false', 'multiple_choice', 'multiple_select']))
                            <div class="border rounded p-3">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="mb-0">الاختيارات</h6>
                                    @if($question['type'] !== 'true_false')
                                        <button type="button" wire:click="addOption({{ $index }})"
                                                class="btn btn-sm btn-light">
                                            <i class="ph ph-plus"></i> إضافة اختيار
                                        </button>
                                    @endif
                                </div>

                                @foreach($question['options'] as $optionIndex => $option)
                                    <div class="border rounded p-2 mb-2">
                                        <div class="d-flex gap-2 align-items-center mb-2">
                                            @if($question['type'] === 'multiple_select')
                                                <input type="checkbox"
                                                       wire:click="markCorrectOption({{ $index }}, {{ $optionIndex }})"
                                                       @checked($option['is_correct']) class="form-check-input">
                                            @else
                                                <input type="radio"
                                                       wire:click="markCorrectOption({{ $index }}, {{ $optionIndex }})"
                                                       @checked($option['is_correct']) class="form-check-input"
                                                       name="correct-{{ $editMode ? 'upd' : 'crt' }}-{{ $index }}">
                                            @endif

                                            <input type="text"
                                                   wire:model="questions.{{ $index }}.options.{{ $optionIndex }}.text"
                                                   class="form-control" placeholder="نص الاختيار">

                                            @if($question['type'] !== 'true_false')
                                                <button type="button"
                                                        wire:click="removeOption({{ $index }}, {{ $optionIndex }})"
                                                        class="btn btn-link text-danger">
                                                    <i class="ph ph-trash"></i>
                                                </button>
                                            @endif
                                        </div>

                                        {{-- Option Image --}}
                                        <div class="d-flex align-items-center gap-2 flex-wrap">
                                            @if(!empty($option['media_url']))
                                                <img src="{{ $option['media_url'] }}" class="rounded border"
                                                     style="max-height: 60px;">
                                                <button type="button"
                                                        wire:click="removeOptionImage({{ $index }}, {{ $optionIndex }})"
                                                        class="btn btn-sm btn-outline-danger">
                                                    <i class="ph ph-trash"></i>
                                                </button>
                                            @endif

                                            <input type="file" accept="image/*"
                                                   wire:change="uploadOptionImage({{ $index }}, {{ $optionIndex }}, $event.target.files[0])"
                                                   class="form-control form-control-sm"
                                                   style="max-width: 240px;"
                                                @disabled($this->isUploading("option.{$index}.{$optionIndex}"))>

                                            @if($this->isUploading("option.{$index}.{$optionIndex}"))
                                                <span class="d-flex align-items-center gap-1 text-primary small">
                                                    <span class="spinner-border spinner-border-sm"></span>
                                                    جاري الرفع...
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach

                                @error("questions.$index.options")
                                <div class="text-danger small mt-2">{{ $message }}</div>
                                @enderror
                            </div>
                        @else
                            <div>
                                <label class="form-label">الإجابة النموذجية</label>
                                <textarea wire:model="questions.{{ $index }}.correct_answer"
                                          class="form-control" rows="3"
                                          placeholder="اكتب الإجابة النموذجية..."></textarea>
                            </div>
                        @endif

                        <div class="form-check mt-3">
                            <input type="checkbox" wire:model="questions.{{ $index }}.is_required"
                                   class="form-check-input" id="required-{{ $editMode ? 'upd' : 'crt' }}-{{ $index }}">
                            <label class="form-check-label" for="required-{{ $editMode ? 'upd' : 'crt' }}-{{ $index }}">
                                السؤال إجباري
                            </label>
                        </div>
                    </div>
                @endforeach

                <div class="dropdown">
                    <button type="button" class="btn btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown">
                        <i class="ph ph-plus"></i> إضافة سؤال
                    </button>
                    <ul class="dropdown-menu">
                        @foreach($questionTypes as $value => $label)
                            <li>
                                <button type="button" wire:click="addQuestion('{{ $value }}')"
                                        class="dropdown-item">{{ $label }}</button>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif
    </div>

    {{-- ============ Settings ============ --}}
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-header bg-transparent d-flex justify-content-between align-items-center"
             wire:click="toggleSection('sectionSettings')" style="cursor:pointer;">
            <div>
                <h5 class="mb-1">إعدادات الامتحان</h5>
                <small class="text-muted">طريقة عرض وترتيب الامتحان.</small>
            </div>
            <i class="ph {{ $sectionSettings ? 'ph-caret-up' : 'ph-caret-down' }}"></i>
        </div>

        @if($sectionSettings)
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="form-check form-switch">
                            <input type="checkbox" wire:model="shuffle_questions"
                                   class="form-check-input" id="{{ $editMode ? 'upd' : 'crt' }}-shuffleQuestions">
                            <label class="form-check-label" for="{{ $editMode ? 'upd' : 'crt' }}-shuffleQuestions">
                                ترتيب الأسئلة عشوائيًا
                            </label>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-check form-switch">
                            <input type="checkbox" wire:model="shuffle_options"
                                   class="form-check-input" id="{{ $editMode ? 'upd' : 'crt' }}-shuffleOptions">
                            <label class="form-check-label" for="{{ $editMode ? 'upd' : 'crt' }}-shuffleOptions">
                                ترتيب الاختيارات عشوائيًا
                            </label>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-check form-switch">
                            <input type="checkbox" wire:model="show_result"
                                   class="form-check-input" id="{{ $editMode ? 'upd' : 'crt' }}-showResult">
                            <label class="form-check-label" for="{{ $editMode ? 'upd' : 'crt' }}-showResult">
                                إظهار النتيجة للطالب
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    {{-- ============ Global upload indicator ============ --}}
    @if($this->hasAnyUploadInProgress())
        <div class="alert alert-info d-flex align-items-center gap-2">
            <span class="spinner-border spinner-border-sm"></span>
            جاري رفع الصور... يرجى الانتظار قبل الحفظ.
        </div>
    @endif

    {{-- ============ Actions ============ --}}
    <div class="d-flex justify-content-end gap-2">
        @if($editMode)
            <a wire:navigate href="{{ route('teacher.exams.show', $exam) }}" class="btn btn-light">
                إلغاء
            </a>
            <button type="button" wire:click="update"
                    wire:loading.attr="disabled" wire:target="update"
                    @disabled($this->hasAnyUploadInProgress())
                    class="btn btn-primary">
                <span wire:loading.remove wire:target="update">
                    <i class="ph ph-check"></i> حفظ التعديلات
                </span>
                <span wire:loading wire:target="update">جاري الحفظ...</span>
            </button>
        @else
            <a wire:navigate href="{{ route('teacher.exams.index') }}" class="btn btn-light">
                إلغاء
            </a>
            <button type="button" wire:click="save"
                    wire:loading.attr="disabled" wire:target="save"
                    @disabled($this->hasAnyUploadInProgress())
                    class="btn btn-primary">
                <span wire:loading.remove wire:target="save">
                    <i class="ph ph-check"></i> حفظ الامتحان
                </span>
                <span wire:loading wire:target="save">جاري الحفظ...</span>
            </button>
        @endif
    </div>

    {{-- ============ Math editor scripts ============ --}}
    @script
    <script>
        window.ClassioExamMath = {
            insert(input, value) {
                const start = input.selectionStart ?? input.value.length;
                const end = input.selectionEnd ?? input.value.length;
                const text = input.value;
                input.value = text.substring(0, start) + value + text.substring(end);
                input.selectionStart = input.selectionEnd = start + value.length;
                input.dispatchEvent(new Event('input', { bubbles: true }));
                input.focus();
            }
        };

        document.addEventListener('click', function (event) {
            const button = event.target.closest('.math-toolbar [data-math]');
            if (!button) return;
            const toolbar = button.closest('.math-toolbar');
            const textarea = toolbar?.parentElement?.querySelector('.exam-question-editor');
            if (!textarea) return;
            window.ClassioExamMath.insert(textarea, button.dataset.math);
        });
    </script>
    @endscript
</div>
