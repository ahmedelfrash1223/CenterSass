<?php

namespace App\Http\Requests\TeacherPanel;

use App\Enums\Teacher\GradeEnum;
use App\Enums\Teacher\SubjectEnum;
use Illuminate\Validation\Rule;

class UpdateTeacherSubjectRequest extends StoreTeacherSubjectRequest
{
    public function rules(): array
    {
        $subjectId = $this->input('subject_id') ?: $this->route('subject');
        $subjectId = is_object($subjectId) ? $subjectId->id : $subjectId;

        return [
            'grade' => ['required', 'string', Rule::in(array_keys(GradeEnum::options()))],
            'subject' => [
                'required',
                'string',
                Rule::in(array_keys(SubjectEnum::options())),
                Rule::unique('teacher_subjects', 'subject')
                    ->where('teacher_id', auth('teacher')->id())
                    ->where('grade', $this->input('grade'))
                    ->ignore($subjectId),
            ],
            'is_active' => ['required', 'integer', 'in:0,1'],
        ];
    }
}