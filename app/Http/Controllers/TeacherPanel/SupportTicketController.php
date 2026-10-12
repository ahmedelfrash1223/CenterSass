<?php

namespace App\Http\Controllers\TeacherPanel;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use Illuminate\Support\Facades\Auth;

class SupportTicketController extends Controller
{
    public function index()
    {
        return view('teacher-panel.pages.support.index');
    }

    public function create()
    {
        return view('teacher-panel.pages.support.create');
    }

    public function show(SupportTicket $ticket)
    {
        $user = Auth::guard('teacher')->user();

        abort_unless($ticket->teacher_id === $user->getOwnerId(), 404);

        return view('teacher-panel.pages.support.show', compact('ticket'));
    }
}
