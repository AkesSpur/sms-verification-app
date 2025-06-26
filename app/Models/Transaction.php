<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'transaction_id',
        'reference',
        'email',
        'payment_method',
        'type',
        'category',
        'amount',
        'balance_before',
        'balance_after',
        'description',
        'metadata',
        'reference_type',
        'reference_id',
        'admin_id',
        'status'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'balance_before' => 'decimal:2',
        'balance_after' => 'decimal:2',
        'metadata' => 'array'
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($transaction) {
            if (empty($transaction->transaction_id)) {
                $transaction->transaction_id = 'TXN' . strtoupper(Str::random(8)) . time();
            }
        });
    }

    /**
     * Get the user that owns the transaction.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the admin that performed the transaction.
     */
    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    /**
     * Get the reference model (polymorphic relationship).
     */
    public function reference()
    {
        return $this->morphTo('reference', 'reference_type', 'reference_id');
    }

    /**
     * Create a new transaction record.
     */
    public static function createTransaction(
        User $user,
        string $type,
        string $category,
        float $amount,
        string $description,
        array $metadata = [],
        $reference = null,
        User $admin = null
    ): self {
        $balanceBefore = $user->balance;
        $balanceAfter = $type === 'credit' ? $balanceBefore + $amount : $balanceBefore - $amount;

        $transactionData = [
            'user_id' => $user->id,
            'type' => $type,
            'category' => $category,
            'amount' => $amount,
            'balance_before' => $balanceBefore,
            'balance_after' => $balanceAfter,
            'description' => $description,
            'metadata' => $metadata,
            'admin_id' => $admin?->id,
            'status' => 'completed'
        ];

        if ($reference) {
            $transactionData['reference_type'] = get_class($reference);
            $transactionData['reference_id'] = $reference->id;
        }

        return self::create($transactionData);
    }

    /**
     * Get formatted amount with currency.
     */
    public function getFormattedAmountAttribute(): string
    {
        return '₦' . number_format($this->amount, 2);
    }

    /**
     * Get transaction type icon.
     */
    public function getTypeIconAttribute(): string
    {
        return match($this->type) {
            'credit' => 'fas fa-arrow-up text-green-600',
            'debit' => 'fas fa-arrow-down text-red-600',
            default => 'fas fa-circle text-gray-600'
        };
    }

    /**
     * Get category display name.
     */
    public function getCategoryDisplayAttribute(): string
    {
        return match($this->category) {
            'fund_addition' => 'Fund Addition',
            'fund_withdrawal' => 'Fund Withdrawal',
            'gift_purchase' => 'Gift Purchase',
            'gift_refund' => 'Gift Refund',
            'digital_purchase' => 'Digital Product Purchase',
            'digital_refund' => 'Digital Product Refund',
            'sms_purchase' => 'SMS Service Purchase',
            'sms_refund' => 'SMS Service Refund',
            default => ucfirst(str_replace('_', ' ', $this->category))
        };
    }

    /**
     * Scope for filtering by user.
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope for filtering by type.
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope for filtering by category.
     */
    public function scopeOfCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope for filtering by date range.
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }
}