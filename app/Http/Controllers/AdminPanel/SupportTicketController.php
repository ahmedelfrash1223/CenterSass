<?php

namespace App\Http\Controllers\AdminPanel;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;

class SupportTicketController extends Controller
{
    public function index()
    {
        return view('admin-panel.pages.support.index');
    }

    public function show(SupportTicket $ticket)
    {
        return view('admin-panel.pages.support.show', compact('ticket'));
    }
}
