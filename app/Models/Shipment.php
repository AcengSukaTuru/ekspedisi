<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Model;

class Shipment extends Model
{
    use HasFactory;

    // Status standar industri
    public const STATUS_CREATED = 'created';
    public const STATUS_PICKED_UP = 'picked_up';
    public const STATUS_AT_ORIGIN_HUB = 'at_origin_hub';
    public const STATUS_IN_TRANSIT = 'in_transit';
    public const STATUS_AT_DEST_HUB = 'at_dest_hub';
    public const STATUS_OUT_FOR_DELIVERY = 'out_for_delivery';
    public const STATUS_DELIVERED = 'delivered';
    public const STATUS_FAILED_DELIVERY = 'failed_delivery';
    public const STATUS_RETURNED_TO_SENDER = 'returned_to_sender';

    // Pickup type
    public const PICKUP_DROP_OFF = 'drop_off';
    public const PICKUP_REQUEST = 'pickup_request';

    protected $fillable = [
        'tracking_number',
        'customer_id',
        'origin_branch_id',
        'destination_branch_id',
        'vehicle_id',
        'sender_name',
        'sender_phone',
        'sender_address',
        'receiver_name',
        'receiver_phone',
        'receiver_address',
        'total_weight',
        'shipping_cost',
        'service_type',
        'pickup_type',
        'pickup_address',
        'pickup_contact_name',
        'pickup_contact_phone',
        'status',
        'max_delivery_attempts',
        'rts_reason',
        'shipment_date',
        'estimated_arrival',
    ];

    protected function casts(): array
    {
        return [
            'total_weight' => 'decimal:2',
            'shipping_cost' => 'decimal:2',
            'shipment_date' => 'date',
            'estimated_arrival' => 'date',
        ];
    }

    // ─── Relationships ──────────────────────────────────────────

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function originBranch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'origin_branch_id');
    }

    public function destinationBranch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'destination_branch_id');
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function shipmentItems(): HasMany
    {
        return $this->hasMany(ShipmentItem::class);
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    public function shipmentTrackings(): HasMany
    {
        return $this->hasMany(ShipmentTracking::class);
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(ShipmentAssignment::class);
    }

    public function activeAssignment(): HasOne
    {
        return $this->hasOne(ShipmentAssignment::class)->where('status', 'active');
    }

    public function deliveryAttempts(): HasMany
    {
        return $this->hasMany(DeliveryAttempt::class)->orderBy('attempt_number');
    }

    // ─── Status Helpers ─────────────────────────────────────────

    public static function statuses(): array
    {
        return [
            self::STATUS_CREATED,
            self::STATUS_PICKED_UP,
            self::STATUS_AT_ORIGIN_HUB,
            self::STATUS_IN_TRANSIT,
            self::STATUS_AT_DEST_HUB,
            self::STATUS_OUT_FOR_DELIVERY,
            self::STATUS_DELIVERED,
            self::STATUS_FAILED_DELIVERY,
            self::STATUS_RETURNED_TO_SENDER,
        ];
    }

    public static function statusLabels(): array
    {
        return [
            self::STATUS_CREATED => 'Pesanan Dibuat',
            self::STATUS_PICKED_UP => 'Paket Dijemput',
            self::STATUS_AT_ORIGIN_HUB => 'Di Hub Asal',
            self::STATUS_IN_TRANSIT => 'Dalam Perjalanan',
            self::STATUS_AT_DEST_HUB => 'Di Hub Tujuan',
            self::STATUS_OUT_FOR_DELIVERY => 'Sedang Diantar',
            self::STATUS_DELIVERED => 'Diterima',
            self::STATUS_FAILED_DELIVERY => 'Gagal Dikirim',
            self::STATUS_RETURNED_TO_SENDER => 'Dikembalikan ke Pengirim',
        ];
    }

    public static function statusColors(): array
    {
        return [
            self::STATUS_CREATED => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400',
            self::STATUS_PICKED_UP => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
            self::STATUS_AT_ORIGIN_HUB => 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-400',
            self::STATUS_IN_TRANSIT => 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400',
            self::STATUS_AT_DEST_HUB => 'bg-cyan-100 text-cyan-700 dark:bg-cyan-900/30 dark:text-cyan-400',
            self::STATUS_OUT_FOR_DELIVERY => 'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400',
            self::STATUS_DELIVERED => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400',
            self::STATUS_FAILED_DELIVERY => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
            self::STATUS_RETURNED_TO_SENDER => 'bg-slate-200 text-slate-700 dark:bg-slate-800 dark:text-slate-300',
        ];
    }

    public function statusLabel(): string
    {
        return self::statusLabels()[$this->status] ?? $this->status;
    }

    public function statusColor(): string
    {
        return self::statusColors()[$this->status] ?? 'bg-slate-100 text-slate-700';
    }

    // ─── Delivery Attempt Logic ──────────────────────────────────

    public function deliveryAttemptCount(): int
    {
        return $this->deliveryAttempts()->count();
    }

    public function failedAttemptCount(): int
    {
        return $this->deliveryAttempts()->where('status', 'failed')->count();
    }

    public function canAttemptDelivery(): bool
    {
        return $this->failedAttemptCount() < $this->max_delivery_attempts;
    }

    public function remainingAttempts(): int
    {
        return max(0, $this->max_delivery_attempts - $this->failedAttemptCount());
    }

    public function shouldReturnToSender(): bool
    {
        return ! $this->canAttemptDelivery();
    }

    // ─── Status Flow ────────────────────────────────────────────

    /**
     * Status yang valid setelah status saat ini.
     */
    public function validNextStatuses(): array
    {
        return match ($this->status) {
            self::STATUS_CREATED => [self::STATUS_PICKED_UP],
            self::STATUS_PICKED_UP => [self::STATUS_AT_ORIGIN_HUB, self::STATUS_IN_TRANSIT],
            self::STATUS_AT_ORIGIN_HUB => [self::STATUS_IN_TRANSIT],
            self::STATUS_IN_TRANSIT => [self::STATUS_AT_DEST_HUB],
            self::STATUS_AT_DEST_HUB => [self::STATUS_OUT_FOR_DELIVERY],
            self::STATUS_OUT_FOR_DELIVERY => [self::STATUS_DELIVERED, self::STATUS_FAILED_DELIVERY],
            self::STATUS_FAILED_DELIVERY => [self::STATUS_OUT_FOR_DELIVERY, self::STATUS_RETURNED_TO_SENDER],
            self::STATUS_DELIVERED => [], // final
            self::STATUS_RETURNED_TO_SENDER => [], // final
            default => [],
        };
    }

    public function canTransitionTo(string $newStatus): bool
    {
        return in_array($newStatus, $this->validNextStatuses(), true);
    }

    public function isFinal(): bool
    {
        return in_array($this->status, [self::STATUS_DELIVERED, self::STATUS_RETURNED_TO_SENDER], true);
    }

    // Backward compatibility: old 'pending' maps to 'created'
    public function getIsPendingAttribute(): bool
    {
        return $this->status === self::STATUS_CREATED;
    }

    public function getIsDeliveredAttribute(): bool
    {
        return $this->status === self::STATUS_DELIVERED;
    }

    public function getIsFailedAttribute(): bool
    {
        return $this->status === self::STATUS_FAILED_DELIVERY;
    }

    public function getIsRtsAttribute(): bool
    {
        return $this->status === self::STATUS_RETURNED_TO_SENDER;
    }
}
