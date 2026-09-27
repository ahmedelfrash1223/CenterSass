<?php

namespace App\Livewire\TeacherPanel\Pages\Exam\Parts;

use App\Enums\Teacher\TeacherPermissionEnum;
use App\Livewire\TeacherPanel\Concerns\Shared\HasDeleteConfirmation;
use App\Models\Exam;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class ExamData extends Component
{
    use WithPagination;
    use HasDeleteConfirmation;

    protected $paginationTheme = 'bootstrap';

    public string $search = '';
    public string $statusFilter = '';
    public string $branchFilter = '';

    protected $listeners = ['refreshData' => '$refresh'];

    public function updatingSearch(): void { $this->resetPage(); }
    public function updatingStatusFilter(): void { $this->resetPage(); }
    public function updatingBranchFilter(): void { $this->resetPage(); }

    public function clearFilters(): void
    {
        $this->search = '';
        $this->statusFilter = '';
        $this->branchFilter = '';
        $this->resetPage();
    }

    public function confirmDelete(int $id): void
    {
        if (!Auth::guard('teacher')->user()->hasPermission(TeacherPermissionEnum::EXAM_DELETE)) {
            $this->dispatch('show-error-message', message: 'ليس لديك صلاحية لحذف الامتحانات.');
            return;
        }

        $this->deleteId = $id;
    }

    public function delete(): void
    {
        if (!$this->deleteId) {
            return;
        }

        $user = Auth::guard('teacher')->user();

        if (!$user->hasPermission(TeacherPermissionEnum::EXAM_DELETE)) {
            $this->dispatch('show-error-message', message: 'ليس لديك صلاحية لحذف الامتحانات.');
            $this->deleteId = null;
            return;
        }

        $exam = Exam::where('id', $this->deleteId)
            ->where('teacher_id', $user->getOwnerId())
            ->firstOrFail();

        if (!$user->isOwner()) {
            $allowedGroupIds = $user->getAccessibleGroups()
                ->pluck('id')->map(fn ($id) => (int) $id)->toArray();

            if (!$exam->targets()->whereIn('group_id', $allowedGroupIds)->exists()) {
                $this->dispatch('show-error-message', message: 'ليس لديك صلاحية على هذا الامتحان.');
                $this->deleteId = null;
                return;
            }
        }

        $exam->delete();

        $this->deleteId = null;
        $this->dispatch('show-success-message', message: 'تم حذف الامتحان بنجاح.');
    }

    public function render()
    {
        $user = Auth::guard('teacher')->user();
        $ownerId = $user->getOwnerId();
        $accessibleBranchIds = $user->getAccessibleBranchIds();

        $query = Exam::query()
            ->with(['creator'])
            ->withCount(['questions', 'targets'])
            ->where('teacher_id', $ownerId);

        if (!$user->isOwner()) {
            $allowedGroupIds = $user->getAccessibleGroups()
                ->pluck('id')->map(fn ($id) => (int) $id)->toArray();

            empty($allowedGroupIds)
                ? $query->whereRaw('1 = 0')
                : $query->whereHas('targets', fn ($q) => $q->whereIn('group_id', $allowedGroupIds));
        }

        $query->when($this->search !== '', fn ($q) => $q->where('title', 'like', "%{$this->search}%"));
        $query->when($this->statusFilter !== '', fn ($q) => $q->where('status', $this->statusFilter));
        $query->when($this->branchFilter !== '', fn ($q) => $q->whereHas(
            'targets',
            fn ($t) => $t->where('branch_id', $this->branchFilter)
        ));

        $exams = $query->latest()->paginate(10);

        return view('teacher-panel.pages.exam.parts.exam-data', [
            'exams'      => $exams,
            'statuses'   => [
                'draft'     => 'مسودة',
                'published' => 'منشور',
                'closed'    => 'مغلق',
            ],
            'branches'   => \App\Models\Branch::whereIn('id', $accessibleBranchIds)->orderBy('name')->get(),
            'isOwner'    => $user->isOwner(),
            'canView'    => $user->hasPermission(TeacherPermissionEnum::EXAM_VIEW),
            'canCreate'  => $user->hasPermission(TeacherPermissionEnum::EXAM_CREATE),
            'canUpdate'  => $user->hasPermission(TeacherPermissionEnum::EXAM_UPDATE),
            'canDelete'  => $user->hasPermission(TeacherPermissionEnum::EXAM_DELETE),
        ]);
    }
}
