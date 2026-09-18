<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function buyerprofile()
    {
        return $this->hasOne(BuyerProfile::class);
    }

    public function sellerProfile()
    {
        return $this->hasOne(SellerProfile::class);
    }

    public function books()
    {
        return $this->hasMany(Book::class, 'seller_id');
    }

    /** Commandes passées par cet utilisateur (côté client). */
    public function orders()
    {
        return $this->hasMany(Order::class, 'buyer_id');
    }

    /** Commandes reçues sur les livres de cet utilisateur (côté vendeur). */
    public function sales()
    {
        return $this->hasMany(Order::class, 'seller_id');
    }

    public function isSeller(): bool
    {
        return $this->role === 'vendeur';
    }

    public function isClient(): bool
    {
        return $this->role === 'client';
    }
}