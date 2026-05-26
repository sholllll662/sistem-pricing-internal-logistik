<?php

namespace Database\Seeders;

use App\Models\CostCategory;
use App\Models\Customer;
use App\Models\CustomerContact;
use App\Models\Inquiry;
use App\Models\InquiryScenario;
use App\Models\LegCostItem;
use App\Models\Location;
use App\Models\Quote;
use App\Models\QuoteApproval;
use App\Models\Role;
use App\Models\ScenarioLeg;
use App\Models\TransportMode;
use App\Models\User;
use App\Models\VehicleType;
use App\Models\Vendor;
use App\Models\VendorContact;
use App\Services\Pricing\PricingCalculationService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

class UatSeedDataSeeder extends Seeder
{
    public function run(): void
    {
        $roles = Role::query()->whereIn('code', ['sales', 'manager', 'admin'])->get()->keyBy('code');

        $admin = $this->upsertUser(
            env('ADMIN_NAME', 'Admin User'),
            env('ADMIN_EMAIL', 'admin@example.com'),
            env('ADMIN_PASSWORD', 'password')
        );
        $sales = $this->upsertUser('Sales UAT', 'sales.uat@example.com', 'password');
        $manager = $this->upsertUser('Manager UAT', 'manager.uat@example.com', 'password');

        $admin->roles()->syncWithoutDetaching([$roles['admin']->id]);
        $sales->roles()->syncWithoutDetaching([$roles['sales']->id]);
        $manager->roles()->syncWithoutDetaching([$roles['manager']->id]);

        $customer = Customer::query()->updateOrCreate(
            ['code' => 'CUST-UAT-001'],
            [
                'name' => 'PT Logistik Nusantara Demo',
                'industry' => 'Distribution',
                'address' => 'Jl. Industri Raya No. 10, Bekasi',
                'notes' => 'Seed customer for UAT flow.',
                'is_active' => true,
            ]
        );

        $pickupContact = CustomerContact::query()->updateOrCreate(
            ['customer_id' => $customer->id, 'email' => 'pickup.uat@customer.test'],
            [
                'name' => 'Rina Pickup',
                'phone' => '+62-811-1000-100',
                'job_title' => 'Warehouse Supervisor',
                'contact_type' => 'pickup',
                'is_primary' => true,
            ]
        );

        $dropContact = CustomerContact::query()->updateOrCreate(
            ['customer_id' => $customer->id, 'email' => 'drop.uat@customer.test'],
            [
                'name' => 'Dimas Drop',
                'phone' => '+62-811-1000-200',
                'job_title' => 'Receiving Lead',
                'contact_type' => 'drop',
                'is_primary' => true,
            ]
        );

        $vendor = Vendor::query()->updateOrCreate(
            ['code' => 'VND-UAT-TRK'],
            [
                'name' => 'CV Armada Truk Demo',
                'vendor_type' => 'trucking',
                'address' => 'Jl. Raya Cakung No. 5, Jakarta Timur',
                'notes' => 'Primary trucking partner for UAT scenario.',
                'is_active' => true,
            ]
        );

        VendorContact::query()->updateOrCreate(
            ['vendor_id' => $vendor->id, 'email' => 'ops.uat@vendor.test'],
            [
                'name' => 'Bagus Vendor Ops',
                'phone' => '+62-811-2000-100',
                'job_title' => 'Operations Coordinator',
                'is_primary' => true,
            ]
        );

        $origin = Location::query()->updateOrCreate(
            ['code' => 'LOC-UAT-JKT-DC'],
            [
                'name' => 'Jakarta Distribution Center',
                'location_type' => 'warehouse',
                'country' => 'Indonesia',
                'province' => 'DKI Jakarta',
                'city' => 'Jakarta Timur',
                'district' => 'Cakung',
                'postal_code' => '13910',
                'address' => 'Kawasan Industri Pulogadung, Jakarta Timur',
            ]
        );

        $destination = Location::query()->updateOrCreate(
            ['code' => 'LOC-UAT-BDG-HUB'],
            [
                'name' => 'Bandung Retail Hub',
                'location_type' => 'warehouse',
                'country' => 'Indonesia',
                'province' => 'Jawa Barat',
                'city' => 'Bandung',
                'district' => 'Gedebage',
                'postal_code' => '40295',
                'address' => 'Jl. Soekarno Hatta No. 99, Bandung',
            ]
        );

        $transportMode = TransportMode::query()->updateOrCreate(
            ['code' => 'TRUCK'],
            ['name' => 'Trucking', 'description' => 'Door-to-door trucking transport mode.']
        );

        $vehicleType = VehicleType::query()->updateOrCreate(
            ['code' => 'CDD-BOX'],
            [
                'name' => 'CDD Box',
                'description' => 'Colt Diesel Double Box',
                'capacity_notes' => 'Approx 4 tons',
            ]
        );

        $costCategoryFreight = CostCategory::query()->updateOrCreate(
            ['code' => 'FREIGHT'],
            ['name' => 'Freight', 'description' => 'Main transportation charges.']
        );
        $costCategoryHandling = CostCategory::query()->updateOrCreate(
            ['code' => 'HANDLING'],
            ['name' => 'Handling', 'description' => 'Loading and unloading charges.']
        );

        $inquiry = Inquiry::query()->updateOrCreate(
            ['inquiry_number' => 'INQ-UAT-0001'],
            [
                'customer_id' => $customer->id,
                'sales_owner_id' => $sales->id,
                'pickup_contact_id' => $pickupContact->id,
                'drop_contact_id' => $dropContact->id,
                'origin_location_id' => $origin->id,
                'destination_location_id' => $destination->id,
                'cargo_name' => 'Consumer Goods Mixed Pallet',
                'cargo_description' => 'Palletized mixed FMCG goods for retail replenishment.',
                'cargo_weight' => 2500,
                'cargo_volume' => 18.500,
                'cargo_dimension_notes' => 'Standard pallet dimensions, stackable.',
                'service_notes' => 'Pickup before 12:00, deliver next day.',
                'status' => Inquiry::STATUS_WAITING_APPROVAL,
                'submitted_at' => Carbon::now()->subDays(2),
            ]
        );

        $scenario = InquiryScenario::query()->updateOrCreate(
            ['inquiry_id' => $inquiry->id, 'scenario_code' => 'SCN-001'],
            [
                'scenario_name' => 'Direct Trucking Option',
                'description' => 'Single leg direct trucking from origin to destination.',
                'status' => InquiryScenario::STATUS_SELECTED,
                'is_selected' => true,
                'calculation_notes' => 'Baseline UAT pricing scenario.',
            ]
        );

        $leg = ScenarioLeg::query()->updateOrCreate(
            ['scenario_id' => $scenario->id, 'sequence_no' => 1],
            [
                'leg_type' => ScenarioLeg::TYPE_FIRST_MILE,
                'origin_location_id' => $origin->id,
                'destination_location_id' => $destination->id,
                'transport_mode_id' => $transportMode->id,
                'vehicle_type_id' => $vehicleType->id,
                'primary_vendor_id' => $vendor->id,
                'distance_notes' => 'Estimated 155 km via toll route.',
                'lead_time_notes' => '1 day transit time.',
                'operation_notes' => 'Dedicated vehicle with sealed cargo area.',
            ]
        );

        LegCostItem::query()->updateOrCreate(
            ['leg_id' => $leg->id, 'item_name' => 'Main trucking charge'],
            [
                'cost_category_id' => $costCategoryFreight->id,
                'vendor_id' => $vendor->id,
                'description' => 'Base trucking rate Jakarta to Bandung.',
                'quantity' => 1,
                'unit_name' => 'trip',
                'unit_price' => 2750000,
                'line_total' => 2750000,
                'price_source_date' => Carbon::today()->subDays(5),
                'price_source_reference' => 'Vendor rate card May 2026',
                'is_manual_override' => false,
            ]
        );

        LegCostItem::query()->updateOrCreate(
            ['leg_id' => $leg->id, 'item_name' => 'Loading and unloading'],
            [
                'cost_category_id' => $costCategoryHandling->id,
                'vendor_id' => $vendor->id,
                'description' => 'Labor handling for both ends.',
                'quantity' => 1,
                'unit_name' => 'lot',
                'unit_price' => 350000,
                'line_total' => 350000,
                'price_source_date' => Carbon::today()->subDays(5),
                'price_source_reference' => 'Ops estimate approved by sales lead',
                'is_manual_override' => false,
            ]
        );

        $pricingSummary = app(PricingCalculationService::class)->refreshSnapshots($scenario, marginNominal: 650000);

        $waitingQuote = Quote::query()->updateOrCreate(
            ['quote_number' => 'Q-UAT-0001'],
            [
                'inquiry_id' => $inquiry->id,
                'scenario_id' => $scenario->id,
                'prepared_by_user_id' => $sales->id,
                'valid_from' => Carbon::today()->subDay(),
                'valid_until' => Carbon::today()->addMonths(3),
                'total_base_cost_snapshot' => $pricingSummary['scenario_base_cost'],
                'total_margin_snapshot' => $pricingSummary['margin_nominal'],
                'total_selling_price_snapshot' => $pricingSummary['selling_price'],
                'status' => Quote::STATUS_WAITING_APPROVAL,
                'approval_status' => Quote::STATUS_WAITING_APPROVAL,
                'customer_notes' => 'UAT quote waiting for manager decision.',
                'internal_notes' => 'Used for approval queue validation.',
            ]
        );

        $approvedQuote = Quote::query()->updateOrCreate(
            ['quote_number' => 'Q-UAT-0002'],
            [
                'inquiry_id' => $inquiry->id,
                'scenario_id' => $scenario->id,
                'prepared_by_user_id' => $sales->id,
                'valid_from' => Carbon::today()->subDays(5),
                'valid_until' => Carbon::today()->addMonths(3)->subDays(5),
                'total_base_cost_snapshot' => $pricingSummary['scenario_base_cost'],
                'total_margin_snapshot' => $pricingSummary['margin_nominal'],
                'total_selling_price_snapshot' => $pricingSummary['selling_price'],
                'status' => Quote::STATUS_APPROVED,
                'approval_status' => Quote::STATUS_APPROVED,
                'customer_notes' => 'UAT approved quote example.',
                'internal_notes' => 'Used for quote history review validation.',
            ]
        );

        QuoteApproval::query()->updateOrCreate(
            ['quote_id' => $approvedQuote->id, 'decision' => QuoteApproval::DECISION_APPROVED],
            [
                'approver_user_id' => $manager->id,
                'decision_notes' => 'Approved for UAT sample data.',
                'decided_at' => Carbon::now()->subDays(1),
            ]
        );

        // Ensure waiting quote stays clean for approval queue flow.
        QuoteApproval::query()->where('quote_id', $waitingQuote->id)->delete();
    }

    private function upsertUser(string $name, string $email, string $password): User
    {
        return User::query()->updateOrCreate(
            ['email' => $email],
            ['name' => $name, 'password' => Hash::make($password)]
        );
    }
}
