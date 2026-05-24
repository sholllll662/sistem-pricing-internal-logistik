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
        Schema::create('inquiry_scenarios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inquiry_id')->constrained()->cascadeOnDelete();
            $table->string('scenario_code');
            $table->string('scenario_name');
            $table->text('description')->nullable();
            $table->string('status')->default('draft');
            $table->boolean('is_selected')->default(false);
            $table->decimal('total_base_cost_snapshot', 16, 2)->default(0);
            $table->decimal('total_margin_snapshot', 16, 2)->default(0);
            $table->decimal('total_selling_price_snapshot', 16, 2)->default(0);
            $table->text('calculation_notes')->nullable();
            $table->jsonb('metadata_jsonb')->nullable();
            $table->timestamps();

            $table->unique(['inquiry_id', 'scenario_code']);
            $table->index('status');
            $table->index('is_selected');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inquiry_scenarios');
    }
};
