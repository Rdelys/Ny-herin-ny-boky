<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminAuthController extends Controller
{
    /**
     * Identifiants en dur pour l'instant, le temps de valider le besoin
     * avec le client. A remplacer par une vraie table "admins" (avec mot
     * de passe hashé) une fois confirmé.
     */
    protected const ADMIN_EMAIL = 'admin@boky.com';
    protected const ADMIN_PASSWORD = '123456';

    public function showLogin(): View
    {
        return view('admin.auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if ($credentials['email'] !== self::ADMIN_EMAIL || $credentials['password'] !== self::ADMIN_PASSWORD) {
            return back()
                ->withErrors(['email' => 'Identifiants administrateur incorrects.'])
                ->withInput(['email' => $credentials['email']]);
        }

        $request->session()->regenerate();
        $request->session()->put('is_admin', true);

        return redirect()->route('admin.dashboard');
    }

    public function logout(Request $request): RedirectResponse
    {
        $request->session()->forget('is_admin');
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}