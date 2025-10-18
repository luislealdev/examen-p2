<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'payment';

    /**
     * The primary key associated with the table.
     */
    protected $primaryKey = 'payment_id';

    /**
     * Indicates if the model should be timestamped.
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'customer_id',
        'staff_id', 
        'rental_id',
        'amount',
        'payment_date',
        'payment_type',
        'notes',
        'last_update'
    ];

    /**
     * Payment types constants
     */
    public const TYPE_RENTAL = 'rental';
    public const TYPE_LATE_FEE = 'late_fee';
    public const TYPE_DAMAGE = 'damage';
    public const TYPE_OTHER = 'other';

    public const PAYMENT_TYPES = [
        self::TYPE_RENTAL => 'Alquiler',
        self::TYPE_LATE_FEE => 'Mora',
        self::TYPE_DAMAGE => 'Daño',
        self::TYPE_OTHER => 'Otro'
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'datetime',
        'last_update' => 'datetime',
    ];

    /**
     * Get formatted amount
     */
    public function getFormattedAmountAttribute(): string
    {
        return '$' . number_format((float)$this->amount, 2);
    }

    /**
     * Get payment type label
     */
    public function getPaymentTypeLabelAttribute(): string
    {
        return self::PAYMENT_TYPES[$this->payment_type] ?? 'Desconocido';
    }

    /**
     * Get payment status based on rental
     */
    public function getStatusAttribute(): string
    {
        if (!$this->rental_id) {
            return 'Pago manual';
        }

        if ($this->rental && $this->rental->return_date) {
            return 'Completado';
        }

        return 'Pendiente';
    }

    /**
     * Get the customer that made this payment.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'customer_id');
    }

    /**
     * Get the staff that processed this payment.
     */
    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'staff_id', 'staff_id');
    }

    /**
     * Get the rental that this payment is for.
     */
    public function rental(): BelongsTo
    {
        return $this->belongsTo(Rental::class, 'rental_id', 'rental_id');
    }
}
