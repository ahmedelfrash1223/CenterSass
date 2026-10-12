<?php

namespace App\Http\Controllers\TeacherPanel;

use App\Http\Controllers\Controller;
use App\Models\CardRequest;
use Illuminate\Support\Facades\Auth;

class CardRequestController extends Controller
{
    public function index()
    {
        return view('teacher-panel.pages.card-request.index');
    }

    public function create()
    {
        return view('teacher-panel.pages.card-request.create');
    }

    public function show(CardRequest $cardRequest)
    {
        $user = Auth::guard('teacher')->user();

        abort_unless($cardRequest->teacher_id === $user->getOwnerId(), 404);

        return view('teacher-panel.pages.card-request.show', compact('cardRequest'));
    }
}
