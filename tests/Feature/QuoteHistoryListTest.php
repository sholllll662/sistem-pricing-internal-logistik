<?php

namespace Tests\Feature;

use App\Filament\Resources\QuoteHistories\Pages\ListQuoteHistories;
use App\Models\Customer;
use App\Models\CostCategory;
use App\Models\Inquiry;
use App\Models\InquiryScenario;
use App\Models\LegCostItem;
use App\Models\Location;
use App\Models\Quote;
use App\Models\Role;
use App\Models\ScenarioLeg;
use App\Models\User;
use App\Models\VehicleType;
use App\Models\Vendor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
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

    public function test_quote_history_can_filter_by_customer_route_vendor_and_vehicle_type(): void
    {
        $salesRole = Role::query()->create(['code' => 'sales', 'name' => 'Sales']);

        $sales = User::factory()->create();
        $sales->roles()->attach($salesRole->id);

        $originA = Location::query()->create(['code' => 'OR-A', 'name' => 'Jakarta', 'location_type' => 'city']);
        $destinationA = Location::query()->create(['code' => 'DS-A', 'name' => 'Surabaya', 'location_type' => 'city']);
        $originB = Location::query()->create(['code' => 'OR-B', 'name' => 'Bandung', 'location_type' => 'city']);
        $destinationB = Location::query()->create(['code' => 'DS-B', 'name' => 'Semarang', 'location_type' => 'city']);

        $customerMatch = Customer::query()->create(['code' => 'CUST-M', 'name' => 'PT Match', 'is_active' => true]);
        $customerOther = Customer::query()->create(['code' => 'CUST-O', 'name' => 'PT Other', 'is_active' => true]);

        $vehicleMatch = VehicleType::query()->create(['code' => 'TRUCK-M', 'name' => 'Truck Match']);
        $vehicleOther = VehicleType::query()->create(['code' => 'TRUCK-O', 'name' => 'Truck Other']);

        $vendorMatch = Vendor::query()->create(['code' => 'V-M', 'name' => 'Vendor Match', 'is_active' => true]);
        $vendorOther = Vendor::query()->create(['code' => 'V-O', 'name' => 'Vendor Other', 'is_active' => true]);
        $costCategory = CostCategory::query()->create(['code' => 'CAT-HIST', 'name' => 'History Cost']);

        $inquiryMatch = Inquiry::query()->create([
            'inquiry_number' => 'INQ-HIST-MATCH',
            'customer_id' => $customerMatch->id,
            'sales_owner_id' => $sales->id,
            'origin_location_id' => $originA->id,
            'destination_location_id' => $destinationA->id,
            'status' => Inquiry::STATUS_DRAFT,
        ]);
        $scenarioMatch = InquiryScenario::query()->create([
            'inquiry_id' => $inquiryMatch->id,
            'scenario_code' => 'SCN-MATCH',
            'scenario_name' => 'Scenario Match',
            'status' => InquiryScenario::STATUS_DRAFT,
            'is_selected' => true,
        ]);
        $legMatch = ScenarioLeg::query()->create([
            'scenario_id' => $scenarioMatch->id,
            'sequence_no' => 1,
            'leg_type' => ScenarioLeg::TYPE_FIRST_MILE,
            'primary_vendor_id' => $vendorMatch->id,
            'vehicle_type_id' => $vehicleMatch->id,
        ]);
        LegCostItem::query()->create([
            'leg_id' => $legMatch->id,
            'cost_category_id' => $costCategory->id,
            'vendor_id' => $vendorMatch->id,
            'item_name' => 'Match item',
            'quantity' => 1,
            'unit_price' => 100000,
            'line_total' => 100000,
            'is_manual_override' => true,
        ]);
        Quote::query()->create([
            'quote_number' => 'Q-HIST-MATCH',
            'inquiry_id' => $inquiryMatch->id,
            'scenario_id' => $scenarioMatch->id,
            'prepared_by_user_id' => $sales->id,
            'valid_from' => now()->toDateString(),
            'valid_until' => now()->addMonths(3)->toDateString(),
            'total_base_cost_snapshot' => 100000,
            'total_margin_snapshot' => 10000,
            'total_selling_price_snapshot' => 110000,
            'status' => Quote::STATUS_DRAFT,
            'approval_status' => Quote::STATUS_DRAFT,
        ]);

        $inquiryOther = Inquiry::query()->create([
            'inquiry_number' => 'INQ-HIST-OTHER',
            'customer_id' => $customerOther->id,
            'sales_owner_id' => $sales->id,
            'origin_location_id' => $originB->id,
            'destination_location_id' => $destinationB->id,
            'status' => Inquiry::STATUS_DRAFT,
        ]);
        $scenarioOther = InquiryScenario::query()->create([
            'inquiry_id' => $inquiryOther->id,
            'scenario_code' => 'SCN-OTHER',
            'scenario_name' => 'Scenario Other',
            'status' => InquiryScenario::STATUS_DRAFT,
            'is_selected' => true,
        ]);
        $legOther = ScenarioLeg::query()->create([
            'scenario_id' => $scenarioOther->id,
            'sequence_no' => 1,
            'leg_type' => ScenarioLeg::TYPE_FIRST_MILE,
            'primary_vendor_id' => $vendorOther->id,
            'vehicle_type_id' => $vehicleOther->id,
        ]);
        LegCostItem::query()->create([
            'leg_id' => $legOther->id,
            'cost_category_id' => $costCategory->id,
            'vendor_id' => $vendorOther->id,
            'item_name' => 'Other item',
            'quantity' => 1,
            'unit_price' => 90000,
            'line_total' => 90000,
            'is_manual_override' => true,
        ]);
        Quote::query()->create([
            'quote_number' => 'Q-HIST-OTHER',
            'inquiry_id' => $inquiryOther->id,
            'scenario_id' => $scenarioOther->id,
            'prepared_by_user_id' => $sales->id,
            'valid_from' => now()->toDateString(),
            'valid_until' => now()->addMonths(3)->toDateString(),
            'total_base_cost_snapshot' => 90000,
            'total_margin_snapshot' => 9000,
            'total_selling_price_snapshot' => 99000,
            'status' => Quote::STATUS_DRAFT,
            'approval_status' => Quote::STATUS_DRAFT,
        ]);

        Livewire::actingAs($sales)
            ->test(ListQuoteHistories::class)
            ->set('tableFilters.customer.value', (string) $customerMatch->id)
            ->set('tableFilters.route.value', $originA->id.'-'.$destinationA->id)
            ->set('tableFilters.vendor.value', (string) $vendorMatch->id)
            ->set('tableFilters.vehicle_type.value', (string) $vehicleMatch->id)
            ->assertSee('Q-HIST-MATCH')
            ->assertDontSee('Q-HIST-OTHER');
    }
}
