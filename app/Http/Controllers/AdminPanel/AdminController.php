<?php

namespace App\Http\Controllers\AdminPanel;

use App\Http\Controllers\Controller;
use App\Models\Admin;

class AdminController extends Controller
{
    public function index()
    {
        return view('admin-panel.pages.admin.index');
    }

    public function create()
    {
        return view('admin-panel.pages.admin.create');
    }

    public function edit(Admin $admin)
    {
        return view('admin-panel.pages.admin.edit', compact('admin'));
    }
}