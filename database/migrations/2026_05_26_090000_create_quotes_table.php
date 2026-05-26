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
        Schema::create('quotes', function (Blueprint $table) {
            $table->id();
            $table->string('quote_number')->unique();
            $table->foreignId('inquiry_id')->constrained()->cascadeOnDelete();
            $table->foreignId('scenario_id')->constrained('inquiry_scenarios')->cascadeOnDelete();
            $table->foreignId('prepared_by_user_id')->constrained('users')->cascadeOnDelete();
            $table->date('valid_from');
            $table->date('valid_until');
            $table->decimal('total_base_cost_snapshot', 16, 2)->default(0);
            $table->decimal('total_margin_snapshot', 16, 2)->default(0);
            $table->decimal('total_selling_price_snapshot', 16, 2)->default(0);
            $table->string('status')->default('draft');
            $table->string('approval_status')->default('draft');
            $table->text('customer_notes')->nullable();
            $table->text('internal_notes')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->timestamp('expired_at')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('approval_status');
            $table->index(['inquiry_id', 'scenario_id']);
            $table->index('valid_until');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotes');
    }
};
