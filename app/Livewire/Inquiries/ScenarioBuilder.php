<?php

namespace App\Livewire\Inquiries;

use App\Models\Inquiry;
use App\Models\InquiryScenario;
use App\Models\LegCostItem;
use App\Models\Location;
use App\Models\CostCategory;
use App\Models\ScenarioLeg;
use App\Models\TransportMode;
use App\Models\VehicleType;
use App\Models\Vendor;
use App\Services\Pricing\PricingCalculationService;
use Illuminate\View\View;
use Livewire\Component;

class ScenarioBuilder extends Component
{
    public Inquiry $inquiry;

    public ?int $activeScenarioId = null;

    public string $newScenarioName = '';

    public ?int $editingLegId = null;

    public array $legForm = [];

    public ?int $editingCostItemId = null;

    public array $costItemForm = [];

    public function mount(Inquiry $inquiry): void
    {
        $this->inquiry = $inquiry->load([
            'customer',
            'pickupContact',
            'dropContact',
            'originLocation',
            'destinationLocation',
            'inquiryScenarios',
        ]);

        $this->activeScenarioId = $this->inquiry->inquiryScenarios
            ->firstWhere('is_selected', true)?->id
            ?? $this->inquiry->inquiryScenarios->first()?->id;

        $this->resetLegForm();
        $this->resetCostItemForm();
    }

    public function createScenario(): void
    {
        $this->validate([
            'newScenarioName' => ['required', 'string', 'max:255'],
        ]);

        $nextNumber = $this->inquiry->inquiryScenarios()->count() + 1;

        $scenario = $this->inquiry->inquiryScenarios()->create([
            'scenario_code' => sprintf('SCN-%03d', $nextNumber),
            'scenario_name' => $this->newScenarioName,
            'status' => InquiryScenario::STATUS_DRAFT,
            'is_selected' => false,
            'total_base_cost_snapshot' => 0,
            'total_margin_snapshot' => 0,
            'total_selling_price_snapshot' => 0,
        ]);

        $this->newScenarioName = '';
        $this->selectScenario($scenario->id);
    }

    public function selectScenario(int $scenarioId): void
    {
        $belongsToInquiry = $this->inquiry->inquiryScenarios()
            ->whereKey($scenarioId)
            ->exists();

        if (! $belongsToInquiry) {
            return;
        }

        $this->inquiry->inquiryScenarios()->update(['is_selected' => false]);
        $this->inquiry->inquiryScenarios()->whereKey($scenarioId)->update(['is_selected' => true]);

        $this->activeScenarioId = $scenarioId;
        $this->inquiry->load('inquiryScenarios');
        $this->editingLegId = null;
        $this->editingCostItemId = null;
        $this->resetLegForm();
        $this->resetCostItemForm();
    }

    public function saveLeg(): void
    {
        if (! $this->activeScenario) {
            return;
        }

        $validated = $this->validate([
            'legForm.sequence_no' => ['required', 'integer', 'min:1'],
            'legForm.leg_type' => ['required', 'in:'.implode(',', ScenarioLeg::legTypes())],
            'legForm.origin_location_id' => ['required', 'exists:locations,id'],
            'legForm.destination_location_id' => ['required', 'exists:locations,id'],
            'legForm.transport_mode_id' => ['nullable', 'exists:transport_modes,id'],
            'legForm.vehicle_type_id' => ['nullable', 'exists:vehicle_types,id'],
            'legForm.primary_vendor_id' => ['nullable', 'exists:vendors,id'],
            'legForm.distance_notes' => ['nullable', 'string'],
            'legForm.lead_time_notes' => ['nullable', 'string'],
            'legForm.operation_notes' => ['nullable', 'string'],
        ]);

        $payload = $validated['legForm'];
        $payload['scenario_id'] = $this->activeScenario->id;

        if ($this->editingLegId) {
            $this->activeScenario->scenarioLegs()->whereKey($this->editingLegId)->update($payload);
        } else {
            $this->activeScenario->scenarioLegs()->create($payload + [
                'base_cost_snapshot' => 0,
            ]);
        }

        $this->editingLegId = null;
        $this->resetLegForm();
        $this->inquiry->load('inquiryScenarios');
        app(PricingCalculationService::class)->refreshSnapshots($this->activeScenario);
    }

    public function editLeg(int $legId): void
    {
        if (! $this->activeScenario) {
            return;
        }

        $leg = $this->activeScenario->scenarioLegs()->whereKey($legId)->first();
        if (! $leg) {
            return;
        }

        $this->editingLegId = $leg->id;
        $this->legForm = [
            'sequence_no' => $leg->sequence_no,
            'leg_type' => $leg->leg_type,
            'origin_location_id' => $leg->origin_location_id,
            'destination_location_id' => $leg->destination_location_id,
            'transport_mode_id' => $leg->transport_mode_id,
            'vehicle_type_id' => $leg->vehicle_type_id,
            'primary_vendor_id' => $leg->primary_vendor_id,
            'distance_notes' => $leg->distance_notes ?? '',
            'lead_time_notes' => $leg->lead_time_notes ?? '',
            'operation_notes' => $leg->operation_notes ?? '',
        ];
    }

    public function deleteLeg(int $legId): void
    {
        if (! $this->activeScenario) {
            return;
        }

        $this->activeScenario->scenarioLegs()->whereKey($legId)->delete();

        if ($this->editingLegId === $legId) {
            $this->editingLegId = null;
            $this->resetLegForm();
        }

        $this->inquiry->load('inquiryScenarios');
        if ($this->activeScenario) {
            app(PricingCalculationService::class)->refreshSnapshots($this->activeScenario);
        }
    }

    public function cancelEditLeg(): void
    {
        $this->editingLegId = null;
        $this->resetLegForm();
    }

    public function saveCostItem(): void
    {
        if (! $this->activeScenario) {
            return;
        }

        $validated = $this->validate([
            'costItemForm.leg_id' => ['required', 'integer', 'exists:scenario_legs,id'],
            'costItemForm.cost_category_id' => ['required', 'integer', 'exists:cost_categories,id'],
            'costItemForm.vendor_id' => ['nullable', 'integer', 'exists:vendors,id'],
            'costItemForm.item_name' => ['required', 'string', 'max:255'],
            'costItemForm.description' => ['nullable', 'string'],
            'costItemForm.quantity' => ['required', 'numeric', 'min:0'],
            'costItemForm.unit_name' => ['nullable', 'string', 'max:100'],
            'costItemForm.unit_price' => ['required', 'numeric', 'min:0'],
            'costItemForm.line_total' => ['nullable', 'numeric', 'min:0'],
            'costItemForm.price_source_date' => ['nullable', 'date'],
            'costItemForm.price_source_reference' => ['nullable', 'string', 'max:255'],
            'costItemForm.is_manual_override' => ['required', 'boolean'],
        ]);

        $payload = $validated['costItemForm'];

        $belongsToActiveScenario = $this->activeScenario->scenarioLegs()
            ->whereKey($payload['leg_id'])
            ->exists();

        if (! $belongsToActiveScenario) {
            $this->addError('costItemForm.leg_id', 'Selected leg is not part of active scenario.');

            return;
        }

        $pricingService = app(PricingCalculationService::class);
        $calculatedLineTotal = $pricingService->calculateLineTotal(
            (float) $payload['quantity'],
            (float) $payload['unit_price']
        );
        $payload['line_total'] = $payload['is_manual_override']
            ? (float) ($payload['line_total'] ?? 0)
            : $calculatedLineTotal;

        if ($this->editingCostItemId) {
            LegCostItem::query()
                ->whereKey($this->editingCostItemId)
                ->whereIn('leg_id', $this->activeScenario->scenarioLegs()->pluck('id'))
                ->update($payload);
        } else {
            LegCostItem::query()->create($payload);
        }

        $this->editingCostItemId = null;
        $this->resetCostItemForm();
        app(PricingCalculationService::class)->refreshSnapshots($this->activeScenario);
    }

    public function startCreateCostItem(int $legId): void
    {
        if (! $this->activeScenario) {
            return;
        }

        $exists = $this->activeScenario->scenarioLegs()->whereKey($legId)->exists();
        if (! $exists) {
            return;
        }

        $this->editingCostItemId = null;
        $this->resetCostItemForm($legId);
    }

    public function editCostItem(int $costItemId): void
    {
        if (! $this->activeScenario) {
            return;
        }

        $costItem = LegCostItem::query()
            ->whereKey($costItemId)
            ->whereIn('leg_id', $this->activeScenario->scenarioLegs()->pluck('id'))
            ->first();

        if (! $costItem) {
            return;
        }

        $this->editingCostItemId = $costItem->id;
        $this->costItemForm = [
            'leg_id' => $costItem->leg_id,
            'cost_category_id' => $costItem->cost_category_id,
            'vendor_id' => $costItem->vendor_id,
            'item_name' => $costItem->item_name,
            'description' => $costItem->description ?? '',
            'quantity' => $costItem->quantity,
            'unit_name' => $costItem->unit_name ?? '',
            'unit_price' => $costItem->unit_price,
            'line_total' => $costItem->line_total,
            'price_source_date' => $costItem->price_source_date?->format('Y-m-d'),
            'price_source_reference' => $costItem->price_source_reference ?? '',
            'is_manual_override' => (bool) $costItem->is_manual_override,
        ];
    }

    public function deleteCostItem(int $costItemId): void
    {
        if (! $this->activeScenario) {
            return;
        }

        LegCostItem::query()
            ->whereKey($costItemId)
            ->whereIn('leg_id', $this->activeScenario->scenarioLegs()->pluck('id'))
            ->delete();

        if ($this->editingCostItemId === $costItemId) {
            $this->editingCostItemId = null;
            $this->resetCostItemForm();
        }

        app(PricingCalculationService::class)->refreshSnapshots($this->activeScenario);
    }

    public function cancelEditCostItem(): void
    {
        $this->editingCostItemId = null;
        $this->resetCostItemForm();
    }

    public function getScenariosProperty()
    {
        return $this->inquiry->inquiryScenarios()->orderBy('id')->get();
    }

    public function getActiveScenarioProperty(): ?InquiryScenario
    {
        return $this->scenarios->firstWhere('id', $this->activeScenarioId);
    }

    public function getScenarioLegsProperty()
    {
        if (! $this->activeScenario) {
            return collect();
        }

        return $this->activeScenario->scenarioLegs()
            ->with([
                'originLocation',
                'destinationLocation',
                'transportMode',
                'vehicleType',
                'primaryVendor',
                'legCostItems.costCategory',
                'legCostItems.vendor',
            ])
            ->orderBy('sequence_no')
            ->orderBy('id')
            ->get();
    }

    public function getCostCategoryOptionsProperty(): array
    {
        return CostCategory::query()->orderBy('name')->pluck('name', 'id')->all();
    }

    public function getLocationOptionsProperty(): array
    {
        return Location::query()->orderBy('name')->pluck('name', 'id')->all();
    }

    public function getTransportModeOptionsProperty(): array
    {
        return TransportMode::query()->orderBy('name')->pluck('name', 'id')->all();
    }

    public function getVehicleTypeOptionsProperty(): array
    {
        return VehicleType::query()->orderBy('name')->pluck('name', 'id')->all();
    }

    public function getVendorOptionsProperty(): array
    {
        return Vendor::query()->where('is_active', true)->orderBy('name')->pluck('name', 'id')->all();
    }

    public function getPricingSummaryProperty(): array
    {
        if (! $this->activeScenario) {
            return [
                'state' => 'empty',
                'message' => 'Select a scenario to see pricing summary.',
                'data' => null,
            ];
        }

        if ($this->scenarioLegs->isEmpty()) {
            return [
                'state' => 'empty',
                'message' => 'Add at least one leg to generate pricing summary.',
                'data' => null,
            ];
        }

        $hasIncompleteLeg = $this->scenarioLegs->contains(fn ($leg) => $leg->legCostItems->isEmpty());

        $summary = app(PricingCalculationService::class)->calculateScenario(
            $this->activeScenario,
            (float) $this->activeScenario->total_margin_snapshot,
            0
        );

        if ($hasIncompleteLeg) {
            return [
                'state' => 'incomplete',
                'message' => 'Some legs do not have cost items yet. Add cost items to complete the summary.',
                'data' => $summary,
            ];
        }

        return [
            'state' => 'ready',
            'message' => null,
            'data' => $summary,
        ];
    }

    private function resetLegForm(): void
    {
        $nextSequenceNo = 1;
        if ($this->activeScenario) {
            $maxSequence = $this->activeScenario->scenarioLegs()->max('sequence_no');
            $nextSequenceNo = ($maxSequence ?? 0) + 1;
        }

        $this->legForm = [
            'sequence_no' => $nextSequenceNo,
            'leg_type' => ScenarioLeg::TYPE_FIRST_MILE,
            'origin_location_id' => null,
            'destination_location_id' => null,
            'transport_mode_id' => null,
            'vehicle_type_id' => null,
            'primary_vendor_id' => null,
            'distance_notes' => '',
            'lead_time_notes' => '',
            'operation_notes' => '',
        ];
    }

    private function resetCostItemForm(?int $legId = null): void
    {
        $defaultLegId = $legId;
        if (! $defaultLegId && $this->activeScenario) {
            $defaultLegId = $this->activeScenario->scenarioLegs()->orderBy('sequence_no')->value('id');
        }

        $this->costItemForm = [
            'leg_id' => $defaultLegId,
            'cost_category_id' => null,
            'vendor_id' => null,
            'item_name' => '',
            'description' => '',
            'quantity' => 1,
            'unit_name' => '',
            'unit_price' => 0,
            'line_total' => 0,
            'price_source_date' => null,
            'price_source_reference' => '',
            'is_manual_override' => false,
        ];
    }

    public function render(): View
    {
        return view('livewire.inquiries.scenario-builder')
            ->layout('layouts.app');
    }
}
