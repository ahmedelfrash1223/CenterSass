<?php

namespace App\Http\Controllers\AdminPanel;

use App\Http\Controllers\Controller;
use App\Models\Subscription;

class SubscriptionController extends Controller
{
    public function index()
    {
        return view('admin-panel.pages.subscription.index');
    }

    public function create()
    {
        return view('admin-panel.pages.subscription.create');
    }

    public function edit(Subscription $subscription)
    {
        return view('admin-panel.pages.subscription.edit', compact('subscription'));
    }

    public function show(Subscription $subscription)
    {
        return view('admin-panel.pages.subscription.show', compact('subscription'));
    }
}
