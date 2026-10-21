<?php

namespace App\Livewire\AdminPanel\Parts;

use Livewire\Attributes\On;
use Livewire\Component;

class FlashMessage extends Component
{
    public ?string $success = null;
    public ?string $error = null;
    public ?string $warning = null;

    public string $successKey = 'success';
    public string $errorKey = 'error';
    public string $warningKey = 'warning';

    public function mount(): void
    {
        $this->loadFromSession();
    }

    public function loadFromSession(): void
    {
        if (session()->has($this->successKey)) {
            $this->success = session($this->successKey);
        }

        if (session()->has($this->errorKey)) {
            $this->error = session($this->errorKey);
        }

        if (session()->has($this->warningKey)) {
            $this->warning = session($this->warningKey);
        }
    }

    #[On('show-success-message')]
    public function showSuccess(string $message): void
    {
        $this->success = $message;
        $this->error = null;
        $this->warning = null;
    }

    #[On('show-error-message')]
    public function showError(string $message): void
    {
        $this->error = $message;
        $this->success = null;
        $this->warning = null;
    }

    #[On('show-warning-message')]
    public function showWarning(string $message): void
    {
        $this->warning = $message;
        $this->success = null;
        $this->error = null;
    }

    public function clear(string $type): void
    {
        match ($type) {
            'success' => $this->success = null,
            'error' => $this->error = null,
            'warning' => $this->warning = null,
            default => null,
        };

        match ($type) {
            'success' => session()->forget($this->successKey),
            'error' => session()->forget($this->errorKey),
            'warning' => session()->forget($this->warningKey),
            default => null,
        };
    }

    public function render()
    {
        return view('admin-panel.parts.flash-message');
    }
}
