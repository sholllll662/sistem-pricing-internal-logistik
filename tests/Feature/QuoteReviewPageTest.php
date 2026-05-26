<?php

namespace Tests\Feature;

use App\Livewire\Quotes\ReviewQuote;
use App\Models\Customer;
use App\Models\Inquiry;
use App\Models\InquiryScenario;
use App\Models\Quote;
use App\Models\QuoteApproval;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class QuoteReviewPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_manager_can_open_quote_review_page(): void
    {
        [$manager, $quote] = $this->createManagerAndWaitingQuote();

        $response = $this->actingAs($manager)->get(route('quotes.review', ['quote' => $quote->id]));

        $response->assertOk();
        $response->assertSee($quote->quote_number);
    }

    public function test_non_manager_cannot_open_quote_review_page(): void
    {
        [, $quote] = $this->createManagerAndWaitingQuote();
        $sales = User::factory()->create();
        $salesRole = Role::query()->create(['code' => 'sales', 'name' => 'Sales']);
        $sales->roles()->attach($salesRole->id);

        $response = $this->actingAs($sales)->get(route('quotes.review', ['quote' => $quote->id]));

        $response->assertForbidden();
    }

    public function test_manager_can_approve_quote(): void
    {
        [$manager, $quote] = $this->createManagerAndWaitingQuote();

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

    public function test_manager_must_provide_reject_reason(): void
    {
        [$manager, $quote] = $this->createManagerAndWaitingQuote();

        Livewire::actingAs($manager)
            ->test(ReviewQuote::class, ['quote' => $quote])
            ->set('rejectNotes', '')
            ->call('reject')
            ->assertHasErrors(['rejectNotes']);

        $this->assertDatabaseCount('quote_approvals', 0);
    }

    private function createManagerAndWaitingQuote(): array
    {
        $manager = User::factory()->create();
        $managerRole = Role::query()->create(['code' => 'manager', 'name' => 'Manager']);
        $manager->roles()->attach($managerRole->id);

        $sales = User::factory()->create();
        $customer = Customer::query()->create([
            'code' => 'CUST-QR-001',
            'name' => 'PT Review Quote',
            'is_active' => true,
        ]);

        $inquiry = Inquiry::query()->create([
            'inquiry_number' => 'INQ-REV-001',
            'customer_id' => $customer->id,
            'sales_owner_id' => $sales->id,
            'status' => Inquiry::STATUS_DRAFT,
        ]);

        $scenario = InquiryScenario::query()->create([
            'inquiry_id' => $inquiry->id,
            'scenario_code' => 'SCN-001',
            'scenario_name' => 'Review Scenario',
            'status' => InquiryScenario::STATUS_DRAFT,
            'is_selected' => true,
        ]);

        $quote = Quote::query()->create([
            'quote_number' => 'Q-REVIEW-0001',
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

        return [$manager, $quote];
    }
}
