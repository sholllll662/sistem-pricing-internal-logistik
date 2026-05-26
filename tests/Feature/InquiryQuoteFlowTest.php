<?php

namespace Tests\Feature;

use App\Livewire\Inquiries\ScenarioBuilder;
use App\Livewire\Quotes\ReviewQuote;
use App\Models\CostCategory;
use App\Models\Customer;
use App\Models\Inquiry;
use App\Models\InquiryScenario;
use App\Models\LegCostItem;
use App\Models\Quote;
use App\Models\QuoteApproval;
use App\Models\Role;
use App\Models\ScenarioLeg;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class InquiryQuoteFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_authorized_user_can_open_inquiry_create_page(): void
    {
        $adminRole = Role::query()->create(['code' => 'admin', 'name' => 'Admin']);
        $admin = User::factory()->create();
        $admin->roles()->attach($adminRole->id);

        $response = $this->actingAs($admin)->get('/admin/inquiries/create');

        $response->assertOk();
    }

    public function test_inquiry_is_saved_with_required_relations_and_initial_status(): void
    {
        $customer = Customer::query()->create([
            'code' => 'CUST-IQ-001',
            'name' => 'PT Inquiry Quote',
            'is_active' => true,
        ]);
        $sales = User::factory()->create();

        $inquiry = Inquiry::query()->create([
            'inquiry_number' => 'INQ-IQ-001',
            'customer_id' => $customer->id,
            'sales_owner_id' => $sales->id,
            'status' => Inquiry::STATUS_DRAFT,
        ]);

        $this->assertDatabaseHas('inquiries', [
            'id' => $inquiry->id,
            'customer_id' => $customer->id,
            'sales_owner_id' => $sales->id,
            'status' => Inquiry::STATUS_DRAFT,
        ]);
        $this->assertSame($customer->id, $inquiry->customer->id);
        $this->assertSame($sales->id, $inquiry->salesOwner->id);
    }

    public function test_inquiry_creation_fails_when_required_fields_are_missing(): void
    {
        $this->expectException(QueryException::class);

        Inquiry::query()->create([
            'inquiry_number' => 'INQ-IQ-INVALID',
            'status' => Inquiry::STATUS_DRAFT,
        ]);
    }

    public function test_quote_draft_from_valid_scenario_saves_snapshot_and_default_status(): void
    {
        $salesRole = Role::query()->create(['code' => 'sales', 'name' => 'Sales']);
        $sales = User::factory()->create();
        $sales->roles()->attach($salesRole->id);

        $customer = Customer::query()->create([
            'code' => 'CUST-IQ-002',
            'name' => 'PT Quote Draft',
            'is_active' => true,
        ]);
        $inquiry = Inquiry::query()->create([
            'inquiry_number' => 'INQ-IQ-002',
            'customer_id' => $customer->id,
            'sales_owner_id' => $sales->id,
            'status' => Inquiry::STATUS_DRAFT,
        ]);
        $scenario = InquiryScenario::query()->create([
            'inquiry_id' => $inquiry->id,
            'scenario_code' => 'SCN-IQ-001',
            'scenario_name' => 'Scenario IQ',
            'status' => InquiryScenario::STATUS_DRAFT,
            'is_selected' => true,
        ]);
        $leg = ScenarioLeg::query()->create([
            'scenario_id' => $scenario->id,
            'sequence_no' => 1,
            'leg_type' => ScenarioLeg::TYPE_FIRST_MILE,
        ]);
        $category = CostCategory::query()->create(['code' => 'CAT-IQ', 'name' => 'Category IQ']);
        LegCostItem::query()->create([
            'leg_id' => $leg->id,
            'cost_category_id' => $category->id,
            'item_name' => 'Line item',
            'quantity' => 2,
            'unit_price' => 120000,
            'line_total' => 0,
            'is_manual_override' => false,
        ]);

        Livewire::actingAs($sales)
            ->test(ScenarioBuilder::class, ['inquiry' => $inquiry])
            ->call('createQuoteDraft')
            ->assertHasNoErrors();

        $quote = Quote::query()->first();
        $this->assertNotNull($quote);
        $this->assertSame(Quote::STATUS_DRAFT, $quote->status);
        $this->assertSame(Quote::STATUS_DRAFT, $quote->approval_status);
        $this->assertSame('240000.00', (string) $quote->total_base_cost_snapshot);
        $this->assertSame('240000.00', (string) $quote->total_selling_price_snapshot);
        $this->assertSame(now()->startOfDay()->format('Y-m-d'), $quote->valid_from->format('Y-m-d'));
        $this->assertSame(now()->startOfDay()->addMonths(3)->format('Y-m-d'), $quote->valid_until->format('Y-m-d'));
    }

    public function test_quote_validity_period_is_validated_on_draft_creation(): void
    {
        $salesRole = Role::query()->create(['code' => 'sales', 'name' => 'Sales']);
        $sales = User::factory()->create();
        $sales->roles()->attach($salesRole->id);

        $customer = Customer::query()->create([
            'code' => 'CUST-IQ-003',
            'name' => 'PT Validity',
            'is_active' => true,
        ]);
        $inquiry = Inquiry::query()->create([
            'inquiry_number' => 'INQ-IQ-003',
            'customer_id' => $customer->id,
            'sales_owner_id' => $sales->id,
            'status' => Inquiry::STATUS_DRAFT,
        ]);
        $scenario = InquiryScenario::query()->create([
            'inquiry_id' => $inquiry->id,
            'scenario_code' => 'SCN-IQ-002',
            'scenario_name' => 'Scenario Validity',
            'status' => InquiryScenario::STATUS_DRAFT,
            'is_selected' => true,
        ]);
        $leg = ScenarioLeg::query()->create([
            'scenario_id' => $scenario->id,
            'sequence_no' => 1,
            'leg_type' => ScenarioLeg::TYPE_FIRST_MILE,
        ]);
        $category = CostCategory::query()->create(['code' => 'CAT-IQ-2', 'name' => 'Category IQ 2']);
        LegCostItem::query()->create([
            'leg_id' => $leg->id,
            'cost_category_id' => $category->id,
            'item_name' => 'Line item',
            'quantity' => 1,
            'unit_price' => 100000,
            'line_total' => 0,
            'is_manual_override' => false,
        ]);

        Livewire::actingAs($sales)
            ->test(ScenarioBuilder::class, ['inquiry' => $inquiry])
            ->set('quoteValidityForm.valid_from', '2026-05-10')
            ->set('quoteValidityForm.valid_until', '2026-05-09')
            ->call('createQuoteDraft')
            ->assertHasErrors(['quoteValidityForm.valid_until']);
    }

    public function test_quote_approval_decision_is_persisted_consistently(): void
    {
        $managerRole = Role::query()->create(['code' => 'manager', 'name' => 'Manager']);
        $manager = User::factory()->create();
        $manager->roles()->attach($managerRole->id);
        $sales = User::factory()->create();

        $customer = Customer::query()->create([
            'code' => 'CUST-IQ-004',
            'name' => 'PT Approval',
            'is_active' => true,
        ]);
        $inquiry = Inquiry::query()->create([
            'inquiry_number' => 'INQ-IQ-004',
            'customer_id' => $customer->id,
            'sales_owner_id' => $sales->id,
            'status' => Inquiry::STATUS_DRAFT,
        ]);
        $scenario = InquiryScenario::query()->create([
            'inquiry_id' => $inquiry->id,
            'scenario_code' => 'SCN-IQ-003',
            'scenario_name' => 'Scenario Approval',
            'status' => InquiryScenario::STATUS_DRAFT,
            'is_selected' => true,
        ]);
        $quote = Quote::query()->create([
            'quote_number' => 'Q-IQ-0001',
            'inquiry_id' => $inquiry->id,
            'scenario_id' => $scenario->id,
            'prepared_by_user_id' => $sales->id,
            'valid_from' => now()->toDateString(),
            'valid_until' => now()->addMonths(3)->toDateString(),
            'total_base_cost_snapshot' => 100000,
            'total_margin_snapshot' => 10000,
            'total_selling_price_snapshot' => 110000,
            'status' => Quote::STATUS_WAITING_APPROVAL,
            'approval_status' => Quote::STATUS_WAITING_APPROVAL,
        ]);

        Livewire::actingAs($manager)
            ->test(ReviewQuote::class, ['quote' => $quote])
            ->call('approve')
            ->assertHasNoErrors();

        $quote->refresh();
        $this->assertSame(Quote::STATUS_APPROVED, $quote->status);
        $this->assertSame(Quote::STATUS_APPROVED, $quote->approval_status);
        $this->assertDatabaseHas('quote_approvals', [
            'quote_id' => $quote->id,
            'approver_user_id' => $manager->id,
            'decision' => QuoteApproval::DECISION_APPROVED,
        ]);
    }
}
