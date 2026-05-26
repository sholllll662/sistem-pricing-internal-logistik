<div class="py-8">
    <div class="mx-auto max-w-6xl space-y-6 px-4 sm:px-6 lg:px-8">
        <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-900">Quote Review</h1>
                    <p class="mt-1 text-sm text-gray-600">
                        {{ $quote->quote_number }} - {{ $quote->inquiry?->inquiry_number }}
                    </p>
                </div>
                <a href="{{ route('filament.admin.resources.quotes.index') }}" class="inline-flex items-center rounded-md border border-gray-300 px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Back to Approval Queue
                </a>
            </div>

            @if (session('reviewSuccess'))
                <div class="mt-4 rounded-md border border-emerald-200 bg-emerald-50 p-3 text-sm text-emerald-800">
                    {{ session('reviewSuccess') }}
                </div>
            @endif
            @error('decision')
                <div class="mt-4 rounded-md border border-red-200 bg-red-50 p-3 text-sm text-red-700">
                    {{ $message }}
                </div>
            @enderror
            <div wire:loading.delay.short wire:target="approve,reject" class="mt-4 rounded-md border border-gray-200 bg-gray-50 p-3 text-sm text-gray-600">
                Processing approval decision...
            </div>
        </div>

        <div class="grid gap-6 md:grid-cols-2">
            <div class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
                <h2 class="text-sm font-semibold text-gray-900">Quote Context</h2>
                <div class="mt-3 space-y-2 text-sm text-gray-700">
                    <p><span class="font-medium">Customer:</span> {{ $quote->inquiry?->customer?->name ?? '-' }}</p>
                    <p><span class="font-medium">Sales Owner:</span> {{ $quote->inquiry?->salesOwner?->name ?? '-' }}</p>
                    <p><span class="font-medium">Scenario:</span> {{ $quote->scenario?->scenario_name ?? '-' }}</p>
                    <p><span class="font-medium">Valid From:</span> {{ $quote->valid_from?->format('Y-m-d') ?? '-' }}</p>
                    <p><span class="font-medium">Valid Until:</span> {{ $quote->valid_until?->format('Y-m-d') ?? '-' }}</p>
                    <p><span class="font-medium">Quote Status:</span> {{ $quote->status }}</p>
                    <p><span class="font-medium">Approval Status:</span> {{ $quote->approval_status }}</p>
                </div>
            </div>

            <div class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
                <h2 class="text-sm font-semibold text-gray-900">Pricing Snapshot</h2>
                <div class="mt-3 grid gap-3 text-sm">
                    <div class="rounded-md border border-gray-200 bg-gray-50 p-3">
                        <p class="text-xs text-gray-600">Base Cost</p>
                        <p class="mt-1 font-semibold text-gray-900">{{ number_format((float) $quote->total_base_cost_snapshot, 2) }}</p>
                    </div>
                    <div class="rounded-md border border-gray-200 bg-gray-50 p-3">
                        <p class="text-xs text-gray-600">Margin</p>
                        <p class="mt-1 font-semibold text-gray-900">{{ number_format((float) $quote->total_margin_snapshot, 2) }}</p>
                    </div>
                    <div class="rounded-md border border-amber-200 bg-amber-50 p-3">
                        <p class="text-xs text-amber-700">Selling Price</p>
                        <p class="mt-1 font-semibold text-amber-900">{{ number_format((float) $quote->total_selling_price_snapshot, 2) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
            <h2 class="text-sm font-semibold text-gray-900">Scenario Legs</h2>
            @if ($quote->scenario?->scenarioLegs?->isEmpty())
                <div class="mt-3 rounded-md border border-dashed border-gray-300 bg-gray-50 p-3 text-sm text-gray-600">
                    No leg details available yet. Ask sales to complete scenario legs before final review.
                </div>
            @else
                <div class="mt-3 overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-3 py-2 text-left font-semibold text-gray-700">Seq</th>
                                <th class="px-3 py-2 text-left font-semibold text-gray-700">Type</th>
                                <th class="px-3 py-2 text-left font-semibold text-gray-700">Origin</th>
                                <th class="px-3 py-2 text-left font-semibold text-gray-700">Destination</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($quote->scenario->scenarioLegs as $leg)
                                <tr>
                                    <td class="px-3 py-2 text-gray-700">{{ $leg->sequence_no }}</td>
                                    <td class="px-3 py-2 text-gray-700">{{ $leg->leg_type }}</td>
                                    <td class="px-3 py-2 text-gray-700">{{ $leg->originLocation?->name ?? '-' }}</td>
                                    <td class="px-3 py-2 text-gray-700">{{ $leg->destinationLocation?->name ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <div class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
            <h2 class="text-sm font-semibold text-gray-900">Decision</h2>
            <div class="mt-3 grid gap-3 md:grid-cols-2">
                <button
                    type="button"
                    wire:click="approve"
                    wire:loading.attr="disabled"
                    wire:target="approve"
                    class="inline-flex items-center justify-center rounded-md bg-emerald-600 px-3 py-2 text-sm font-medium text-white hover:bg-emerald-700 disabled:opacity-50"
                    @disabled($quote->approval_status !== 'waiting_approval')
                >
                    <span wire:loading.remove wire:target="approve">Approve Quote</span>
                    <span wire:loading wire:target="approve">Approving...</span>
                </button>
                <div class="space-y-2">
                    <textarea
                        rows="3"
                        wire:model="rejectNotes"
                        class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-red-500 focus:ring-red-500"
                        placeholder="Reject reason (required)"
                    ></textarea>
                    @error('rejectNotes') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                    <button
                        type="button"
                        wire:click="reject"
                        wire:loading.attr="disabled"
                        wire:target="reject"
                        class="inline-flex items-center rounded-md bg-red-600 px-3 py-2 text-sm font-medium text-white hover:bg-red-700 disabled:opacity-50"
                        @disabled($quote->approval_status !== 'waiting_approval')
                    >
                        <span wire:loading.remove wire:target="reject">Reject Quote</span>
                        <span wire:loading wire:target="reject">Rejecting...</span>
                    </button>
                </div>
            </div>
        </div>

        <div class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
            <h2 class="text-sm font-semibold text-gray-900">Approval History</h2>
            @if ($quote->approvals->isEmpty())
                <div class="mt-3 rounded-md border border-dashed border-gray-300 bg-gray-50 p-3 text-sm text-gray-600">
                    No approval decision yet. Manager can approve or reject once the quote is in waiting approval status.
                </div>
            @else
                <ul class="mt-3 space-y-2 text-sm text-gray-700">
                    @foreach ($quote->approvals as $approval)
                        <li class="rounded-md border border-gray-200 bg-gray-50 p-3">
                            <p><span class="font-medium">Decision:</span> {{ $approval->decision }}</p>
                            <p><span class="font-medium">Approver:</span> {{ $approval->approver?->name ?? '-' }}</p>
                            <p><span class="font-medium">Decided At:</span> {{ $approval->decided_at?->format('Y-m-d H:i') ?? '-' }}</p>
                            <p><span class="font-medium">Notes:</span> {{ $approval->decision_notes ?? '-' }}</p>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
</div>
