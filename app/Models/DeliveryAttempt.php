<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeliveryAttempt extends Model
{
    use HasFactory;

    public const STATUS_SUCCESS = 'success';
    public const STATUS_FAILED = 'failed';

    // Alasan gagal
    public const REASON_NOT_AVAILABLE = 'receiver_not_available';
    public const REASON_WRONG_ADDRESS = 'wrong_address';
    public const REASON_REFUSED = 'refused';
    public const REASON_OTHER = 'other';

    protected $fillable = [
        'shipment_id',
        'attempted_by',
        'attempt_number',
        'status',
        'failure_reason',
        'notes',
        'proof_path',
        'attempted_at',
        'next_retry_at',
    ];

    protected function casts(): array
    {
        return [
            'attempted_at' => 'datetime',
            'next_retry_at' => 'datetime',
        ];
    }

    public function shipment(): BelongsTo
    {
        return $this->belongsTo(Shipment::class);
    }

    public function attemptedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'attempted_by');
    }

    public function isSuccessful(): bool
    {
        return $this->status === self::STATUS_SUCCESS;
    }

    public function isFailed(): bool
    {
        return $this->status === self::STATUS_FAILED;
    }

    public static function failureReasons(): array
    {
        return [
            self::REASON_NOT_AVAILABLE => 'Penerima tidak ada di tempat',
            self::REASON_WRONG_ADDRESS => 'Alamat salah/tidak ditemukan',
            self::REASON_REFUSED => 'Penerima menolak',
            self::REASON_OTHER => 'Lainnya',
        ];
    }

    public function failureReasonLabel(): string
    {
        return self::failureReasons()[$this->failure_reason] ?? $this->failure_reason ?? '-';
    }
}
