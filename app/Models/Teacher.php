<?php

namespace App\Models;

use App\Enums\Admin\SubscriptionStatusEnum;
use App\Enums\Teacher\TeacherRoleEnum;
use App\Traits\Teacher\HasAccountAccess;
use App\Traits\Teacher\HasBranchAccess;
use App\Traits\Teacher\HasCreator;
use App\Traits\Teacher\HasIdentityDefaults;
use App\Traits\Teacher\HasPermissions;
use App\Traits\Teacher\HasRoles;
use App\Traits\Teacher\HasSubscriptionLimits;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Teacher extends Authenticatable
{
    use HasAccountAccess;
    use HasBranchAccess;
    use HasCreator;
    use HasIdentityDefaults;
    use HasPermissions;
    use HasRoles;
    use HasSubscriptionLimits;
    use Notifiable;

    protected $guard = 'teacher';

    protected $fillable = [
        'code',
        'uuid',
        'username',
        'name',
        'email',
        'password',
        'phone',
        'owner_id',
        'created_by',
        'role',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'is_active' => 'integer',
            'role' => TeacherRoleEnum::class,
        ];
    }

    // =============================================
    // 📌 RELATIONS
    // =============================================

    public function owner(): BelongsTo
    {
        return $this->belongsTo(Teacher::class, 'owner_id');
    }

    public function staff(): HasMany
    {
        return $this->hasMany(Teacher::class, 'owner_id');
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    public function branches(): BelongsToMany
    {
        return $this->belongsToMany(Branch::class, 'branch_teacher', 'teacher_id', 'branch_id')
            ->withPivot(['created_by'])
            ->withTimestamps();
    }

    public function subjects(): HasMany
    {
        return $this->hasMany(TeacherSubject::class);
    }

    public function groups(): HasMany
    {
        return $this->hasMany(Group::class);
    }

    public function themeSettings(): HasOne
    {
        return $this->hasOne(TeacherThemeSetting::class);
    }

    public function subscription(): HasOne
    {
        return $this->hasOne(Subscription::class, 'teacher_id');
    }

    public function activeSubscription(): HasOne
    {
        return $this->subscription()
            ->with('package')
            ->where('status', SubscriptionStatusEnum::ACTIVE->value);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class, 'teacher_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'teacher_id');
    }

    public function grades(): BelongsToMany
    {
        return $this->belongsToMany(
            TeacherSubject::class,
            'teacher_grades',
            'teacher_id',
            'grade',
            'id',
            'grade'
        );
    }

    public function assignedSubjects(): BelongsToMany
    {
        return $this->belongsToMany(
            TeacherSubject::class,
            'teacher_grade_subjects',
            'teacher_id',
            'teacher_subject_id'
        );
    }

    public function assignedGrades(): HasMany
    {
        return $this->hasMany(TeacherGrade::class, 'teacher_id');
    }

    public function payrollSetting()
    {
        return $this->hasOne(TeacherPayrollSetting::class, 'teacher_id');
    }

    public function branchPayrollSettings()
    {
        return $this->hasMany(TeacherBranchPayrollSetting::class, 'teacher_id');
    }

    public function payrolls()
    {
        return $this->hasMany(TeacherPayroll::class, 'teacher_id');
    }

    public function payrollPayments()
    {
        return $this->hasMany(TeacherPayrollPayment::class, 'teacher_id');
    }

    public function expensesAsPayee()
    {
        return $this->hasMany(Expense::class, 'teacher_payee_id');
    }

    public function groupSchedules()
    {
        return $this->hasMany(GroupSchedule::class, 'teacher_id');
    }
}
