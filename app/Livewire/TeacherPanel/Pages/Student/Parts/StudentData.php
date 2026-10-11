<?php

namespace App\Livewire\TeacherPanel\Pages\Student\Parts;

use App\Enums\Admin\PackageFeatureKeyEnum;
use App\Enums\Teacher\TeacherPermissionEnum;
use App\Livewire\TeacherPanel\Concerns\Shared\HasDeleteConfirmation;
use App\Livewire\TeacherPanel\Concerns\Shared\HasSubscriptionLimitCheck;
use App\Models\Branch;
use App\Models\Group;
use App\Models\Student;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class StudentData extends Component
{
    use HasDeleteConfirmation;
    use HasSubscriptionLimitCheck;
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public string $search = '';

    public string $statusFilter = '1';

    public string $groupFilter = '';

    public string $branchFilter = '';

    protected $listeners = ['refreshData' => '$refresh'];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatingGroupFilter(): void
    {
        $this->resetPage();
    }

    public function updatingBranchFilter(): void
    {
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->search = '';
        $this->statusFilter = '';
        $this->groupFilter = '';
        $this->branchFilter = '';
        $this->resetPage();
    }

    public function confirmDelete(int $id): void
    {
        if (! Auth::guard('teacher')->user()->hasPermission(TeacherPermissionEnum::STUDENT_DELETE)) {
            $this->dispatch('show-error-message', message: 'ليس لديك صلاحية لحذف الطلاب.');

            return;
        }

        $this->deleteId = $id;
    }

    public function delete(): void
    {
        if (! $this->deleteId) {
            return;
        }

        $user = Auth::guard('teacher')->user();

        if (! $user->hasPermission(TeacherPermissionEnum::STUDENT_DELETE)) {
            $this->dispatch('show-error-message', message: 'ليس لديك صلاحية لحذف الطلاب.');
            $this->deleteId = null;

            return;
        }

        $student = Student::where('id', $this->deleteId)
            ->where('teacher_id', $user->getOwnerId())
            ->firstOrFail();

        if (! $user->isOwner() && ! in_array($student->branch_id, $user->getAccessibleBranchIds())) {
            $this->dispatch('show-error-message', message: 'ليس لديك صلاحية على هذا الطالب.');
            $this->deleteId = null;

            return;
        }

        $student->delete();
        $this->deleteId = null;
        $this->dispatch('show-success-message', message: 'تم حذف الطالب بنجاح.');
    }

    public function render()
    {
        $user = Auth::guard('teacher')->user();
        $ownerId = $user->getOwnerId();
        $accessibleBranchIds = $user->getAccessibleBranchIds();

        $query = Student::query()
            ->with(['creator', 'branch'])
            ->withCount(['activeGroups as groups_count'])
            ->where('teacher_id', $ownerId);

        if (! $user->isOwner()) {
            empty($accessibleBranchIds)
                ? $query->whereRaw('1 = 0')
                : $query->whereIn('branch_id', $accessibleBranchIds);
        }

        $query->when($this->search !== '', fn ($q) => $q->where(function ($sub) {
            $sub->where('name', 'like', "%{$this->search}%")
                ->orWhere('phone', 'like', "%{$this->search}%");
        }));

        $query->when($this->statusFilter !== '', fn ($q) => $q->where('is_active', $this->statusFilter));
        $query->when($this->branchFilter !== '', fn ($q) => $q->where('branch_id', $this->branchFilter));
        $query->when($this->groupFilter !== '', fn ($q) => $q->whereHas('activeGroups', fn ($sub) => $sub->where('groups.id', $this->groupFilter)));

        $students = $query->orderBy('name')->paginate(10);

        $total = Student::where('teacher_id', $ownerId)->count();

        return view('teacher-panel.pages.student.parts.student-data', [
            'students' => $students,
            'groups' => Group::where('teacher_id', $ownerId)
                ->when(! $user->isOwner(), fn ($q) => $q->whereIn('branch_id', $accessibleBranchIds))
                ->orderBy('name')
                ->get(),
            'branches' => Branch::whereIn('id', $accessibleBranchIds)->orderBy('name')->get(),
            'currentTeacher' => $user,
            'ownerId' => $ownerId,
            'isOwner' => $user->isOwner(),
            'limit' => $this->getLimitData(PackageFeatureKeyEnum::MAX_STUDENTS->value, $total),
            'totalStudents' => $total,
            'canView' => $user->hasPermission(TeacherPermissionEnum::STUDENT_VIEW),
            'canCreate' => $user->hasPermission(TeacherPermissionEnum::STUDENT_CREATE),
            'canUpdate' => $user->hasPermission(TeacherPermissionEnum::STUDENT_UPDATE),
            'canDelete' => $user->hasPermission(TeacherPermissionEnum::STUDENT_DELETE),
        ]);
    }
}
