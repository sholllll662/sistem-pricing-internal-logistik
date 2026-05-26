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

class QuoteHistoryListTest extends TestCase
{
    use RefreshDatabase;

    public function test_sales_can_access_quote_history_list(): void
    {
        $sales = User::factory()->create();
        $salesRole = Role::query()->create(['code' => 'sales', 'name' => 'Sales']);
        $sales->roles()->attach($salesRole->id);

        $response = $this->actingAs($sales)->get('/admin/quote-histories');

        $response->assertOk();
        $response->assertSee('Quote History');
    }

    public function test_quote_history_shows_empty_state_when_no_data(): void
    {
        $admin = User::factory()->create();
        $adminRole = Role::query()->create(['code' => 'admin', 'name' => 'Admin']);
        $admin->roles()->attach($adminRole->id);

        $response = $this->actingAs($admin)->get('/admin/quote-histories');

        $response->assertOk();
        $response->assertSee('No quote history yet');
    }

    public function test_quote_history_lists_existing_quotes(): void
    {
        $admin = User::factory()->create();
        $adminRole = Role::query()->create(['code' => 'admin', 'name' => 'Admin']);
        $admin->roles()->attach($adminRole->id);

        $sales = User::factory()->create();
        $customer = Customer::query()->create([
            'code' => 'CUST-HIST-001',
            'name' => 'PT History',
            'is_active' => true,
        ]);

        $inquiry = Inquiry::query()->create([
            'inquiry_number' => 'INQ-HIST-001',
            'customer_id' => $customer->id,
            'sales_owner_id' => $sales->id,
            'status' => Inquiry::STATUS_DRAFT,
        ]);

        $scenario = InquiryScenario::query()->create([
            'inquiry_id' => $inquiry->id,
            'scenario_code' => 'SCN-H-001',
            'scenario_name' => 'History Scenario',
            'status' => InquiryScenario::STATUS_DRAFT,
            'is_selected' => true,
        ]);

        Quote::query()->create([
            'quote_number' => 'Q-HIST-0001',
            'inquiry_id' => $inquiry->id,
            'scenario_id' => $scenario->id,
            'prepared_by_user_id' => $sales->id,
            'valid_from' => now()->toDateString(),
            'valid_until' => now()->addMonths(3)->toDateString(),
            'total_base_cost_snapshot' => 100000,
            'total_margin_snapshot' => 20000,
            'total_selling_price_snapshot' => 120000,
            'status' => Quote::STATUS_DRAFT,
            'approval_status' => Quote::STATUS_DRAFT,
        ]);

        $response = $this->actingAs($admin)->get('/admin/quote-histories');

        $response->assertOk();
        $response->assertSee('Q-HIST-0001');
        $response->assertSee('PT History');
    }
}
