<?php

namespace App\Livewire\TeacherPanel\Pages\Student\Parts;

use App\Enums\Teacher\TeacherPermissionEnum;
use App\Http\Requests\TeacherPanel\UpdateStudentRequest;
use App\Livewire\TeacherPanel\Concerns\Shared\HasStudentGroupSync;
use App\Livewire\TeacherPanel\Concerns\Shared\HasStudentGroupValidation;
use App\Livewire\TeacherPanel\Concerns\Shared\LoadsStudentFormData;
use App\Models\Student;
use App\Models\StudentProfile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class StudentUpdate extends Component
{
    use HasStudentGroupSync;
    use HasStudentGroupValidation;
    use LoadsStudentFormData;

    public Student $student;

    public string $name = '';

    public string $phone = '';

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

    public function mount(Student $student): void
    {
        $user = Auth::guard('teacher')->user();

        abort_unless(
            $user->hasPermission(TeacherPermissionEnum::STUDENT_UPDATE),
            403
        );

        abort_unless($student->teacher_id === $user->getOwnerId(), 404);

        if (! $user->isOwner()) {
            abort_unless(
                in_array($student->branch_id, $user->getAccessibleBranchIds()),
                404
            );
        }

        $student->load(['creator', 'profile']);

        $this->student = $student;
        $this->name = $student->name;
        $this->phone = $student->phone ?? '';
        $this->grade = $student->grade ?? '';
        $this->branch_id = (string) ($student->branch_id ?? '');
        $this->is_active = (int) $student->is_active;

        $this->group_ids = $student->activeGroups()
            ->pluck('groups.id')
            ->map(fn ($id) => (string) $id)
            ->toArray();

        if ($student->profile) {
            $this->guardian_name = $student->profile->guardian_name ?? '';
            $this->guardian_phone = $student->profile->guardian_phone ?? '';
            $this->guardian_relation = $student->profile->guardian_relation ?? '';
            $this->school_name = $student->profile->school_name ?? '';
            $this->address = $student->profile->address ?? '';
            $this->city = $student->profile->city ?? '';
            $this->notes = $student->profile->notes ?? '';

            if ($student->profile->guardian_name || $student->profile->school_name) {
                $this->sectionProfile = true;
            }
        }
    }

    public function toggleSection(string $section): void
    {
        $this->{$section} = ! $this->{$section};
    }

    public function updatedGrade(): void
    {
        $this->group_ids = [];
    }

    public function updatedBranchId(): void
    {
        $this->group_ids = [];
    }

    public function update(): void
    {
        $user = Auth::guard('teacher')->user();

        abort_unless(
            $user->hasPermission(TeacherPermissionEnum::STUDENT_UPDATE),
            403
        );

        abort_unless($this->student->teacher_id === $user->getOwnerId(), 404);

        if (! $user->isOwner()) {
            abort_unless(
                in_array($this->student->branch_id, $user->getAccessibleBranchIds()),
                404
            );
        }

        $request = new UpdateStudentRequest;
        $request->merge(['student_id' => $this->student->id]);

        try {
            $this->validate($request->rules(), $request->messages());
        } catch (ValidationException $e) {
            $this->openSectionsWithErrors();
            throw $e;
        }

        if (! $user->hasAccessToBranch((int) $this->branch_id)) {
            $this->addError('branch_id', 'الفرع غير متاح لك.');
            $this->sectionBasic = true;

            return;
        }

        if (! $this->ensureGroupsAuthorized($this->group_ids)) {
            $this->sectionGroups = true;

            return;
        }

        $capacityErrors = $this->validateGroupCapacity($this->student->id);
        if (! empty($capacityErrors)) {
            $this->sectionGroups = true;
            $this->failWithCapacityErrors($capacityErrors);

            return;
        }

        DB::transaction(function () {
            $this->student->update([
                'name' => $this->name,
                'phone' => $this->phone ?: null,
                'grade' => $this->grade,
                'branch_id' => $this->branch_id,
                'is_active' => (int) $this->is_active,
            ]);

            $this->saveProfile($this->student);
            $this->syncStudentGroups($this->student, $this->group_ids);
        });

        session()->flash('student-success', 'تم تحديث بيانات الطالب بنجاح.');
        $this->redirect(route('teacher.students.index'), navigate: true);
    }

    protected function saveProfile(Student $student): void
    {
        StudentProfile::updateOrCreate(
            ['student_id' => $student->id],
            [
                'guardian_name' => $this->guardian_name ?: null,
                'guardian_phone' => $this->guardian_phone ?: null,
                'guardian_relation' => $this->guardian_relation ?: null,
                'school_name' => $this->school_name ?: null,
                'address' => $this->address ?: null,
                'city' => $this->city ?: null,
                'notes' => $this->notes ?: null,
            ]
        );
    }

    protected function openSectionsWithErrors(): void
    {
        $errorFields = array_keys($this->getErrorBag()->toArray());

        $sectionMap = [
            'sectionBasic' => ['name', 'phone', 'grade', 'branch_id', 'is_active'],
            'sectionProfile' => ['guardian_name', 'guardian_phone', 'guardian_relation', 'school_name', 'address', 'city', 'notes'],
            'sectionGroups' => ['group_ids'],
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
        return view('teacher-panel.pages.student.parts.student-update',
            $this->loadStudentFormData(
                $this->grade ?: null,
                $this->branch_id ?: null
            )
        );
    }
}
