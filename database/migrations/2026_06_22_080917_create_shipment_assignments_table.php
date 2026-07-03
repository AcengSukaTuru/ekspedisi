<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipment_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipment_id')->constrained('shipments')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('courier_id')->constrained('users')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('vehicle_id')->constrained('vehicles')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('assigned_by')->nullable()->constrained('users')->cascadeOnUpdate()->nullOnDelete();
            $table->string('status')->default('active'); // active, completed, cancelled
            $table->timestamp('assigned_at')->useCurrent();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            // Indexes required by instruction
            $table->index('shipment_id');
            $table->index('courier_id');
            $table->index('vehicle_id');
            $table->index('status');
            $table->index(['courier_id', 'status']);
            $table->index(['shipment_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipment_assignments');
    }
};
