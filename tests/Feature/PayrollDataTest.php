<?php

namespace Tests\Feature;

use App\Enums\Teacher\TeacherPayrollStatusEnum;
use App\Enums\Teacher\TeacherPermissionEnum;
use App\Enums\Teacher\TeacherRoleEnum;
use App\Livewire\TeacherPanel\Pages\Payroll\Parts\PayrollData;
use App\Livewire\TeacherPanel\Pages\Payroll\Parts\PayrollGeneratePage;
use App\Models\Branch;
use App\Models\Teacher;
use App\Models\TeacherPayroll;
use Database\Factories\BranchFactory;
use Database\Factories\TeacherFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PayrollDataTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Attendance is outside this feature's scope and its migration defines duplicate columns.
     *
     * @return array<string, mixed>
     */
    protected function migrateFreshUsing(): array
    {
        return [
            '--path' => collect(glob(database_path('migrations/*.php')))
                ->reject(fn (string $path) => str_contains($path, 'create_attendances_table'))
                ->values()->all(),
            '--realpath' => true,
        ];
    }

    public function test_staff_sees_owner_family_payrolls_only_for_accessible_branches_and_owner_sees_all(): void
    {
        $owner = TeacherFactory::new()->create(['role' => TeacherRoleEnum::OWNER]);
        $visibleTeacher = TeacherFactory::new()->create([
            'owner_id' => $owner->id,
            'role' => TeacherRoleEnum::STAFF,
            'name' => 'مدرس الفرع المتاح',
        ]);
        $hiddenTeacher = TeacherFactory::new()->create([
            'owner_id' => $owner->id,
            'role' => TeacherRoleEnum::STAFF,
            'name' => 'مدرس الفرع غير المتاح',
        ]);
        $otherOwner = TeacherFactory::new()->create(['role' => TeacherRoleEnum::OWNER]);

        $visibleBranch = BranchFactory::new()->create(['teacher_id' => $owner->id]);
        $hiddenBranch = BranchFactory::new()->create(['teacher_id' => $owner->id]);
        $otherBranch = BranchFactory::new()->create(['teacher_id' => $otherOwner->id]);
        $visibleTeacher->branches()->attach($visibleBranch->id);
        $hiddenTeacher->branches()->attach($hiddenBranch->id);
        $visibleTeacher->branches()->attach($hiddenBranch->id);

        $visiblePayroll = $this->createPayroll($visibleTeacher, $visibleBranch, 'PR-VISIBLE');
        $hiddenPayroll = $this->createPayroll($visibleTeacher, $hiddenBranch, 'PR-HIDDEN-BRANCH');
        $otherTenantPayroll = $this->createPayroll($otherOwner, $otherBranch, 'PR-OTHER-OWNER');

        $this->actingAs($visibleTeacher, 'teacher');

        Livewire::test(PayrollData::class)
            ->assertSee($visiblePayroll->reference)
            ->assertDontSee($hiddenPayroll->reference)
            ->assertDontSee($otherTenantPayroll->reference)
            ->assertSee($visibleTeacher->name)
            ->assertDontSee($hiddenTeacher->name);

        $this->actingAs($owner, 'teacher');

        Livewire::test(PayrollData::class)
            ->assertSee($visiblePayroll->reference)
            ->assertSee($hiddenPayroll->reference)
            ->assertDontSee($otherTenantPayroll->reference);
    }

    public function test_staff_must_choose_an_accessible_branch_to_generate_payrolls(): void
    {
        $owner = TeacherFactory::new()->create(['role' => TeacherRoleEnum::OWNER]);
        $staff = TeacherFactory::new()->create([
            'owner_id' => $owner->id,
            'role' => TeacherRoleEnum::STAFF,
        ]);
        $accessibleBranch = BranchFactory::new()->create(['teacher_id' => $owner->id]);
        $inaccessibleBranch = BranchFactory::new()->create(['teacher_id' => $owner->id]);
        $staff->branches()->attach($accessibleBranch->id);
        $staff->givePermission(TeacherPermissionEnum::PAYROLL_GENERATE);

        $this->actingAs($staff, 'teacher');

        Livewire::test(PayrollGeneratePage::class)
            ->set('selectedTeacherIds', [$staff->id])
            ->assertSee('اختر فرعًا')
            ->call('generate')
            ->assertDispatched('show-error-message')
            ->set('branchId', (string) $inaccessibleBranch->id)
            ->call('generate')
            ->assertDispatched('show-error-message');
    }

    private function createPayroll(Teacher $teacher, Branch $branch, string $reference): TeacherPayroll
    {
        return TeacherPayroll::create([
            'reference' => $reference,
            'teacher_id' => $teacher->id,
            'branch_id' => $branch->id,
            'period_start' => now()->startOfMonth()->toDateString(),
            'period_end' => now()->endOfMonth()->toDateString(),
            'period_label' => now()->format('F Y'),
            'payment_type' => 'monthly',
            'status' => TeacherPayrollStatusEnum::PENDING,
        ]);
    }
}
