<?php

namespace App\Livewire\TeacherPanel\Pages\Exam\Parts;

use App\Enums\Teacher\ExamQuestionTypeEnum;
use App\Enums\Teacher\TeacherPermissionEnum;
use App\Http\Requests\TeacherPanel\StoreExamRequest;
use App\Livewire\TeacherPanel\Concerns\Shared\HasExamMediaUploads;
use App\Livewire\TeacherPanel\Concerns\Shared\LoadsExamFormData;
use App\Models\Exam;
use App\Models\ExamQuestion;
use App\Models\ExamQuestionOption;
use App\Models\ExamTarget;
use App\Models\Media;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class ExamCreate extends Component
{
    use LoadsExamFormData;
    use HasExamMediaUploads;

    public string $title = '';
    public string $description = '';
    public string $instructions = '';
    public string $duration = '';
    public string $starts_at = '';
    public string $ends_at = '';
    public string $status = 'draft';

    public bool $shuffle_questions = false;
    public bool $shuffle_options = false;
    public bool $show_result = true;

    public int $targetBranchId = 0;
    public string $targetGrade = '';
    public int $targetSubjectId = 0;

    public array $availableGroups = [];
    public array $selectedGroupIds = [];
    public array $questions = [];

    public bool $sectionBasic = true;
    public bool $sectionTargets = true;
    public bool $sectionQuestions = true;
    public bool $sectionSettings = true;

    public function mount(): void
    {
        abort_unless(
            Auth::guard('teacher')->user()->hasPermission(TeacherPermissionEnum::EXAM_CREATE),
            403
        );

        $this->addQuestion();
    }

    public function toggleSection(string $section): void
    {
        if (property_exists($this, $section)) {
            $this->{$section} = !$this->{$section};
        }
    }

    // =============================================
    // Targets
    // =============================================

    public function updatedTargetBranchId(): void
    {
        $this->targetGrade = '';
        $this->targetSubjectId = 0;
        $this->selectedGroupIds = [];
        $this->availableGroups = [];
    }

    public function updatedTargetGrade(): void
    {
        $this->targetSubjectId = 0;
        $this->selectedGroupIds = [];
        $this->availableGroups = [];
    }

    public function updatedTargetSubjectId(): void
    {
        $this->selectedGroupIds = [];

        if (!$this->targetBranchId || !$this->targetGrade || !$this->targetSubjectId) {
            $this->availableGroups = [];
            return;
        }

        $this->availableGroups = $this->loadExamGroups(
            $this->targetBranchId,
            $this->targetGrade,
            $this->targetSubjectId
        );
    }

    public function toggleGroup(int $groupId): void
    {
        if (in_array($groupId, $this->selectedGroupIds, true)) {
            $this->selectedGroupIds = array_values(
                array_diff($this->selectedGroupIds, [$groupId])
            );
            return;
        }

        $this->selectedGroupIds[] = $groupId;
    }

    public function selectAllGroups(): void
    {
        $this->selectedGroupIds = collect($this->availableGroups)
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->toArray();
    }

    public function deselectAllGroups(): void
    {
        $this->selectedGroupIds = [];
    }

    // =============================================
    // Questions
    // =============================================

    public function addQuestion(string $type = 'multiple_choice'): void
    {
        $questionType = ExamQuestionTypeEnum::tryFrom($type)
            ?? ExamQuestionTypeEnum::MULTIPLE_CHOICE;

        $options = match ($questionType) {
            ExamQuestionTypeEnum::TRUE_FALSE => [
                ['id' => null, 'text' => 'صح', 'is_correct' => false, 'media_id' => null, 'media_url' => null],
                ['id' => null, 'text' => 'خطأ', 'is_correct' => false, 'media_id' => null, 'media_url' => null],
            ],
            ExamQuestionTypeEnum::MULTIPLE_CHOICE,
            ExamQuestionTypeEnum::MULTIPLE_SELECT => [
                ['id' => null, 'text' => '', 'is_correct' => false, 'media_id' => null, 'media_url' => null],
                ['id' => null, 'text' => '', 'is_correct' => false, 'media_id' => null, 'media_url' => null],
            ],
            default => [],
        };

        $this->questions[] = [
            'id'             => null,
            'type'           => $questionType->value,
            'question'       => '',
            'description'    => '',
            'marks'          => 1,
            'is_required'    => true,
            'correct_answer' => '',
            'media_id'       => null,
            'media_url'      => null,
            'options'        => $options,
        ];
    }

    public function removeQuestion(int $index): void
    {
        if (count($this->questions) <= 1) {
            return;
        }

        if (!empty($this->questions[$index]['media_id'])) {
            $m = Media::find($this->questions[$index]['media_id']);
            if ($m) app(\App\Services\MediaService::class)->delete($m);
        }

        foreach ($this->questions[$index]['options'] ?? [] as $opt) {
            if (!empty($opt['media_id'])) {
                $m = Media::find($opt['media_id']);
                if ($m) app(\App\Services\MediaService::class)->delete($m);
            }
        }

        unset($this->questions[$index]);
        $this->questions = array_values($this->questions);
    }

    public function changeQuestionType(int $index, string $type): void
    {
        $questionType = ExamQuestionTypeEnum::tryFrom($type);

        if (!$questionType) {
            return;
        }

        $this->questions[$index]['type'] = $questionType->value;
        $this->questions[$index]['correct_answer'] = '';
        $this->questions[$index]['options'] = [];

        if ($questionType === ExamQuestionTypeEnum::TRUE_FALSE) {
            $this->questions[$index]['options'] = [
                ['id' => null, 'text' => 'صح', 'is_correct' => false, 'media_id' => null, 'media_url' => null],
                ['id' => null, 'text' => 'خطأ', 'is_correct' => false, 'media_id' => null, 'media_url' => null],
            ];
        }

        if (in_array($questionType, [
            ExamQuestionTypeEnum::MULTIPLE_CHOICE,
            ExamQuestionTypeEnum::MULTIPLE_SELECT,
        ], true)) {
            $this->questions[$index]['options'] = [
                ['id' => null, 'text' => '', 'is_correct' => false, 'media_id' => null, 'media_url' => null],
                ['id' => null, 'text' => '', 'is_correct' => false, 'media_id' => null, 'media_url' => null],
            ];
        }
    }

    public function addOption(int $questionIndex): void
    {
        $this->questions[$questionIndex]['options'][] = [
            'id'         => null,
            'text'       => '',
            'is_correct' => false,
            'media_id'   => null,
            'media_url'  => null,
        ];
    }

    public function removeOption(int $questionIndex, int $optionIndex): void
    {
        if (count($this->questions[$questionIndex]['options']) <= 2) {
            return;
        }

        if (!empty($this->questions[$questionIndex]['options'][$optionIndex]['media_id'])) {
            $m = Media::find($this->questions[$questionIndex]['options'][$optionIndex]['media_id']);
            if ($m) app(\App\Services\MediaService::class)->delete($m);
        }

        unset($this->questions[$questionIndex]['options'][$optionIndex]);
        $this->questions[$questionIndex]['options'] = array_values(
            $this->questions[$questionIndex]['options']
        );
    }

    public function markCorrectOption(int $questionIndex, int $optionIndex): void
    {
        $type = $this->questions[$questionIndex]['type'];

        if ($type === ExamQuestionTypeEnum::MULTIPLE_CHOICE->value) {
            foreach ($this->questions[$questionIndex]['options'] as $key => $option) {
                $this->questions[$questionIndex]['options'][$key]['is_correct'] = $key === $optionIndex;
            }
            return;
        }

        $this->questions[$questionIndex]['options'][$optionIndex]['is_correct'] =
            !$this->questions[$questionIndex]['options'][$optionIndex]['is_correct'];
    }

    // =============================================
    // Save
    // =============================================

    public function save(): void
    {
        $user = Auth::guard('teacher')->user();

        abort_unless(
            $user->hasPermission(TeacherPermissionEnum::EXAM_CREATE),
            403
        );

        if ($this->hasAnyUploadInProgress()) {
            $this->dispatch('show-error-message', message: 'فيه صور لسه بترفع. استنى لما تخلص.');
            return;
        }

        $rules = (new StoreExamRequest())->rules();
        $messages = (new StoreExamRequest())->messages();
        $this->validate($rules, $messages);

        // Access checks
        abort_unless(
            in_array($this->targetBranchId, $this->examAccessibleBranchIds(), true),
            403
        );
        abort_unless(
            in_array($this->targetGrade, $this->examAccessibleGrades(), true),
            403
        );
        abort_unless(
            in_array($this->targetSubjectId, $this->examAccessibleSubjectIds(), true),
            403
        );

        foreach ($this->selectedGroupIds as $groupId) {
            abort_unless(
                $this->ensureExamTargetAuthorized(
                    $this->targetBranchId,
                    $this->targetGrade,
                    $this->targetSubjectId,
                    $groupId
                ),
                403
            );
        }

        // Custom checks: options + correct answer
        foreach ($this->questions as $index => $question) {
            if (in_array($question['type'], [
                ExamQuestionTypeEnum::TRUE_FALSE->value,
                ExamQuestionTypeEnum::MULTIPLE_CHOICE->value,
                ExamQuestionTypeEnum::MULTIPLE_SELECT->value,
            ], true)) {
                if (empty($question['options'])) {
                    $this->addError("questions.$index.options", 'أضف اختيارات السؤال.');
                }
                if (!collect($question['options'])->contains(fn ($o) => (bool) $o['is_correct'])) {
                    $this->addError("questions.$index.options", 'حدد الإجابة الصحيحة.');
                }
            }
        }

        if ($this->getErrorBag()->isNotEmpty()) {
            return;
        }

        DB::transaction(function () use ($user) {
            $totalMarks = collect($this->questions)
                ->sum(fn ($q) => (float) $q['marks']);

            $exam = Exam::create([
                'teacher_id'         => $user->getOwnerId(),
                'created_by'         => $user->id,
                'title'              => $this->title,
                'description'        => $this->description ?: null,
                'instructions'       => $this->instructions ?: null,
                'duration'           => $this->duration !== '' ? (int) $this->duration : null,
                'status'             => $this->status,
                'starts_at'          => $this->starts_at ?: null,
                'ends_at'            => $this->ends_at ?: null,
                'shuffle_questions'  => $this->shuffle_questions,
                'shuffle_options'    => $this->shuffle_options,
                'show_result'        => $this->show_result,
                'total_marks'        => $totalMarks,
            ]);

            foreach ($this->selectedGroupIds as $groupId) {
                ExamTarget::create([
                    'exam_id'            => $exam->id,
                    'branch_id'          => $this->targetBranchId,
                    'grade'              => $this->targetGrade,
                    'teacher_subject_id' => $this->targetSubjectId,
                    'group_id'           => $groupId,
                    'created_by'         => $user->id,
                ]);
            }

            foreach ($this->questions as $index => $question) {
                $examQuestion = ExamQuestion::create([
                    'exam_id'        => $exam->id,
                    'type'           => $question['type'],
                    'question'       => $question['question'],
                    'description'    => $question['description'] ?: null,
                    'marks'          => $question['marks'],
                    'sort_order'     => $index,
                    'is_required'    => $question['is_required'],
                    'correct_answer' => $question['correct_answer'] ?: null,
                ]);

                if (!empty($question['media_id'])) {
                    $this->attachMedia($question['media_id'], $examQuestion);
                }

                foreach ($question['options'] ?? [] as $optionIndex => $option) {
                    if (trim($option['text']) === '' && empty($option['media_id'])) {
                        continue;
                    }

                    $examOption = ExamQuestionOption::create([
                        'exam_question_id' => $examQuestion->id,
                        'option_text'      => $option['text'] ?? '',
                        'is_correct'       => (bool) ($option['is_correct'] ?? false),
                        'sort_order'       => $optionIndex,
                    ]);

                    if (!empty($option['media_id'])) {
                        $this->attachMedia($option['media_id'], $examOption);
                    }
                }
            }
        });

        session()->flash('exam-success', 'تم إنشاء الامتحان بنجاح.');
        $this->redirect(route('teacher.exams.index'), navigate: true);
    }


    protected function attachMedia(int $mediaId, $model): void
    {
        $media = Media::find($mediaId);

        if (!$media) {
            return;
        }

        $media->update([
            'mediable_id'   => $model->getKey(),
            'mediable_type' => $model->getMorphClass(),
        ]);

        if (in_array('media_id', $model->getFillable(), true)) {
            $model->update(['media_id' => $media->id]);
        }
    }

    public function render()
    {
        return view('teacher-panel.pages.exam.parts.exam-create', [
            ...$this->loadExamFormData(),
        ]);
    }
}
