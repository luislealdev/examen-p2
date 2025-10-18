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
        'last_update'
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
