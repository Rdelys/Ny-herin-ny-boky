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

    protected function casts(): array
    {
        return [
            'livraison_disponible' => 'boolean',
        ];
    }

    /**
     * Prix d'achat affiché au CLIENT (prix vendeur + commission plateforme).
     * `prix_achat` reste le prix brut défini par le vendeur (ce qu'il perçoit).
     * Le taux vient de Setting::commissionRate() — réglable par l'admin
     * depuis /admin/parametres, donc ce prix suit automatiquement tout
     * changement de taux, sans rien à modifier dans le code.
     */
    public function getPrixAchatClientAttribute(): ?int
    {
        return $this->prix_achat !== null
            ? (int) round($this->prix_achat * (1 + Setting::commissionRate() / 100))
            : null;
    }

    /**
     * Prix de location affiché au CLIENT (même logique).
     */
    public function getPrixLocationClientAttribute(): ?int
    {
        return $this->prix_location !== null
            ? (int) round($this->prix_location * (1 + Setting::commissionRate() / 100))
            : null;
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }
}