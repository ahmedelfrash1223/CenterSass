<?php

namespace App\Http\Controllers\TeacherPanel\Concerns;

use App\Models\Exam;

trait EnsuresExamAccess
{
    protected function ensureExamAccess(Exam $exam): void
    {
        $user = auth('teacher')->user();

        abort_unless(
            (int) $exam->teacher_id === (int) $user->getOwnerId(),
            404
        );

        if ($user->isOwner()) {
            return;
        }

        $allowedGroupIds = $user->getAccessibleGroups()
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->toArray();

        abort_unless(
            $exam->targets()
                ->whereIn('group_id', $allowedGroupIds)
                ->exists(),
            403
        );
    }
}
