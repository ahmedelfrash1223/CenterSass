<?php

namespace App\Livewire\TeacherPanel\Pages\Booking\Parts;

use App\Enums\Teacher\TeacherPermissionEnum;
use App\Http\Requests\TeacherPanel\StoreBookingRequest;
use App\Livewire\TeacherPanel\Concerns\Shared\HasStudentGroupValidation;
use App\Livewire\TeacherPanel\Concerns\Shared\LoadsStudentFormData;
use App\Models\Booking;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class BookingCreate extends Component
{
    use HasStudentGroupValidation;
    use LoadsStudentFormData;

    public string $name = '';

    public string $phone = '';

    public string $grade = '';

    public string $branch_id = '';

    public array $group_ids = [];

    public string $guardian_name = '';

    public string $guardian_phone = '';

    public string $guardian_relation = '';

    public string $school_name = '';

    public string $address = '';

    public string $city = '';

    public string $notes = '';

    public bool $sectionBasic = true;

    public bool $sectionProfile = false;

    public bool $sectionGroups = true;

    public function mount(): void
    {
        $user = Auth::guard('teacher')->user();

        abort_unless(
            $user->hasPermission(TeacherPermissionEnum::BOOKING_CREATE),
            403
        );
    }

    public function toggleSection(string $section): void
    {
        $this->{$section} = ! $this->{$section};
    }

    public function updatedGrade(): void
    {
        $this->group_ids = [];
    }

    public function updatedBranchId(): void
    {
        $this->group_ids = [];
    }

    public function save(bool $createNew = false): void
    {
        $user = Auth::guard('teacher')->user();
        $ownerId = $user->getOwnerId();

        $request = new StoreBookingRequest;

        try {
            $this->validate($request->rules(), $request->messages());
        } catch (ValidationException $e) {
            $this->openSectionsWithErrors();
            throw $e;
        }

        if (! $user->hasAccessToBranch((int) $this->branch_id)) {
            $this->addError('branch_id', 'الفرع غير متاح لك.');
            $this->sectionBasic = true;

            return;
        }

        if (! $this->ensureGroupsAuthorized($this->group_ids)) {
            $this->sectionGroups = true;

            return;
        }

        Booking::create([
            'name' => $this->name,
            'phone' => $this->phone ?: null,
            'grade' => $this->grade,
            'branch_id' => $this->branch_id,
            'teacher_id' => $ownerId,
            'created_by' => $user->id,
            'guardian_name' => $this->guardian_name ?: null,
            'guardian_phone' => $this->guardian_phone ?: null,
            'guardian_relation' => $this->guardian_relation ?: null,
            'school_name' => $this->school_name ?: null,
            'address' => $this->address ?: null,
            'city' => $this->city ?: null,
            'notes' => $this->notes ?: null,
            'group_ids' => $this->group_ids,
            'status' => 'pending',
        ]);

        if ($createNew) {
            $this->reset([
                'name', 'phone',
                'grade', 'branch_id', 'group_ids',
                'guardian_name', 'guardian_phone', 'guardian_relation',
                'school_name', 'address', 'city', 'notes',
            ]);

            $this->dispatch('show-success-message', message: 'تم إضافة الحجز بنجاح. يمكنك إضافة حجز آخر.');

            return;
        }

        session()->flash('booking-success', 'تم إضافة الحجز بنجاح.');
        $this->redirect(route('teacher.bookings.index'), navigate: true);
    }

    protected function openSectionsWithErrors(): void
    {
        $errorFields = array_keys($this->getErrorBag()->toArray());

        $sectionMap = [
            'sectionBasic' => ['name', 'phone', 'grade', 'branch_id'],
            'sectionProfile' => ['guardian_name', 'guardian_phone', 'guardian_relation', 'school_name', 'address', 'city', 'notes'],
            'sectionGroups' => ['group_ids'],
        ];

        foreach ($errorFields as $field) {
            $fieldName = explode('.', $field)[0];
            foreach ($sectionMap as $section => $fields) {
                if (in_array($fieldName, $fields)) {
                    $this->{$section} = true;
                }
            }
        }
    }

    public function render()
    {
        return view('teacher-panel.pages.booking.parts.booking-create',
            $this->loadStudentFormData(
                $this->grade ?: null,
                $this->branch_id ?: null
            )
        );
    }
}
