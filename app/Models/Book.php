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
        'quantite',
        'categorie',
        'etat',
        'image_path',
        'livraison_disponible',
    ];

    /**
     * Taux de commission appliqué au-dessus du prix fixé par le vendeur
     * pour obtenir le prix affiché au client (10%).
     */
    public const COMMISSION_RATE = 0.10;

    protected function casts(): array
    {
        return [
            'livraison_disponible' => 'boolean',
        ];
    }

    /**
     * Prix d'achat affiché au CLIENT (prix vendeur + 10% de commission).
     * `prix_achat` reste le prix brut défini par le vendeur (ce qu'il perçoit).
     */
    public function getPrixAchatClientAttribute(): ?int
    {
        return $this->prix_achat !== null
            ? (int) round($this->prix_achat * (1 + self::COMMISSION_RATE))
            : null;
    }

    /**
     * Prix de location affiché au CLIENT (prix vendeur + 10% de commission).
     */
    public function getPrixLocationClientAttribute(): ?int
    {
        return $this->prix_location !== null
            ? (int) round($this->prix_location * (1 + self::COMMISSION_RATE))
            : null;
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }
}