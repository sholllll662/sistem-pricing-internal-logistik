<div class="py-8">
    <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
        <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-900">Scenario Builder</h1>
                    <p class="mt-1 text-sm text-gray-600">
                        Inquiry {{ $inquiry->inquiry_number }} - {{ $inquiry->customer?->name ?? '-' }}
                    </p>
                </div>
                <a
                    href="{{ route('filament.admin.resources.inquiries.edit', ['record' => $inquiry->id]) }}"
                    class="inline-flex items-center rounded-md border border-gray-300 px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                >
                    Back to Inquiry
                </a>
            </div>

            <div class="mt-4 grid gap-3 text-sm text-gray-700 md:grid-cols-2">
                <p><span class="font-medium">Pickup Contact:</span> {{ $inquiry->pickupContact?->name ?? '-' }}</p>
                <p><span class="font-medium">Drop Contact:</span> {{ $inquiry->dropContact?->name ?? '-' }}</p>
                <p><span class="font-medium">Origin:</span> {{ $inquiry->originLocation?->name ?? '-' }}</p>
                <p><span class="font-medium">Destination:</span> {{ $inquiry->destinationLocation?->name ?? '-' }}</p>
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-3">
            <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm lg:col-span-1">
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-900">Scenarios</h2>
                    <span class="text-xs text-gray-500">{{ $this->scenarios->count() }} total</span>
                </div>

                @if ($this->scenarios->isEmpty())
                    <div class="rounded-md border border-dashed border-gray-300 bg-gray-50 p-4 text-sm text-gray-600">
                        No scenario yet. Create your first scenario to start building shipment options.
                    </div>
                @else
                    <div class="space-y-2">
                        @foreach ($this->scenarios as $scenario)
                            <button
                                type="button"
                                wire:click="selectScenario({{ $scenario->id }})"
                                class="w-full rounded-md border px-3 py-2 text-left text-sm transition {{ $activeScenarioId === $scenario->id ? 'border-amber-400 bg-amber-50 text-amber-900' : 'border-gray-200 bg-white text-gray-700 hover:bg-gray-50' }}"
                            >
                                <p class="font-medium">{{ $scenario->scenario_name }}</p>
                                <p class="text-xs opacity-80">{{ $scenario->scenario_code }} · {{ $scenario->status }}</p>
                            </button>
                        @endforeach
                    </div>
                @endif

                <form wire:submit="createScenario" class="mt-4 space-y-2">
                    <label for="newScenarioName" class="text-sm font-medium text-gray-700">New Scenario Name</label>
                    <input
                        id="newScenarioName"
                        type="text"
                        wire:model="newScenarioName"
                        class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-amber-500 focus:ring-amber-500"
                        placeholder="e.g. Sea + Last Mile Option"
                    >
                    @error('newScenarioName')
                        <p class="text-xs text-red-600">{{ $message }}</p>
                    @enderror
                    <button
                        type="submit"
                        class="inline-flex items-center rounded-md bg-amber-600 px-3 py-2 text-sm font-medium text-white hover:bg-amber-700"
                    >
                        Create Scenario
                    </button>
                </form>
            </div>

            <div class="space-y-6 lg:col-span-2">
                <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
                    <h2 class="text-lg font-semibold text-gray-900">Active Scenario Workspace</h2>
                    @if ($this->activeScenario)
                        <p class="mt-1 text-sm text-gray-600">
                            Working on: <span class="font-medium">{{ $this->activeScenario->scenario_name }}</span>
                            ({{ $this->activeScenario->scenario_code }})
                        </p>
                    @else
                        <p class="mt-1 text-sm text-gray-600">Select or create a scenario to start working.</p>
                    @endif
                </div>

                <div class="grid gap-6 md:grid-cols-3">
                    <div class="rounded-lg border border-dashed border-gray-300 bg-white p-5 shadow-sm">
                        <h3 class="text-sm font-semibold text-gray-900">Legs</h3>
                        <p class="mt-2 text-sm text-gray-600">Leg builder will be added in the next issue.</p>
                    </div>
                    <div class="rounded-lg border border-dashed border-gray-300 bg-white p-5 shadow-sm">
                        <h3 class="text-sm font-semibold text-gray-900">Cost Items</h3>
                        <p class="mt-2 text-sm text-gray-600">Cost item entry placeholder for upcoming implementation.</p>
                    </div>
                    <div class="rounded-lg border border-dashed border-gray-300 bg-white p-5 shadow-sm">
                        <h3 class="text-sm font-semibold text-gray-900">Pricing Summary</h3>
                        <p class="mt-2 text-sm text-gray-600">Pricing summary and calculations will be added next.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

