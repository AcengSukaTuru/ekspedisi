<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            // Pickup type: customer antar ke counter atau minta dijemput
            $table->string('pickup_type')->default('drop_off')->after('service_type'); // drop_off, pickup_request
            // Jika pickup_request: alamat penjemputan
            $table->text('pickup_address')->nullable()->after('pickup_type');
            $table->string('pickup_contact_name')->nullable()->after('pickup_address');
            $table->string('pickup_contact_phone', 20)->nullable()->after('pickup_contact_name');
            // Max delivery attempts sebelum RTS
            $table->unsignedTinyInteger('max_delivery_attempts')->default(3)->after('status');
            // Catatan alasan RTS
            $table->text('rts_reason')->nullable()->after('max_delivery_attempts');
        });

        // Migrate existing 'pending' status ke 'created'
        DB::table('shipments')->where('status', 'pending')->update(['status' => 'created']);
    }

    public function down(): void
    {
        DB::table('shipments')->where('status', 'created')->update(['status' => 'pending']);

        Schema::table('shipments', function (Blueprint $table) {
            $table->dropColumn([
                'pickup_type', 'pickup_address', 'pickup_contact_name',
                'pickup_contact_phone', 'max_delivery_attempts', 'rts_reason',
            ]);
        });
    }
};
