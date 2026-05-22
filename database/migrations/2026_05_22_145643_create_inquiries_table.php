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
        Schema::create('inquiries', function (Blueprint $table) {
            $table->id();
            $table->string('inquiry_number')->unique();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('sales_owner_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('pickup_contact_id')->nullable()->constrained('customer_contacts')->nullOnDelete();
            $table->foreignId('drop_contact_id')->nullable()->constrained('customer_contacts')->nullOnDelete();
            $table->foreignId('origin_location_id')->nullable()->constrained('locations')->nullOnDelete();
            $table->foreignId('destination_location_id')->nullable()->constrained('locations')->nullOnDelete();
            $table->string('cargo_name')->nullable();
            $table->text('cargo_description')->nullable();
            $table->decimal('cargo_weight', 14, 3)->nullable();
            $table->decimal('cargo_volume', 14, 3)->nullable();
            $table->text('cargo_dimension_notes')->nullable();
            $table->text('service_notes')->nullable();
            $table->string('status')->default('draft');
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->jsonb('metadata_jsonb')->nullable();
            $table->timestamps();

            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inquiries');
    }
};
