<?php

namespace App\Livewire\TeacherPanel\Pages\Group\Parts;

use App\Enums\Teacher\GroupStatusEnum;
use App\Http\Requests\TeacherPanel\UpdateGroupRequest;
use App\Models\Group;
use App\Models\Branch;
use App\Models\TeacherSubject;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class GroupUpdate extends Component
{
    public Group $group;
    public string $name = '';
    public string $branch_id = '';
    public string $description = '';
    public int $max_students = 0;
    public string $status = 'active';
    public string $start_date = '';
    public string $end_date = '';
    public array $schedules = [];

    public bool $updating = false;

    public function mount(Group $group): void
    {
        abort_unless($group->teacher_id === Auth::guard('teacher')->id(), 404);

        $this->group = $group;
        $this->name = $group->name;
        $this->branch_id = (string) $group->branch_id;
        $this->description = $group->description ?? '';
        $this->max_students = (int) $group->max_students;
        $this->status = $group->status;
        $this->start_date = $group->start_date?->format('Y-m-d') ?? '';
        $this->end_date = $group->end_date?->format('Y-m-d') ?? '';

        // Load schedules with proper time format
        $this->schedules = $group->schedules->map(function ($schedule) {
            return [
                'id' => $schedule->id,
                'day' => $schedule->day,
                'start_time' => $schedule->start_time?->format('H:i') ?? '',
                'end_time' => $schedule->end_time?->format('H:i') ?? '',
                'subject_id' => (string) $schedule->subject_id,
            ];
        })->toArray();

        if (empty($this->schedules)) {
            $this->addScheduleRow();
        }
    }

    public function addScheduleRow(): void
    {
        $this->schedules[] = [
            'id' => null,
            'day' => '',
            'start_time' => '',
            'end_time' => '',
            'subject_id' => '',
        ];
    }

    public function removeScheduleRow($index): void
    {
        // If it's an existing schedule, delete it
        if (isset($this->schedules[$index]['id']) && $this->schedules[$index]['id']) {
            $scheduleId = $this->schedules[$index]['id'];
            $this->group->schedules()->where('id', $scheduleId)->delete();
        }

        unset($this->schedules[$index]);
        $this->schedules = array_values($this->schedules);
    }

    public function update(): void
    {
        $this->updating = true;

        $request = new UpdateGroupRequest();
        $this->validate($request->rules(), $request->messages());

        // Update group
        $this->group->update([
            'name' => $this->name,
            'branch_id' => $this->branch_id,
            'description' => $this->description ?: null,
            'max_students' => $this->max_students,
            'status' => $this->status,
            'start_date' => $this->start_date ?: null,
            'end_date' => $this->end_date ?: null,
        ]);

        // Get existing schedule IDs
        $existingScheduleIds = $this->group->schedules()->pluck('id')->toArray();
        $updatedScheduleIds = [];

        // Update or create schedules
        foreach ($this->schedules as $schedule) {
            if (!empty($schedule['day']) && !empty($schedule['start_time']) && !empty($schedule['end_time']) && !empty($schedule['subject_id'])) {
                $data = [
                    'day' => $schedule['day'],
                    'start_time' => $schedule['start_time'],
                    'end_time' => $schedule['end_time'],
                    'subject_id' => $schedule['subject_id'],
                ];

                if (isset($schedule['id']) && $schedule['id']) {
                    // Update existing schedule
                    $this->group->schedules()->where('id', $schedule['id'])->update($data);
                    $updatedScheduleIds[] = $schedule['id'];
                } else {
                    // Create new schedule
                    $newSchedule = $this->group->schedules()->create($data);
                    $updatedScheduleIds[] = $newSchedule->id;
                }
            }
        }

        // Delete schedules that were removed
        $schedulesToDelete = array_diff($existingScheduleIds, $updatedScheduleIds);
        if (!empty($schedulesToDelete)) {
            $this->group->schedules()->whereIn('id', $schedulesToDelete)->delete();
        }

        $this->updating = false;

        session()->flash('group-success', 'تم تحديث المجموعة بنجاح.');
        $this->redirectRoute('teacher.groups.index', navigate: true);
    }

    public function render()
    {
        $teacherSubjects = TeacherSubject::where('teacher_id', Auth::guard('teacher')->id())->get();

        return view('teacher-panel.pages.group.parts.group-update', [
            'branches' => Branch::where('teacher_id', Auth::guard('teacher')->id())->orderBy('name')->get(),
            'statuses' => GroupStatusEnum::options(),
            'days' => [
                'Saturday' => 'السبت',
                'Sunday' => 'الأحد',
                'Monday' => 'الإثنين',
                'Tuesday' => 'الثلاثاء',
                'Wednesday' => 'الأربعاء',
                'Thursday' => 'الخميس',
                'Friday' => 'الجمعة',
            ],
            'teacherSubjects' => $teacherSubjects,
        ]);
    }
}
