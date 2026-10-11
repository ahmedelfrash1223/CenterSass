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

class AssignmentUpdate extends Component
{
    public Assignment $assignment;

    public string $title = '';

    public string $description = '';

    public string $type = '';

    public string $status = 'active';

    public string $max_score = '10';

    public string $due_date = '';

    public string $group_id = '';

    public bool $sectionBasic = true;

    public bool $sectionDescription = false;

    public function mount(Assignment $assignment): void
    {
        $user = Auth::guard('teacher')->user();

        abort_unless(
            $user->hasPermission(TeacherPermissionEnum::ASSIGNMENT_UPDATE),
            403
        );

        abort_unless($assignment->teacher_id === $user->getOwnerId(), 404);

        if (! $user->isOwner()) {
            abort_unless(
                in_array($assignment->branch_id, $user->getAccessibleBranchIds()),
                404
            );
        }

        $this->assignment = $assignment;

        $this->title = $assignment->title;
        $this->description = $assignment->description ?? '';
        $this->type = $assignment->type->value;
        $this->status = $assignment->status->value;
        $this->max_score = (string) $assignment->max_score;
        $this->due_date = $assignment->due_date?->format('Y-m-d') ?? '';
        $this->group_id = (string) $assignment->group_id;

        if ($assignment->description) {
            $this->sectionDescription = true;
        }
    }

    public function toggleSection(string $section): void
    {
        $this->{$section} = ! $this->{$section};
    }

    public function updatedGroupId(): void
    {
        // optional
    }

    public function update(): void
    {
        $user = Auth::guard('teacher')->user();

        abort_unless(
            $user->hasPermission(TeacherPermissionEnum::ASSIGNMENT_UPDATE),
            403
        );

        abort_unless($this->assignment->teacher_id === $user->getOwnerId(), 404);

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

        $this->assignment->update([
            'title' => $this->title,
            'description' => $this->description ?: null,
            'type' => $this->type,
            'status' => $this->status,
            'max_score' => $this->max_score,
            'due_date' => $this->due_date ?: null,
            'group_id' => $group->id,
            'branch_id' => $group->branch_id,
        ]);

        session()->flash('assignment-success', 'تم تحديث المتابعة بنجاح.');
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

        return view('teacher-panel.pages.assignment.parts.assignment-update', [
            'groups' => $groups,
            'types' => AssignmentTypeEnum::options(),
            'statuses' => AssignmentStatusEnum::options(),
        ]);
    }
}
