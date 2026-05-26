<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Inquiry;
use App\Models\InquiryScenario;
use App\Models\Quote;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ManagerApprovalQueueTest extends TestCase
{
    use RefreshDatabase;

    public function test_manager_can_access_approval_queue(): void
    {
        $manager = User::factory()->create();
        $managerRole = Role::query()->create(['code' => 'manager', 'name' => 'Manager']);
        $manager->roles()->attach($managerRole->id);

        $response = $this->actingAs($manager)->get('/admin/quotes');

        $response->assertOk();
        $response->assertSee('Approval Queue');
    }

    public function test_non_manager_cannot_access_approval_queue(): void
    {
        $sales = User::factory()->create();
        $salesRole = Role::query()->create(['code' => 'sales', 'name' => 'Sales']);
        $sales->roles()->attach($salesRole->id);

        $response = $this->actingAs($sales)->get('/admin/quotes');

        $response->assertForbidden();
    }

    public function test_waiting_approval_quotes_are_listed_in_queue(): void
    {
        $manager = User::factory()->create();
        $managerRole = Role::query()->create(['code' => 'manager', 'name' => 'Manager']);
        $manager->roles()->attach($managerRole->id);

        $sales = User::factory()->create();
        $customer = Customer::query()->create([
            'code' => 'CUST-001',
            'name' => 'PT Queue Test',
            'is_active' => true,
        ]);

        $inquiry = Inquiry::query()->create([
            'inquiry_number' => 'INQ-QUEUE-001',
            'customer_id' => $customer->id,
            'sales_owner_id' => $sales->id,
            'status' => Inquiry::STATUS_DRAFT,
        ]);

        $scenario = InquiryScenario::query()->create([
            'inquiry_id' => $inquiry->id,
            'scenario_code' => 'SCN-001',
            'scenario_name' => 'Queue Scenario',
            'status' => InquiryScenario::STATUS_DRAFT,
            'is_selected' => true,
        ]);

        Quote::query()->create([
            'quote_number' => 'Q-TEST-0001',
            'inquiry_id' => $inquiry->id,
            'scenario_id' => $scenario->id,
            'prepared_by_user_id' => $sales->id,
            'valid_from' => now()->toDateString(),
            'valid_until' => now()->addMonths(3)->toDateString(),
            'total_base_cost_snapshot' => 100000,
            'total_margin_snapshot' => 10000,
            'total_selling_price_snapshot' => 110000,
            'status' => Quote::STATUS_DRAFT,
            'approval_status' => Quote::STATUS_WAITING_APPROVAL,
        ]);

        Quote::query()->create([
            'quote_number' => 'Q-TEST-0002',
            'inquiry_id' => $inquiry->id,
            'scenario_id' => $scenario->id,
            'prepared_by_user_id' => $sales->id,
            'valid_from' => now()->toDateString(),
            'valid_until' => now()->addMonths(3)->toDateString(),
            'total_base_cost_snapshot' => 200000,
            'total_margin_snapshot' => 20000,
            'total_selling_price_snapshot' => 220000,
            'status' => Quote::STATUS_DRAFT,
            'approval_status' => Quote::STATUS_DRAFT,
        ]);

        $response = $this->actingAs($manager)->get('/admin/quotes');

        $response->assertOk();
        $response->assertSee('Q-TEST-0001');
        $response->assertDontSee('Q-TEST-0002');
    }
}
