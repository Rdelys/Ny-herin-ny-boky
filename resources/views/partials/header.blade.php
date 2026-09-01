{{-- ============ HEADER / MENU ============ --}}
<header class="site-header">
    <div class="wrap">
        <a href="{{ url('/') }}" class="brand">
            <img src="{{ asset('logo.png') }}" alt="Ny Herin'ny Boky">
        </a>

        {{-- Sur desktop : liens, dropdown de langue et boutons auth en ligne.
             Sur mobile : tout se replie dans un même panneau dropdown
             (voir @media max-width: 760px dans le layout). --}}
        <nav class="main-nav" id="mainNav">
            <ul>
                <li><a href="{{ url('/') }}">{{ __('home.nav_home') }}</a></li>
                <li><a href="{{ url('/vendeur') }}">{{ __('home.nav_seller') }}</a></li>
            </ul>

            {{-- Dropdown de langue stylé, avec drapeaux SVG (flag-icons) --}}
            @php
                // Madagascar = mg, mais l'anglais utilise le drapeau britannique (gb)
                $flagCodes = ['fr' => 'fr', 'mg' => 'mg', 'en' => 'gb'];
            @endphp
            <div class="lang-dropdown" id="langDropdown">
                <button type="button" class="lang-toggle" id="langToggle" aria-haspopup="listbox" aria-expanded="false">
                    <span class="fi fi-{{ $flagCodes[app()->getLocale()] ?? 'fr' }} lang-flag"></span>
                    <span>{{ strtoupper(app()->getLocale()) }}</span>
                    <svg class="lang-chevron" width="10" height="6" viewBox="0 0 10 6" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <path d="M1 1L5 5L9 1" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
                <ul class="lang-menu" id="langMenu" role="listbox" aria-label="Choix de langue / Fifidianana fiteny / Language choice">
                    <li>
                        <a href="{{ route('home') }}" hreflang="fr" role="option" class="{{ app()->getLocale() === 'fr' ? 'active' : '' }}">
                            <span class="lang-option"><span class="fi fi-fr lang-flag"></span> Français</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('home.locale', 'mg') }}" hreflang="mg" role="option" class="{{ app()->getLocale() === 'mg' ? 'active' : '' }}">
                            <span class="lang-option"><span class="fi fi-mg lang-flag"></span> Malagasy</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('home.locale', 'en') }}" hreflang="en" role="option" class="{{ app()->getLocale() === 'en' ? 'active' : '' }}">
                            <span class="lang-option"><span class="fi fi-gb lang-flag"></span> English</span>
                        </a>
                    </li>
                </ul>
            </div>

            {{-- Connexion / Inscription : liens placeholder pour l'instant (pas de modal,
                 pas encore de routes login/register). Remplacez href="#" par route('login')
                 et route('register') dès que ces routes existeront. --}}
            <div class="auth-actions">
                <a href="#" class="btn-auth btn-login">{{ __('home.nav_login') }}</a>
                <a href="#" class="btn-auth btn-register">{{ __('home.nav_register') }}</a>
            </div>
        </nav>

        <button class="menu-toggle" id="menuToggle" aria-expanded="false" aria-controls="mainNav" aria-label="Menu">
            <span></span>
        </button>
    </div>
</header>