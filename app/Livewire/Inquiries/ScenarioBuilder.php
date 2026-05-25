<?php

namespace App\Livewire\Inquiries;

use App\Models\Inquiry;
use App\Models\InquiryScenario;
use App\Models\Location;
use App\Models\ScenarioLeg;
use App\Models\TransportMode;
use App\Models\VehicleType;
use App\Models\Vendor;
use Illuminate\View\View;
use Livewire\Component;

class ScenarioBuilder extends Component
{
    public Inquiry $inquiry;

    public ?int $activeScenarioId = null;

    public string $newScenarioName = '';

    public ?int $editingLegId = null;

    public array $legForm = [];

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
        $this->resetLegForm();
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
    }

    public function cancelEditLeg(): void
    {
        $this->editingLegId = null;
        $this->resetLegForm();
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
            ->with(['originLocation', 'destinationLocation', 'transportMode', 'vehicleType', 'primaryVendor'])
            ->orderBy('sequence_no')
            ->orderBy('id')
            ->get();
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

    public function render(): View
    {
        return view('livewire.inquiries.scenario-builder')
            ->layout('layouts.app');
    }
}
