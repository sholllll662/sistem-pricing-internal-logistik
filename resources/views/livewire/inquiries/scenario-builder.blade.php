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

                <div class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
                    <div class="flex items-center justify-between">
                        <h3 class="text-sm font-semibold text-gray-900">Leg Management</h3>
                        <span class="text-xs text-gray-500">{{ $this->scenarioLegs->count() }} legs</span>
                    </div>

                    @if (! $this->activeScenario)
                        <p class="mt-2 text-sm text-gray-600">Select a scenario first to manage legs.</p>
                    @else
                        <form wire:submit="saveLeg" class="mt-4 grid gap-3 md:grid-cols-2">
                            <div>
                                <label class="text-xs font-medium text-gray-600">Sequence No</label>
                                <input type="number" min="1" wire:model="legForm.sequence_no" class="mt-1 w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-amber-500 focus:ring-amber-500">
                                @error('legForm.sequence_no') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="text-xs font-medium text-gray-600">Leg Type</label>
                                <select wire:model="legForm.leg_type" class="mt-1 w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-amber-500 focus:ring-amber-500">
                                    <option value="first_mile">first_mile</option>
                                    <option value="middle_mile">middle_mile</option>
                                    <option value="last_mile">last_mile</option>
                                    <option value="custom">custom</option>
                                </select>
                                @error('legForm.leg_type') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="text-xs font-medium text-gray-600">Origin</label>
                                <select wire:model="legForm.origin_location_id" class="mt-1 w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-amber-500 focus:ring-amber-500">
                                    <option value="">Select origin</option>
                                    @foreach($this->locationOptions as $id => $name)
                                        <option value="{{ $id }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                                @error('legForm.origin_location_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="text-xs font-medium text-gray-600">Destination</label>
                                <select wire:model="legForm.destination_location_id" class="mt-1 w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-amber-500 focus:ring-amber-500">
                                    <option value="">Select destination</option>
                                    @foreach($this->locationOptions as $id => $name)
                                        <option value="{{ $id }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                                @error('legForm.destination_location_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="text-xs font-medium text-gray-600">Transport Mode</label>
                                <select wire:model="legForm.transport_mode_id" class="mt-1 w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-amber-500 focus:ring-amber-500">
                                    <option value="">Optional</option>
                                    @foreach($this->transportModeOptions as $id => $name)
                                        <option value="{{ $id }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="text-xs font-medium text-gray-600">Vehicle Type</label>
                                <select wire:model="legForm.vehicle_type_id" class="mt-1 w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-amber-500 focus:ring-amber-500">
                                    <option value="">Optional</option>
                                    @foreach($this->vehicleTypeOptions as $id => $name)
                                        <option value="{{ $id }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="md:col-span-2">
                                <label class="text-xs font-medium text-gray-600">Primary Vendor</label>
                                <select wire:model="legForm.primary_vendor_id" class="mt-1 w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-amber-500 focus:ring-amber-500">
                                    <option value="">Optional</option>
                                    @foreach($this->vendorOptions as $id => $name)
                                        <option value="{{ $id }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="md:col-span-2">
                                <label class="text-xs font-medium text-gray-600">Distance Notes</label>
                                <textarea rows="2" wire:model="legForm.distance_notes" class="mt-1 w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-amber-500 focus:ring-amber-500"></textarea>
                            </div>
                            <div>
                                <label class="text-xs font-medium text-gray-600">Lead Time Notes</label>
                                <textarea rows="2" wire:model="legForm.lead_time_notes" class="mt-1 w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-amber-500 focus:ring-amber-500"></textarea>
                            </div>
                            <div>
                                <label class="text-xs font-medium text-gray-600">Operation Notes</label>
                                <textarea rows="2" wire:model="legForm.operation_notes" class="mt-1 w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-amber-500 focus:ring-amber-500"></textarea>
                            </div>

                            <div class="md:col-span-2 flex flex-wrap gap-2">
                                <button type="submit" class="inline-flex items-center rounded-md bg-amber-600 px-3 py-2 text-sm font-medium text-white hover:bg-amber-700">
                                    {{ $editingLegId ? 'Update Leg' : 'Add Leg' }}
                                </button>
                                @if ($editingLegId)
                                    <button type="button" wire:click="cancelEditLeg" class="inline-flex items-center rounded-md border border-gray-300 px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                                        Cancel Edit
                                    </button>
                                @endif
                            </div>
                        </form>

                        @if ($this->scenarioLegs->isEmpty())
                            <div class="mt-4 rounded-md border border-dashed border-gray-300 bg-gray-50 p-4 text-sm text-gray-600">
                                No legs yet. Add the first leg to start mapping this scenario flow.
                            </div>
                        @else
                            <div class="mt-4 overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 text-sm">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-3 py-2 text-left font-semibold text-gray-700">Seq</th>
                                            <th class="px-3 py-2 text-left font-semibold text-gray-700">Type</th>
                                            <th class="px-3 py-2 text-left font-semibold text-gray-700">Origin</th>
                                            <th class="px-3 py-2 text-left font-semibold text-gray-700">Destination</th>
                                            <th class="px-3 py-2 text-left font-semibold text-gray-700">Transport</th>
                                            <th class="px-3 py-2 text-left font-semibold text-gray-700">Vehicle</th>
                                            <th class="px-3 py-2 text-left font-semibold text-gray-700">Vendor</th>
                                            <th class="px-3 py-2 text-right font-semibold text-gray-700">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100">
                                        @foreach ($this->scenarioLegs as $leg)
                                            <tr>
                                                <td class="px-3 py-2 text-gray-700">{{ $leg->sequence_no }}</td>
                                                <td class="px-3 py-2 text-gray-700">{{ $leg->leg_type }}</td>
                                                <td class="px-3 py-2 text-gray-700">{{ $leg->originLocation?->name ?? '-' }}</td>
                                                <td class="px-3 py-2 text-gray-700">{{ $leg->destinationLocation?->name ?? '-' }}</td>
                                                <td class="px-3 py-2 text-gray-700">{{ $leg->transportMode?->name ?? '-' }}</td>
                                                <td class="px-3 py-2 text-gray-700">{{ $leg->vehicleType?->name ?? '-' }}</td>
                                                <td class="px-3 py-2 text-gray-700">{{ $leg->primaryVendor?->name ?? '-' }}</td>
                                                <td class="px-3 py-2 text-right">
                                                    <div class="inline-flex items-center gap-2">
                                                        <button type="button" wire:click="editLeg({{ $leg->id }})" class="text-xs font-medium text-amber-700 hover:text-amber-900">Edit</button>
                                                        <button type="button" wire:click="deleteLeg({{ $leg->id }})" class="text-xs font-medium text-red-600 hover:text-red-800">Delete</button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    @endif
                </div>

                <div class="grid gap-6 md:grid-cols-2">
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

