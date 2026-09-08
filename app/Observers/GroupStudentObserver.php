<?php

namespace App\Observers;

use App\Models\GroupStudent;

class GroupStudentObserver
{
    public function created(GroupStudent $groupStudent): void
    {
        // Check if group is full after adding student
        $group = $groupStudent->group;
        if ($group->is_full) {
            // Optionally update group status or send notification
        }
    }

    public function updated(GroupStudent $groupStudent): void
    {
        // Handle status changes
        if ($groupStudent->isDirty('status') && $groupStudent->status === 'left') {
            // Student left the group
        }
    }
}
