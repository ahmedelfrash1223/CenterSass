<?php

namespace App\Http\Requests\TeacherPanel;

use App\Enums\Teacher\ExamQuestionTypeEnum;
use Illuminate\Foundation\Http\FormRequest;

class StoreExamRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'instructions'=> ['nullable', 'string', 'max:5000'],
            'duration'    => ['nullable', 'integer', 'min:1', 'max:600'],
            'starts_at'   => ['nullable', 'date'],
            'ends_at'     => ['nullable', 'date', 'after_or_equal:starts_at'],
            'status'      => ['required', 'in:draft,published,closed'],

            'shuffle_questions' => ['boolean'],
            'shuffle_options'   => ['boolean'],
            'show_result'       => ['boolean'],

            'targetBranchId'      => ['required', 'integer', 'exists:branches,id'],
            'targetGrade'         => ['required', 'string'],
            'targetSubjectId'     => ['required', 'integer', 'exists:teacher_subjects,id'],
            'selectedGroupIds'    => ['required', 'array', 'min:1'],
            'selectedGroupIds.*'  => ['integer', 'exists:groups,id'],

            'questions'               => ['required', 'array', 'min:1'],
            'questions.*.id'          => ['nullable', 'integer'],
            'questions.*.type'        => ['required', 'string', 'in:' . implode(',', array_column(ExamQuestionTypeEnum::cases(), 'value'))],
            'questions.*.question'    => ['required', 'string'],
            'questions.*.description' => ['nullable', 'string'],
            'questions.*.marks'       => ['required', 'numeric', 'min:0.25'],
            'questions.*.is_required' => ['boolean'],
            'questions.*.correct_answer' => ['nullable', 'string'],
            'questions.*.media_id'    => ['nullable', 'integer', 'exists:media,id'],

            'questions.*.options'                => ['nullable', 'array'],
            'questions.*.options.*.id'           => ['nullable', 'integer'],
            'questions.*.options.*.text'         => ['nullable', 'string'],
            'questions.*.options.*.is_correct'   => ['boolean'],
            'questions.*.options.*.media_id'     => ['nullable', 'integer', 'exists:media,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required'          => 'اسم الامتحان مطلوب.',
            'duration.min'            => 'مدة الامتحان يجب أن تكون دقيقة واحدة على الأقل.',
            'targetBranchId.required' => 'اختر الفرع.',
            'targetGrade.required'    => 'اختر الصف.',
            'targetSubjectId.required'=> 'اختر المادة.',
            'selectedGroupIds.required' => 'اختر مجموعة واحدة على الأقل.',
            'selectedGroupIds.min'    => 'اختر مجموعة واحدة على الأقل.',
            'questions.min'           => 'أضف سؤالًا واحدًا على الأقل.',
            'questions.*.question.required' => 'نص السؤال مطلوب.',
            'questions.*.marks.min'   => 'درجة السؤال يجب أن تكون أكبر من صفر.',
        ];
    }
}
