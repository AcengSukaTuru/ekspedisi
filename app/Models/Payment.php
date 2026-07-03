<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    // Payment status
    public const STATUS_PENDING = 'pending';
    public const STATUS_PAID = 'paid';
    public const STATUS_FAILED = 'failed';

    // Payment type
    public const TYPE_PREPAID = 'prepaid'; // bayar di depan
    public const TYPE_COD = 'cod';         // bayar saat terima barang

    protected $fillable = [
        'shipment_id',
        'amount',
        'payment_date',
        'payment_method',
        'payment_type',
        'payment_status',
        'proof_of_payment',
        'verified_at',
        'verified_by',
        'admin_note',
        'collected_by',
        'cod_collection_proof',
        'cod_collected_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'payment_date' => 'date',
            'verified_at' => 'datetime',
            'cod_collected_at' => 'datetime',
        ];
    }

    // ─── Relationships ──────────────────────────────────────────

    public function shipment(): BelongsTo
    {
        return $this->belongsTo(Shipment::class);
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function collectedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'collected_by');
    }

    // ─── Helpers ────────────────────────────────────────────────

    public static function statuses(): array
    {
        return [
            self::STATUS_PENDING,
            self::STATUS_PAID,
            self::STATUS_FAILED,
        ];
    }

    public static function types(): array
    {
        return [
            self::TYPE_PREPAID => 'Bayar di Depan',
            self::TYPE_COD => 'Bayar di Tempat (COD)',
        ];
    }

    public function isCod(): bool
    {
        return $this->payment_type === self::TYPE_COD;
    }

    public function isPrepaid(): bool
    {
        return $this->payment_type === self::TYPE_PREPAID;
    }

    /**
     * COD: kurir sudah setor uang ke sistem?
     */
    public function isCodCollected(): bool
    {
        return $this->isCod() && $this->cod_collected_at !== null;
    }

    public function typeLabel(): string
    {
        return self::types()[$this->payment_type] ?? $this->payment_type;
    }

    public function statusLabel(): string
    {
        return match ($this->payment_status) {
            self::STATUS_PENDING => 'Menunggu',
            self::STATUS_PAID => 'Lunas',
            self::STATUS_FAILED => 'Gagal',
            default => $this->payment_status,
        };
    }
}
