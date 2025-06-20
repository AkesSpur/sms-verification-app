<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_admin',
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
            'balance' => 'decimal:2',
        ];
    }

    /**
     * Check if user is admin
     */
    public function isAdmin()
    {
        return $this->is_admin || $this->role === 'admin';
    }

    /**
     * Check if user is active
     */
    public function isActive()
    {
        return $this->status === 'active';
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
     * Check if user has sufficient balance for purchase.
     */
    public function hasSufficientBalance($amount)
    {
        return $this->balance >= $amount;
    }

    /**
     * Deduct amount from user balance.
     */
    public function deductBalance($amount)
    {
        if (!$this->hasSufficientBalance($amount)) {
            throw new \Exception('Insufficient balance');
        }
        
        $this->decrement('balance', $amount);
        return $this;
    }
}
