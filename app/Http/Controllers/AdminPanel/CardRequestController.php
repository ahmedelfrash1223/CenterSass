<?php

namespace App\Http\Controllers\AdminPanel;

use App\Http\Controllers\Controller;
use App\Models\CardRequest;

class CardRequestController extends Controller
{
    public function index()
    {
        return view('admin-panel.pages.card-request.index');
    }

    public function show(CardRequest $cardRequest)
    {
        return view('admin-panel.pages.card-request.show', compact('cardRequest'));
    }
}
