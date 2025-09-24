<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Order model representing a transaction.
 *
 * Columns:
 * - id (int)
 * - user_id (int, nullable if guest orders are allowed)
 * - total (decimal 12,2)
 * - status (string) e.g., pending, paid, refunded
 * - paid_at (datetime, nullable)
 * - created_at/updated_at (timestamps)
 *
 * @author Manohar Zarkar
 */
class Order extends Model
{
    use HasFactory;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'total',
        'status',
        'paid_at',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'total' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    /**
     * The owning user of the order.
     *
     * @return BelongsTo<User, Order>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope a query to only include paid orders.
     *
     * @param  \Illuminate\Database\Eloquent\Builder<Order>  $query
     * @return \Illuminate\Database\Eloquent\Builder<Order>
     */
    public function scopePaid($query)
    {
        return $query->whereNotNull('paid_at')->where('status', 'paid');
    }
}


