<?php

namespace App\Http\Controllers\AdminPanel;

use App\Http\Controllers\Controller;
use App\Models\Package;

class PackageController extends Controller
{
    public function index()
    {
        return view('admin-panel.pages.package.index');
    }

    public function create()
    {
        return view('admin-panel.pages.package.create');
    }

    public function edit(Package $package)
    {
        return view('admin-panel.pages.package.edit', compact('package'));
    }

    public function show(Package $package)
    {
        return view('admin-panel.pages.package.show', compact('package'));
    }
}
