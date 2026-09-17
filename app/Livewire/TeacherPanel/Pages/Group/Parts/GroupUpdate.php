<?php

namespace App\Livewire\TeacherPanel\Pages\Group\Parts;

use App\Http\Requests\TeacherPanel\UpdateGroupRequest;
use App\Livewire\TeacherPanel\Concerns\HasGroupScheduleManagement;
use App\Livewire\TeacherPanel\Concerns\LoadsGroupFormData;
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

    // Pricing
    public bool $has_pricing = true;
    public string $pricing_type = 'fixed_monthly';
    public string $pricing_amount = '0';

    // Duplicate confirmation modal state
    public bool $showDuplicateConfirm = false;
    public ?int $duplicateNewIndex = null;
    public ?int $duplicateExistingIndex = null;
    public bool $allowDuplicateOnNextSave = false;

    public function mount(Group $group): void
    {
        $user = Auth::guard('teacher')->user();

        abort_unless(
            $group->teacher_id === $user->getOwnerId(),
            404
        );

        if (!$user->isOwner()) {
            abort_unless(
                in_array($group->branch_id, $user->getAccessibleBranchIds()),
                404
            );
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

        // Pricing
        $this->has_pricing = (bool) ($group->has_pricing ?? true);
        $this->pricing_type = $group->pricing_type ?? 'fixed_monthly';
        $this->pricing_amount = (string) ($group->pricing_amount ?? '0');

        // Schedules
        $this->schedules = $group->schedules->map(fn($schedule) => [
            'id' => $schedule->id,
            'day' => $schedule->day,
            'start_time' => $schedule->start_time?->format('H:i') ?? '',
            'end_time' => $schedule->end_time?->format('H:i') ?? '',
            'subject_id' => (string) $schedule->subject_id,
            'teacher_id' => (string) $schedule->teacher_id,
        ])->toArray();

        if (empty($this->schedules)) {
            $this->addScheduleRow();
        }

        $this->allowDuplicateOnNextSave = false;
    }

    public function addScheduleRow(): void
    {
        $this->schedules[] = [
            'id' => null,
            'day' => '',
            'start_time' => '',
            'end_time' => '',
            'subject_id' => '',
            'teacher_id' => '',
        ];
    }

    public function removeScheduleRow($index): void
    {
        if (!empty($this->schedules[$index]['id'])) {
            $this->group->schedules()->where('id', $this->schedules[$index]['id'])->delete();
        }

        unset($this->schedules[$index]);
        $this->schedules = array_values($this->schedules);
    }

    public function updatedGrade(): void
    {
        foreach ($this->schedules as $index => $schedule) {
            $this->schedules[$index]['subject_id'] = '';
        }
    }

    public function updatedSchedules($value, $key): void
    {
        $parts = explode('.', $key);
        $field = end($parts);
        $index = (int) ($parts[0] ?? -1);

        if (!in_array($field, ['day', 'subject_id'])) {
            return;
        }

        if ($index < 0 || !isset($this->schedules[$index])) {
            return;
        }

        $schedule = $this->schedules[$index];

        if (empty($schedule['day']) || empty($schedule['subject_id'])) {
            return;
        }

        $existingIndex = $this->findDuplicateScheduleIndex($this->schedules, $index);

        if ($existingIndex !== null) {
            $this->duplicateNewIndex = $index;
            $this->duplicateExistingIndex = $existingIndex;
            $this->showDuplicateConfirm = true;
        }
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
            $this->schedules[$this->duplicateNewIndex]['day'] = '';
        }

        $this->showDuplicateConfirm = false;
        $this->duplicateNewIndex = null;
        $this->duplicateExistingIndex = null;
    }

    protected function validateScheduleTimes(): bool
    {
        $errors = [];

        foreach ($this->schedules as $index => $schedule) {
            if (
                empty($schedule['day'])
                && empty($schedule['start_time'])
                && empty($schedule['end_time'])
                && empty($schedule['subject_id'])
                && empty($schedule['teacher_id'])
            ) {
                continue;
            }

            $minutes = $this->computeScheduleMinutes($schedule);

            if ($minutes === null) {
                if (empty($schedule['start_time']) || empty($schedule['end_time'])) {
                    $errors["schedules.{$index}.end_time"] = 'يجب إدخال وقت البداية والنهاية.';
                } else {
                    $errors["schedules.{$index}.end_time"] = 'وقت النهاية يجب أن يكون بعد وقت البداية.';
                }
                continue;
            }

            if ($minutes < 15) {
                $errors["schedules.{$index}.end_time"] = 'مدة الحصة يجب أن تكون 15 دقيقة على الأقل.';
            }

            if ($minutes > 480) {
                $errors["schedules.{$index}.end_time"] = 'مدة الحصة لا يمكن أن تتجاوز 8 ساعات.';
            }
        }

        if (!$this->allowDuplicateOnNextSave) {
            $duplicates = $this->findAllDuplicateSchedules($this->schedules);

            foreach ($duplicates as $duplicate) {
                $dayLabel = $this->getDayLabel($duplicate['day']);
                $errors["schedules.{$duplicate['row_index']}.subject_id"] =
                    "المادة دي مضافة بالفعل يوم {$dayLabel}.";
            }
        }

        if (!empty($errors)) {
            foreach ($errors as $key => $message) {
                $this->addError($key, $message);
            }

            $this->dispatch('show-error-message', message: 'فيه أخطاء في المواعيد. راجع الجدول.');
            return false;
        }

        return true;
    }

    public function update(): void
    {
        $user = Auth::guard('teacher')->user();

        abort_unless(
            $this->group->teacher_id === $user->getOwnerId(),
            404
        );

        if (!$user->isOwner()) {
            abort_unless(
                in_array($this->group->branch_id, $user->getAccessibleBranchIds()),
                404
            );
        }

        $request = new UpdateGroupRequest();
        $this->validate($request->rules(), $request->messages());

        if (!$this->validateScheduleTimes()) {
            return;
        }

        if (!$user->hasAccessToBranch((int) $this->branch_id)) {
            $this->addError('branch_id', 'الفرع غير متاح لك.');
            return;
        }

        foreach ($this->schedules as $index => $schedule) {
            if (!empty($schedule['teacher_id'])) {
                $scheduleTeacher = Teacher::find($schedule['teacher_id']);

                if (!$scheduleTeacher || !$user->hasAccessToTeacher($scheduleTeacher)) {
                    $this->addError("schedules.{$index}.teacher_id", 'المدرس غير متاح لك.');
                    return;
                }
            }
        }

        $this->group->update([
            'name' => $this->name,
            'branch_id' => $this->branch_id,
            'grade' => $this->grade,
            'description' => $this->description ?: null,
            'max_students' => $this->max_students,
            'status' => $this->status,
            'start_date' => $this->start_date ?: null,
            'end_date' => $this->end_date ?: null,
            // Pricing
            'has_pricing' => $this->has_pricing,
            'pricing_type' => $this->pricing_type,
            'pricing_amount' => (float) $this->pricing_amount,
        ]);

        $existingIds = $this->group->schedules()->pluck('id')->toArray();
        $keptIds = [];

        foreach ($this->schedules as $schedule) {
            if ($this->isScheduleComplete($schedule)) {
                $minutes = $this->computeScheduleMinutes($schedule);
                $hours = $minutes !== null ? round($minutes / 60, 1) : 1.0;

                $data = [
                    'day' => $schedule['day'],
                    'start_time' => $schedule['start_time'],
                    'end_time' => $schedule['end_time'],
                    'hours' => $hours,
                    'subject_id' => $schedule['subject_id'],
                    'teacher_id' => $schedule['teacher_id'],
                ];

                if (!empty($schedule['id'])) {
                    $this->group->schedules()->where('id', $schedule['id'])->update($data);
                    $keptIds[] = $schedule['id'];
                } else {
                    $new = $this->group->schedules()->create($data);
                    $keptIds[] = $new->id;
                }
            }
        }

        $toDelete = array_diff($existingIds, $keptIds);
        if (!empty($toDelete)) {
            $this->group->schedules()->whereIn('id', $toDelete)->delete();
        }

        $this->allowDuplicateOnNextSave = false;

        session()->flash('group-success', 'تم تحديث المجموعة بنجاح.');
        $this->redirect(route('teacher.groups.index'), navigate: true);
    }

    public function render()
    {
        return view('teacher-panel.pages.group.parts.group-update',
            $this->loadGroupFormData()
        );
    }
}
