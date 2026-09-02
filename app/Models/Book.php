<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    protected $fillable = [
        'seller_id',
        'titre',
        'description',
        'prix_achat',
        'prix_location',
        'categorie',
        'image_path',
    ];

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }
}