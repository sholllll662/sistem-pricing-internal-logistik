<?php

namespace Tests\Feature;

use App\Models\CostCategory;
use App\Models\Customer;
use App\Models\Inquiry;
use App\Models\InquiryScenario;
use App\Models\LegCostItem;
use App\Models\ScenarioLeg;
use App\Models\User;
use App\Models\Vendor;
use App\Services\Pricing\PricingCalculationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PricingCalculationServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_calculates_line_totals_leg_totals_and_scenario_totals(): void
    {
        $service = app(PricingCalculationService::class);
        $scenario = $this->createScenarioFixture();

        $legOne = ScenarioLeg::query()->create([
            'scenario_id' => $scenario->id,
            'sequence_no' => 1,
            'leg_type' => ScenarioLeg::TYPE_FIRST_MILE,
        ]);
        $legTwo = ScenarioLeg::query()->create([
            'scenario_id' => $scenario->id,
            'sequence_no' => 2,
            'leg_type' => ScenarioLeg::TYPE_LAST_MILE,
        ]);

        $category = CostCategory::query()->create([
            'code' => 'TRUCK',
            'name' => 'Trucking',
        ]);

        LegCostItem::query()->create([
            'leg_id' => $legOne->id,
            'cost_category_id' => $category->id,
            'item_name' => 'Pickup truck',
            'quantity' => 2,
            'unit_price' => 100000,
            'line_total' => 0,
            'is_manual_override' => false,
        ]);
        LegCostItem::query()->create([
            'leg_id' => $legTwo->id,
            'cost_category_id' => $category->id,
            'item_name' => 'Last mile handling',
            'quantity' => 1,
            'unit_price' => 50000,
            'line_total' => 80000,
            'is_manual_override' => true,
        ]);

        $result = $service->calculateScenario($scenario, marginNominal: 25000, surchargeNominal: 10000);

        $this->assertSame(280000.0, $result['scenario_base_cost']);
        $this->assertSame(25000.0, $result['margin_nominal']);
        $this->assertSame(10000.0, $result['surcharge_nominal']);
        $this->assertSame(315000.0, $result['selling_price']);
        $this->assertCount(2, $result['legs']);
        $this->assertSame(200000.0, $result['legs'][0]['base_cost']);
        $this->assertSame(80000.0, $result['legs'][1]['base_cost']);
    }

    public function test_it_refreshes_leg_and_scenario_snapshots(): void
    {
        $service = app(PricingCalculationService::class);
        $scenario = $this->createScenarioFixture();
        $category = CostCategory::query()->create([
            'code' => 'ADM',
            'name' => 'Admin',
        ]);

        $leg = ScenarioLeg::query()->create([
            'scenario_id' => $scenario->id,
            'sequence_no' => 1,
            'leg_type' => ScenarioLeg::TYPE_MIDDLE_MILE,
        ]);

        LegCostItem::query()->create([
            'leg_id' => $leg->id,
            'cost_category_id' => $category->id,
            'item_name' => 'Admin fee',
            'quantity' => 3,
            'unit_price' => 12000,
            'line_total' => 0,
            'is_manual_override' => false,
        ]);

        $service->refreshSnapshots($scenario, marginNominal: 5000);

        $this->assertSame('36000.00', (string) $leg->fresh()->base_cost_snapshot);
        $this->assertSame('36000.00', (string) $scenario->fresh()->total_base_cost_snapshot);
        $this->assertSame('5000.00', (string) $scenario->fresh()->total_margin_snapshot);
        $this->assertSame('41000.00', (string) $scenario->fresh()->total_selling_price_snapshot);
    }

    private function createScenarioFixture(): InquiryScenario
    {
        $user = User::factory()->create();

        $customer = Customer::query()->create([
            'code' => 'CUST-001',
            'name' => 'PT Contoh',
            'is_active' => true,
        ]);

        $inquiry = Inquiry::query()->create([
            'inquiry_number' => 'INQ-001',
            'customer_id' => $customer->id,
            'sales_owner_id' => $user->id,
            'status' => Inquiry::STATUS_DRAFT,
        ]);

        return InquiryScenario::query()->create([
            'inquiry_id' => $inquiry->id,
            'scenario_code' => 'SCN-001',
            'scenario_name' => 'Test Scenario',
            'status' => InquiryScenario::STATUS_DRAFT,
            'is_selected' => true,
        ]);
    }
}
