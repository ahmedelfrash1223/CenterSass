<?php

namespace App\Livewire\TeacherPanel\Pages\Student\Parts;

use App\Enums\Teacher\TeacherPermissionEnum;
use App\Http\Requests\TeacherPanel\UpdateStudentRequest;
use App\Livewire\TeacherPanel\Concerns\HasStudentGroupSync;
use App\Livewire\TeacherPanel\Concerns\HasStudentGroupValidation;
use App\Livewire\TeacherPanel\Concerns\LoadsStudentFormData;
use App\Models\Student;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class StudentUpdate extends Component
{
    use HasStudentGroupValidation;
    use HasStudentGroupSync;
    use LoadsStudentFormData;

    public Student $student;
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

    public function mount(Student $student): void
    {
        $user = Auth::guard('teacher')->user();

        abort_unless(
            $user->hasPermission(TeacherPermissionEnum::STUDENT_UPDATE),
            403
        );

        // Student must belong to owner account
        abort_unless($student->teacher_id === $user->getOwnerId(), 404);

        // Non-owner: student must be in accessible branches
        if (!$user->isOwner()) {
            abort_unless(
                in_array($student->branch_id, $user->getAccessibleBranchIds()),
                404
            );
        }

        $student->load('creator');

        $this->student = $student;
        $this->name = $student->name;
        $this->username = $student->username;
        $this->email = $student->email;
        $this->phone = $student->phone ?? '';
        $this->grade = $student->grade ?? '';
        $this->branch_id = (string) ($student->branch_id ?? '');
        $this->is_active = (int) $student->is_active;

        // Pre-load active group ids as strings for checkbox binding
        $this->group_ids = $student->activeGroups()
            ->pluck('groups.id')
            ->map(fn($id) => (string) $id)
            ->toArray();
    }

    public function update(): void
    {
        $user = Auth::guard('teacher')->user();

        abort_unless(
            $user->hasPermission(TeacherPermissionEnum::STUDENT_UPDATE),
            403
        );

        // Re-check ownership and access on update
        abort_unless($this->student->teacher_id === $user->getOwnerId(), 404);

        if (!$user->isOwner()) {
            abort_unless(
                in_array($this->student->branch_id, $user->getAccessibleBranchIds()),
                404
            );
        }

        $request = new UpdateStudentRequest();
        $request->merge(['student_id' => $this->student->id]);
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

        // Validate group capacity, excluding current student from already-joined groups
        $capacityErrors = $this->validateGroupCapacity($this->student->id);
        if (!empty($capacityErrors)) {
            $this->failWithCapacityErrors($capacityErrors);
            return;
        }

        $data = [
            'name' => $this->name,
            'username' => $this->username,
            'email' => $this->email,
            'phone' => $this->phone ?: null,
            'grade' => $this->grade,
            'branch_id' => $this->branch_id,
            'is_active' => (int) $this->is_active,
        ];

        if (!empty($this->password)) {
            $data['password'] = Hash::make($this->password);
        }

        $this->student->update($data);

        // Sync groups (removes unchecked ones)
        $this->syncStudentGroups($this->student, $this->group_ids);

        session()->flash('student-success', 'تم تحديث بيانات الطالب بنجاح.');
        $this->redirect(route('teacher.students.index'), navigate: true);
    }

    public function render()
    {
        return view('teacher-panel.pages.student.parts.student-update',
            $this->loadStudentFormData()
        );
    }
}
