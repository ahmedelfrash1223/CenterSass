<?php

namespace App\Livewire\TeacherPanel\Pages\Assignment\Parts;

use App\Enums\Teacher\AssignmentStatusEnum;
use App\Enums\Teacher\AssignmentTypeEnum;
use App\Enums\Teacher\TeacherPermissionEnum;
use App\Http\Requests\TeacherPanel\StoreAssignmentRequest;
use App\Models\Assignment;
use App\Models\Group;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class AssignmentCreate extends Component
{
    public string $title = '';

    public string $description = '';

    public string $type = '';

    public string $status = 'active';

    public string $max_score = '10';

    public string $due_date = '';

    public string $group_id = '';

    public string $branch_id = '';

    public bool $sectionBasic = true;

    public bool $sectionDescription = false;

    public function mount(): void
    {
        $user = Auth::guard('teacher')->user();

        abort_unless(
            $user->hasPermission(TeacherPermissionEnum::ASSIGNMENT_CREATE),
            403
        );
    }

    public function toggleSection(string $section): void
    {
        $this->{$section} = ! $this->{$section};
    }

    public function updatedGroupId(): void
    {
        $group = Group::find($this->group_id);

        if ($group) {
            $this->branch_id = (string) $group->branch_id;
        }
    }

    public function save(): void
    {
        $user = Auth::guard('teacher')->user();
        $ownerId = $user->getOwnerId();

        $request = new StoreAssignmentRequest;

        try {
            $this->validate($request->rules(), $request->messages());
        } catch (ValidationException $e) {
            $this->openSectionsWithErrors();
            throw $e;
        }

        $group = Group::findOrFail($this->group_id);

        if (! $user->hasAccessToBranch((int) $group->branch_id)) {
            $this->addError('group_id', 'المجموعة غير متاحة لك.');
            $this->sectionBasic = true;

            return;
        }

        Assignment::create([
            'title' => $this->title,
            'description' => $this->description ?: null,
            'type' => $this->type,
            'status' => $this->status,
            'max_score' => $this->max_score,
            'due_date' => $this->due_date ?: null,
            'group_id' => $group->id,
            'branch_id' => $group->branch_id,
            'teacher_id' => $ownerId,
            'created_by' => $user->id,
        ]);

        session()->flash('assignment-success', 'تم إضافة المتابعة بنجاح.');
        $this->redirect(route('teacher.assignments.index'), navigate: true);
    }

    protected function openSectionsWithErrors(): void
    {
        $errorFields = array_keys($this->getErrorBag()->toArray());

        $sectionMap = [
            'sectionBasic' => ['title', 'type', 'status', 'max_score', 'due_date', 'group_id'],
            'sectionDescription' => ['description'],
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
        $user = Auth::guard('teacher')->user();
        $ownerId = $user->getOwnerId();
        $accessibleBranchIds = $user->getAccessibleBranchIds();

        $groups = Group::query()
            ->where('teacher_id', $ownerId)
            ->when(! $user->isOwner(), fn ($q) => $q->whereIn('branch_id', $accessibleBranchIds))
            ->orderBy('name')
            ->get();

        return view('teacher-panel.pages.assignment.parts.assignment-create', [
            'groups' => $groups,
            'types' => AssignmentTypeEnum::options(),
            'statuses' => AssignmentStatusEnum::options(),
        ]);
    }
}
