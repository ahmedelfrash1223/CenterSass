<?php

namespace App\Livewire\TeacherPanel\Concerns\Timetable;

use App\Models\GroupSchedule;
use Illuminate\Support\Facades\Auth;

trait LoadsTimetable
{
    public array $timetable = [];

    public string $selectedDay = '';

    protected function loadTimetable(): void
    {
        $user = Auth::guard('teacher')->user();
        $ownerId = $user->getOwnerId();
        $isOwner = $user->isOwner();
        $accessibleBranchIds = $user->getAccessibleBranchIds();

        $days = array_keys($this->arabicDays());

        $schedules = GroupSchedule::query()
            ->with([
                'subject',
                'teacher',
                'group.branch',
                'group' => fn($q) => $q->withCount('activeStudents as students_count'),
            ])
            ->whereHas('group', function ($q) use ($ownerId, $isOwner, $accessibleBranchIds) {
                $q->where('teacher_id', $ownerId);

                if (!$isOwner && !empty($accessibleBranchIds)) {
                    $q->whereIn('branch_id', $accessibleBranchIds);
                } elseif (!$isOwner && empty($accessibleBranchIds)) {
                    $q->whereRaw('1 = 0');
                }
            })
            ->when(!$isOwner, fn($q) => $q->where('teacher_id', $user->id))
            ->orderBy('start_time')
            ->get();

        $mapped = $schedules->map(function ($s) {
            $subjectLabel = null;

            if ($s->subject) {
                $subjectLabel = $s->subject->subject_label
                    ?? $s->subject->name
                    ?? $s->subject->title
                    ?? null;
            }

            if (!$subjectLabel && $s->subject_id) {
                $subjectLabel = \App\Models\TeacherSubject::find($s->subject_id)?->subject_label
                    ?? \App\Models\TeacherSubject::find($s->subject_id)?->name
                    ?? null;
            }

            return [
                'id'             => $s->id,
                'group_id'       => $s->group_id,
                'day'            => $s->day,
                'start_time'     => $s->start_time?->format('H:i') ?? '',
                'end_time'       => $s->end_time?->format('H:i') ?? '',
                'subject_label'  => $subjectLabel ?: 'مادة',
                'group_name'     => $s->group?->name ?? '—',
                'branch_name'    => $s->group?->branch?->name ?? '—',
                'teacher_name'   => $s->teacher?->name ?? '—',
                'students_count' => (int) ($s->group?->students_count ?? 0),
            ];
        });

        $grouped = [];

        foreach ($days as $day) {
            $grouped[$day] = $mapped
                ->filter(fn($s) => $s['day'] === $day)
                ->values()
                ->toArray();
        }

        $this->timetable = $grouped;
    }


    public function selectDay(string $day): void
    {
        if (!array_key_exists($day, $this->arabicDays())) {
            return;
        }

        $this->selectedDay = $day;

        session(['teacher.timetable.selected_day' => $day]);
    }


    public function goToToday(): void
    {
        $today = $this->getTodayName();
        $days = array_keys($this->arabicDays());

        $this->selectedDay = in_array($today, $days) ? $today : $this->firstDayKey();

        session(['teacher.timetable.selected_day' => $this->selectedDay]);
    }

    public function getTodayName(): string
    {
        return now()->format('l');
    }

    public function isSelectedDayToday(): bool
    {
        return $this->selectedDay === $this->getTodayName();
    }

    public function getDisplayedSchedulesProperty()
    {
        return collect($this->timetable[$this->selectedDay] ?? []);
    }
}
