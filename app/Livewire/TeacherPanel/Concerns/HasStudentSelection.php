<?php

namespace App\Livewire\TeacherPanel\Concerns;

use App\Models\Student;
use App\Services\Teacher\PaymentQueryService;

trait HasStudentSelection
{
    public ?Student $student = null;
    public array $dueGroups = [];


    protected function loadStudentData(int $studentId): bool
    {
        $user = $this->currentUser();

        $this->student = Student::query()
            ->where('teacher_id', $user->getOwnerId())
            ->with(['branch', 'activeGroups'])
            ->withCount(['activeGroups as active_groups_count'])
            ->find($studentId);

        if (!$this->student) {
            $this->dispatch('show-error-message', message: 'الطالب غير موجود.');
            return false;
        }

        if (!$user->isOwner() && !in_array($this->student->branch_id, $user->getAccessibleBranchIds())) {
            $this->student = null;
            $this->dispatch('show-error-message', message: 'هذا الطالب لا ينتمي لفروعك.');
            return false;
        }

        return true;
    }


    protected function refreshDueGroups(): void
    {
        if (!$this->student) {
            $this->dueGroups = [];
            return;
        }

        $user = $this->currentUser();

        $this->dueGroups = app(PaymentQueryService::class)
            ->dueGroupsForStudent($user, $this->student, $this->selectedMonth);
    }


    protected function findDue(int $itemId): ?array
    {
        return collect($this->dueGroups)->firstWhere('item_id', $itemId);
    }


    public function clearStudent(): void
    {
        $this->student = null;
        $this->dueGroups = [];
        $this->resetAll();
    }
}
