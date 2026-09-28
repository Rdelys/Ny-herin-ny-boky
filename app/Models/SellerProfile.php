<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SellerProfile extends Model
{
    protected $fillable = [
        'user_id',
        'nom_entreprise',
        'localisation',
        'code_postal',
        'numero_paiement',
        'mode_paiement',
        'commission_status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}