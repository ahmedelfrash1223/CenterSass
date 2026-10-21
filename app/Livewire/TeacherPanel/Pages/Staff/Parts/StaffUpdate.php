<?php

namespace App\Livewire\TeacherPanel\Pages\Staff\Parts;

use App\Enums\Teacher\GradeEnum;
use App\Enums\Teacher\SubjectEnum;
use App\Enums\Teacher\TeacherPermissionEnum;
use App\Enums\Teacher\TeacherRoleEnum;
use App\Http\Requests\TeacherPanel\UpdateStaffRequest;
use App\Livewire\TeacherPanel\Concerns\Shared\HasStaffBranchSync;
use App\Livewire\TeacherPanel\Concerns\Shared\LoadsStaffFormData;
use App\Models\Branch;
use App\Models\Teacher;
use App\Models\TeacherBranchPayrollSetting;
use App\Models\TeacherGrade;
use App\Models\TeacherPayrollSetting;
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

    // Payroll
    public string $payment_type = 'monthly';

    public string $monthly_salary = '0';

    public string $per_session_rate = '0';

    public string $hourly_rate = '0';

    public string $session_deduction_rate = '0';

    public int $pay_day = 1;

    public array $branchPayrollSettings = [];

    public bool $sectionBasic = true;

    public bool $sectionGrades = false;

    public bool $sectionBranches = false;

    public bool $sectionPermissions = false;

    public bool $sectionPayroll = false;

    public array $sectionsWithErrors = [];

    public function toggleSection(string $section): void
    {
        $this->{$section} = ! $this->{$section};
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
            ->map(fn ($id) => (string) $id)
            ->toArray();

        $this->assigned_grades = TeacherGrade::where('teacher_id', $teacher->id)
            ->pluck('grade')
            ->map(fn ($g) => (string) $g)
            ->toArray();

        $this->assigned_subjects = DB::table('teacher_grade_subjects')
            ->where('teacher_id', $teacher->id)
            ->pluck('teacher_subject_id')
            ->map(fn ($id) => (string) $id)
            ->toArray();

        $this->syncFilteredSubjects();

        $this->groupedPermissions = TeacherPermissionEnum::grouped();
        $this->initializePermissions();

        foreach ($teacher->getAllPermissions() as $permission) {
            if (isset($this->permissions[$permission])) {
                $this->permissions[$permission] = true;
            }
        }

        $this->sectionBasic = true;
        $this->sectionGrades = false;
        $this->sectionBranches = false;
        $this->sectionPermissions = false;
        $this->sectionPayroll = false;

        $this->loadExistingPayrollSettings($teacher);
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
            fn ($id) => collect($this->availableSubjectsFiltered)->contains('id', (int) $id)
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
            ->map(fn ($s) => [
                'id' => $s->id,
                'grade' => $s->grade,
                'grade_label' => GradeEnum::tryFrom($s->grade)?->label() ?? $s->grade,
                'subject_label' => SubjectEnum::tryFrom($s->subject)?->label() ?? $s->subject,
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
            ->map(fn ($g) => (string) $g)
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
            ->map(fn ($i) => (string) $i)
            ->toArray();
    }

    public function deselectAllSubjects(): void
    {
        $this->assigned_subjects = [];
    }

    // ============================================
    // Payroll
    // ============================================

    protected function loadExistingPayrollSettings(Teacher $teacher): void
    {
        $user = Auth::guard('teacher')->user();
        $ownerId = $user->getOwnerId();

        $setting = TeacherPayrollSetting::where('teacher_id', $teacher->id)->first();
        if ($setting) {
            $this->payment_type = $setting->payment_type->value;
            $this->monthly_salary = (string) $setting->monthly_salary;
            $this->per_session_rate = (string) $setting->per_session_rate;
            $this->hourly_rate = (string) $setting->hourly_rate;
            $this->session_deduction_rate = (string) $setting->session_deduction_rate;
            $this->pay_day = (int) ($setting->pay_day ?? 1);
        }

        $branches = Branch::where('teacher_id', $ownerId)->orderBy('name')->get();
        $this->branchPayrollSettings = [];

        foreach ($branches as $branch) {
            $existing = TeacherBranchPayrollSetting::where('teacher_id', $teacher->id)
                ->where('branch_id', $branch->id)
                ->first();

            $this->branchPayrollSettings[$branch->id] = [
                'branch_name' => $branch->name,
                'enabled' => (bool) $existing,
                'payment_type' => $existing?->payment_type?->value ?? $this->payment_type,
                'monthly_salary' => (string) ($existing?->monthly_salary ?? 0),
                'per_session_rate' => (string) ($existing?->per_session_rate ?? 0),
                'hourly_rate' => (string) ($existing?->hourly_rate ?? 0),
                'session_deduction_rate' => (string) ($existing?->session_deduction_rate ?? 0),
            ];
        }
    }

    protected function savePayrollSettings(Teacher $teacher): void
    {
        TeacherPayrollSetting::updateOrCreate(
            ['teacher_id' => $teacher->id],
            [
                'payment_type' => $this->payment_type,
                'monthly_salary' => (float) $this->monthly_salary,
                'per_session_rate' => (float) $this->per_session_rate,
                'hourly_rate' => (float) $this->hourly_rate,
                'session_deduction_rate' => (float) $this->session_deduction_rate,
                'pay_day' => $this->pay_day,
                'is_active' => true,
            ]
        );

        foreach ($this->branchPayrollSettings as $branchId => $data) {
            if (! empty($data['enabled'])) {
                TeacherBranchPayrollSetting::updateOrCreate(
                    ['teacher_id' => $teacher->id, 'branch_id' => $branchId],
                    [
                        'payment_type' => $data['payment_type'],
                        'monthly_salary' => (float) $data['monthly_salary'],
                        'per_session_rate' => (float) $data['per_session_rate'],
                        'hourly_rate' => (float) $data['hourly_rate'],
                        'session_deduction_rate' => (float) $data['session_deduction_rate'],
                        'is_active' => true,
                    ]
                );
            } else {
                TeacherBranchPayrollSetting::where('teacher_id', $teacher->id)
                    ->where('branch_id', $branchId)
                    ->delete();
            }
        }
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
            'sectionPayroll' => [
                'payment_type', 'monthly_salary', 'per_session_rate',
                'hourly_rate', 'session_deduction_rate', 'pay_day',
                'branchPayrollSettings',
            ],
        ];

        $this->sectionsWithErrors = [];

        foreach ($errorFields as $field) {
            $fieldName = explode('.', $field)[0];

            foreach ($sectionMap as $section => $fields) {
                if (in_array($fieldName, $fields)) {
                    $this->{$section} = true;
                    $this->sectionsWithErrors[] = $section;
                }
            }
        }

        $this->sectionsWithErrors = array_values(array_unique($this->sectionsWithErrors));
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

        $request = new UpdateStaffRequest;
        $request->merge(['teacher_id' => $this->teacher->id]);

        try {
            $this->validate($request->rules(), $request->messages());
        } catch (ValidationException $e) {
            $this->openSectionsWithErrors();
            throw $e;
        }

        if (! $this->ensureBranchesAuthorized($this->branch_ids)) {
            $this->sectionBranches = true;
            $this->sectionsWithErrors[] = 'sectionBranches';

            return;
        }

        DB::transaction(function () use ($user) {
            $data = [
                'name' => $this->name,
                'username' => $this->username,
                'email' => $this->email,
                'phone' => $this->phone ?: null,
                'role' => (int) $this->role,
                'is_active' => (int) $this->is_active,
            ];

            if (! empty($this->password)) {
                $data['password'] = Hash::make($this->password);
            }

            $this->teacher->update($data);

            if (! $this->teacher->isOwner()) {
                $this->teacher->syncPermissions($this->getSelectedPermissions());
                $this->teacher->syncBranches($this->branch_ids, $user->id);

                if ((int) $this->role === TeacherRoleEnum::STAFF->value) {
                    $this->syncTeacherGradesAndSubjects($this->teacher);
                } else {
                    TeacherGrade::where('teacher_id', $this->teacher->id)->delete();
                    DB::table('teacher_grade_subjects')->where('teacher_id', $this->teacher->id)->delete();
                }

                $this->savePayrollSettings($this->teacher);
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
                'grade' => $grade,
            ]);
        }

        foreach ($this->assigned_subjects as $subjectId) {
            DB::table('teacher_grade_subjects')->insert([
                'teacher_id' => $teacher->id,
                'teacher_subject_id' => $subjectId,
                'created_at' => now(),
                'updated_at' => now(),
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
