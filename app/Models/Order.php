<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Order extends Model
{
    /** Statut appliqué à toute nouvelle commande. */
    public const STATUT_DEFAUT = 'en_attente_livraison';

    /** Seul l'admin fait évoluer une commande dans cette liste. */
    public const STATUTS = [
        'en_attente_livraison',
        'en_livraison',
        'livree',
        'annulee',
    ];

    /** Statut qui exige qu'un livreur soit assigné. */
    public const STATUT_EN_LIVRAISON = 'en_livraison';

    /** Libellés français, utilisés par le back-office (admin) uniquement. */
    public const STATUT_LABELS = [
        'en_attente_livraison' => 'En attente de livraison',
        'en_livraison' => 'En livraison',
        'livree' => 'Livrée',
        'annulee' => 'Annulée',
    ];

    /**
     * Reversement de l'argent de la plateforme vers le vendeur.
     * Toute commande naît « dû » ; l'admin bascule sur « envoyé » depuis
     * /admin/paiements une fois le transfert mobile money effectué.
     */
    public const PAIEMENT_DU = 'du';
    public const PAIEMENT_ENVOYE = 'envoye';

    public const PAIEMENT_LABELS = [
        self::PAIEMENT_DU => 'Dû',
        self::PAIEMENT_ENVOYE => 'Envoyé',
    ];

    protected $fillable = [
        'reference',
        'buyer_id',
        'guest_name',
        'guest_phone',
        'guest_email',
        'adresse_livraison',   // <-- renommé
        'facture_path',        // <-- ajouté
        'seller_id',
        'book_id',
        'book_titre',
        'quantite',
        'prix_unitaire',
        'total',
        'commission_rate',
        'montant_vendeur',
        'mode_paiement',
        'reference_paiement',
        'ville',
        'statut',
        'deliverer_id',
        'livree_at',
        'paiement_vendeur',
        'paiement_vendeur_at',
    ];

    protected function casts(): array
    {
        return [
            'commission_rate' => 'float',
            'livree_at' => 'datetime',
            'paiement_vendeur_at' => 'datetime',
        ];
    }

    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function deliverer()
    {
        return $this->belongsTo(Deliverer::class);
    }

    /** Référence lisible et unique de la commande (ex: CMD-260917-4XK9T). */
    public static function genererReference(): string
    {
        do {
            $reference = 'CMD-' . now()->format('ymd') . '-' . strtoupper(Str::random(5));
        } while (static::where('reference', $reference)->exists());

        return $reference;
    }

    /** Libellé français du statut (back-office). */
    public function getStatutLabelAttribute(): string
    {
        return self::STATUT_LABELS[$this->statut] ?? $this->statut;
    }

    /** Libellé traduit du statut (front-office : client / vendeur). */
    public function getStatutTraduitAttribute(): string
    {
        return __('home.order_status_' . $this->statut);
    }

    /** Libellé du mode de paiement (MVola, Orange Money...). */
    public function getModePaiementLabelAttribute(): string
    {
        return Setting::PAYMENT_METHODS[$this->mode_paiement] ?? $this->mode_paiement;
    }

    /** Libellé français du reversement vendeur (back-office). */
    public function getPaiementVendeurLabelAttribute(): string
    {
        return self::PAIEMENT_LABELS[$this->paiement_vendeur] ?? $this->paiement_vendeur;
    }

    /** Ce que la plateforme garde sur cette commande. */
    public function getCommissionAttribute(): int
    {
        return (int) $this->total - (int) $this->montant_vendeur;
    }

    /**
     * Commandes qui comptent dans l'argent : une commande annulée ne
     * génère ni chiffre d'affaires, ni reversement au vendeur.
     */
    public function scopeFacturables($query)
    {
        return $query->where('statut', '!=', 'annulee');
    }

    public function scopeDues($query)
    {
        return $query->facturables()->where('paiement_vendeur', self::PAIEMENT_DU);
    }

    public function scopeEnvoyees($query)
    {
        return $query->where('paiement_vendeur', self::PAIEMENT_ENVOYE);
    }

    /** Nom de l'acheteur, qu'il ait un compte ou non. */
    public function getBuyerNameAttribute(): string
    {
        return $this->buyer?->name ?? $this->guest_name ?? '—';
    }

    /** Coordonnées de contact — email du compte, ou téléphone/email fournis en invité. */
    public function getBuyerContactAttribute(): string
    {
        if ($this->buyer) {
            return $this->buyer->email;
        }

        return $this->guest_phone ?: ($this->guest_email ?: '—');
    }

    public function isGuestOrder(): bool
    {
        return $this->buyer_id === null;
    }

    public function getFactureUrlAttribute(): ?string
    {
        return $this->facture_path ? \Storage::disk('public')->url($this->facture_path) : null;
    }
}
