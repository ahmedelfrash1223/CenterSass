<?php

namespace App\Livewire\TeacherPanel\Pages\Booking\Parts;

use App\Enums\Teacher\GradeEnum;
use App\Enums\Teacher\TeacherPermissionEnum;
use App\Livewire\TeacherPanel\Concerns\Shared\HasStudentGroupSync;
use App\Models\Booking;
use App\Models\Student;
use App\Models\StudentProfile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class BookingShow extends Component
{
    use HasStudentGroupSync;

    public Booking $booking;

    public bool $confirming = false;

    public function mount(Booking $booking): void
    {
        $user = Auth::guard('teacher')->user();

        abort_unless($booking->teacher_id === $user->getOwnerId(), 404);

        if (! $user->isOwner()) {
            abort_unless(
                in_array($booking->branch_id, $user->getAccessibleBranchIds()),
                404
            );
        }

        $this->booking = $booking->load(['branch', 'creator', 'confirmedStudent']);
    }

    public function askConfirm(): void
    {
        $user = Auth::guard('teacher')->user();

        abort_unless($user->hasPermission(TeacherPermissionEnum::BOOKING_CONFIRM), 403);

        if (! $this->booking->isPending()) {
            $this->dispatch('show-error-message', message: 'هذا الحجز تمت معالجته بالفعل.');

            return;
        }

        $this->confirming = true;
    }

    public function cancelConfirm(): void
    {
        $this->confirming = false;
    }

    public function confirmBooking(): void
    {
        $user = Auth::guard('teacher')->user();

        abort_unless($user->hasPermission(TeacherPermissionEnum::BOOKING_CONFIRM), 403);

        if (! $this->booking->isPending()) {
            $this->dispatch('show-error-message', message: 'هذا الحجز تمت معالجته بالفعل.');
            $this->confirming = false;

            return;
        }

        DB::transaction(function () use ($user) {
            $student = Student::create([
                'name' => $this->booking->name,
                'phone' => $this->booking->phone,
                'grade' => $this->booking->grade,
                'branch_id' => $this->booking->branch_id,
                'teacher_id' => $this->booking->teacher_id,
                'created_by' => $user->id,
                'is_active' => 1,
            ]);

            $hasProfile = $this->booking->guardian_name
                || $this->booking->guardian_phone
                || $this->booking->guardian_relation
                || $this->booking->school_name
                || $this->booking->address
                || $this->booking->city
                || $this->booking->notes;

            if ($hasProfile) {
                StudentProfile::create([
                    'student_id' => $student->id,
                    'guardian_name' => $this->booking->guardian_name,
                    'guardian_phone' => $this->booking->guardian_phone,
                    'guardian_relation' => $this->booking->guardian_relation,
                    'school_name' => $this->booking->school_name,
                    'address' => $this->booking->address,
                    'city' => $this->booking->city,
                    'notes' => $this->booking->notes,
                ]);
            }

            if (! empty($this->booking->group_ids)) {
                $this->syncStudentGroups($student, $this->booking->group_ids);
            }

            $this->booking->update([
                'status' => 'confirmed',
                'confirmed_student_id' => $student->id,
                'confirmed_at' => now(),
            ]);
        });

        $this->booking->refresh()->load(['confirmedStudent']);

        $this->confirming = false;
        $this->dispatch('show-success-message', message: 'تم تأكيد الحجز وإضافة الطالب بنجاح.');
    }

    public function cancelBooking(): void
    {
        $user = Auth::guard('teacher')->user();

        abort_unless($user->hasPermission(TeacherPermissionEnum::BOOKING_CONFIRM), 403);

        if (! $this->booking->isPending()) {
            $this->dispatch('show-error-message', message: 'هذا الحجز تمت معالجته بالفعل.');

            return;
        }

        $this->booking->update(['status' => 'cancelled']);
        $this->booking->refresh();

        $this->dispatch('show-success-message', message: 'تم إلغاء الحجز.');
    }

    public function render()
    {
        $user = Auth::guard('teacher')->user();

        $gradeLabel = $this->booking->grade
            ? GradeEnum::tryFrom($this->booking->grade)?->label() ?? $this->booking->grade
            : null;

        return view('teacher-panel.pages.booking.parts.booking-show', [
            'gradeLabel' => $gradeLabel,
            'canConfirm' => $user->hasPermission(TeacherPermissionEnum::BOOKING_CONFIRM),
            'canUpdate' => $user->hasPermission(TeacherPermissionEnum::BOOKING_UPDATE),
        ]);
    }
}
