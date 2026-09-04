<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    protected $fillable = [
        'seller_id',
        'titre',
        'auteur',
        'description',
        'prix_achat',
        'prix_location',
        'categorie',
        'etat',
        'image_path',
        'livraison_disponible',
        'frais_livraison',
    ];

    protected function casts(): array
    {
        return [
            'livraison_disponible' => 'boolean',
        ];
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }
}