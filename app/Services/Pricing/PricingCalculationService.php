<?php

namespace App\Services\Pricing;

use App\Models\InquiryScenario;
use App\Models\LegCostItem;

class PricingCalculationService
{
    public function calculateLineTotal(float $quantity, float $unitPrice): float
    {
        if ($quantity < 0 || $unitPrice < 0) {
            return 0.0;
        }

        return round($quantity * $unitPrice, 2);
    }

    public function calculateScenario(
        InquiryScenario $scenario,
        float $marginNominal = 0,
        float $surchargeNominal = 0
    ): array {
        $scenario->loadMissing('scenarioLegs.legCostItems');

        $legResults = [];
        $scenarioBaseCost = 0.0;

        foreach ($scenario->scenarioLegs as $leg) {
            $itemResults = [];
            $legBaseCost = 0.0;

            foreach ($leg->legCostItems as $item) {
                $lineTotal = $this->resolveItemLineTotal($item);
                $itemResults[] = [
                    'id' => $item->id,
                    'item_name' => $item->item_name,
                    'quantity' => (float) $item->quantity,
                    'unit_price' => (float) $item->unit_price,
                    'line_total' => $lineTotal,
                    'is_manual_override' => (bool) $item->is_manual_override,
                ];
                $legBaseCost += $lineTotal;
            }

            $legBaseCost = round($legBaseCost, 2);
            $scenarioBaseCost += $legBaseCost;

            $legResults[] = [
                'leg_id' => $leg->id,
                'sequence_no' => $leg->sequence_no,
                'base_cost' => $legBaseCost,
                'cost_items' => $itemResults,
            ];
        }

        $scenarioBaseCost = round($scenarioBaseCost, 2);
        $margin = max(0, round($marginNominal, 2));
        $surcharge = max(0, round($surchargeNominal, 2));
        $sellingPrice = round($scenarioBaseCost + $margin + $surcharge, 2);

        return [
            'scenario_id' => $scenario->id,
            'scenario_base_cost' => $scenarioBaseCost,
            'margin_nominal' => $margin,
            'surcharge_nominal' => $surcharge,
            'selling_price' => $sellingPrice,
            'legs' => $legResults,
        ];
    }

    public function refreshSnapshots(
        InquiryScenario $scenario,
        float $marginNominal = 0,
        float $surchargeNominal = 0
    ): array {
        $result = $this->calculateScenario($scenario, $marginNominal, $surchargeNominal);

        foreach ($result['legs'] as $legResult) {
            $scenario->scenarioLegs()
                ->whereKey($legResult['leg_id'])
                ->update(['base_cost_snapshot' => $legResult['base_cost']]);
        }

        $scenario->update([
            'total_base_cost_snapshot' => $result['scenario_base_cost'],
            'total_margin_snapshot' => $result['margin_nominal'],
            'total_selling_price_snapshot' => $result['selling_price'],
        ]);

        return $result;
    }

    private function resolveItemLineTotal(LegCostItem $item): float
    {
        if ($item->is_manual_override) {
            return round(max(0, (float) $item->line_total), 2);
        }

        return $this->calculateLineTotal(
            (float) $item->quantity,
            (float) $item->unit_price
        );
    }
}
