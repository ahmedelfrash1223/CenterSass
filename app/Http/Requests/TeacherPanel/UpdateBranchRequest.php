<?php

namespace App\Http\Requests\TeacherPanel;

use Illuminate\Validation\Rule;

class UpdateBranchRequest extends StoreBranchRequest
{
    public function rules(): array
    {
        $branchId = $this->input('branch_id') ?: $this->route('branch');
        $branchId = is_object($branchId) ? $branchId->id : $branchId;

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('branches', 'name')
                    ->where('teacher_id', auth('teacher')->id())
                    ->ignore($branchId),
            ],
            'address' => ['required', 'string'],
            'phone' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'is_active' => ['required', 'integer', 'in:0,1'],
        ];
    }
}