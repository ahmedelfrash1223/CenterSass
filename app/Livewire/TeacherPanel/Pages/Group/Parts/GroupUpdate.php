<?php

namespace App\Livewire\TeacherPanel\Pages\Group\Parts;

use App\Http\Requests\TeacherPanel\UpdateGroupRequest;
use App\Livewire\TeacherPanel\Concerns\LoadsGroupFormData;
use App\Models\Group;
use App\Models\Teacher;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class GroupUpdate extends Component
{
    use LoadsGroupFormData;

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

    public function mount(Group $group): void
    {
        $user = Auth::guard('teacher')->user();

        // Group must belong to the same owner account
        abort_unless(
            $group->teacher_id === $user->getOwnerId(),
            404
        );

        // For non-owner, group branch must be within accessible branches
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
        // Delete existing schedule immediately when removed
        if (!empty($this->schedules[$index]['id'])) {
            $this->group->schedules()->where('id', $this->schedules[$index]['id'])->delete();
        }

        unset($this->schedules[$index]);
        $this->schedules = array_values($this->schedules);
    }

    public function updatedGrade(): void
    {
        // Reset selected subjects when grade changes
        foreach ($this->schedules as $index => $schedule) {
            $this->schedules[$index]['subject_id'] = '';
        }
    }

    public function update(): void
    {
        $user = Auth::guard('teacher')->user();

        // Re-check ownership and access on update
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

        if (!$user->hasAccessToBranch((int) $this->branch_id)) {
            $this->addError('branch_id', 'الفرع غير متاح لك.');
            return;
        }

        // Ensure selected teachers belong to accessible scope
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
        ]);

        $existingIds = $this->group->schedules()->pluck('id')->toArray();
        $keptIds = [];

        // Update or create schedules, track ids that survived
        foreach ($this->schedules as $schedule) {
            if (
                !empty($schedule['day']) &&
                !empty($schedule['start_time']) &&
                !empty($schedule['end_time']) &&
                !empty($schedule['subject_id']) &&
                !empty($schedule['teacher_id'])
            ) {
                $data = [
                    'day' => $schedule['day'],
                    'start_time' => $schedule['start_time'],
                    'end_time' => $schedule['end_time'],
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

        // Remove schedules that were in db but not resubmitted
        $toDelete = array_diff($existingIds, $keptIds);
        if (!empty($toDelete)) {
            $this->group->schedules()->whereIn('id', $toDelete)->delete();
        }

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
