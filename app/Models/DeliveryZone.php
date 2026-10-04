<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryZone extends Model
{
    protected $fillable = ['nom', 'est_capitale', 'frais', 'delai_min_h', 'delai_max_h', 'actif', 'position'];

    protected function casts(): array
    {
        return ['est_capitale' => 'boolean', 'actif' => 'boolean'];
    }

    public function quartiers() { return $this->hasMany(DeliveryQuartier::class, 'zone_id'); }
    public function cooperatives() { return $this->hasMany(DeliveryCooperative::class, 'zone_id'); }

    public function scopeActifs($query) { return $query->where('actif', true); }
}