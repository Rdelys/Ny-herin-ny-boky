<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BuyerProfile extends Model
{
    protected $fillable = [
        'user_id',
        'localisation',
        'motif_inscription',
        'types_livres_recherches',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}