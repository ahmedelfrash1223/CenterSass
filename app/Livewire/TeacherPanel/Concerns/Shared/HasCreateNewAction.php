<?php

namespace App\Livewire\TeacherPanel\Concerns\Shared;

trait HasCreateNewAction
{
    protected function handleCreateNew(string $successMessage, array $resetFields): void
    {
        $this->reset($resetFields);
        $this->dispatch('show-success-message', message: $successMessage);
    }
}
