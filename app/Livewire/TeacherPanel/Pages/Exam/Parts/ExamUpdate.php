<?php

namespace App\Livewire\TeacherPanel\Pages\Exam\Parts;

use App\Enums\Teacher\ExamQuestionTypeEnum;
use App\Enums\Teacher\TeacherPermissionEnum;
use App\Http\Requests\TeacherPanel\UpdateExamRequest;
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

class ExamUpdate extends Component
{
    use LoadsExamFormData;
    use HasExamMediaUploads;

    public Exam $exam;

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

    public function mount(Exam $exam): void
    {
        $user = Auth::guard('teacher')->user();

        abort_unless($user->hasPermission(TeacherPermissionEnum::EXAM_UPDATE), 403);
        abort_unless((int) $exam->teacher_id === (int) $user->getOwnerId(), 404);

        if (!$user->isOwner()) {
            $allowedGroupIds = $this->examAccessibleGroups()
                ->pluck('id')->map(fn ($id) => (int) $id)->toArray();

            abort_unless(
                $exam->targets()->whereIn('group_id', $allowedGroupIds)->exists(),
                403
            );
        }

        $this->exam = $exam->load(['targets', 'questions.options', 'questions.media', 'questions.options.media']);

        $firstTarget = $this->exam->targets->first();
        if ($firstTarget) {
            $this->targetBranchId  = (int) $firstTarget->branch_id;
            $this->targetGrade     = (string) $firstTarget->grade;
            $this->targetSubjectId = (int) $firstTarget->teacher_subject_id;

            $this->availableGroups = $this->loadExamGroups(
                $this->targetBranchId,
                $this->targetGrade,
                $this->targetSubjectId
            );

            $this->selectedGroupIds = $this->exam->targets
                ->pluck('group_id')->map(fn ($id) => (int) $id)->toArray();
        }

        $this->title             = $exam->title;
        $this->description       = $exam->description ?? '';
        $this->instructions      = $exam->instructions ?? '';
        $this->duration          = $exam->duration !== null ? (string) $exam->duration : '';
        $this->starts_at         = $exam->starts_at?->format('Y-m-d\TH:i') ?? '';
        $this->ends_at           = $exam->ends_at?->format('Y-m-d\TH:i') ?? '';
        $this->status            = $exam->status;
        $this->shuffle_questions = $exam->shuffle_questions;
        $this->shuffle_options   = $exam->shuffle_options;
        $this->show_result       = $exam->show_result;

        $this->questions = $exam->questions
            ->sortBy('sort_order')
            ->map(fn ($q) => [
                'id'             => $q->id,
                'type'           => $q->type,
                'question'       => $q->question,
                'description'    => $q->description ?? '',
                'marks'          => (float) $q->marks,
                'is_required'    => $q->is_required,
                'correct_answer' => $q->correct_answer ?? '',
                'media_id'       => $q->media_id,
                'media_url'      => $q->media?->url,
                'options'        => $q->options->sortBy('sort_order')->map(fn ($o) => [
                    'id'         => $o->id,
                    'text'       => $o->option_text,
                    'is_correct' => $o->is_correct,
                    'media_id'   => $o->media_id,
                    'media_url'  => $o->media?->url,
                ])->values()->toArray(),
            ])
            ->values()
            ->toArray();
    }

    public function toggleSection(string $section): void
    {
        if (property_exists($this, $section)) {
            $this->{$section} = !$this->{$section};
        }
    }

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

    public function toggleGroup(int $groupId): void { /* same as create */ }
    public function addQuestion(string $type = 'multiple_choice'): void { /* same as create */ }
    public function removeQuestion(int $index): void { /* same as create but check id */ }
    public function changeQuestionType(int $index, string $type): void { /* same as create */ }
    public function addOption(int $questionIndex): void { /* same as create */ }
    public function removeOption(int $questionIndex, int $optionIndex): void { /* same as create */ }
    public function markCorrectOption(int $questionIndex, int $optionIndex): void { /* same as create */ }

    public function update(): void
    {
        $user = Auth::guard('teacher')->user();

        abort_unless($user->hasPermission(TeacherPermissionEnum::EXAM_UPDATE), 403);

        if ($this->hasAnyUploadInProgress()) {
            $this->dispatch('show-error-message', message: 'فيه صور لسه بترفع. استنى لما تخلص.');
            return;
        }

        $request = new UpdateExamRequest();
        $this->validate($request->rules(), $request->messages());

        abort_unless(in_array($this->targetBranchId, $this->examAccessibleBranchIds(), true), 403);
        abort_unless(in_array($this->targetGrade, $this->examAccessibleGrades(), true), 403);
        abort_unless(in_array($this->targetSubjectId, $this->examAccessibleSubjectIds(), true), 403);

        foreach ($this->selectedGroupIds as $groupId) {
            abort_unless($this->ensureExamTargetAuthorized(
                $this->targetBranchId, $this->targetGrade, $this->targetSubjectId, $groupId
            ), 403);
        }

        foreach ($this->questions as $index => $question) {
            if (in_array($question['type'], [
                    ExamQuestionTypeEnum::TRUE_FALSE->value,
                    ExamQuestionTypeEnum::MULTIPLE_CHOICE->value,
                    ExamQuestionTypeEnum::MULTIPLE_SELECT->value,
                ], true) && !collect($question['options'] ?? [])->contains(fn ($o) => (bool) $o['is_correct'])) {
                $this->addError("questions.$index.options", 'حدد الإجابة الصحيحة.');
            }
        }

        if ($this->getErrorBag()->isNotEmpty()) {
            return;
        }

        DB::transaction(function () use ($user) {
            $totalMarks = collect($this->questions)->sum(fn ($q) => (float) $q['marks']);

            $this->exam->update([
                'title'             => $this->title,
                'description'       => $this->description ?: null,
                'instructions'      => $this->instructions ?: null,
                'duration'          => $this->duration !== '' ? (int) $this->duration : null,
                'status'            => $this->status,
                'starts_at'         => $this->starts_at ?: null,
                'ends_at'           => $this->ends_at ?: null,
                'shuffle_questions' => $this->shuffle_questions,
                'shuffle_options'   => $this->shuffle_options,
                'show_result'       => $this->show_result,
                'total_marks'       => $totalMarks,
            ]);

            ExamTarget::where('exam_id', $this->exam->id)->delete();

            foreach ($this->selectedGroupIds as $groupId) {
                ExamTarget::create([
                    'exam_id'            => $this->exam->id,
                    'branch_id'          => $this->targetBranchId,
                    'grade'              => $this->targetGrade,
                    'teacher_subject_id' => $this->targetSubjectId,
                    'group_id'           => $groupId,
                    'created_by'         => $user->id,
                ]);
            }

            $existingIds = [];

            foreach ($this->questions as $index => $question) {
                $examQuestion = !empty($question['id'])
                    ? ExamQuestion::where('exam_id', $this->exam->id)->findOrFail($question['id'])
                    : new ExamQuestion();

                $examQuestion->exam_id        = $this->exam->id;
                $examQuestion->type           = $question['type'];
                $examQuestion->question       = $question['question'];
                $examQuestion->description    = $question['description'] ?: null;
                $examQuestion->marks          = $question['marks'];
                $examQuestion->sort_order     = $index;
                $examQuestion->is_required    = $question['is_required'];
                $examQuestion->correct_answer = $question['correct_answer'] ?: null;
                $examQuestion->save();

                $existingIds[] = $examQuestion->id;

                if (!empty($question['media_id'])) {
                    $this->attachMedia($question['media_id'], $examQuestion);
                }

                ExamQuestionOption::where('exam_question_id', $examQuestion->id)->delete();

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

            ExamQuestion::where('exam_id', $this->exam->id)
                ->whereNotIn('id', $existingIds)
                ->delete();
        });

        session()->flash('exam-success', 'تم تعديل الامتحان بنجاح.');
        $this->redirect(route('teacher.exams.show', $this->exam), navigate: true);
    }

    protected function attachMedia(int $mediaId, $model): void
    {
        $media = Media::find($mediaId);
        if (!$media) return;

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
        return view('teacher-panel.pages.exam.parts.exam-update', [
            ...$this->loadExamFormData(),
        ]);
    }
}
