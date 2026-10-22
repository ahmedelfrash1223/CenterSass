<?php

namespace Tests\Feature;

use App\Enums\Teacher\CardRequestStatusEnum;
use App\Enums\Teacher\RevenueSourceEnum;
use App\Livewire\AdminPanel\Pages\CardRequest\Parts\CardRequestData;
use App\Livewire\AdminPanel\Pages\CardRequest\Parts\CardRequestShow;
use App\Models\Admin;
use App\Models\CardRequest;
use App\Models\CardRequestItem;
use App\Models\Student;
use App\Models\Teacher;
use App\Services\Teacher\RevenueService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AdminCardRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_card_requests_from_all_teachers(): void
    {
        $admin = Admin::factory()->create();
        $firstTeacher = Teacher::factory()->create();
        $secondTeacher = Teacher::factory()->create();

        $firstRequest = $this->createCardRequest($firstTeacher);
        $secondRequest = $this->createCardRequest($secondTeacher);

        $this->actingAs($admin, 'admin')
            ->get(route('admin.card-requests.index'))
            ->assertOk()
            ->assertSee($firstRequest->reference)
            ->assertSee($secondRequest->reference);

        Livewire::actingAs($admin, 'admin')
            ->test(CardRequestData::class)
            ->assertSee($firstTeacher->name)
            ->assertSee($secondTeacher->name);
    }

    public function test_admin_can_approve_or_reject_pending_requests_and_rejection_requires_notes(): void
    {
        $admin = Admin::factory()->create();
        $teacher = Teacher::factory()->create();
        $approveRequest = $this->createCardRequest($teacher);
        $rejectRequest = $this->createCardRequest($teacher);

        Livewire::actingAs($admin, 'admin')
            ->test(CardRequestShow::class, ['cardRequest' => $approveRequest])
            ->set('adminNotes', 'مقبول')
            ->call('review', 'approve')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('card_requests', [
            'id' => $approveRequest->id,
            'status' => CardRequestStatusEnum::APPROVED->value,
            'admin_notes' => 'مقبول',
        ]);

        Livewire::actingAs($admin, 'admin')
            ->test(CardRequestShow::class, ['cardRequest' => $rejectRequest])
            ->call('review', 'reject')
            ->assertHasErrors(['admin_notes' => 'required_if']);

        $this->assertDatabaseHas('card_requests', [
            'id' => $rejectRequest->id,
            'status' => CardRequestStatusEnum::PENDING->value,
        ]);

        Livewire::actingAs($admin, 'admin')
            ->test(CardRequestShow::class, ['cardRequest' => $rejectRequest])
            ->set('adminNotes', 'البيانات غير مكتملة')
            ->call('review', 'reject')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('card_requests', [
            'id' => $rejectRequest->id,
            'status' => CardRequestStatusEnum::REJECTED->value,
            'admin_notes' => 'البيانات غير مكتملة',
        ]);
    }

    public function test_admin_can_cancel_only_requests_allowed_by_the_model_state_rule(): void
    {
        $admin = Admin::factory()->create();
        $teacher = Teacher::factory()->create();
        $pendingRequest = $this->createCardRequest($teacher);
        $rejectedRequest = $this->createCardRequest($teacher, [
            'status' => CardRequestStatusEnum::REJECTED->value,
        ]);

        Livewire::actingAs($admin, 'admin')
            ->test(CardRequestData::class)
            ->call('confirmCancel', $pendingRequest->id)
            ->call('cancelRequest')
            ->assertDispatched('show-success-message');

        $this->assertDatabaseHas('card_requests', [
            'id' => $pendingRequest->id,
            'status' => CardRequestStatusEnum::CANCELLED->value,
        ]);

        Livewire::actingAs($admin, 'admin')
            ->test(CardRequestData::class)
            ->call('confirmCancel', $rejectedRequest->id)
            ->call('cancelRequest')
            ->assertDispatched('show-error-message');

        $this->assertDatabaseHas('card_requests', [
            'id' => $rejectedRequest->id,
            'status' => CardRequestStatusEnum::REJECTED->value,
        ]);
    }

    public function test_admin_payment_records_revenue_and_admin_audit_identity(): void
    {
        $admin = Admin::factory()->create();
        $teacher = Teacher::factory()->create();
        $student = Student::factory()->create(['teacher_id' => $teacher->id]);
        $cardRequest = $this->createCardRequest($teacher, [
            'cards_count' => 1,
            'unit_price' => 25,
            'total_price' => 25,
        ]);
        $item = CardRequestItem::create([
            'card_request_id' => $cardRequest->id,
            'student_id' => $student->id,
            'student_name' => $student->name,
            'student_code' => $student->code,
            'card_price' => 25,
        ]);

        Livewire::actingAs($admin, 'admin')
            ->test(CardRequestShow::class, ['cardRequest' => $cardRequest])
            ->set('paidItemIds', [$item->id])
            ->call('markAsPaid')
            ->assertHasNoErrors();

        $item->refresh();
        $cardRequest->refresh();

        $this->assertTrue($item->is_paid);
        $this->assertSame($admin->id, $item->paid_by_admin_id);
        $this->assertNull($item->paid_by);
        $this->assertSame(1, $cardRequest->paid_cards_count);
        $this->assertSame('25.00', $cardRequest->paid_amount);

        $this->assertDatabaseHas('revenues', [
            'id' => $item->revenue_id,
            'teacher_id' => $teacher->id,
            'recorded_by' => null,
            'recorded_by_admin_id' => $admin->id,
            'source_id' => $cardRequest->id,
            'student_id' => $student->id,
            'amount' => '25.00',
        ]);
    }

    public function test_revenue_service_keeps_teacher_payment_attribution_unchanged(): void
    {
        $teacher = Teacher::factory()->create();

        $revenue = app(RevenueService::class)->record(
            user: $teacher,
            source: RevenueSourceEnum::CARD_REQUEST,
            amount: 10,
        );

        $this->assertSame($teacher->id, $revenue->teacher_id);
        $this->assertSame($teacher->id, $revenue->recorded_by);
        $this->assertNull($revenue->recorded_by_admin_id);
    }

    private function createCardRequest(Teacher $teacher, array $attributes = []): CardRequest
    {
        return CardRequest::create(array_merge([
            'teacher_id' => $teacher->id,
            'created_by' => $teacher->id,
            'status' => CardRequestStatusEnum::PENDING->value,
            'cards_count' => 0,
            'unit_price' => 0,
            'total_price' => 0,
        ], $attributes));
    }
}
