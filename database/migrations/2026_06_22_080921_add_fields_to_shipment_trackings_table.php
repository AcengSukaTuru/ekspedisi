<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('shipment_trackings', function (Blueprint $table) {
            $table->foreignId('created_by')->nullable()->constrained('users')->cascadeOnUpdate()->nullOnDelete();
            $table->string('proof_path')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('shipment_trackings', function (Blueprint $table) {
            $table->dropConstrainedForeignId('created_by');
            $table->dropColumn('proof_path');
        });
    }
};
