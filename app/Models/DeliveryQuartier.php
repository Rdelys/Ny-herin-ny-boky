<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryQuartier extends Model
{
    protected $fillable = ['zone_id', 'nom', 'frais', 'actif'];

    protected function casts(): array { return ['actif' => 'boolean']; }

    public function zone() { return $this->belongsTo(DeliveryZone::class, 'zone_id'); }
}