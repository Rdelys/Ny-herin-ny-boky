<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    public const PROMO_PERCENT = 'percent';
    public const PROMO_AMOUNT = 'amount';
    public const PROMO_MAX_PERCENT = 90;

    protected $fillable = [
        'seller_id',
        'titre',
        'auteur',
        'description',
        'prix_achat',
        'prix_location',
        'promo_type',
        'promo_valeur',
        'quantite',
        'categorie',
        'etat',
        'nombre_pages',
        'langue',
        'format',
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

    protected function casts(): array
    {
        return [
            'livraison_disponible' => 'boolean',
        ];
    }

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

    /* ============================================================
       PRIX & PROMOTION
       ------------------------------------------------------------
       - prix_achat           : prix brut défini par le vendeur
       - prix_achat_promo     : prix vendeur après promotion (ce qu'il perçoit)
       - prix_achat_client_original : prix client SANS promotion
       - prix_achat_client    : prix client réellement payé
                                (promo incluse, commission recalculée)
    ============================================================ */

    /** Prix client (vendeur + commission) pour un prix vendeur donné. */
    private function prixClientPour(int $net): int
    {
        $rate = Setting::commissionRateFor($net);

        return (int) round($net * (1 + $rate / 100));
    }

    /** Prix vendeur après promotion (égal à prix_achat s'il n'y a pas de promo). */
    public function getPrixAchatPromoAttribute(): ?int
    {
        if ($this->prix_achat === null) {
            return null;
        }

        $base = (int) $this->prix_achat;
        $valeur = (int) $this->promo_valeur;

        if ($valeur <= 0) {
            return $base;
        }

        return match ($this->promo_type) {
            self::PROMO_PERCENT => max(0, (int) round($base * (1 - $valeur / 100))),
            self::PROMO_AMOUNT => max(0, $base - $valeur),
            default => $base,
        };
    }

    /** Le livre est-il actuellement en promotion ? */
    public function getEnPromoAttribute(): bool
    {
        return $this->prix_achat !== null
            && (int) $this->prix_achat > 0
            && $this->prix_achat_promo < (int) $this->prix_achat;
    }

    /** Prix client avant promotion (null si pas de prix de vente). */
    public function getPrixAchatClientOriginalAttribute(): ?int
    {
        if ($this->prix_achat === null) {
            return null;
        }

        return $this->prixClientPour((int) $this->prix_achat);
    }

    /**
     * Prix d'achat affiché au CLIENT (prix vendeur, remisé si promo, + commission).
     * Le taux vient de Setting::commissionRateFor() — réglable par l'admin.
     */
    public function getPrixAchatClientAttribute(): ?int
    {
        if ($this->prix_achat === null) {
            return null;
        }

        $net = $this->en_promo ? $this->prix_achat_promo : (int) $this->prix_achat;

        return $this->prixClientPour($net);
    }

    /** Économie réalisée par le client, en Ar (null hors promo). */
    public function getPromoEconomieClientAttribute(): ?int
    {
        if (! $this->en_promo) {
            return null;
        }

        return max(0, $this->prix_achat_client_original - $this->prix_achat_client);
    }

    /** Badge prêt à afficher : "-20%" ou "-1 100 Ar" (null hors promo). */
    public function getPromoLabelAttribute(): ?string
    {
        if (! $this->en_promo) {
            return null;
        }

        if ($this->promo_type === self::PROMO_PERCENT) {
            return '-' . (int) $this->promo_valeur . '%';
        }

        return '-' . number_format($this->promo_economie_client, 0, ',', ' ') . ' Ar';
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

    /** Libellé traduit du format du livre. */
    public function getFormatLabelAttribute(): ?string
    {
        if (! $this->format) {
            return null;
        }

        return __('home.book_format_' . $this->format);
    }

    /** « 320 pages » (null si non renseigné). */
    public function getPagesLabelAttribute(): ?string
    {
        return $this->nombre_pages
            ? __('home.book_pages_count', ['n' => $this->nombre_pages])
            : null;
    }

    public function getLangueFlagAttribute(): ?string
    {
        if (! $this->langue) {
            return null;
        }

        return self::LANGUE_FLAGS[$this->langue] ?? null;
    }

    /** Livres du compte officiel du site : livraison offerte. */
    public function getLivraisonGratuiteAttribute(): bool
    {
        return (bool) $this->seller?->is_platform;
    }
}