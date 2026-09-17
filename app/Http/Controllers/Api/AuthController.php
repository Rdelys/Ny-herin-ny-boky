<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

/**
 * Authentification pour l'application mobile (acheteurs uniquement).
 * Utilise Laravel Sanctum (jetons personnels) : chaque connexion réussie
 * renvoie un "token" que l'app stocke et renvoie ensuite dans le header
 * Authorization: Bearer <token> sur chaque requête protégée.
 *
 * Aucune route d'inscription "vendeur" ici : l'espace vendeur reste
 * exclusivement sur le site web.
 */
class AuthController extends Controller
{
    /**
     * Inscription d'un acheteur (équivalent de AuthController::registerClient
     * côté site web, mais renvoie du JSON + un token au lieu d'une session).
     */
    public function register(Request $request): JsonResponse
    {
        $data = $request->validate([
            'prenom' => ['required', 'string', 'max:255'],
            'nom' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'localisation' => ['nullable', 'string', 'max:255'],
            'motif_inscription' => ['nullable', 'string', 'max:255'],
            'types_livres_recherches' => ['nullable', 'string', 'max:255'],
            'device_name' => ['nullable', 'string', 'max:255'],
        ]);

        $user = User::create([
            'name' => trim($data['prenom'] . ' ' . $data['nom']),
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'client',
        ]);

        $user->buyerProfile()->create([
            'localisation' => $data['localisation'] ?? null,
            'motif_inscription' => $data['motif_inscription'] ?? null,
            'types_livres_recherches' => $data['types_livres_recherches'] ?? null,
        ]);

        $token = $user->createToken($data['device_name'] ?? 'mobile-app')->plainTextToken;

        return response()->json([
            'user' => $this->formatUser($user->fresh('buyerProfile')),
            'token' => $token,
        ], 201);
    }

    /**
     * Connexion. Un compte vendeur peut se connecter (mêmes identifiants
     * que sur le site) mais reçoit une erreur explicite : l'app mobile
     * n'a rien à lui proposer.
     */
    public function login(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'device_name' => ['nullable', 'string', 'max:255'],
        ]);

        $user = User::where('email', $data['email'])->first();

        if (! $user || ! Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ["Email ou mot de passe incorrect."],
            ]);
        }

        if (! $user->isClient()) {
            throw ValidationException::withMessages([
                'email' => ["Ce compte est un compte vendeur. L'application mobile est réservée aux acheteurs — utilisez le site web pour gérer votre boutique."],
            ]);
        }

        $token = $user->createToken($data['device_name'] ?? 'mobile-app')->plainTextToken;

        return response()->json([
            'user' => $this->formatUser($user->load('buyerProfile')),
            'token' => $token,
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'ok']);
    }

    /** Déconnecte tous les appareils (tous les jetons de l'utilisateur). */
    public function logoutAll(Request $request): JsonResponse
    {
        $request->user()->tokens()->delete();

        return response()->json(['message' => 'ok']);
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'user' => $this->formatUser($request->user()->load('buyerProfile')),
        ]);
    }

    protected function formatUser(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'profile' => $user->buyerProfile ? [
                'localisation' => $user->buyerProfile->localisation,
                'motif_inscription' => $user->buyerProfile->motif_inscription,
                'types_livres_recherches' => $user->buyerProfile->types_livres_recherches,
            ] : null,
        ];
    }
}
