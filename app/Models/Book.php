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
        'langue',              // <-- ajouté
        'image_path',
        'livraison_disponible',
        'delai_livraison_min',
        'delai_livraison_max',
    ];

    private const LANGUE_FLAGS = [
    'mg' => 'mg', // Madagascar
    'fr' => 'fr', // France
    'en' => 'gb', // Royaume-Uni
    'zh' => 'cn', // Chine
    'it' => 'it', // Italie
    'de' => 'de', // Allemagne
    'es' => 'es', // Espagne
];

    /** Libellé prêt à afficher : "Disponible, livraison sous 24h" ou "Livraison sous X à Y jours". */
    public function getDelaiLivraisonLabelAttribute(): string
    {
        if ($this->delai_livraison_min <= 1 && $this->delai_livraison_max <= 1) {
            return __('home.book_delivery_now');
        }

        if ($this->delai_livraison_min == $this->delai_livraison_max) {
            return __('home.book_delivery_days', ['n' => $this->delai_livraison_min]);
        }

        return __('home.book_delivery_range', [
            'min' => $this->delai_livraison_min,
            'max' => $this->delai_livraison_max,
        ]);
    }

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
        if ($this->prix_achat === null) {
            return null;
        }
 
        $rate = Setting::commissionRateFor($this->prix_achat);
 
        return (int) round($this->prix_achat * (1 + $rate / 100));
    }
 
    public function getPrixLocationClientAttribute(): ?int
    {
        if ($this->prix_location === null) {
            return null;
        }
 
        $rate = Setting::commissionRateFor($this->prix_location);
 
        return (int) round($this->prix_location * (1 + $rate / 100));
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    /** Libellé traduit de la langue du livre, selon la locale active. */
public function getLangueLabelAttribute(): ?string
{
    if (! $this->langue) {
        return null;
    }

    return __('home.book_language_' . $this->langue);
}

public function getLangueFlagAttribute(): ?string
{
    if (! $this->langue) {
        return null;
    }

    return self::LANGUE_FLAGS[$this->langue] ?? null;
}
}