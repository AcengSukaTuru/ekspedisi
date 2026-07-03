<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // COD: bayar ke kurir saat terima barang
            $table->string('payment_type')->default('prepaid')->after('payment_method'); // prepaid, cod
            // COD: kurir yang narik uang
            $table->foreignId('collected_by')->nullable()->after('verified_by')
                ->constrained('users')->cascadeOnUpdate()->nullOnDelete();
            // COD: bukti setoran kurir ke sistem
            $table->string('cod_collection_proof')->nullable()->after('collected_by');
            $table->timestamp('cod_collected_at')->nullable()->after('cod_collection_proof');
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['collected_by']);
            $table->dropColumn([
                'payment_type', 'collected_by', 'cod_collection_proof', 'cod_collected_at',
            ]);
        });
    }
};
