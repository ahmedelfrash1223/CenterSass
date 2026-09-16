<?php

namespace App\Livewire\TeacherPanel\Pages\Student\Parts;

use App\Enums\Admin\PackageFeatureKeyEnum;
use App\Enums\Teacher\TeacherPermissionEnum;
use App\Http\Requests\TeacherPanel\StoreStudentRequest;
use App\Livewire\TeacherPanel\Concerns\HasStudentGroupSync;
use App\Livewire\TeacherPanel\Concerns\HasStudentGroupValidation;
use App\Livewire\TeacherPanel\Concerns\HasSubscriptionLimitCheck;
use App\Livewire\TeacherPanel\Concerns\LoadsStudentFormData;
use App\Models\Student;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class StudentCreate extends Component
{
    use HasSubscriptionLimitCheck;
    use HasStudentGroupValidation;
    use HasStudentGroupSync;
    use LoadsStudentFormData;

    private const DEFAULT_PASSWORD = '12345678';

    public string $name = '';
    public string $username = '';
    public string $email = '';
    public string $phone = '';
    public string $password = '';
    public string $password_confirmation = '';
    public string $grade = '';
    public string $branch_id = '';
    public int $is_active = 1;
    public array $group_ids = [];

    public function mount(): void
    {
        $user = Auth::guard('teacher')->user();

        abort_unless(
            $user->hasPermission(TeacherPermissionEnum::STUDENT_CREATE),
            403
        );

        // Redirect if plan limit is already reached
        $count = Student::where('teacher_id', $user->getOwnerId())->count();

        $this->checkLimitOrRedirect(
            PackageFeatureKeyEnum::MAX_STUDENTS->value,
            $count,
            'student-error',
            'teacher.students.index'
        );
    }

    public function save(bool $createNew = false): void
    {
        $user = Auth::guard('teacher')->user();
        $ownerId = $user->getOwnerId();

        // Re-check plan limit on submit
        $count = Student::where('teacher_id', $ownerId)->count();

        if (!$this->checkLimitOrFail(PackageFeatureKeyEnum::MAX_STUDENTS->value, $count)) {
            return;
        }

        // Apply default password if empty
        if (empty($this->password)) {
            $this->password = self::DEFAULT_PASSWORD;
            $this->password_confirmation = self::DEFAULT_PASSWORD;
        }

        $request = new StoreStudentRequest();
        $this->validate($request->rules(), $request->messages());

        // Validate branch access
        if (!$user->hasAccessToBranch((int) $this->branch_id)) {
            $this->addError('branch_id', 'الفرع غير متاح لك.');
            return;
        }

        // Validate groups belong to accessible branches
        if (!$this->ensureGroupsAuthorized($this->group_ids)) {
            return;
        }

        // Validate group capacity (create: no exclusion)
        $capacityErrors = $this->validateGroupCapacity();
        if (!empty($capacityErrors)) {
            $this->failWithCapacityErrors($capacityErrors);
            return;
        }

        $student = Student::create([
            'name' => $this->name,
            'username' => $this->username,
            'email' => $this->email,
            'phone' => $this->phone ?: null,
            'password' => Hash::make($this->password),
            'grade' => $this->grade,
            'branch_id' => $this->branch_id,
            'teacher_id' => $ownerId,
            'created_by' => $user->id,
            'is_active' => (int) $this->is_active,
        ]);

        // Attach student to selected groups
        $this->syncStudentGroups($student, $this->group_ids);

        if ($createNew) {
            $this->reset(['name', 'username', 'email', 'phone', 'password', 'password_confirmation', 'grade', 'branch_id', 'group_ids']);
            $this->is_active = 1;

            // If new count hits the plan limit, bounce back to index
            $newCount = Student::where('teacher_id', $ownerId)->count();

            if (!$this->checkLimitOrRedirect(
                PackageFeatureKeyEnum::MAX_STUDENTS->value,
                $newCount,
                'student-error',
                'teacher.students.index'
            )) {
                session()->flash('student-success', 'تم إضافة الطالب بنجاح.');
                return;
            }

            $this->dispatch('show-success-message', message: 'تم إضافة الطالب بنجاح. يمكنك إضافة طالب آخر.');
            return;
        }

        session()->flash('student-success', 'تم إضافة الطالب بنجاح.');
        $this->redirect(route('teacher.students.index'), navigate: true);
    }

    public function render()
    {
        $user = Auth::guard('teacher')->user();
        $count = Student::where('teacher_id', $user->getOwnerId())->count();

        return view('teacher-panel.pages.student.parts.student-create', array_merge(
            $this->loadStudentFormData(),
            [
                'limit' => $this->getLimitData(PackageFeatureKeyEnum::MAX_STUDENTS->value, $count),
            ]
        ));
    }
}
