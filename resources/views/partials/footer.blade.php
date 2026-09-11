{{-- ============ FOOTER ============ --}}
<footer>
    <div class="wrap">
        <div class="foot-top">
            <div class="foot-brand">
                <img src="{{ asset('logo.png') }}" alt="Ny Herin'ny Boky">
            </div>
            <ul class="foot-links">
                <li><a href="{{ route('pages.about') }}">{{ __('home.footer_about') }}</a></li>
                <li><a href="{{ route('pages.privacy') }}">{{ __('home.footer_privacy') }}</a></li>
                <li><a href="{{ route('pages.terms') }}">{{ __('home.footer_terms') }}</a></li>
            </ul>
        </div>
        <p class="foot-bottom">{{ __('home.footer_copyright') }}</p>
    </div>
</footer>