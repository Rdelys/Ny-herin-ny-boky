<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    public const PROMO_PERCENT = 'percent';
    public const PROMO_AMOUNT = 'amount';
    public const PROMO_MAX_PERCENT = 90;

    public const DELAI_UNITE_HEURES = 'heures';
    public const DELAI_UNITE_JOURS = 'jours';
    public const DELAI_UNITES = [self::DELAI_UNITE_HEURES, self::DELAI_UNITE_JOURS];
    public const DELAI_MAX_HEURES = 168; // 7 jours
    public const DELAI_MAX_JOURS = 60;

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
        'delai_livraison_unite',
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

    /* ============================================================
       DÉLAI DE LIVRAISON (heures ou jours)
    ============================================================ */

    /** Le délai est-il exprimé en heures ? */
    public function getDelaiEnHeuresAttribute(): bool
    {
        return $this->delai_livraison_unite === self::DELAI_UNITE_HEURES;
    }

    /** Délai minimum converti en heures (pour comparer avec le VIP, etc.). */
    public function getDelaiLivraisonMinHeuresAttribute(): int
    {
        return (int) $this->delai_livraison_min * ($this->delai_en_heures ? 1 : 24);
    }

    /** Délai maximum converti en heures. */
    public function getDelaiLivraisonMaxHeuresAttribute(): int
    {
        return (int) $this->delai_livraison_max * ($this->delai_en_heures ? 1 : 24);
    }

    /**
     * Libellé prêt à afficher :
     * - heures : "Livraison sous 2 h" / "Livraison sous 2 à 5 h"
     * - jours  : "Disponible, livraison sous 24h" / "Livraison sous X jours" / "X à Y jours"
     */
    public function getDelaiLivraisonLabelAttribute(): string
    {
        $min = (int) $this->delai_livraison_min;
        $max = (int) $this->delai_livraison_max;

        if ($this->delai_en_heures) {
            if ($min === $max) {
                return __('home.book_delivery_hours', ['n' => $min]);
            }

            return __('home.book_delivery_hours_range', ['min' => $min, 'max' => $max]);
        }

        if ($min <= 1 && $max <= 1) {
            return __('home.book_delivery_now');
        }

        if ($min == $max) {
            return __('home.book_delivery_days', ['n' => $min]);
        }

        return __('home.book_delivery_range', [
            'min' => $min,
            'max' => $max,
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