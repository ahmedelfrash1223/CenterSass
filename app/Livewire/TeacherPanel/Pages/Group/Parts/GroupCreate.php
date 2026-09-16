<?php

namespace App\Livewire\TeacherPanel\Pages\Group\Parts;

use App\Enums\Admin\PackageFeatureKeyEnum;
use App\Http\Requests\TeacherPanel\StoreGroupRequest;
use App\Livewire\TeacherPanel\Concerns\HasSubscriptionLimitCheck;
use App\Livewire\TeacherPanel\Concerns\LoadsGroupFormData;
use App\Models\Group;
use App\Models\Teacher;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class GroupCreate extends Component
{
    use HasSubscriptionLimitCheck;
    use LoadsGroupFormData;

    public string $name = '';
    public string $branch_id = '';
    public string $grade = '';
    public string $description = '';
    public int $max_students = 0;
    public string $status = 'active';
    public string $start_date = '';
    public string $end_date = '';
    public array $schedules = [];

    public function mount(): void
    {
        $user = Auth::guard('teacher')->user();

        // Redirect if plan limit reached before showing form
        $count = Group::where('teacher_id', $user->getOwnerId())->count();

        $this->checkLimitOrRedirect(
            PackageFeatureKeyEnum::MAX_GROUPS->value,
            $count,
            'group-error',
            'teacher.groups.index'
        ) ?: $this->addScheduleRow();
    }

    public function addScheduleRow(): void
    {
        $this->schedules[] = [
            'day' => '',
            'start_time' => '',
            'end_time' => '',
            'subject_id' => '',
            'teacher_id' => '',
        ];
    }

    public function removeScheduleRow($index): void
    {
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

    public function save(bool $createNew = false): void
    {
        $user = Auth::guard('teacher')->user();
        $ownerId = $user->getOwnerId();

        // Re-check limit on direct submit
        $count = Group::where('teacher_id', $ownerId)->count();

        if (!$this->checkLimitOrFail(PackageFeatureKeyEnum::MAX_GROUPS->value, $count)) {
            return;
        }

        $request = new StoreGroupRequest();
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

        $group = Group::create([
            'teacher_id' => $ownerId,
            'created_by' => $user->id,
            'branch_id' => $this->branch_id,
            'grade' => $this->grade,
            'name' => $this->name,
            'description' => $this->description ?: null,
            'max_students' => $this->max_students,
            'status' => $this->status,
            'start_date' => $this->start_date ?: null,
            'end_date' => $this->end_date ?: null,
        ]);

        // Create schedules only for fully-filled rows
        foreach ($this->schedules as $schedule) {
            if (
                !empty($schedule['day']) &&
                !empty($schedule['start_time']) &&
                !empty($schedule['end_time']) &&
                !empty($schedule['subject_id']) &&
                !empty($schedule['teacher_id'])
            ) {
                $group->schedules()->create([
                    'day' => $schedule['day'],
                    'start_time' => $schedule['start_time'],
                    'end_time' => $schedule['end_time'],
                    'subject_id' => $schedule['subject_id'],
                    'teacher_id' => $schedule['teacher_id'],
                ]);
            }
        }

        if ($createNew) {
            $this->reset(['name', 'branch_id', 'grade', 'description', 'max_students', 'status', 'start_date', 'end_date']);
            $this->status = 'active';
            $this->schedules = [];
            $this->addScheduleRow();

            // If newly created hits the limit, bounce back
            $newCount = Group::where('teacher_id', $ownerId)->count();

            if (!$this->checkLimitOrRedirect(
                PackageFeatureKeyEnum::MAX_GROUPS->value,
                $newCount,
                'group-error',
                'teacher.groups.index'
            )) {
                session()->flash('group-success', 'تم إنشاء المجموعة بنجاح.');
                return;
            }

            $this->dispatch('show-success-message', message: 'تم إنشاء المجموعة بنجاح. يمكنك إضافة مجموعة أخرى.');
            return;
        }

        session()->flash('group-success', 'تم إنشاء المجموعة بنجاح.');
        $this->redirect(route('teacher.groups.index'), navigate: true);
    }

    public function render()
    {
        $user = Auth::guard('teacher')->user();
        $count = Group::where('teacher_id', $user->getOwnerId())->count();

        return view('teacher-panel.pages.group.parts.group-create', array_merge(
            $this->loadGroupFormData(),
            [
                'limit' => $this->getLimitData(PackageFeatureKeyEnum::MAX_GROUPS->value, $count),
            ]
        ));
    }
}
