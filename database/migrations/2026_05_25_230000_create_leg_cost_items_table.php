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
        Schema::create('leg_cost_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('leg_id')->constrained('scenario_legs')->cascadeOnDelete();
            $table->foreignId('cost_category_id')->constrained('cost_categories')->restrictOnDelete();
            $table->foreignId('vendor_id')->nullable()->constrained('vendors')->nullOnDelete();
            $table->string('item_name');
            $table->text('description')->nullable();
            $table->decimal('quantity', 16, 4)->default(1);
            $table->string('unit_name')->nullable();
            $table->decimal('unit_price', 16, 2)->default(0);
            $table->decimal('line_total', 16, 2)->default(0);
            $table->date('price_source_date')->nullable();
            $table->string('price_source_reference')->nullable();
            $table->boolean('is_manual_override')->default(false);
            $table->timestamps();

            $table->index('leg_id');
            $table->index('cost_category_id');
            $table->index('vendor_id');
            $table->index('is_manual_override');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leg_cost_items');
    }
};
