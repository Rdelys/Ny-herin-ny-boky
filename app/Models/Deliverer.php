<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Livreur. La liste est gérée par l'admin (/admin/livreurs) ; un livreur
 * est assigné à une commande au moment où l'admin passe son statut
 * en « En livraison ».
 */
class Deliverer extends Model
{
    protected $fillable = ['nom', 'telephone', 'zone', 'actif'];

    protected function casts(): array
    {
        return [
            'actif' => 'boolean',
        ];
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function scopeActifs($query)
    {
        return $query->where('actif', true);
    }
}
