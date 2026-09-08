<?php

namespace App\Livewire\TeacherPanel\Pages\Group\Parts;

use App\Enums\Teacher\GroupStatusEnum;
use App\Http\Requests\TeacherPanel\StoreGroupRequest;
use App\Models\Group;
use App\Models\Branch;
use App\Models\TeacherSubject;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class GroupCreate extends Component
{
    public string $name = '';
    public string $branch_id = '';
    public string $description = '';
    public int $max_students = 0;
    public string $status = 'active';
    public string $start_date = '';
    public string $end_date = '';
    public array $schedules = [];

    public bool $saving = false;
    public bool $savingAndNew = false;

    public function mount(): void
    {
        // Add one empty schedule row by default
        $this->addScheduleRow();
    }

    public function addScheduleRow(): void
    {
        $this->schedules[] = [
            'day' => '',
            'start_time' => '',
            'end_time' => '',
            'subject_id' => '',
        ];
    }

    public function removeScheduleRow($index): void
    {
        unset($this->schedules[$index]);
        $this->schedules = array_values($this->schedules);
    }

    public function save(bool $createNew = false): void
    {
        $createNew ? $this->savingAndNew = true : $this->saving = true;

        $request = new StoreGroupRequest();
        $this->validate($request->rules(), $request->messages());

        // Check package limits - MAX_GROUPS = 2
        $teacher = Auth::guard('teacher')->user();
        $subscription = $teacher->activeSubscription;

        if ($subscription) {
            $maxGroups = $subscription->features()
                ->where('package_feature_key', 2) // MAX_GROUPS
                ->value('value');

            if ($maxGroups && $maxGroups > 0) {
                $currentGroups = Group::where('teacher_id', $teacher->id)->count();
                if ($currentGroups >= $maxGroups) {
                    $this->addError('name', 'لقد تجاوزت الحد الأقصى للمجموعات المسموح بها في باقتك.');
                    $this->saving = false;
                    $this->savingAndNew = false;
                    return;
                }
            }
        }

        // Create group
        $group = Group::create([
            'teacher_id' => Auth::guard('teacher')->id(),
            'branch_id' => $this->branch_id,
            'name' => $this->name,
            'description' => $this->description ?: null,
            'max_students' => $this->max_students,
            'status' => $this->status,
            'start_date' => $this->start_date ?: null,
            'end_date' => $this->end_date ?: null,
        ]);

        // Create schedules
        foreach ($this->schedules as $schedule) {
            if (!empty($schedule['day']) && !empty($schedule['start_time']) && !empty($schedule['end_time']) && !empty($schedule['subject_id'])) {
                $group->schedules()->create([
                    'day' => $schedule['day'],
                    'start_time' => $schedule['start_time'],
                    'end_time' => $schedule['end_time'],
                    'subject_id' => $schedule['subject_id'],
                ]);
            }
        }

        $this->saving = false;
        $this->savingAndNew = false;

        if ($createNew) {
            $this->reset(['name', 'branch_id', 'description', 'max_students', 'status', 'start_date', 'end_date']);
            $this->status = 'active';
            $this->schedules = [];
            $this->addScheduleRow();
            session()->flash('group-success', 'تم إنشاء المجموعة بنجاح. يمكنك إضافة مجموعة أخرى.');
            return;
        }

        session()->flash('group-success', 'تم إنشاء المجموعة بنجاح.');
        $this->redirectRoute('teacher.groups.index', navigate: true);
    }

    public function render()
    {
        $teacherSubjects = TeacherSubject::where('teacher_id', Auth::guard('teacher')->id())->get();

        return view('teacher-panel.pages.group.parts.group-create', [
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
