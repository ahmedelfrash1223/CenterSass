<?php

namespace App\Livewire\TeacherPanel\Pages\Subject\Parts;

use App\Enums\Teacher\GradeEnum;
use App\Enums\Teacher\SubjectEnum;
use App\Enums\Teacher\TeacherPermissionEnum;
use App\Models\TeacherSubject;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class SubjectData extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public string $search = '';
    public ?int $deleteId = null;
    public ?string $deleteGrade = null;
    public ?string $deleteAllGrade = null;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function confirmDelete(int $id, string $grade): void
    {
        if (!Auth::guard('teacher')->user()->hasPermission(TeacherPermissionEnum::SUBJECT_DELETE)) {
            $this->dispatch('show-error-message', message: 'ليس لديك صلاحية لحذف المواد.');
            return;
        }

        $this->deleteId = $id;
        $this->deleteGrade = $grade;
    }

    public function cancelDelete(): void
    {
        $this->deleteId = null;
        $this->deleteGrade = null;
    }

    public function confirmDeleteAll(string $grade): void
    {
        if (!Auth::guard('teacher')->user()->hasPermission(TeacherPermissionEnum::SUBJECT_DELETE)) {
            $this->dispatch('show-error-message', message: 'ليس لديك صلاحية لحذف المواد.');
            return;
        }

        $this->deleteAllGrade = $grade;
        $this->deleteId = null;
        $this->deleteGrade = null;
    }

    public function cancelDeleteAll(): void
    {
        $this->deleteAllGrade = null;
    }

    public function delete(): void
    {
        if (!$this->deleteId) {
            return;
        }

        if (!Auth::guard('teacher')->user()->hasPermission(TeacherPermissionEnum::SUBJECT_DELETE)) {
            $this->dispatch('show-error-message', message: 'ليس لديك صلاحية لحذف المواد.');
            $this->deleteId = null;
            $this->deleteGrade = null;
            return;
        }

        TeacherSubject::where('teacher_id', Auth::guard('teacher')->user()->getOwnerId())
            ->findOrFail($this->deleteId)
            ->delete();

        $this->deleteId = null;
        $this->deleteGrade = null;

        $this->dispatch('show-success-message', message: 'تم حذف المادة بنجاح.');
    }

    public function deleteAll(): void
    {
        if (!$this->deleteAllGrade) {
            return;
        }

        if (!Auth::guard('teacher')->user()->hasPermission(TeacherPermissionEnum::SUBJECT_DELETE)) {
            $this->dispatch('show-error-message', message: 'ليس لديك صلاحية لحذف المواد.');
            $this->deleteAllGrade = null;
            return;
        }

        $count = TeacherSubject::where('teacher_id', Auth::guard('teacher')->user()->getOwnerId())
            ->where('grade', $this->deleteAllGrade)
            ->delete();

        $gradeLabel = GradeEnum::tryFrom($this->deleteAllGrade)?->label() ?? $this->deleteAllGrade;
        $this->deleteAllGrade = null;

        $this->dispatch('show-success-message', message: "تم حذف جميع مواد ({$gradeLabel}) بنجاح ({$count} مادة).");
    }

    public function render()
    {
        $user = Auth::guard('teacher')->user();
        $ownerId = $user->getOwnerId();

        $allSubjects = TeacherSubject::query()
            ->with('creator')
            ->where('teacher_id', $ownerId)
            ->get();

        if ($this->search !== '') {
            $searchTerm = mb_strtolower(trim($this->search), 'UTF-8');

            $allSubjects = $allSubjects->filter(function ($item) use ($searchTerm) {
                $gradeLabel = GradeEnum::tryFrom($item->grade)?->label() ?? '';
                $gradeValue = $item->grade;
                $subjectLabel = SubjectEnum::tryFrom($item->subject)?->label() ?? '';
                $subjectValue = $item->subject;

                return mb_stripos($gradeLabel, $searchTerm, 0, 'UTF-8') !== false
                    || mb_stripos($gradeValue, $searchTerm, 0, 'UTF-8') !== false
                    || mb_stripos($subjectLabel, $searchTerm, 0, 'UTF-8') !== false
                    || mb_stripos($subjectValue, $searchTerm, 0, 'UTF-8') !== false;
            });
        }

        $grouped = $allSubjects
            ->groupBy('grade')
            ->map(function ($items, $grade) {
                return [
                    'grade' => $grade,
                    'grade_label' => GradeEnum::tryFrom($grade)?->label() ?? $grade,
                    'grade_color' => $this->getGradeColor($grade),
                    'subjects' => $items->map(function ($item) {
                        return [
                            'id' => $item->id,
                            'subject' => $item->subject,
                            'subject_label' => SubjectEnum::tryFrom($item->subject)?->label() ?? $item->subject,
                            'is_active' => (bool) $item->is_active,
                            'creator_name' => $item->creator?->name,
                            'creator_role_color' => $item->creator?->role?->color(),
                            'creator_role_icon' => $item->creator?->role?->icon(),
                            'creator_role_label' => $item->creator?->role?->label(),
                        ];
                    })->values()->toArray(),
                    'count' => $items->count(),
                    'active_count' => $items->where('is_active', 1)->count(),
                ];
            })
            ->values();

        $perPage = 10;
        $currentPage = $this->getPage();
        $total = $grouped->count();
        $paginated = $grouped->slice(($currentPage - 1) * $perPage, $perPage)->values();

        $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
            $paginated,
            $total,
            $perPage,
            $currentPage,
            ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath()]
        );

        return view('teacher-panel.pages.subject.parts.subject-data', [
            'groupedSubjects' => $paginated,
            'subjects' => $paginator,
            'grades' => GradeEnum::options(),
            'subjectOptions' => SubjectEnum::options(),
            'canCreate' => $user->hasPermission(TeacherPermissionEnum::SUBJECT_CREATE),
            'canUpdate' => $user->hasPermission(TeacherPermissionEnum::SUBJECT_UPDATE),
            'canDelete' => $user->hasPermission(TeacherPermissionEnum::SUBJECT_DELETE),
        ]);
    }
    protected function getGradeColor(string $grade): string
    {
        if (str_starts_with($grade, 'primary_')) {
            return 'success';
        }
        if (str_starts_with($grade, 'prep_')) {
            return 'primary';
        }
        if (str_starts_with($grade, 'secondary_')) {
            return 'info';
        }
        return 'secondary';
    }
}
