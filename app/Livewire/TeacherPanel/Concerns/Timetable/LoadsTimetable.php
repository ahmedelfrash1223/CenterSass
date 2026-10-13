<?php

namespace App\Livewire\TeacherPanel\Concerns\Timetable;

use App\Enums\Teacher\GradeEnum;
use App\Enums\Teacher\SubjectEnum;
use App\Enums\Teacher\TeacherPermissionEnum;
use App\Models\Branch;
use App\Models\Group;
use App\Models\GroupSchedule;
use App\Models\TeacherSubject;
use Illuminate\Support\Facades\Auth;

trait LoadsTimetable
{
    public array $timetable = [];

    public string $selectedDay = '';

    public string $branchFilter = '';

    public string $viewBranchId = '';

    public string $viewGrade = '';

    public string $viewGroupId = '';

    public array $selectedGroupTimetable = [];

    public ?array $selectedGroupInfo = null;

    protected function loadTimetable(): void
    {
        $user = Auth::guard('teacher')->user();
        $ownerId = $user->getOwnerId();
        $isOwner = $user->isOwner();
        $accessibleBranchIds = $user->getAccessibleBranchIds();

        $days = array_keys($this->arabicDays());

        $isRegularTeacher = $this->isRegularTeacher();

        $query = GroupSchedule::query()
            ->with([
                'subject',
                'teacher',
                'group.branch',
                'group' => fn ($q) => $q->withCount('activeStudents as students_count'),
            ])
            ->whereHas('group', function ($q) use ($ownerId, $isOwner, $accessibleBranchIds) {
                $q->where('teacher_id', $ownerId);

                if (! $isOwner && ! empty($accessibleBranchIds)) {
                    $q->whereIn('branch_id', $accessibleBranchIds);
                } elseif (! $isOwner && empty($accessibleBranchIds)) {
                    $q->whereRaw('1 = 0');
                }
            });

        if ($isRegularTeacher) {
            $query->where('teacher_id', $user->id);
        }

        if ($this->branchFilter !== '') {
            $query->whereHas('group', fn ($q) => $q->where('branch_id', (int) $this->branchFilter));
        }

        $schedules = $query->orderBy('start_time')->get();

        $mapped = $schedules->map(fn ($s) => $this->mapSchedule($s));

        $grouped = [];
        foreach ($days as $day) {
            $grouped[$day] = $mapped
                ->filter(fn ($s) => $s['day'] === $day)
                ->values()
                ->toArray();
        }

        $this->timetable = $grouped;
    }

    protected function isRegularTeacher(): bool
    {
        $user = Auth::guard('teacher')->user();

        if (! $user) {
            return false;
        }

        if ($user->isOwner()) {
            return false;
        }

        if ($user->hasPermission(TeacherPermissionEnum::GROUP_VIEW)) {
            return false;
        }

        return true;
    }

    protected function mapSchedule($s): array
    {
        return [
            'id' => $s->id,
            'group_id' => $s->group_id,
            'day' => $s->day,
            'start_time' => $s->start_time?->format('H:i') ?? '',
            'end_time' => $s->end_time?->format('H:i') ?? '',
            'subject_label' => $this->resolveSubjectLabel($s),
            'group_name' => $s->group?->name ?? '—',
            'branch_name' => $s->group?->branch?->name ?? '—',
            'teacher_name' => $s->teacher?->name ?? '—',
            'students_count' => (int) ($s->group?->students_count ?? 0),
        ];
    }

    protected function resolveSubjectLabel($schedule): string
    {
        if ($schedule->subject) {
            $subjectValue = $schedule->subject->subject
                ?? $schedule->subject->subject_value
                ?? null;

            if ($subjectValue) {
                $enum = SubjectEnum::tryFrom($subjectValue);
                if ($enum) {
                    return $enum->label();
                }
            }

            if (isset($schedule->subject->subject_label) && $schedule->subject->subject_label) {
                return $schedule->subject->subject_label;
            }
        }

        if ($schedule->subject_id) {
            $ts = TeacherSubject::find($schedule->subject_id);
            if ($ts) {
                $enum = SubjectEnum::tryFrom($ts->subject);
                if ($enum) {
                    return $enum->label();
                }
            }
        }

        return 'مادة';
    }

    public function updatedViewBranchId(): void
    {
        $this->viewGrade = '';
        $this->viewGroupId = '';
        $this->selectedGroupTimetable = [];
        $this->selectedGroupInfo = null;
    }

    public function updatedViewGrade(): void
    {
        $this->viewGroupId = '';
        $this->selectedGroupTimetable = [];
        $this->selectedGroupInfo = null;
    }

    public function updatedViewGroupId(): void
    {
        $this->loadSelectedGroupTimetable();
    }

    public function clearSelectedGroup(): void
    {
        $this->viewBranchId = '';
        $this->viewGrade = '';
        $this->viewGroupId = '';
        $this->selectedGroupTimetable = [];
        $this->selectedGroupInfo = null;
    }

    protected function loadSelectedGroupTimetable(): void
    {
        $this->selectedGroupTimetable = [];
        $this->selectedGroupInfo = null;

        if ($this->viewGroupId === '') {
            return;
        }

        $user = Auth::guard('teacher')->user();
        $ownerId = $user->getOwnerId();
        $accessibleBranchIds = $user->getAccessibleBranchIds();

        $group = Group::query()
            ->with(['branch'])
            ->where('id', (int) $this->viewGroupId)
            ->where('teacher_id', $ownerId)
            ->when(! $user->isOwner(), fn ($q) => $q->whereIn('branch_id', $accessibleBranchIds))
            ->first();

        if (! $group) {
            return;
        }

        if ($this->isRegularTeacher() && ! $this->teacherHasAccessToGroup($group)) {
            return;
        }

        $schedules = GroupSchedule::query()
            ->with(['subject', 'teacher'])
            ->where('group_id', $group->id)
            ->orderBy('start_time')
            ->get();

        $days = array_keys($this->arabicDays());
        $mapped = $schedules->map(fn ($s) => $this->mapSchedule($s));

        $grouped = [];
        foreach ($days as $day) {
            $grouped[$day] = $mapped
                ->filter(fn ($s) => $s['day'] === $day)
                ->values()
                ->toArray();
        }

        $this->selectedGroupTimetable = $grouped;
        $this->selectedGroupInfo = [
            'id' => $group->id,
            'name' => $group->name,
            'grade' => $group->grade,
            'grade_label' => GradeEnum::tryFrom($group->grade)?->label() ?? $group->grade,
            'branch_name' => $group->branch?->name ?? '—',
            'students_count' => $group->activeStudents()->count(),
        ];
    }

    protected function teacherHasAccessToGroup(Group $group): bool
    {
        $user = Auth::guard('teacher')->user();

        // لو المدرس دا داخل تحت المادة
        return GroupSchedule::where('group_id', $group->id)
            ->where('teacher_id', $user->id)
            ->exists();
    }

    public function getBranchesForFilterProperty()
    {
        $user = Auth::guard('teacher')->user();
        $accessibleBranches = $user->getAccessibleBranchIds();

        $query = Branch::query()->where('is_active', 1);

        if (! $user->isOwner()) {
            empty($accessibleBranches)
                ? $query->whereRaw('1 = 0')
                : $query->whereIn('id', $accessibleBranches);
        }

        return $query->orderBy('name')->get();
    }

    public function getGradesForViewProperty(): array
    {
        if ($this->viewBranchId === '') {
            return [];
        }

        $user = Auth::guard('teacher')->user();
        $ownerId = $user->getOwnerId();
        $accessibleBranches = $user->getAccessibleBranchIds();

        $grades = Group::query()
            ->where('teacher_id', $ownerId)
            ->where('branch_id', (int) $this->viewBranchId)
            ->where('status', 'active')
            ->when(! $user->isOwner(), fn ($q) => $q->whereIn('branch_id', $accessibleBranches))
            ->distinct()
            ->pluck('grade')
            ->filter()
            ->toArray();

        $options = GradeEnum::options();

        return array_filter(
            $options,
            fn ($label, $value) => in_array($value, $grades),
            ARRAY_FILTER_USE_BOTH
        );
    }

    public function getGroupsForViewProperty()
    {
        if ($this->viewBranchId === '' || $this->viewGrade === '') {
            return collect();
        }

        $user = Auth::guard('teacher')->user();
        $ownerId = $user->getOwnerId();
        $accessibleBranches = $user->getAccessibleBranchIds();

        return Group::query()
            ->where('teacher_id', $ownerId)
            ->where('branch_id', (int) $this->viewBranchId)
            ->where('grade', $this->viewGrade)
            ->where('status', 'active')
            ->when(! $user->isOwner(), fn ($q) => $q->whereIn('branch_id', $accessibleBranches))
            ->when($this->isRegularTeacher(), function ($q) use ($user) {
                // المدرس العادي: بس المجموعات اللي بيدرّس فيها
                $q->whereHas('schedules', fn ($s) => $s->where('teacher_id', $user->id));
            })
            ->orderBy('name')
            ->get();
    }

    // ============================================
    // Helpers
    // ============================================
    public function selectDay(string $day): void
    {
        if (! array_key_exists($day, $this->arabicDays())) {
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

    public function getDisplayedGroupSchedulesProperty()
    {
        return collect($this->selectedGroupTimetable[$this->selectedDay] ?? []);
    }
}
