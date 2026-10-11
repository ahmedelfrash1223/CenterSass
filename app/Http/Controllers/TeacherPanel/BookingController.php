<?php

namespace App\Http\Controllers\TeacherPanel;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    public function index()
    {
        return view('teacher-panel.pages.booking.index');
    }

    public function create()
    {
        return view('teacher-panel.pages.booking.create');
    }

    public function show(Booking $booking)
    {
        $user = Auth::guard('teacher')->user();

        abort_unless($booking->teacher_id === $user->getOwnerId(), 404);

        if (! $user->isOwner()) {
            abort_unless(in_array($booking->branch_id, $user->getAccessibleBranchIds()), 404);
        }

        return view('teacher-panel.pages.booking.show', compact('booking'));
    }

    public function edit(Booking $booking)
    {
        $user = Auth::guard('teacher')->user();

        abort_unless($booking->teacher_id === $user->getOwnerId(), 404);

        if (! $user->isOwner()) {
            abort_unless(in_array($booking->branch_id, $user->getAccessibleBranchIds()), 404);
        }

        return view('teacher-panel.pages.booking.edit', compact('booking'));
    }
}
