<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    /**
     * Connexion (modal "Connexion").
     */
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()
                ->withErrors(['email' => "Email ou mot de passe incorrect."])
                ->withInput($request->only('email', '_auth_form'));
        }

        $request->session()->regenerate();

        return redirect()->intended('/profil');
    }

    /**
     * Inscription client / acheteur (modal "Inscription Client").
     */
    public function registerClient(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'prenom' => ['required', 'string', 'max:255'],
            'nom' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'localisation' => ['nullable', 'string', 'max:255'],
            'motif_inscription' => ['nullable', 'string', 'max:255'],
            'types_livres_recherches' => ['nullable', 'string', 'max:255'],
        ]);

        $user = User::create([
            'name' => trim($data['prenom'].' '.$data['nom']),
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'client',
        ]);

        $user->buyerProfile()->create([
            'localisation' => $data['localisation'] ?? null,
            'motif_inscription' => $data['motif_inscription'] ?? null,
            'types_livres_recherches' => $data['types_livres_recherches'] ?? null,
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect('/profil');
    }

    /**
     * Inscription vendeur (modal "Inscription Vendeur" — étape 1).
     * Les étapes 2/3 (infos boutique détaillées, ajout du 1er livre)
     * peuvent être ajoutées ensuite sur la page /profil du vendeur.
     */
    public function registerSeller(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nom_entreprise' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'localisation' => ['nullable', 'string', 'max:255'],
            'code_postal' => ['nullable', 'string', 'max:20'],
            'numero_paiement' => ['nullable', 'string', 'max:30'],
            // L'abonnement est désactivé pour l'instant : seule "commission" est acceptée,
            // même si le formulaire envoyait autre chose.
            'mode_paiement' => ['nullable', Rule::in(['commission'])],
        ]);

        $user = User::create([
            'name' => $data['nom_entreprise'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'vendeur',
        ]);

        $user->sellerProfile()->create([
            'nom_entreprise' => $data['nom_entreprise'],
            'localisation' => $data['localisation'] ?? null,
            'code_postal' => $data['code_postal'] ?? null,
            'numero_paiement' => $data['numero_paiement'] ?? null,
            'mode_paiement' => 'commission',
            'commission_status' => '10%',
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect('/profil');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}