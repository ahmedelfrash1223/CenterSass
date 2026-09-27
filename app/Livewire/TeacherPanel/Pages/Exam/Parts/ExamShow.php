<?php

namespace App\Livewire\TeacherPanel\Pages\Exam\Parts;

use App\Enums\Teacher\TeacherPermissionEnum;
use App\Models\Exam;
use Livewire\Component;

class ExamShow extends Component
{
    public Exam $exam;

    public function mount(Exam $exam): void
    {
        $user = auth('teacher')->user();

        abort_unless(
            $user->hasPermission(TeacherPermissionEnum::EXAM_VIEW),
            403
        );

        abort_unless(
            (int) $exam->teacher_id === (int) $user->getOwnerId(),
            404
        );

        if (!$user->isOwner()) {
            $allowedGroups = $user->getAccessibleGroups()
                ->pluck('id')
                ->map(fn ($id) => (int) $id)
                ->toArray();

            abort_unless(
                $exam->targets()
                    ->whereIn('group_id', $allowedGroups)
                    ->exists(),
                403
            );
        }

        $this->exam = $exam->load([
            'creator',
            'questions.options',
            'targets.branch',
            'targets.group',
            'targets.subject',
        ]);
    }

    public function render()
    {
        return view('teacher-panel.pages.exam.parts.exam-show');
    }
}
