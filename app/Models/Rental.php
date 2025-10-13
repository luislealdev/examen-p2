<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rental extends Model
{
    protected $primaryKey = 'rental_id';
    
    protected $fillable = [
        'inventory_id',
        'customer_id', 
        'staff_id',
        'rental_date',
        'return_date',
        'due_date',
        'rental_amount',
        'late_fee',
        'late_fee_applied',
        'status',
        'notes'
    ];

    protected $casts = [
        'rental_date' => 'datetime',
        'return_date' => 'datetime',
        'due_date' => 'datetime',
        'rental_amount' => 'decimal:2',
        'late_fee' => 'decimal:2',
        'late_fee_applied' => 'boolean',
        'last_update' => 'datetime'
    ];

    const UPDATED_AT = 'last_update';
    const CREATED_AT = null;

    /**
     * Get the inventory record associated with the rental
     */
    public function inventory()
    {
        return $this->belongsTo(Inventory::class, 'inventory_id', 'inventory_id');
    }

    /**
     * Get the customer associated with the rental
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'customer_id');
    }

    /**
     * Get the staff member who processed the rental
     */
    public function staff()
    {
        return $this->belongsTo(Staff::class, 'staff_id', 'staff_id');
    }

    /**
     * Get the film through inventory
     */
    public function film()
    {
        return $this->hasOneThrough(Film::class, Inventory::class, 'inventory_id', 'film_id', 'inventory_id', 'film_id');
    }

    /**
     * Get the store through inventory
     */
    public function store()
    {
        return $this->hasOneThrough(Store::class, Inventory::class, 'inventory_id', 'store_id', 'inventory_id', 'store_id');
    }

    /**
     * Check if rental is overdue
     */
    public function getIsOverdueAttribute(): bool
    {
        if ($this->status === 'returned') return false;
        
        return $this->due_date && now()->isAfter($this->due_date);
    }

    /**
     * Get rental duration in days
     */
    public function getRentalDaysAttribute(): int
    {
        if ($this->return_date) {
            return $this->rental_date->diffInDays($this->return_date);
        }
        return $this->rental_date->diffInDays(now());
    }

    /**
     * Calculate late fee based on overdue days
     */
    public function calculateLateFee()
    {
        if (!$this->due_date || !$this->is_overdue || $this->status === 'returned') {
            return 0.00;
        }

        $overdueDays = $this->due_date->diffInDays(now());
        $dailyLateFee = 1.50; // $1.50 per day late fee
        
        return $overdueDays * $dailyLateFee;
    }

    /**
     * Get current late fee amount
     */
    public function getCurrentLateFee()
    {
        if ($this->late_fee_applied) {
            return (float) $this->late_fee;
        }
        
        return $this->calculateLateFee();
    }

    /**
     * Apply late fee to the rental
     */
    public function applyLateFee(): void
    {
        if (!$this->late_fee_applied && $this->is_overdue) {
            $this->update([
                'late_fee' => $this->calculateLateFee(),
                'late_fee_applied' => true
            ]);
        }
    }

    /**
     * Get overdue days
     */
    public function getOverdueDaysAttribute(): int
    {
        if (!$this->due_date || !$this->is_overdue) {
            return 0;
        }
        
        return $this->due_date->diffInDays(now());
    }

    /**
     * Scope for active rentals
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for overdue rentals
     */
    public function scopeOverdue($query)
    {
        return $query->where('status', 'overdue');
    }

    /**
     * Scope for returned rentals
     */
    public function scopeReturned($query)
    {
        return $query->where('status', 'returned');
    }
}
