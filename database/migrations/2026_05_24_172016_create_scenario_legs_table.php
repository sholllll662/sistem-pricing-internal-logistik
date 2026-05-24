<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('scenario_legs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('scenario_id')->constrained('inquiry_scenarios')->cascadeOnDelete();
            $table->unsignedInteger('sequence_no');
            $table->string('leg_type');
            $table->foreignId('origin_location_id')->nullable()->constrained('locations')->nullOnDelete();
            $table->foreignId('destination_location_id')->nullable()->constrained('locations')->nullOnDelete();
            $table->foreignId('transport_mode_id')->nullable()->constrained('transport_modes')->nullOnDelete();
            $table->foreignId('vehicle_type_id')->nullable()->constrained('vehicle_types')->nullOnDelete();
            $table->foreignId('primary_vendor_id')->nullable()->constrained('vendors')->nullOnDelete();
            $table->text('distance_notes')->nullable();
            $table->text('lead_time_notes')->nullable();
            $table->text('operation_notes')->nullable();
            $table->decimal('base_cost_snapshot', 16, 2)->default(0);
            $table->jsonb('metadata_jsonb')->nullable();
            $table->timestamps();

            $table->index(['scenario_id', 'sequence_no']);
            $table->index('leg_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scenario_legs');
    }
};
