<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Affiche le profil : vue "vendeur" ou "client" selon le rôle de l'utilisateur connecté.
     */
    public function show(Request $request): View
    {
        $user = $request->user();

        if ($user->isSeller()) {
            return view('profile.seller', [
                'user' => $user,
                'profile' => $user->sellerProfile,
                'books' => $user->books()->latest()->get(),
            ]);
        }

        return view('profile.client', [
            'user' => $user,
            'profile' => $user->buyerProfile,
        ]);
    }
}