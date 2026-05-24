<?php

namespace App\Livewire\Inquiries;

use App\Models\Inquiry;
use App\Models\InquiryScenario;
use Illuminate\View\View;
use Livewire\Component;

class ScenarioBuilder extends Component
{
    public Inquiry $inquiry;

    public ?int $activeScenarioId = null;

    public string $newScenarioName = '';

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
    }

    public function getScenariosProperty()
    {
        return $this->inquiry->inquiryScenarios()->orderBy('id')->get();
    }

    public function getActiveScenarioProperty(): ?InquiryScenario
    {
        return $this->scenarios->firstWhere('id', $this->activeScenarioId);
    }

    public function render(): View
    {
        return view('livewire.inquiries.scenario-builder')
            ->layout('layouts.app');
    }
}

