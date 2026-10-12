<?php

namespace App\Livewire\TeacherPanel\Pages\CardRequest\Parts;

use App\Enums\Teacher\CardRequestStatusEnum;
use App\Enums\Teacher\GradeEnum;
use App\Enums\Teacher\TeacherPermissionEnum;
use App\Models\Branch;
use App\Models\CardRequest;
use App\Models\Group;
use App\Models\Student;
use App\Models\TeacherSubject;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class CardRequestCreate extends Component
{
    public string $branchFilter = '';

    public string $gradeFilter = '';

    public string $groupSearch = '';

    /** @var array<int> */
    public array $selectedStudentIds = [];

    /** @var array<int, bool> */
    public array $openGroups = [];

    public string $notes = '';

    public bool $confirming = false;

    public function mount(): void
    {
        $user = Auth::guard('teacher')->user();

        abort_unless(
            $user->hasPermission(TeacherPermissionEnum::CARD_REQUEST_CREATE),
            403
        );
    }

    protected function accessibleBranches()
    {
        $user = Auth::guard('teacher')->user();
        $ids = $user->getAccessibleBranchIds();

        return Branch::whereIn('id', $ids)
            ->where('is_active', 1)
            ->orderBy('name')
            ->get();
    }

    protected function gradesWithSubjects(): array
    {
        $user = Auth::guard('teacher')->user();
        $ownerId = $user->getOwnerId();

        $grades = TeacherSubject::where('teacher_id', $ownerId)
            ->distinct()
            ->pluck('grade')
            ->filter()
            ->toArray();

        $options = GradeEnum::options();

        return array_filter(
            $options,
            fn ($label, $value) => in_array($value, $grades),
            ARRAY_FILTER_USE_BOTH
        );
    }

    protected function filteredGroups()
    {
        $user = Auth::guard('teacher')->user();
        $ownerId = $user->getOwnerId();
        $accessibleBranchIds = $user->getAccessibleBranchIds();

        if ($this->branchFilter === '' || ! in_array((int) $this->branchFilter, $accessibleBranchIds)) {
            return collect();
        }

        $query = Group::query()
            ->where('teacher_id', $ownerId)
            ->where('branch_id', (int) $this->branchFilter)
            ->where('status', 'active');

        if ($this->gradeFilter !== '') {
            $query->where('grade', $this->gradeFilter);
        }

        if ($this->groupSearch !== '') {
            $query->where('name', 'like', "%{$this->groupSearch}%");
        }

        return $query->orderBy('name')->get();
    }

    protected function studentsInGroup(Group $group)
    {
        $query = $group->students()
            ->wherePivot('status', 'active')
            ->orderBy('name');

        return $query->get();
    }

    // =============================================
    // Actions
    // =============================================

    public function updatedBranchFilter(): void
    {
        $this->gradeFilter = '';
        $this->selectedStudentIds = [];
        $this->openGroups = [];
    }

    public function updatedGradeFilter(): void
    {
        $this->selectedStudentIds = [];
        $this->openGroups = [];
    }

    public function updatedGroupSearch(): void
    {
        $this->selectedStudentIds = [];
        $this->openGroups = [];
    }

    public function toggleGroup(int $groupId): void
    {
        $this->openGroups[$groupId] = ! ($this->openGroups[$groupId] ?? false);
    }

    public function toggleStudent(int $studentId): void
    {
        if (in_array($studentId, $this->selectedStudentIds)) {
            $this->selectedStudentIds = array_values(
                array_diff($this->selectedStudentIds, [$studentId])
            );
        } else {
            $this->selectedStudentIds[] = $studentId;
        }
    }

    public function selectWholeGroup(int $groupId): void
    {
        $group = $this->filteredGroups()->firstWhere('id', $groupId);
        if (! $group) {
            return;
        }

        $ids = $this->studentsInGroup($group)->pluck('id')->map(fn ($id) => (int) $id)->toArray();

        $this->selectedStudentIds = array_values(array_unique(
            array_merge($this->selectedStudentIds, $ids)
        ));
    }

    public function deselectWholeGroup(int $groupId): void
    {
        $group = $this->filteredGroups()->firstWhere('id', $groupId);
        if (! $group) {
            return;
        }

        $ids = $this->studentsInGroup($group)->pluck('id')->map(fn ($id) => (int) $id)->toArray();

        $this->selectedStudentIds = array_values(
            array_diff($this->selectedStudentIds, $ids)
        );
    }

    public function clearAll(): void
    {
        $this->selectedStudentIds = [];
    }

    public function confirm(): void
    {
        if (empty($this->selectedStudentIds)) {
            $this->dispatch('show-error-message', message: 'اختر طالب واحد على الأقل.');

            return;
        }

        $this->confirming = true;
    }

    public function cancelConfirm(): void
    {
        $this->confirming = false;
    }

    public function save(): void
    {
        $user = Auth::guard('teacher')->user();
        $ownerId = $user->getOwnerId();

        if (empty($this->selectedStudentIds)) {
            $this->dispatch('show-error-message', message: 'اختر طالب واحد على الأقل.');

            return;
        }

        $students = Student::whereIn('id', $this->selectedStudentIds)
            ->where('teacher_id', $ownerId)
            ->get();

        if ($students->count() !== count($this->selectedStudentIds)) {
            $this->dispatch('show-error-message', message: 'بعض الطلاب غير متاحين لك.');

            return;
        }

        if (! $user->isOwner()) {
            $accessibleBranchIds = $user->getAccessibleBranchIds();
            foreach ($students as $student) {
                if (! in_array($student->branch_id, $accessibleBranchIds)) {
                    $this->dispatch('show-error-message', message: 'بعض الطلاب في فروع غير متاحة لك.');

                    return;
                }
            }
        }

        DB::transaction(function () use ($user, $ownerId, $students) {
            $request = CardRequest::create([
                'teacher_id' => $ownerId,
                'created_by' => $user->id,
                'status' => CardRequestStatusEnum::PENDING->value,
                'cards_count' => $students->count(),
                'notes' => $this->notes ?: null,
            ]);

            foreach ($students as $student) {
                $firstGroup = $student->activeGroups()->first();

                $request->items()->create([
                    'student_id' => $student->id,
                    'student_name' => $student->name,
                    'student_code' => $student->code,
                    'grade' => $student->grade,
                    'branch_id' => $student->branch_id,
                    'group_id' => $firstGroup?->id,
                ]);
            }
        });

        session()->flash('card-request-success', 'تم إرسال طلب الكروت بنجاح.');
        $this->redirect(route('teacher.card-requests.index'), navigate: true);
    }

    public function render()
    {
        $groups = $this->filteredGroups();

        $groupsData = $groups->map(function ($group) {
            return [
                'group' => $group,
                'students' => $this->studentsInGroup($group),
            ];
        });

        return view('teacher-panel.pages.card-request.parts.card-request-create', [
            'branches' => $this->accessibleBranches(),
            'grades' => $this->gradesWithSubjects(),
            'groups' => $groupsData,
        ]);
    }
}
