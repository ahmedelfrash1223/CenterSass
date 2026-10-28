<?php

namespace App\Services\Api\Student\Schedule;

use App\Enums\Teacher\SubjectEnum;
use App\Models\StudentAccount;
use Illuminate\Support\Collection;

class StudentScheduleService
{

    public function groupedForAccount(StudentAccount $account): Collection
    {
        $students = $account->students()->with('teacher')->get();

        $groups = collect();

        foreach ($students as $student) {
            $studentGroups = $student->activeGroups()
                ->with([
                    'teacher',
                    'branch',
                    'schedules' => function ($q) {
                        $q->orderByRaw("FIELD(day, 'Saturday','Sunday','Monday','Tuesday','Wednesday','Thursday','Friday')")
                            ->orderBy('start_time');
                    },
                    'schedules.subject',
                    'schedules.teacher',
                ])
                ->get();

            foreach ($studentGroups as $group) {
                if (! $groups->contains('id', $group->id)) {
                    $groups->push($group);
                }
            }
        }

        return $groups->groupBy('teacher_id')->map(function ($teacherGroups) {
            $teacher = $teacherGroups->first()->teacher;

            return [
                'teacher' => $teacher ? [
                    'id' => $teacher->id,
                    'name' => $teacher->name,
                    'phone' => $teacher->phone,
                    'email' => $teacher->email,
                    'code' => $teacher->code,
                ] : null,
                'branches' => $teacherGroups->groupBy('branch_id')->map(function ($branchGroups) {
                    $branch = $branchGroups->first()->branch;

                    return [
                        'branch' => $branch ? [
                            'id' => $branch->id,
                            'name' => $branch->name,
                            'address' => $branch->address,
                            'phone' => $branch->phone,
                        ] : null,
                        'groups' => $branchGroups->map(function ($group) {
                            return [
                                'group_id' => $group->id,
                                'group_name' => $group->name,
                                'grade' => $group->grade,
                                'grade_label' => \App\Enums\Teacher\GradeEnum::tryFrom($group->grade)?->label() ?? $group->grade,
                                'schedules' => $group->schedules->map(function ($schedule) {
                                    return $this->mapSchedule($schedule);
                                })->values(),
                            ];
                        })->values(),
                    ];
                })->values(),
            ];
        })->values();
    }


    public function todayForAccount(StudentAccount $account): Collection
    {
        $today = now()->format('l'); // Saturday, Sunday, ...
        $currentTime = now()->format('H:i');

        $students = $account->students()->with('teacher')->get();

        $groups = collect();

        foreach ($students as $student) {
            $studentGroups = $student->activeGroups()
                ->with([
                    'teacher',
                    'branch',
                    'schedules' => function ($q) use ($today) {
                        $q->where('day', $today)
                            ->orderBy('start_time');
                    },
                    'schedules.subject',
                    'schedules.teacher',
                ])
                ->whereHas('schedules', function ($q) use ($today) {
                    $q->where('day', $today);
                })
                ->get();

            foreach ($studentGroups as $group) {
                if (! $groups->contains('id', $group->id)) {
                    $groups->push($group);
                }
            }
        }

        return $groups->groupBy('teacher_id')->map(function ($teacherGroups) use ($currentTime) {
            $teacher = $teacherGroups->first()->teacher;

            return [
                'teacher' => $teacher ? [
                    'id' => $teacher->id,
                    'name' => $teacher->name,
                    'phone' => $teacher->phone,
                    'email' => $teacher->email,
                    'code' => $teacher->code,
                ] : null,
                'branches' => $teacherGroups->groupBy('branch_id')->map(function ($branchGroups) use ($currentTime) {
                    $branch = $branchGroups->first()->branch;

                    return [
                        'branch' => $branch ? [
                            'id' => $branch->id,
                            'name' => $branch->name,
                            'address' => $branch->address,
                            'phone' => $branch->phone,
                        ] : null,
                        'groups' => $branchGroups->map(function ($group) use ($currentTime) {
                            return [
                                'group_id' => $group->id,
                                'group_name' => $group->name,
                                'grade' => $group->grade,
                                'grade_label' => \App\Enums\Teacher\GradeEnum::tryFrom($group->grade)?->label() ?? $group->grade,
                                'schedules' => $group->schedules->map(function ($schedule) use ($currentTime) {
                                    $data = $this->mapSchedule($schedule);
                                    $data['status'] = $this->getScheduleStatus($schedule, $currentTime);
                                    $data['status_label'] = $this->getStatusLabel($data['status']);

                                    return $data;
                                })->values(),
                            ];
                        })->values(),
                    ];
                })->values(),
            ];
        })->values();
    }

    // =============================================
    // Helpers
    // =============================================

    private function mapSchedule($schedule): array
    {
        $subjectValue = $schedule->subject?->subject;
        $subjectEnum = $subjectValue ? SubjectEnum::tryFrom($subjectValue) : null;

        return [
            'id' => $schedule->id,
            'day' => $schedule->day,
            'day_label' => $this->getDayLabel($schedule->day),
            'start_time' => $schedule->start_time?->format('H:i'),
            'end_time' => $schedule->end_time?->format('H:i'),
            'hours' => (float) $schedule->hours,
            'is_cancelled' => (bool) $schedule->is_cancelled,
            'subject' => $schedule->subject ? [
                'id' => $schedule->subject->id,
                'value' => $subjectValue,
                'name' => $subjectEnum?->label() ?? $subjectValue,
                'grade_label' => $schedule->subject->grade_label ?? null,
            ] : null,
            'teacher' => $schedule->teacher ? [
                'id' => $schedule->teacher->id,
                'name' => $schedule->teacher->name,
            ] : null,
        ];
    }

    private function getScheduleStatus($schedule, string $currentTime): string
    {
        $start = $schedule->start_time?->format('H:i');
        $end = $schedule->end_time?->format('H:i');

        if (! $start || ! $end) {
            return 'upcoming';
        }

        if ($schedule->is_cancelled) {
            return 'cancelled';
        }

        if ($start <= $currentTime && $end >= $currentTime) {
            return 'running';
        }

        if ($end < $currentTime) {
            return 'finished';
        }

        return 'upcoming';
    }

    private function getStatusLabel(string $status): string
    {
        return match ($status) {
            'running'   => 'جارية الآن',
            'finished'  => 'انتهت',
            'cancelled' => 'ملغية',
            default     => 'لم تبدأ',
        };
    }

    private function getDayLabel(string $day): string
    {
        return match ($day) {
            'Saturday' => 'السبت',
            'Sunday' => 'الأحد',
            'Monday' => 'الإثنين',
            'Tuesday' => 'الثلاثاء',
            'Wednesday' => 'الأربعاء',
            'Thursday' => 'الخميس',
            'Friday' => 'الجمعة',
            default => $day,
        };
    }
}
