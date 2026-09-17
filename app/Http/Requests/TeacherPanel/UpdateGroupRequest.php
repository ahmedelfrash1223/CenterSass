<?php

namespace App\Http\Requests\TeacherPanel;

use Illuminate\Validation\Rule;

class UpdateGroupRequest extends StoreGroupRequest
{
    public function rules(): array
    {
        $rules = parent::rules();

        return $rules;
    }
}
