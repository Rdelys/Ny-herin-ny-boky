{{-- ============ HEADER / MENU ============ --}}
<header class="site-header">
    <div class="wrap">
        <a href="{{ url('/') }}" class="brand">
            <img src="{{ asset('logo.png') }}" alt="Ny Herin'ny Boky">
            <span>Ny Herin'ny Boky</span>
        </a>

        {{-- Sur desktop : liens, dropdown de langue et boutons auth en ligne.
             Sur mobile : tout se replie dans un même panneau dropdown
             (voir @media max-width: 760px dans le layout). --}}
        <nav class="main-nav" id="mainNav">
            <ul>
                <li><a href="{{ url('/') }}">{{ __('home.nav_home') }}</a></li>
                <li><a href="{{ url('/vendeur') }}">{{ __('home.nav_seller') }}</a></li>
            </ul>

            {{-- Dropdown de langue stylé, avec drapeaux SVG (flag-icons).
                 Malagasy est la langue par défaut du site (route racine "/"),
                 français et anglais utilisent le préfixe explicite /fr, /en. --}}
            @php
                // Madagascar = mg, mais l'anglais utilise le drapeau britannique (gb)
                $flagCodes = ['fr' => 'fr', 'mg' => 'mg', 'en' => 'gb'];
            @endphp
            <div class="lang-dropdown" id="langDropdown">
                <button type="button" class="lang-toggle" id="langToggle" aria-haspopup="listbox" aria-expanded="false">
                    <span class="fi fi-{{ $flagCodes[app()->getLocale()] ?? 'mg' }} lang-flag"></span>
                    <span>{{ strtoupper(app()->getLocale()) }}</span>
                    <svg class="lang-chevron" width="10" height="6" viewBox="0 0 10 6" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <path d="M1 1L5 5L9 1" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
                <ul class="lang-menu" id="langMenu" role="listbox" aria-label="Choix de langue / Fifidianana fiteny / Language choice">
                    <li>
                        <a href="{{ route('home.locale', 'mg') }}" hreflang="mg" role="option" class="{{ app()->getLocale() === 'mg' ? 'active' : '' }}">
                            <span class="lang-option"><span class="fi fi-mg lang-flag"></span> Malagasy</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('home.locale', 'fr') }}" hreflang="fr" role="option" class="{{ app()->getLocale() === 'fr' ? 'active' : '' }}">
                            <span class="lang-option"><span class="fi fi-fr lang-flag"></span> Français</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('home.locale', 'en') }}" hreflang="en" role="option" class="{{ app()->getLocale() === 'en' ? 'active' : '' }}">
                            <span class="lang-option"><span class="fi fi-gb lang-flag"></span> English</span>
                        </a>
                    </li>
                </ul>
            </div>

            {{-- Connecté : nom + menu (profil, déconnexion). Sinon : boutons qui
                 ouvrent les modals de connexion / inscription (pas de rechargement). --}}
            @auth
                <div class="user-dropdown" id="userDropdown">
                    <button type="button" class="user-toggle" id="userToggle" aria-haspopup="true" aria-expanded="false">
                        <span class="user-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                        <span class="user-name">{{ auth()->user()->name }}</span>
                        <svg class="lang-chevron" width="10" height="6" viewBox="0 0 10 6" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <path d="M1 1L5 5L9 1" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>
                    <ul class="user-menu" id="userMenu" role="menu">
                        <li>
                            <a href="{{ route('profile') }}" role="menuitem" class="user-menu-link">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                    <circle cx="12" cy="8" r="4" stroke="currentColor" stroke-width="1.8"/>
                                    <path d="M4 20c0-3.9 3.6-7 8-7s8 3.1 8 7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                </svg>
                                {{ auth()->user()->isSeller() ? __('home.nav_my_seller_space') : __('home.nav_my_profile') }}
                            </a>
                        </li>
                        <li class="user-menu-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="user-menu-logout">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                        <path d="M15 17l5-5-5-5M20 12H9M13 21H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                    {{ __('home.nav_logout') }}
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            @else
                <div class="auth-actions">
                    <button type="button" class="btn-auth btn-login" data-auth-open="login">{{ __('home.nav_login') }}</button>
                    <button type="button" class="btn-auth btn-register" data-auth-open="registerClient">{{ __('home.nav_register') }}</button>
                </div>
            @endauth
        </nav>

        <button class="menu-toggle" id="menuToggle" aria-expanded="false" aria-controls="mainNav" aria-label="Menu">
            <span></span>
        </button>
    </div>
</header>

@include('partials.auth-modals')