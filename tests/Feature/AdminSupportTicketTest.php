<?php

namespace Tests\Feature;

use App\Enums\Admin\SupportTicketStatusEnum;
use App\Livewire\AdminPanel\Pages\Support\Parts\SupportTicketShow;
use App\Models\Admin;
use App\Models\SupportTicket;
use App\Models\Teacher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AdminSupportTicketTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_support_index_page_is_accessible(): void
    {
        $admin = Admin::factory()->create();

        $response = $this
            ->actingAs($admin, 'admin')
            ->get(route('admin.support.index'));

        $response->assertOk();
    }

    public function test_admin_can_view_any_support_ticket(): void
    {
        $admin = Admin::factory()->create();
        $teacher = Teacher::factory()->create();

        $ticket = SupportTicket::create([
            'teacher_id' => $teacher->id,
            'created_by' => $teacher->id,
            'subject' => 'مشكلة في تسجيل الحضور',
            'category' => 'technical',
            'priority' => 'high',
            'status' => SupportTicketStatusEnum::OPEN->value,
        ]);

        $response = $this
            ->actingAs($admin, 'admin')
            ->get(route('admin.support.show', $ticket));

        $response->assertOk();
        $response->assertSee($ticket->reference);
    }

    public function test_admin_can_reply_without_validating_a_missing_status_property(): void
    {
        $admin = Admin::factory()->create();
        $teacher = Teacher::factory()->create();

        $ticket = SupportTicket::create([
            'teacher_id' => $teacher->id,
            'created_by' => $teacher->id,
            'subject' => 'مشكلة في تسجيل الحضور',
            'category' => 'technical',
            'priority' => 'high',
            'status' => SupportTicketStatusEnum::OPEN->value,
        ]);

        $this->actingAs($admin, 'admin');

        Livewire::test(SupportTicketShow::class, ['ticket' => $ticket])
            ->set('newMessage', 'تم استلام طلبك.')
            ->call('sendMessage')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('support_ticket_messages', [
            'ticket_id' => $ticket->id,
            'sender_type' => 'admin',
            'sender_id' => $admin->id,
            'body' => 'تم استلام طلبك.',
        ]);
    }
}
