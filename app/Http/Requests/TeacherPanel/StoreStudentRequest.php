<?php

namespace App\Http\Requests\TeacherPanel;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class StoreStudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $currentTeacher = Auth::guard('teacher')->user();
        $accessibleBranchIds = $currentTeacher->getAccessibleBranchIds();

        return [
            'name' => ['required', 'string', 'max:255'],
            'username' => [
                'required', 'string', 'max:255', 'alpha_dash',
                Rule::unique('students', 'username'),
            ],
            'email' => [
                'required', 'email', 'max:255',
                Rule::unique('students', 'email'),
            ],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => ['nullable', 'string', 'min:6', 'confirmed'],
            'grade' => ['required', 'string'],
            'branch_id' => [
                'required',
                Rule::in($accessibleBranchIds),
            ],
            'is_active' => ['required', 'in:0,1'],
            'group_ids' => ['nullable', 'array'],
            'group_ids.*' => [
                Rule::exists('groups', 'id')->where('teacher_id', $currentTeacher->getOwnerId()),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'اسم الطالب مطلوب.',
            'username.required' => 'اسم المستخدم مطلوب.',
            'username.unique' => 'اسم المستخدم مستخدم بالفعل.',
            'username.alpha_dash' => 'اسم المستخدم يقبل أحرف وأرقام و - و _ فقط.',
            'email.required' => 'البريد الإلكتروني مطلوب.',
            'email.unique' => 'البريد الإلكتروني مستخدم بالفعل.',
            'password.confirmed' => 'تأكيد كلمة المرور غير متطابق.',
            'password.min' => 'كلمة المرور يجب أن تكون 6 أحرف على الأقل.',
            'grade.required' => 'المرحلة الدراسية مطلوبة.',
            'branch_id.required' => 'الفرع مطلوب.',
            'branch_id.in' => 'الفرع غير متاح لك.',
            'group_ids.*.exists' => 'إحدى المجموعات غير صالحة.',
        ];
    }
}
