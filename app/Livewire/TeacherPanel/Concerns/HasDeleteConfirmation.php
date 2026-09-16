<?php

namespace App\Livewire\TeacherPanel\Concerns;

trait HasDeleteConfirmation
{
    public ?int $deleteId = null;

    public function confirmDelete(int $id): void
    {
        $this->deleteId = $id;
    }

    public function cancelDelete(): void
    {
        $this->deleteId = null;
    }
}
