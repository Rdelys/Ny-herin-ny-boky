{{-- ============ HEADER / MENU ============ --}}
<header class="site-header">
    <div class="wrap">
        <a href="{{ url('/') }}" class="brand">
            <img src="{{ asset('logo.png') }}" alt="Ny Herin'ny Boky">
            <span>Ny Herin'ny Boky</span>
        </a>

        {{-- Sur desktop : liens + langue affichés en ligne.
             Sur mobile : toute la nav devient un panneau dropdown
             (voir @media max-width: 760px dans le layout). --}}
        <nav class="main-nav" id="mainNav">
            <ul>
                <li><a href="{{ url('/') }}">{{ __('home.nav_home') }}</a></li>
                <li><a href="{{ url('/vendeur') }}">{{ __('home.nav_seller') }}</a></li>
            </ul>

            <div class="lang-switch" role="group" aria-label="Choix de langue / Fifidianana fiteny / Language choice">
                <a href="{{ route('home') }}" hreflang="fr" class="{{ app()->getLocale() === 'fr' ? 'active' : '' }}">FR</a>
                <a href="{{ route('home.locale', 'mg') }}" hreflang="mg" class="{{ app()->getLocale() === 'mg' ? 'active' : '' }}">MG</a>
                <a href="{{ route('home.locale', 'en') }}" hreflang="en" class="{{ app()->getLocale() === 'en' ? 'active' : '' }}">EN</a>
            </div>
        </nav>

        <button class="menu-toggle" id="menuToggle" aria-expanded="false" aria-controls="mainNav" aria-label="Menu">
            <span></span>
        </button>
    </div>
</header>