<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Delivery extends Model
{
    protected $fillable = [
        'groupe_reference', 'type', 'zone_id', 'zone_nom',
        'quartier_id', 'quartier_nom', 'quartier_personnalise',
        'cooperative_id', 'cooperative_nom', 'cooperative_personnalisee',
        'frais_base', 'supplement_vip', 'frais', 'frais_gratuit', 'frais_a_confirmer',
        'taxi_brousse_pa', 'heure_prevue', 'delai_min_h', 'delai_max_h',
    ];

    protected function casts(): array
    {
        return [
            'quartier_personnalise' => 'boolean',
            'cooperative_personnalisee' => 'boolean',
            'frais_gratuit' => 'boolean',
            'frais_a_confirmer' => 'boolean',
            'taxi_brousse_pa' => 'boolean',
            'heure_prevue' => 'datetime',
        ];
    }

    public function zone() { return $this->belongsTo(DeliveryZone::class, 'zone_id'); }
    public function orders() { return $this->hasMany(Order::class, 'groupe_reference', 'groupe_reference'); }

    public function getTypeLabelAttribute(): string
    {
        return $this->type === 'vip' ? 'VIP' : 'Standard';
    }

    /** Quartier (Antananarivo) ou coopérative (provinces). */
    public function getLieuLabelAttribute(): string
    {
        $nom = $this->quartier_nom ?? $this->cooperative_nom ?? '—';
        $perso = ($this->quartier_personnalise && ! $this->quartier_id)
            || ($this->cooperative_personnalisee && ! $this->cooperative_id);

        return $nom . ($perso ? ' (saisi par le client)' : '');
    }
}