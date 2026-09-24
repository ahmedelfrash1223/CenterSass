<?php

namespace App\Traits\TeacherSubject;

use App\Enums\Teacher\GradeEnum;
use App\Enums\Teacher\SubjectEnum;

trait HasEnumLabels
{
    public function getGradeLabelAttribute(): string
    {
        return GradeEnum::tryFrom($this->grade)?->label() ?? $this->grade;
    }

    public function getSubjectLabelAttribute(): string
    {
        return SubjectEnum::tryFrom($this->subject)?->label() ?? $this->subject;
    }
}
