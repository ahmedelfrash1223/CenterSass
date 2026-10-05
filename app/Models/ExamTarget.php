<?php

namespace App\Models;

use App\Traits\Exam\HasExamTargetAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExamTarget extends Model
{
//    use HasExamTargetAttributes;

    #-----------------------------------------------------FILLABLE
    protected $fillable = [
        'exam_id',
        'branch_id',
        'grade',
        'teacher_subject_id',
        'group_id',
        'created_by'
    ];

    #-----------------------------------------------------RELATIONSHIPS
    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(TeacherSubject::class, 'teacher_subject_id');
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(Teacher::class, 'created_by');
    }
}
