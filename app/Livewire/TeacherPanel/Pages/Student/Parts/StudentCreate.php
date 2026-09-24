<?php

namespace App\Livewire\TeacherPanel\Pages\Student\Parts;

use App\Enums\Admin\PackageFeatureKeyEnum;
use App\Enums\Teacher\TeacherPermissionEnum;
use App\Http\Requests\TeacherPanel\StoreStudentRequest;
use App\Livewire\TeacherPanel\Concerns\Shared\HasStudentGroupSync;
use App\Livewire\TeacherPanel\Concerns\Shared\HasStudentGroupValidation;
use App\Livewire\TeacherPanel\Concerns\Shared\HasSubscriptionLimitCheck;
use App\Livewire\TeacherPanel\Concerns\Shared\LoadsStudentFormData;
use App\Models\Student;
use App\Models\StudentProfile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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

    public string $guardian_name = '';
    public string $guardian_phone = '';
    public string $guardian_relation = '';
    public string $school_name = '';
    public string $address = '';
    public string $city = '';
    public string $notes = '';

    public bool $sectionBasic = true;
    public bool $sectionProfile = false;
    public bool $sectionGroups = true;

    public function mount(): void
    {
        $user = Auth::guard('teacher')->user();

        abort_unless(
            $user->hasPermission(TeacherPermissionEnum::STUDENT_CREATE),
            403
        );

        $count = Student::where('teacher_id', $user->getOwnerId())->count();

        $this->checkLimitOrRedirect(
            PackageFeatureKeyEnum::MAX_STUDENTS->value,
            $count,
            'student-error',
            'teacher.students.index'
        );
    }

    public function toggleSection(string $section): void
    {
        $this->{$section} = !$this->{$section};
    }


    public function updatedGrade(): void
    {
        $this->group_ids = [];
    }


    public function updatedBranchId(): void
    {
        $this->group_ids = [];
    }

    public function save(bool $createNew = false): void
    {
        $user = Auth::guard('teacher')->user();
        $ownerId = $user->getOwnerId();

        $count = Student::where('teacher_id', $ownerId)->count();

        if (!$this->checkLimitOrFail(PackageFeatureKeyEnum::MAX_STUDENTS->value, $count)) {
            return;
        }

        if (empty($this->password)) {
            $this->password = self::DEFAULT_PASSWORD;
            $this->password_confirmation = self::DEFAULT_PASSWORD;
        }

        $request = new StoreStudentRequest();

        try {
            $this->validate($request->rules(), $request->messages());
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->openSectionsWithErrors();
            throw $e;
        }

        if (!$user->hasAccessToBranch((int) $this->branch_id)) {
            $this->addError('branch_id', 'الفرع غير متاح لك.');
            $this->sectionBasic = true;
            return;
        }

        if (!$this->ensureGroupsAuthorized($this->group_ids)) {
            $this->sectionGroups = true;
            return;
        }

        $capacityErrors = $this->validateGroupCapacity();
        if (!empty($capacityErrors)) {
            $this->sectionGroups = true;
            $this->failWithCapacityErrors($capacityErrors);
            return;
        }

        DB::transaction(function () use ($user, $ownerId) {
            $student = Student::create([
                'name'       => $this->name,
                'username'   => $this->username,
                'email'      => $this->email,
                'phone'      => $this->phone ?: null,
                'password'   => Hash::make($this->password),
                'grade'      => $this->grade,
                'branch_id'  => $this->branch_id,
                'teacher_id' => $ownerId,
                'created_by' => $user->id,
                'is_active'  => (int) $this->is_active,
            ]);

            $this->saveProfile($student);
            $this->syncStudentGroups($student, $this->group_ids);
        });

        if ($createNew) {
            $this->reset([
                'name', 'username', 'email', 'phone',
                'password', 'password_confirmation',
                'grade', 'branch_id', 'group_ids',
                'guardian_name', 'guardian_phone', 'guardian_relation',
                'school_name', 'address', 'city', 'notes',
            ]);
            $this->is_active = 1;

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

    protected function saveProfile(Student $student): void
    {
        if (
            empty($this->guardian_name)
            && empty($this->guardian_phone)
            && empty($this->guardian_relation)
            && empty($this->school_name)
            && empty($this->address)
            && empty($this->city)
            && empty($this->notes)
        ) {
            return;
        }

        StudentProfile::updateOrCreate(
            ['student_id' => $student->id],
            [
                'guardian_name'     => $this->guardian_name ?: null,
                'guardian_phone'    => $this->guardian_phone ?: null,
                'guardian_relation' => $this->guardian_relation ?: null,
                'school_name'       => $this->school_name ?: null,
                'address'           => $this->address ?: null,
                'city'              => $this->city ?: null,
                'notes'             => $this->notes ?: null,
            ]
        );
    }

    protected function openSectionsWithErrors(): void
    {
        $errorFields = array_keys($this->getErrorBag()->toArray());

        $sectionMap = [
            'sectionBasic'   => ['name', 'username', 'email', 'phone', 'password', 'password_confirmation', 'grade', 'branch_id', 'is_active'],
            'sectionProfile' => ['guardian_name', 'guardian_phone', 'guardian_relation', 'school_name', 'address', 'city', 'notes'],
            'sectionGroups'  => ['group_ids'],
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

    public function render()
    {
        $user = Auth::guard('teacher')->user();
        $count = Student::where('teacher_id', $user->getOwnerId())->count();

        return view('teacher-panel.pages.student.parts.student-create', array_merge(
            $this->loadStudentFormData(
                $this->grade ?: null,
                $this->branch_id ?: null
            ),
            [
                'limit' => $this->getLimitData(PackageFeatureKeyEnum::MAX_STUDENTS->value, $count),
            ]
        ));
    }
}
