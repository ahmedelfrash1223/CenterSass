<?php

namespace App\Livewire\TeacherPanel\Concerns\Shared;

trait HasFilters
{
    public function clearFilters(array $filterProperties): void
    {
        foreach ($filterProperties as $property) {
            $this->{$property} = '';
        }
        $this->resetPage();
    }

    public function resetPageOnUpdate(string ...$properties): void
    {
        foreach ($properties as $property) {
            $method = 'updating' . ucfirst($property);
        }
    }
}
