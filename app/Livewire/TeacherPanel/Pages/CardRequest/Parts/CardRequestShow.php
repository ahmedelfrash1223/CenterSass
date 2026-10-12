<?php

namespace App\Livewire\TeacherPanel\Pages\CardRequest\Parts;

use App\Enums\Teacher\TeacherPermissionEnum;
use App\Models\CardRequest;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class CardRequestShow extends Component
{
    public CardRequest $cardRequest;

    public function mount(CardRequest $cardRequest): void
    {
        $user = Auth::guard('teacher')->user();

        abort_unless(
            $user->hasPermission(TeacherPermissionEnum::CARD_REQUEST_VIEW),
            403
        );

        abort_unless($cardRequest->teacher_id === $user->getOwnerId(), 404);

        $this->cardRequest = $cardRequest->load(['items.branch', 'items.group', 'creator']);
    }

    public function render()
    {
        return view('teacher-panel.pages.card-request.parts.card-request-show');
    }
}
