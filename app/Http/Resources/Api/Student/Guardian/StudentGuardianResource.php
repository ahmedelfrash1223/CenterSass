<?php

namespace App\Http\Resources\Api\Student\Guardian;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentGuardianResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $profile = $this->profile;

        return [
            'student_id' => $this->id,
            'student_name' => $this->name,
            'student_code' => $this->code,
            'teacher' => $this->teacher ? [
                'id' => $this->teacher->id,
                'name' => $this->teacher->name,
                'phone' => $this->teacher->phone,
                'email' => $this->teacher->email,
                'code' => $this->teacher->code,
            ] : null,
            'guardian' => $profile ? [
                'name' => $profile->guardian_name,
                'phone' => $profile->guardian_phone,
                'relation' => $profile->guardian_relation,
            ] : null,
            'school' => $profile ? [
                'name' => $profile->school_name,
                'address' => $profile->address,
                'city' => $profile->city,
            ] : null,
            'notes' => $profile?->notes,
        ];
    }
}
