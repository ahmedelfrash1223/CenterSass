<?php

namespace App\Livewire\TeacherPanel\Pages\CardRequest\Parts;

use App\Enums\Teacher\CardRequestStatusEnum;
use App\Enums\Teacher\GradeEnum;
use App\Enums\Teacher\TeacherPermissionEnum;
use App\Models\Branch;
use App\Models\CardRequest;
use App\Models\CardRequestItem;
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

    public string $unit_price = '0';

    public array $selectedGroupIds = [];

    public array $selectedStudentIds = [];

    public string $notes = '';

    public bool $confirming = false;

    public array $studentsWithExistingCards = [];

    public function mount(): void
    {
        $user = Auth::guard('teacher')->user();

        abort_unless(
            $user->hasPermission(TeacherPermissionEnum::CARD_REQUEST_CREATE),
            403
        );

        $lastRequest = CardRequest::where('teacher_id', $user->getOwnerId())
            ->where('unit_price', '>', 0)
            ->latest('id')
            ->first();

        $this->unit_price = $lastRequest
            ? (string) $lastRequest->unit_price
            : '0';
    }

    // ============================================
    // Data
    // ============================================

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
        return $group->students()
            ->wherePivot('status', 'active')
            ->orderBy('name')
            ->get();
    }

    public function allStudentsFromSelectedGroups()
    {
        if (empty($this->selectedGroupIds)) {
            return collect();
        }

        $groups = Group::whereIn('id', $this->selectedGroupIds)->get();

        $studentIds = collect();
        foreach ($groups as $group) {
            $studentIds = $studentIds->merge(
                $group->students()->wherePivot('status', 'active')->pluck('students.id')
            );
        }

        $studentIds = $studentIds->unique()->values();

        return Student::whereIn('id', $studentIds)
            ->orderBy('name')
            ->get();
    }

    // ============================================
    // Filters
    // ============================================

    public function updatedBranchFilter(): void
    {
        $this->gradeFilter = '';
        $this->selectedGroupIds = [];
        $this->selectedStudentIds = [];
    }

    public function updatedGradeFilter(): void
    {
        $this->selectedGroupIds = [];
        $this->selectedStudentIds = [];
    }

    public function updatedGroupSearch(): void
    {
        $this->selectedGroupIds = [];
        $this->selectedStudentIds = [];
    }

    // ============================================
    // Selection
    // ============================================

    public function toggleGroup(int $groupId): void
    {
        if (in_array($groupId, $this->selectedGroupIds)) {
            $this->selectedGroupIds = array_values(
                array_diff($this->selectedGroupIds, [$groupId])
            );
        } else {
            $this->selectedGroupIds[] = $groupId;
        }

        $this->rebuildSelectedStudents();
    }

    protected function rebuildSelectedStudents(): void
    {
        if (empty($this->selectedGroupIds)) {
            $this->selectedStudentIds = [];

            return;
        }

        $students = $this->allStudentsFromSelectedGroups();
        $this->selectedStudentIds = $students->pluck('id')->map(fn ($id) => (int) $id)->toArray();
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

    public function selectAllStudents(): void
    {
        $students = $this->allStudentsFromSelectedGroups();
        $this->selectedStudentIds = $students->pluck('id')->map(fn ($id) => (int) $id)->toArray();
    }

    public function deselectAllStudents(): void
    {
        $this->selectedStudentIds = [];
    }

    public function clearAll(): void
    {
        $this->selectedGroupIds = [];
        $this->selectedStudentIds = [];
    }

    // ============================================
    // Confirm & Save
    // ============================================

    public function confirm(): void
    {
        if (empty($this->selectedStudentIds)) {
            $this->dispatch('show-error-message', message: 'اختر طالب واحد على الأقل.');

            return;
        }

        if ((float) $this->unit_price <= 0) {
            $this->dispatch('show-error-message', message: 'أدخل سعر الكارت.');

            return;
        }

        $this->studentsWithExistingCards = CardRequestItem::whereIn('student_id', $this->selectedStudentIds)
            ->whereHas('cardRequest', function ($q) {
                $q->where('teacher_id', Auth::guard('teacher')->user()->getOwnerId())
                    ->whereNotIn('status', [CardRequestStatusEnum::REJECTED->value]);
            })
            ->pluck('student_id')
            ->unique()
            ->toArray();

        $this->confirming = true;
    }

    public function cancelConfirm(): void
    {
        $this->confirming = false;
        $this->studentsWithExistingCards = [];
    }

    public function save(): void
    {
        $user = Auth::guard('teacher')->user();
        $ownerId = $user->getOwnerId();

        if (empty($this->selectedStudentIds)) {
            $this->dispatch('show-error-message', message: 'اختر طالب واحد على الأقل.');

            return;
        }

        if ((float) $this->unit_price <= 0) {
            $this->dispatch('show-error-message', message: 'أدخل سعر الكارت.');

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

        $unitPrice = (float) $this->unit_price;
        $totalPrice = $unitPrice * $students->count();

        DB::transaction(function () use ($user, $ownerId, $students, $unitPrice, $totalPrice) {
            $request = CardRequest::create([
                'teacher_id' => $ownerId,
                'created_by' => $user->id,
                'status' => CardRequestStatusEnum::PENDING->value,
                'cards_count' => $students->count(),
                'unit_price' => $unitPrice,
                'total_price' => $totalPrice,
                'paid_amount' => 0,
                'paid_cards_count' => 0,
                'notes' => $this->notes ?: null,
            ]);

            foreach ($students as $student) {
                $firstGroup = $student->activeGroups()
                    ->whereIn('groups.id', $this->selectedGroupIds)
                    ->first() ?? $student->activeGroups()->first();

                $request->items()->create([
                    'student_id' => $student->id,
                    'student_name' => $student->name,
                    'student_code' => $student->code,
                    'grade' => $student->grade,
                    'branch_id' => $student->branch_id,
                    'group_id' => $firstGroup?->id,
                    'card_price' => $unitPrice,
                    'is_paid' => false,
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

        $allStudents = $this->allStudentsFromSelectedGroups();

        return view('teacher-panel.pages.card-request.parts.card-request-create', [
            'branches' => $this->accessibleBranches(),
            'grades' => $this->gradesWithSubjects(),
            'groups' => $groupsData,
            'allStudents' => $allStudents,
        ]);
    }
}
