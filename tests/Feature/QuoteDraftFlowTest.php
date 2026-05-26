<?php

namespace Tests\Feature;

use App\Livewire\Inquiries\ScenarioBuilder;
use App\Models\CostCategory;
use App\Models\Customer;
use App\Models\Inquiry;
use App\Models\InquiryScenario;
use App\Models\LegCostItem;
use App\Models\Quote;
use App\Models\ScenarioLeg;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class QuoteDraftFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_quote_draft_from_complete_scenario(): void
    {
        $user = User::factory()->create();
        $inquiry = $this->createInquiry($user);
        $scenario = InquiryScenario::query()->create([
            'inquiry_id' => $inquiry->id,
            'scenario_code' => 'SCN-001',
            'scenario_name' => 'Scenario A',
            'status' => InquiryScenario::STATUS_DRAFT,
            'is_selected' => true,
        ]);

        $leg = ScenarioLeg::query()->create([
            'scenario_id' => $scenario->id,
            'sequence_no' => 1,
            'leg_type' => ScenarioLeg::TYPE_FIRST_MILE,
        ]);

        $category = CostCategory::query()->create([
            'code' => 'TRK',
            'name' => 'Trucking',
        ]);

        LegCostItem::query()->create([
            'leg_id' => $leg->id,
            'cost_category_id' => $category->id,
            'item_name' => 'Pickup truck',
            'quantity' => 2,
            'unit_price' => 150000,
            'line_total' => 0,
            'is_manual_override' => false,
        ]);

        Livewire::actingAs($user)
            ->test(ScenarioBuilder::class, ['inquiry' => $inquiry])
            ->call('createQuoteDraft')
            ->assertHasNoErrors('quoteDraft');

        $quote = Quote::query()->first();
        $this->assertNotNull($quote);
        $this->assertSame($inquiry->id, $quote->inquiry_id);
        $this->assertSame($scenario->id, $quote->scenario_id);
        $this->assertSame($user->id, $quote->prepared_by_user_id);
        $this->assertSame(Quote::STATUS_DRAFT, $quote->status);
        $this->assertSame('300000.00', (string) $quote->total_base_cost_snapshot);
        $this->assertSame('300000.00', (string) $quote->total_selling_price_snapshot);
        $this->assertSame(now()->startOfDay()->format('Y-m-d'), $quote->valid_from->format('Y-m-d'));
        $this->assertSame(now()->startOfDay()->addMonths(3)->format('Y-m-d'), $quote->valid_until->format('Y-m-d'));
    }

    public function test_quote_draft_is_rejected_for_incomplete_scenario(): void
    {
        $user = User::factory()->create();
        $inquiry = $this->createInquiry($user);
        $scenario = InquiryScenario::query()->create([
            'inquiry_id' => $inquiry->id,
            'scenario_code' => 'SCN-001',
            'scenario_name' => 'Scenario A',
            'status' => InquiryScenario::STATUS_DRAFT,
            'is_selected' => true,
        ]);

        ScenarioLeg::query()->create([
            'scenario_id' => $scenario->id,
            'sequence_no' => 1,
            'leg_type' => ScenarioLeg::TYPE_FIRST_MILE,
        ]);

        Livewire::actingAs($user)
            ->test(ScenarioBuilder::class, ['inquiry' => $inquiry])
            ->call('createQuoteDraft')
            ->assertHasErrors(['quoteDraft']);

        $this->assertDatabaseCount('quotes', 0);
    }

    public function test_quote_draft_is_rejected_if_valid_until_is_before_valid_from(): void
    {
        $user = User::factory()->create();
        $inquiry = $this->createInquiry($user);
        $scenario = InquiryScenario::query()->create([
            'inquiry_id' => $inquiry->id,
            'scenario_code' => 'SCN-001',
            'scenario_name' => 'Scenario A',
            'status' => InquiryScenario::STATUS_DRAFT,
            'is_selected' => true,
        ]);

        $leg = ScenarioLeg::query()->create([
            'scenario_id' => $scenario->id,
            'sequence_no' => 1,
            'leg_type' => ScenarioLeg::TYPE_FIRST_MILE,
        ]);

        $category = CostCategory::query()->create([
            'code' => 'TRK',
            'name' => 'Trucking',
        ]);

        LegCostItem::query()->create([
            'leg_id' => $leg->id,
            'cost_category_id' => $category->id,
            'item_name' => 'Pickup truck',
            'quantity' => 1,
            'unit_price' => 100000,
            'line_total' => 0,
            'is_manual_override' => false,
        ]);

        Livewire::actingAs($user)
            ->test(ScenarioBuilder::class, ['inquiry' => $inquiry])
            ->set('quoteValidityForm.valid_from', '2026-01-10')
            ->set('quoteValidityForm.valid_until', '2026-01-09')
            ->call('createQuoteDraft')
            ->assertHasErrors(['quoteValidityForm.valid_until']);

        $this->assertDatabaseCount('quotes', 0);
    }

    private function createInquiry(User $user): Inquiry
    {
        $customer = Customer::query()->create([
            'code' => 'CUST-001',
            'name' => 'PT Contoh',
            'is_active' => true,
        ]);

        return Inquiry::query()->create([
            'inquiry_number' => 'INQ-001',
            'customer_id' => $customer->id,
            'sales_owner_id' => $user->id,
            'status' => Inquiry::STATUS_DRAFT,
        ]);
    }
}
