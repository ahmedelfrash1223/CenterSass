<?php

namespace App\Livewire\TeacherPanel\Pages\Timetable\Parts;

use App\Enums\Teacher\TeacherPermissionEnum;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use App\Livewire\TeacherPanel\Concerns\Timetable\LoadsTimetable;
use App\Livewire\TeacherPanel\Concerns\Timetable\FormatsTimetableDays;
use App\Livewire\TeacherPanel\Concerns\Timetable\DeterminesScheduleStatus;

class TimetablePage extends Component
{
    use LoadsTimetable;
    use FormatsTimetableDays;
    use DeterminesScheduleStatus;

    public bool $canViewGroups = false;

    public function mount(): void
    {
        $user = Auth::guard('teacher')->user();

        abort_unless(
            $user->hasPermission(TeacherPermissionEnum::TIMETABLE_VIEW),
            403
        );

        $this->canViewGroups = $user->hasPermission(TeacherPermissionEnum::GROUP_VIEW);

        $savedDay = session('teacher.timetable.selected_day');
        $today = $this->getTodayName();
        $days = array_keys($this->arabicDays());

        if ($savedDay && in_array($savedDay, $days)) {
            $this->selectedDay = $savedDay;
        } else {
            $this->selectedDay = in_array($today, $days) ? $today : $this->firstDayKey();
        }

        $this->loadTimetable();
    }

    public function render()
    {
        $days = $this->arabicDays();
        $today = $this->getTodayName();
        $isToday = $this->isSelectedDayToday();

        return view('teacher-panel.pages.timetable.parts.timetable-page', [
            'days'      => $days,
            'today'     => $today,
            'isToday'   => $isToday,
            'schedules' => $this->getDisplayedSchedulesProperty(),
        ]);
    }
}
