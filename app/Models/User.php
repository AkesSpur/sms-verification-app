<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * 
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'is_admin',
        'is_reseller',
        'balance',
        'role',
        'status'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
            'is_reseller' => 'boolean',
            'balance' => 'decimal:2',
        ];
    }

    public function isReseller()
    {
        return (bool) ($this->is_reseller ?? false);
    }

    /**
     * Check if user is admin
     */
    public function isAdmin()
    {
        return $this->is_admin || $this->role == 'admin';
    }

    /**
     * Check if user is active
     */
    public function isActive()
    {
        return $this->status == 'active';
    }

    public function daisyOrders()
    {
        return $this->hasMany(DaisyOrder::class);
    }
    
    /**
     * Get the orders for the user.
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Get the digital product orders for the user.
     */
    public function digitalProductOrders()
    {
        return $this->hasMany(DigitalProductOrder::class);
    }

    /**
     * Get the gift orders for the user.
     */
    public function giftOrders()
    {
        return $this->hasMany(GiftOrder::class);
    }

    /**
     * Get the social media orders for the user.
     */
    public function socialMediaOrders()
    {
        return $this->hasMany(SocialMediaOrder::class);
    }

    /**
     * Get the transactions for the user.
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Get transactions where this user was the admin.
     */
    public function adminTransactions()
    {
        return $this->hasMany(Transaction::class, 'admin_id');
    }

    /**
     * Virtual account associated with the user (PaymentPoint).
     */
    public function virtualAccount()
    {
        return $this->hasOne(VirtualAccount::class);
    }

    /**
     * Check if user has sufficient balance for purchase.
     */
    public function hasSufficientBalance($amount)
    {
        return $this->balance >= $amount;
    }

    /**
     * Deduct amount from user balance with transaction logging.
     */
    public function deductBalance($amount, $category = null, $description = null, $reference = null, $admin = null)
    {
        $amount = (float) $amount;

        $updated = static::where('id', $this->id)
            ->where('balance', '>=', $amount)
            ->update(['balance' => DB::raw('balance - ' . $amount)]);

        if (!$updated) {
            throw new \Exception('Insufficient balance');
        }

        $this->refresh();
        $balanceAfter = (float) $this->balance;
        $balanceBefore = $balanceAfter + $amount;

        $txn = null;
        if ($category && $description) {
            $data = [
                'user_id' => $this->id,
                'type' => 'debit',
                'category' => $category,
                'amount' => $amount,
                'balance_before' => $balanceBefore,
                'balance_after' => $balanceAfter,
                'description' => $description,
                'metadata' => [],
                'admin_id' => $admin?->id,
                'status' => 'completed'
            ];

            if ($reference) {
                $data['reference_type'] = get_class($reference);
                $data['reference_id'] = $reference->id;
            }

            $txn = Transaction::create($data);
        }

        return $txn ?? $this;
    }

    /**
     * Add amount to user balance with transaction logging.
     */
    public function addBalance($amount, $category = null, $description = null, $reference = null, $admin = null)
    {
        $amount = (float) $amount;

        static::where('id', $this->id)
            ->update(['balance' => DB::raw('balance + ' . $amount)]);

        $this->refresh();
        $balanceAfter = (float) $this->balance;
        $balanceBefore = $balanceAfter - $amount;

        $txn = null;
        if ($category && $description) {
            $data = [
                'user_id' => $this->id,
                'type' => 'credit',
                'category' => $category,
                'amount' => $amount,
                'balance_before' => $balanceBefore,
                'balance_after' => $balanceAfter,
                'description' => $description,
                'metadata' => [],
                'admin_id' => $admin?->id,
                'status' => 'completed'
            ];

            if ($reference) {
                $data['reference_type'] = get_class($reference);
                $data['reference_id'] = $reference->id;
            }

            $txn = Transaction::create($data);
        }

        return $txn ?? $this;
    }
}
