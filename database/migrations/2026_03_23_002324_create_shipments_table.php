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
        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->string('tracking_number')->unique();

            $table->foreignId('customer_id')
                ->constrained('customers')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->foreignId('origin_branch_id')
                ->constrained('branches')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->foreignId('destination_branch_id')
                ->constrained('branches')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->foreignId('vehicle_id')
                ->nullable()
                ->constrained('vehicles')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->string('sender_name');
            $table->string('sender_phone', 20);
            $table->text('sender_address');

            $table->string('receiver_name');
            $table->string('receiver_phone', 20);
            $table->text('receiver_address');

            $table->decimal('total_weight', 10, 2);
            $table->decimal('shipping_cost', 12, 2);
            $table->string('service_type')->default('regular');
            $table->string('status')->default('pending');
            $table->date('shipment_date')->nullable();
            $table->date('estimated_arrival')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipments');
    }
};
