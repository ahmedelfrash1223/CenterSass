<?php

namespace App\Services\Api\Student\Group;

use App\Models\Group;
use App\Models\StudentAccount;
use Illuminate\Support\Collection;

class StudentGroupService
{

    public function allForAccount(StudentAccount $account): Collection
    {
        $students = $account->students()->with('teacher')->get();
        $groups = collect();

        foreach ($students as $student) {
            $studentGroups = $student->activeGroups()
                ->with(['teacher', 'branch'])
                ->withCount(['students' => fn ($q) => $q->where('group_student.status', 'active')])
                ->get();

            foreach ($studentGroups as $group) {
                if (! $groups->contains('id', $group->id)) {
                    $group->student_id = $student->id;
                    $group->student_name = $student->name;
                    $groups->push($group);
                }
            }
        }

        return $groups;
    }


    public function statsForAccount(StudentAccount $account): array
    {
        $groups = $this->allForAccount($account);

        $totalGroups = $groups->count();

        $totalSubjects = 0;
        $totalSchedules = 0;

        foreach ($groups as $group) {
            $schedules = $group->schedules;
            $totalSchedules += $schedules->count();

            $subjectIds = $schedules->pluck('subject_id')->unique()->filter();
            $totalSubjects += $subjectIds->count();
        }

        $totalTeachers = $account->students()
            ->distinct('teacher_id')
            ->count('teacher_id');

        return [
            'total_groups' => $totalGroups,
            'total_teachers' => $totalTeachers,
            'total_subjects' => $totalSubjects,
            'total_schedules' => $totalSchedules,
        ];
    }


    public function findForAccount(StudentAccount $account, int $groupId): Group
    {
        $studentIds = $account->students()->pluck('students.id');

        return Group::query()
            ->with(['teacher', 'branch'])
            ->withCount(['students' => fn ($q) => $q->where('group_student.status', 'active')])
            ->whereHas('students', function ($q) use ($studentIds) {
                $q->whereIn('students.id', $studentIds)
                    ->where('group_student.status', 'active');
            })
            ->findOrFail($groupId);
    }
}
