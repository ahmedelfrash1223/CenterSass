<?php

namespace App\Livewire\TeacherPanel\Pages\Group\Parts;

use App\Http\Requests\TeacherPanel\UpdateGroupRequest;
use App\Livewire\TeacherPanel\Concerns\Shared\HasGroupScheduleManagement;
use App\Livewire\TeacherPanel\Concerns\Shared\LoadsGroupFormData;
use App\Models\Group;
use App\Models\Teacher;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class GroupUpdate extends Component
{
    use LoadsGroupFormData;
    use HasGroupScheduleManagement;

    public Group $group;
    public string $name = '';
    public string $branch_id = '';
    public string $grade = '';
    public string $description = '';
    public int $max_students = 0;
    public string $status = 'active';
    public string $start_date = '';
    public string $end_date = '';

    public array $schedules = [];

    public bool $has_pricing = true;
    public string $pricing_type = 'fixed_monthly';
    public string $pricing_amount = '0';

    public bool $sectionBasic   = true;
    public bool $sectionSchedule = true;
    public bool $sectionPricing = true;

    public bool $showDuplicateConfirm = false;
    public ?int $duplicateNewIndex = null;
    public ?int $duplicateExistingIndex = null;
    public bool $allowDuplicateOnNextSave = false;

    public array $availableTeachers = [];
    public array $hourOptions = [];
    public array $minuteOptions = [];
    public array $days = [];

    public function mount(Group $group): void
    {
        $user = Auth::guard('teacher')->user();

        abort_unless($group->teacher_id === $user->getOwnerId(), 404);

        if (!$user->isOwner()) {
            abort_unless(in_array($group->branch_id, $user->getAccessibleBranchIds()), 404);
        }

        $group->load('creator');

        $this->group = $group;
        $this->name = $group->name;
        $this->branch_id = (string) $group->branch_id;
        $this->grade = $group->grade ?? '';
        $this->description = $group->description ?? '';
        $this->max_students = (int) $group->max_students;
        $this->status = $group->status;
        $this->start_date = $group->start_date?->format('Y-m-d') ?? '';
        $this->end_date = $group->end_date?->format('Y-m-d') ?? '';

        $this->has_pricing = (bool) ($group->has_pricing ?? true);
        $this->pricing_type = $group->pricing_type ?? 'fixed_monthly';
        $this->pricing_amount = (string) ($group->pricing_amount ?? '0');

        $rawSchedules = $group->schedules->map(function ($schedule) {
            $start = $schedule->start_time?->format('H:i') ?? '';
            $end = $schedule->end_time?->format('H:i') ?? '';

            [$sH, $sM] = $start ? explode(':', $start) : ['', '00'];
            [$eH, $eM] = $end ? explode(':', $end) : ['', '00'];

            return [
                'id'         => $schedule->id,
                'day'        => $schedule->day,
                'start_hour' => $sH,
                'start_min'  => $sM,
                'end_hour'   => $eH,
                'end_min'    => $eM,
                'start_time' => $start,
                'end_time'   => $end,
                'subject_id' => (string) $schedule->subject_id,
                'teacher_id' => (string) $schedule->teacher_id,
            ];
        })->toArray();

        $grouped = [];
        foreach ($rawSchedules as $s) {
            $key = $s['teacher_id'] . '|' . $s['subject_id'] . '|' . $s['start_time'] . '|' . $s['end_time'];

            if (!isset($grouped[$key])) {
                $grouped[$key] = [
                    'uid'        => uniqid('sch_'),
                    'days'       => [],
                    'ids'        => [],
                    'start_hour' => $s['start_hour'] ?: '',
                    'start_min'  => $s['start_min'] ?: '00',
                    'end_hour'   => $s['end_hour'] ?: '',
                    'end_min'    => $s['end_min'] ?: '00',
                    'start_time' => $s['start_time'],
                    'end_time'   => $s['end_time'],
                    'subject_id' => $s['subject_id'],
                    'teacher_id' => $s['teacher_id'],
                ];
            }

            $grouped[$key]['days'][] = $s['day'];
            $grouped[$key]['ids'][] = $s['id'];
        }

        $this->schedules = array_values($grouped);

        if (empty($this->schedules)) {
            $this->addScheduleRow();
        }

        $this->allowDuplicateOnNextSave = false;

        $this->availableTeachers = $this->teachersForGrade($this->grade, $user->getOwnerId())
            ->map(fn($t) => ['id' => $t->id, 'name' => $t->name])
            ->toArray();

        $this->hourOptions = $this->hourOptions();
        $this->minuteOptions = $this->minuteOptions();
        $this->days = $this->daysList();
    }

    protected function daysList(): array
    {
        return [
            'Saturday'  => 'السبت',
            'Sunday'    => 'الأحد',
            'Monday'    => 'الإثنين',
            'Tuesday'   => 'الثلاثاء',
            'Wednesday' => 'الأربعاء',
            'Thursday'  => 'الخميس',
            'Friday'    => 'الجمعة',
        ];
    }

    public function toggleSection(string $section): void
    {
        $this->{$section} = !$this->{$section};
    }

    public function addScheduleRow(): void
    {
        $this->schedules[] = [
            'uid'        => uniqid('sch_'),
            'days'       => [],
            'start_hour' => '',
            'start_min'  => '00',
            'end_hour'   => '',
            'end_min'    => '00',
            'start_time' => '',
            'end_time'   => '',
            'subject_id' => '',
            'teacher_id' => '',
        ];
    }

    public function duplicateScheduleRow(int $index): void
    {
        if (!isset($this->schedules[$index])) return;

        $original = $this->schedules[$index];

        $copy = $original;
        $copy['uid'] = uniqid('sch_');
        $copy['ids'] = [];

        array_splice($this->schedules, $index + 1, 0, [$copy]);
    }

    public function removeScheduleRow($index): void
    {
        if (!isset($this->schedules[$index])) return;

        unset($this->schedules[$index]);
        $this->schedules = array_values($this->schedules);
    }

    public function reorderSchedules(int $oldIndex, int $newIndex): void
    {
        if (!isset($this->schedules[$oldIndex])) return;

        $moved = $this->schedules[$oldIndex];
        unset($this->schedules[$oldIndex]);
        $this->schedules = array_values($this->schedules);

        array_splice($this->schedules, $newIndex, 0, [$moved]);
    }

    public function selectAllDays(int $index): void
    {
        if (!isset($this->schedules[$index])) return;
        $this->schedules[$index]['days'] = array_keys($this->days);
    }

    public function deselectAllDays(int $index): void
    {
        if (!isset($this->schedules[$index])) return;
        $this->schedules[$index]['days'] = [];
    }

    public function updatedGrade(): void
    {
        $user = Auth::guard('teacher')->user();
        $this->availableTeachers = $this->teachersForGrade($this->grade, $user->getOwnerId())
            ->map(fn($t) => ['id' => $t->id, 'name' => $t->name])
            ->toArray();

        foreach ($this->schedules as $i => $schedule) {
            $this->schedules[$i]['teacher_id'] = '';
            $this->schedules[$i]['subject_id'] = '';
        }
    }

    public function updatedSchedules($value, $key): void
    {
        $parts = explode('.', $key);
        $field = end($parts);
        $index = (int)($parts[0] ?? -1);

        if ($index < 0 || !isset($this->schedules[$index])) return;

        if ($field === 'teacher_id') {
            $this->schedules[$index]['subject_id'] = '';
        }

        if (in_array($field, ['start_hour', 'start_min'])) {
            $this->schedules[$index]['start_time'] = $this->buildTime(
                $this->schedules[$index]['start_hour'],
                $this->schedules[$index]['start_min']
            );
        }

        if (in_array($field, ['end_hour', 'end_min'])) {
            $this->schedules[$index]['end_time'] = $this->buildTime(
                $this->schedules[$index]['end_hour'],
                $this->schedules[$index]['end_min']
            );
        }

        if (!in_array($field, ['days', 'subject_id', 'teacher_id'])) return;

        $schedule = $this->schedules[$index];
        if (empty($schedule['days']) || empty($schedule['subject_id'])) return;

        $existingIndex = $this->findDuplicateScheduleIndex($this->schedules, $index);

        if ($existingIndex !== null) {
            $this->duplicateNewIndex = $index;
            $this->duplicateExistingIndex = $existingIndex;
            $this->showDuplicateConfirm = true;
        }
    }

    protected function buildTime(string $hour, string $min): string
    {
        if ($hour === '') return '';
        $min = $min !== '' ? $min : '00';
        return "{$hour}:{$min}";
    }

    public function confirmDuplicate(): void
    {
        $this->allowDuplicateOnNextSave = true;
        $this->showDuplicateConfirm = false;
        $this->duplicateNewIndex = null;
        $this->duplicateExistingIndex = null;
    }

    public function cancelDuplicate(): void
    {
        if ($this->duplicateNewIndex !== null && isset($this->schedules[$this->duplicateNewIndex])) {
            $this->schedules[$this->duplicateNewIndex]['days'] = [];
        }

        $this->showDuplicateConfirm = false;
        $this->duplicateNewIndex = null;
        $this->duplicateExistingIndex = null;
    }

    protected function validateScheduleTimes(): bool
    {
        $errors = [];

        foreach ($this->schedules as $index => $schedule) {
            $days = $schedule['days'] ?? [];

            if (empty($days) && empty($schedule['start_time']) && empty($schedule['subject_id']) && empty($schedule['teacher_id'])) {
                continue;
            }

            $minutes = $this->computeScheduleMinutes($schedule);

            if ($minutes === null) {
                $errors["schedules.{$index}.end_time"] = empty($schedule['start_time']) || empty($schedule['end_time'])
                    ? 'يجب إدخال وقت البداية والنهاية.'
                    : 'وقت النهاية يجب أن يكون بعد وقت البداية.';
                continue;
            }

            if ($minutes < 15) $errors["schedules.{$index}.end_time"] = 'مدة الحصة يجب أن تكون 15 دقيقة على الأقل.';
            if ($minutes > 480) $errors["schedules.{$index}.end_time"] = 'مدة الحصة لا يمكن أن تتجاوز 8 ساعات.';

            if (empty($days)) {
                $errors["schedules.{$index}.days"] = 'اختر يوم واحد على الأقل.';
            }
        }

        if (!$this->allowDuplicateOnNextSave) {
            $duplicates = $this->findAllDuplicateSchedules($this->schedules);

            foreach ($duplicates as $duplicate) {
                $dayLabel = $this->getDayLabel($duplicate['day']);
                $errors["schedules.{$duplicate['row_index']}.days"] = "المادة دي مضافة بالفعل يوم {$dayLabel}.";
            }
        }

        if (!empty($errors)) {
            foreach ($errors as $key => $message) {
                $this->addError($key, $message);
            }
            $this->sectionSchedule = true;
            $this->dispatch('show-error-message', message: 'فيه أخطاء في المواعيد. راجع الجدول.');
            return false;
        }

        return true;
    }

    protected function openSectionsWithErrors(): void
    {
        $errorFields = array_keys($this->getErrorBag()->toArray());

        $sectionMap = [
            'sectionBasic'    => ['name', 'branch_id', 'grade', 'description', 'max_students', 'status', 'start_date', 'end_date'],
            'sectionSchedule' => ['schedules'],
            'sectionPricing'  => ['pricing_type', 'pricing_amount', 'has_pricing'],
        ];

        foreach ($errorFields as $field) {
            $fieldName = explode('.', $field)[0];
            foreach ($sectionMap as $section => $fields) {
                if (in_array($fieldName, $fields)) {
                    $this->{$section} = true;
                }
            }
        }
    }

    public function update(): void
    {
        $user = Auth::guard('teacher')->user();

        abort_unless($this->group->teacher_id === $user->getOwnerId(), 404);

        if (!$user->isOwner()) {
            abort_unless(in_array($this->group->branch_id, $user->getAccessibleBranchIds()), 404);
        }

        $request = new UpdateGroupRequest();

        try {
            $this->validate($request->rules(), $request->messages());
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->openSectionsWithErrors();
            throw $e;
        }

        if (!$this->validateScheduleTimes()) return;

        if (!$user->hasAccessToBranch((int) $this->branch_id)) {
            $this->addError('branch_id', 'الفرع غير متاح لك.');
            $this->sectionBasic = true;
            return;
        }

        foreach ($this->schedules as $index => $schedule) {
            if (!empty($schedule['teacher_id'])) {
                $scheduleTeacher = Teacher::find($schedule['teacher_id']);
                if (!$scheduleTeacher || !$user->hasAccessToTeacher($scheduleTeacher)) {
                    $this->addError("schedules.{$index}.teacher_id", 'المدرس غير متاح لك.');
                    $this->sectionSchedule = true;
                    return;
                }
            }
        }

        $this->group->update([
            'name'           => $this->name,
            'branch_id'      => $this->branch_id,
            'grade'          => $this->grade,
            'description'    => $this->description ?: null,
            'max_students'   => $this->max_students,
            'status'         => $this->status,
            'start_date'     => $this->start_date ?: null,
            'end_date'       => $this->end_date ?: null,
            'has_pricing'    => $this->has_pricing,
            'pricing_type'   => $this->pricing_type,
            'pricing_amount' => (float) $this->pricing_amount,
        ]);

        $this->group->schedules()->delete();

        $expandedSchedules = $this->expandRecurringSchedules($this->schedules);

        foreach ($expandedSchedules as $schedule) {
            if ($this->isScheduleComplete($schedule)) {
                $minutes = $this->computeScheduleMinutes($schedule);
                $hours = $minutes !== null ? round($minutes / 60, 1) : 1.0;

                $this->group->schedules()->create([
                    'day'        => $schedule['day'],
                    'start_time' => $schedule['start_time'],
                    'end_time'   => $schedule['end_time'],
                    'hours'      => $hours,
                    'subject_id' => $schedule['subject_id'],
                    'teacher_id' => $schedule['teacher_id'],
                ]);
            }
        }

        $this->allowDuplicateOnNextSave = false;

        session()->flash('group-success', 'تم تحديث المجموعة بنجاح.');
        $this->redirect(route('teacher.groups.index'), navigate: true);
    }

    public function subjectsForTeacher(int $teacherId): array
    {
        if (!$teacherId || !$this->grade) return [];

        $user = Auth::guard('teacher')->user();

        return $this->subjectsForTeacherAndGrade($teacherId, $this->grade, $user->getOwnerId())
            ->map(fn($s) => ['id' => $s->id, 'label' => $s->subject_label])
            ->toArray();
    }

    public function render()
    {
        return view('teacher-panel.pages.group.parts.group-update',
            $this->loadGroupFormData()
        );
    }
}
