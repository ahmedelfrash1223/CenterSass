<?php

namespace App\Livewire\TeacherPanel\Pages\Staff\Parts;

use App\Enums\Admin\PackageFeatureKeyEnum;
use App\Enums\Teacher\GradeEnum;
use App\Enums\Teacher\TeacherPermissionEnum;
use App\Enums\Teacher\TeacherRoleEnum;
use App\Http\Requests\TeacherPanel\StoreStaffRequest;
use App\Livewire\TeacherPanel\Concerns\Shared\HasStaffBranchSync;
use App\Livewire\TeacherPanel\Concerns\Shared\HasSubscriptionLimitCheck;
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

class StaffCreate extends Component
{
    use HasPermissionSelection;
    use HasSubscriptionLimitCheck;
    use HasStaffBranchSync;
    use LoadsStaffFormData;

    private const DEFAULT_PASSWORD = '12345678';

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

    public function mount(): void
    {
        $user = Auth::guard('teacher')->user();

        abort_unless(
            $user->hasPermission(TeacherPermissionEnum::STAFF_CREATE),
            403
        );

        $count = Teacher::where('owner_id', $user->getOwnerId())->count();

        if (!$this->checkLimitOrRedirect(
            PackageFeatureKeyEnum::MAX_EMPLOYEES->value,
            $count,
            'staff-error',
            'teacher.staff.index'
        )) {
            return;
        }

        $this->groupedPermissions = TeacherPermissionEnum::grouped();
        $this->initializePermissions();
        $this->syncFilteredSubjects();
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

    // ============================================
    // Grades & Subjects
    // ============================================

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
    // Save
    // ============================================

    public function save(bool $createNew = false): void
    {
        $user = Auth::guard('teacher')->user();
        $ownerId = $user->getOwnerId();

        $count = Teacher::where('owner_id', $ownerId)->count();

        if (!$this->checkLimitOrFail(PackageFeatureKeyEnum::MAX_EMPLOYEES->value, $count)) {
            return;
        }

        if (empty($this->password)) {
            $this->password = self::DEFAULT_PASSWORD;
            $this->password_confirmation = self::DEFAULT_PASSWORD;
        }

        $request = new StoreStaffRequest();

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

        DB::transaction(function () use ($user, $ownerId) {
            $teacher = Teacher::create([
                'name'       => $this->name,
                'username'   => $this->username,
                'email'      => $this->email,
                'phone'      => $this->phone ?: null,
                'password'   => Hash::make($this->password),
                'role'       => (int) $this->role,
                'owner_id'   => $ownerId,
                'created_by' => $user->id,
                'is_active'  => (int) $this->is_active,
            ]);

            if (!$teacher->isOwner()) {
                $teacher->syncPermissions($this->getSelectedPermissions());
                $this->syncStaffBranches($teacher, $this->branch_ids);

                if ((int) $this->role === TeacherRoleEnum::STAFF->value) {
                    $this->syncTeacherGradesAndSubjects($teacher);
                }
            }
        });

        if ($createNew) {
            $this->reset([
                'name', 'username', 'email', 'phone',
                'password', 'password_confirmation',
                'branch_ids', 'assigned_grades', 'assigned_subjects',
                'availableSubjectsFiltered',
            ]);
            $this->is_active = 1;
            $this->role = TeacherRoleEnum::STAFF->value;
            $this->initializePermissions();
            $this->syncFilteredSubjects();

            $newCount = Teacher::where('owner_id', $ownerId)->count();

            if (!$this->checkLimitOrRedirect(
                PackageFeatureKeyEnum::MAX_EMPLOYEES->value,
                $newCount,
                'staff-error',
                'teacher.staff.index'
            )) {
                session()->flash('staff-success', 'تم إضافة عضو الفريق بنجاح.');
                return;
            }

            $this->dispatch('show-success-message', message: 'تم إضافة عضو الفريق بنجاح. يمكنك إضافة عضو آخر.');
            return;
        }

        session()->flash('staff-success', 'تم إضافة عضو الفريق بنجاح.');
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
        return view('teacher-panel.pages.staff.parts.staff-create',
            $this->loadStaffFormData()
        );
    }
}
