<?php

namespace App\Livewire\Quotes;

use App\Models\Quote;
use App\Models\QuoteApproval;
use App\Services\Audit\AuditLogService;
use Illuminate\Support\Carbon;
use Illuminate\View\View;
use Livewire\Component;

class ReviewQuote extends Component
{
    public Quote $quote;

    public string $rejectNotes = '';

    public function mount(Quote $quote): void
    {
        abort_unless(auth()->user()?->hasRole('manager'), 403);

        $this->quote = $quote->load([
            'inquiry.customer',
            'inquiry.salesOwner',
            'scenario.scenarioLegs.originLocation',
            'scenario.scenarioLegs.destinationLocation',
            'approvals.approver',
        ]);
    }

    public function approve(): void
    {
        abort_unless(auth()->user()?->hasRole('manager'), 403);
        if (! $this->canDecideApproval()) {
            return;
        }

        if ($this->quote->valid_until->isBefore(Carbon::today())) {
            $this->addError('decision', 'Quote validity has expired and cannot be approved.');

            return;
        }

        if ((float) $this->quote->total_selling_price_snapshot < (float) $this->quote->total_base_cost_snapshot) {
            $this->addError('decision', 'Quote selling price snapshot is below base cost and cannot be approved.');

            return;
        }

        QuoteApproval::query()->create([
            'quote_id' => $this->quote->id,
            'approver_user_id' => auth()->id(),
            'decision' => QuoteApproval::DECISION_APPROVED,
            'decided_at' => Carbon::now(),
        ]);

        $this->quote->update([
            'status' => Quote::STATUS_APPROVED,
            'approval_status' => Quote::STATUS_APPROVED,
        ]);

        app(AuditLogService::class)->log(
            auditable: $this->quote,
            eventName: 'approval_decided',
            oldValues: [
                'status' => Quote::STATUS_WAITING_APPROVAL,
                'approval_status' => Quote::STATUS_WAITING_APPROVAL,
            ],
            newValues: [
                'status' => Quote::STATUS_APPROVED,
                'approval_status' => Quote::STATUS_APPROVED,
                'decision' => QuoteApproval::DECISION_APPROVED,
            ],
            changedByUserId: auth()->id(),
            changedAt: Carbon::now(),
        );

        $this->quote->refresh();
        $this->quote->load('approvals.approver');
        session()->flash('reviewSuccess', 'Quote approved successfully.');
    }

    public function reject(): void
    {
        abort_unless(auth()->user()?->hasRole('manager'), 403);
        if (! $this->canDecideApproval()) {
            return;
        }

        $validated = $this->validate([
            'rejectNotes' => ['required', 'string', 'min:5'],
        ], [
            'rejectNotes.required' => 'Reject reason is required.',
        ]);

        QuoteApproval::query()->create([
            'quote_id' => $this->quote->id,
            'approver_user_id' => auth()->id(),
            'decision' => QuoteApproval::DECISION_REJECTED,
            'decision_notes' => $validated['rejectNotes'],
            'decided_at' => Carbon::now(),
        ]);

        $this->quote->update([
            'status' => Quote::STATUS_REJECTED,
            'approval_status' => Quote::STATUS_REJECTED,
        ]);

        app(AuditLogService::class)->log(
            auditable: $this->quote,
            eventName: 'approval_decided',
            oldValues: [
                'status' => Quote::STATUS_WAITING_APPROVAL,
                'approval_status' => Quote::STATUS_WAITING_APPROVAL,
            ],
            newValues: [
                'status' => Quote::STATUS_REJECTED,
                'approval_status' => Quote::STATUS_REJECTED,
                'decision' => QuoteApproval::DECISION_REJECTED,
                'decision_notes' => $validated['rejectNotes'],
            ],
            changedByUserId: auth()->id(),
            changedAt: Carbon::now(),
        );

        $this->rejectNotes = '';
        $this->quote->refresh();
        $this->quote->load('approvals.approver');
        session()->flash('reviewSuccess', 'Quote rejected successfully.');
    }

    public function render(): View
    {
        return view('livewire.quotes.review-quote')
            ->layout('layouts.app');
    }

    private function canDecideApproval(): bool
    {
        if (
            $this->quote->approval_status !== Quote::STATUS_WAITING_APPROVAL
            || $this->quote->status !== Quote::STATUS_WAITING_APPROVAL
        ) {
            $this->addError('decision', 'Quote is not in waiting approval status.');

            return false;
        }

        $alreadyDecided = QuoteApproval::query()
            ->where('quote_id', $this->quote->id)
            ->whereIn('decision', [
                QuoteApproval::DECISION_APPROVED,
                QuoteApproval::DECISION_REJECTED,
            ])
            ->exists();

        if ($alreadyDecided) {
            $this->addError('decision', 'Quote approval decision has already been made.');

            return false;
        }

        return true;
    }
}
