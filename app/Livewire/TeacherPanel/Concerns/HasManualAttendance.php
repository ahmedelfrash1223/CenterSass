<?php

namespace App\Livewire\TeacherPanel\Concerns;

use App\Models\Attendance;
use App\Models\Student;

trait HasManualAttendance
{
    public string $manualGroupId = '';
    public array $manualStudentIds = [];

    /**
     * Reset students when group changes.
     */
    public function updatedManualGroupId(): void
    {
        $this->manualStudentIds = [];
    }

    /**
     * Toggle a single student in the selection.
     * Blocks toggle if the student already registered today.
     */
    public function toggleManualStudent(int $studentId): void
    {
        // Prevent selecting already-registered students
        if ($this->isStudentRegisteredToday($studentId)) {
            $this->dispatch('show-error-message', message: 'هذا الطالب مسجل بالفعل اليوم.');
            return;
        }

        $id = (string)$studentId;

        if (in_array($id, $this->manualStudentIds)) {
            $this->manualStudentIds = array_values(
                array_diff($this->manualStudentIds, [$id])
            );
        } else {
            $this->manualStudentIds[] = $id;
        }
    }

    /**
     * Select every unregistered student in the current group.
     */
    public function selectAllManual(): void
    {
        if (!$this->manualGroupId) {
            return;
        }

        $registeredIds = $this->getRegisteredStudentIds();

        $this->manualStudentIds = Student::whereHas('activeGroups', function ($q) {
            $q->where('groups.id', $this->manualGroupId);
        })
            ->whereNotIn('id', $registeredIds)
            ->pluck('id')
            ->map(fn($id) => (string)$id)
            ->toArray();
    }

    /**
     * Clear the selection.
     */
    public function deselectAllManual(): void
    {
        $this->manualStudentIds = [];
    }

    /**
     * Student IDs that already have attendance today in the current group.
     */
    protected function getRegisteredStudentIds(): array
    {
        if (!$this->manualGroupId) {
            return [];
        }

        return Attendance::where('group_id', $this->manualGroupId)
            ->where('attendance_date', now()->toDateString())
            ->pluck('student_id')
            ->unique()
            ->toArray();
    }

    /**
     * Check if a single student is already registered today.
     */
    protected function isStudentRegisteredToday(int $studentId): bool
    {
        return in_array($studentId, $this->getRegisteredStudentIds());
    }
}
