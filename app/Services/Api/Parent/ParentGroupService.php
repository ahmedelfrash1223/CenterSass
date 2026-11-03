<?php

namespace App\Services\Api\Parent;

use App\Models\Group;
use App\Models\Student;
use Illuminate\Support\Collection;

class ParentGroupService
{

    public function getChildGroups(Student $child): Collection
    {
        return $child->activeGroups()
            ->with(['teacher', 'branch'])
            ->withCount(['students' => fn ($q) => $q->where('group_student.status', 'active')])
            ->get();
    }


    public function getChildGroupsStats(Student $child): array
    {
        $groups = $this->getChildGroups($child);

        $totalGroups = $groups->count();
        $totalSubjects = 0;
        $totalSchedules = 0;

        foreach ($groups as $group) {
            $schedules = $group->schedules;
            $totalSchedules += $schedules->count();
            $subjectIds = $schedules->pluck('subject_id')->unique()->filter();
            $totalSubjects += $subjectIds->count();
        }

        $totalTeachers = $groups->pluck('teacher_id')->unique()->filter()->count();

        return [
            'total_groups' => $totalGroups,
            'total_teachers' => $totalTeachers,
            'total_subjects' => $totalSubjects,
            'total_schedules' => $totalSchedules,
        ];
    }
}
