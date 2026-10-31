<?php

namespace App\Http\Resources\Api\Student\Profile;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentIdCardResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'account_uuid' => $this->uuid,
            'account_name' => $this->name,
            'account_email' => $this->email,

            'linked_cards' => $this->students->map(function ($student) {
                return [
                    'student_id'     => $student->id,
                    'student_name'   => $student->name,
                    'barcode_digits' => $student->barcode_digits,
                    'barcode_code'   => $student->code,
                    'teacher'        => $student->teacher ? [
                        'id'   => $student->teacher->id,
                        'name' => $student->teacher->name,
                    ] : null,
                ];
            })->values(),
        ];
    }
}
