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
        Schema::create('rates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('origin_branch_id')
                ->constrained('branches')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->foreignId('destination_branch_id')
                ->constrained('branches')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->decimal('price_per_kg', 12, 2);
            $table->string('service_type')->default('regular');
            $table->timestamps();

            $table->unique(
                ['origin_branch_id', 'destination_branch_id', 'service_type'],
                'rates_origin_destination_service_unique'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rates');
    }
};
