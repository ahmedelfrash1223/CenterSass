<?php

namespace App\Livewire\TeacherPanel\Pages\Staff\Parts;

use App\Enums\Teacher\GradeEnum;
use App\Enums\Teacher\TeacherPermissionEnum;
use App\Enums\Teacher\TeacherRoleEnum;
use App\Http\Requests\TeacherPanel\UpdateStaffRequest;
use App\Livewire\TeacherPanel\Concerns\Shared\HasStaffBranchSync;
use App\Livewire\TeacherPanel\Concerns\Shared\LoadsStaffFormData;
use App\Models\Teacher;
use App\Models\TeacherGrade;
use App\Models\TeacherSubject;
use App\Traits\Teacher\HasPermissionSelection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class StaffUpdate extends Component
{
    use HasPermissionSelection;
    use HasStaffBranchSync;
    use LoadsStaffFormData;

    public Teacher $teacher;
    public string $name = '';
    public string $username = '';
    public string $email = '';
    public string $phone = '';
    public string $password = '';
    public string $password_confirmation = '';
    public int $is_active = 1;
    public int $role = 2;

    public array $branch_ids = [];

    public array $assigned_grades = [];
    public array $assigned_subjects = [];
    public array $availableSubjectsFiltered = [];

    public bool $sectionBasic = true;
    public bool $sectionGrades = true;
    public bool $sectionBranches = true;
    public bool $sectionPermissions = true;

    public function toggleSection(string $section): void
    {
        $this->{$section} = !$this->{$section};
    }

    public function mount(Teacher $teacher): void
    {
        $user = Auth::guard('teacher')->user();

        abort_unless(
            $user->hasPermission(TeacherPermissionEnum::STAFF_UPDATE),
            403
        );

        abort_unless(
            $user->hasAccessToTeacher($teacher),
            404
        );

        $teacher->load('creator');

        $this->teacher = $teacher;
        $this->name = $teacher->name;
        $this->username = $teacher->username;
        $this->email = $teacher->email;
        $this->phone = $teacher->phone ?? '';
        $this->is_active = (int) $teacher->is_active;
        $this->role = (int) $teacher->role->value;

        $this->branch_ids = $teacher->branches()
            ->pluck('branches.id')
            ->map(fn($id) => (string) $id)
            ->toArray();

        $this->assigned_grades = TeacherGrade::where('teacher_id', $teacher->id)
            ->pluck('grade')
            ->map(fn($g) => (string) $g)
            ->toArray();

        $this->assigned_subjects = DB::table('teacher_grade_subjects')
            ->where('teacher_id', $teacher->id)
            ->pluck('teacher_subject_id')
            ->map(fn($id) => (string) $id)
            ->toArray();

        $this->syncFilteredSubjects();

        $this->groupedPermissions = TeacherPermissionEnum::grouped();
        $this->initializePermissions();

        foreach ($teacher->getAllPermissions() as $permission) {
            if (isset($this->permissions[$permission])) {
                $this->permissions[$permission] = true;
            }
        }
    }

    public function updatedRole(): void
    {
        $this->initializePermissions();

        if ((int) $this->role !== TeacherRoleEnum::STAFF->value) {
            $this->assigned_grades = [];
            $this->assigned_subjects = [];
            $this->availableSubjectsFiltered = [];
        }
    }

    public function updatedAssignedGrades(): void
    {
        $this->syncFilteredSubjects();

        $this->assigned_subjects = array_values(array_filter(
            $this->assigned_subjects,
            fn($id) => collect($this->availableSubjectsFiltered)->contains('id', (int) $id)
        ));
    }

    protected function syncFilteredSubjects(): void
    {
        $user = Auth::guard('teacher')->user();
        $ownerId = $user->getOwnerId();

        if (empty($this->assigned_grades)) {
            $this->availableSubjectsFiltered = [];
            return;
        }

        $this->availableSubjectsFiltered = TeacherSubject::query()
            ->where('teacher_id', $ownerId)
            ->where('is_active', 1)
            ->whereIn('grade', $this->assigned_grades)
            ->get()
            ->map(fn($s) => [
                'id'            => $s->id,
                'grade'         => $s->grade,
                'grade_label'   => GradeEnum::tryFrom($s->grade)?->label() ?? $s->grade,
                'subject_label' => $s->subject_label,
            ])
            ->values()
            ->toArray();
    }

    public function selectAllGrades(): void
    {
        $availableGrades = TeacherSubject::query()
            ->where('teacher_id', Auth::guard('teacher')->user()->getOwnerId())
            ->where('is_active', 1)
            ->whereNotNull('grade')
            ->distinct()
            ->pluck('grade')
            ->map(fn($g) => (string) $g)
            ->toArray();

        $this->assigned_grades = $availableGrades;
        $this->updatedAssignedGrades();
    }

    public function deselectAllGrades(): void
    {
        $this->assigned_grades = [];
        $this->assigned_subjects = [];
        $this->availableSubjectsFiltered = [];
    }

    public function selectAllSubjects(): void
    {
        $this->assigned_subjects = collect($this->availableSubjectsFiltered)
            ->pluck('id')
            ->map(fn($i) => (string) $i)
            ->toArray();
    }

    public function deselectAllSubjects(): void
    {
        $this->assigned_subjects = [];
    }

    // ============================================
    // ✅ Section auto-open on errors
    // ============================================

    protected function openSectionsWithErrors(): void
    {
        $errorFields = array_keys($this->getErrorBag()->toArray());

        $sectionMap = [
            'sectionBasic' => [
                'name', 'username', 'email', 'phone', 'password',
                'password_confirmation', 'is_active', 'role',
            ],
            'sectionGrades' => [
                'assigned_grades', 'assigned_subjects', 'availableSubjectsFiltered',
            ],
            'sectionBranches' => [
                'branch_ids',
            ],
            'sectionPermissions' => [
                'permissions',
            ],
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

    // ============================================
    // Update
    // ============================================

    public function update(): void
    {
        $user = Auth::guard('teacher')->user();

        abort_unless(
            $user->hasPermission(TeacherPermissionEnum::STAFF_UPDATE),
            403
        );

        abort_unless(
            $user->hasAccessToTeacher($this->teacher),
            404
        );

        $request = new UpdateStaffRequest();
        $request->merge(['teacher_id' => $this->teacher->id]);

        try {
            $this->validate($request->rules(), $request->messages());
        } catch (ValidationException $e) {
            $this->openSectionsWithErrors();
            throw $e;
        }

        if (!$this->ensureBranchesAuthorized($this->branch_ids)) {
            $this->sectionBranches = true;
            return;
        }

        DB::transaction(function () use ($user) {
            $data = [
                'name'      => $this->name,
                'username'  => $this->username,
                'email'     => $this->email,
                'phone'     => $this->phone ?: null,
                'role'      => (int) $this->role,
                'is_active' => (int) $this->is_active,
            ];

            if (!empty($this->password)) {
                $data['password'] = Hash::make($this->password);
            }

            $this->teacher->update($data);

            if (!$this->teacher->isOwner()) {
                $this->teacher->syncPermissions($this->getSelectedPermissions());
                $this->teacher->syncBranches($this->branch_ids, $user->id);

                if ((int) $this->role === TeacherRoleEnum::STAFF->value) {
                    $this->syncTeacherGradesAndSubjects($this->teacher);
                } else {
                    TeacherGrade::where('teacher_id', $this->teacher->id)->delete();
                    DB::table('teacher_grade_subjects')->where('teacher_id', $this->teacher->id)->delete();
                }
            }
        });

        session()->flash('staff-success', 'تم تحديث بيانات العضو والصلاحيات بنجاح.');
        $this->redirect(route('teacher.staff.index'), navigate: true);
    }

    protected function syncTeacherGradesAndSubjects(Teacher $teacher): void
    {
        TeacherGrade::where('teacher_id', $teacher->id)->delete();
        DB::table('teacher_grade_subjects')->where('teacher_id', $teacher->id)->delete();

        foreach ($this->assigned_grades as $grade) {
            TeacherGrade::create([
                'teacher_id' => $teacher->id,
                'grade'      => $grade,
            ]);
        }

        foreach ($this->assigned_subjects as $subjectId) {
            DB::table('teacher_grade_subjects')->insert([
                'teacher_id'         => $teacher->id,
                'teacher_subject_id' => $subjectId,
                'created_at'         => now(),
                'updated_at'         => now(),
            ]);
        }
    }

    public function render()
    {
        return view('teacher-panel.pages.staff.parts.staff-update',
            $this->loadStaffFormData(includeLimit: false)
        );
    }
}
