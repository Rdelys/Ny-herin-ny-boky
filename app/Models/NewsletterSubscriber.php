<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewsletterSubscriber extends Model
{
    // Pas de colonne updated_at : un abonné ne se "modifie" pas.
    const UPDATED_AT = null;

    protected $fillable = ['email'];
}