<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('delivery_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipment_id')
                ->constrained('shipments')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->foreignId('attempted_by')
                ->constrained('users')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->unsignedTinyInteger('attempt_number'); // 1, 2, 3...
            $table->string('status'); // success, failed
            $table->string('failure_reason')->nullable(); // receiver_not_available, wrong_address, refused, other
            $table->text('notes')->nullable();
            $table->string('proof_path')->nullable(); // foto bukti serahan / foto gagal
            $table->timestamp('attempted_at')->useCurrent();
            $table->timestamp('next_retry_at')->nullable(); // kapan jadwal ulang
            $table->timestamps();

            $table->index('shipment_id');
            $table->index('attempted_by');
            $table->index('status');
            $table->index(['shipment_id', 'attempt_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_attempts');
    }
};
