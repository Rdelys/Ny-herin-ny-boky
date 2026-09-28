{{-- ============ FOOTER ============ --}}
<footer>
    <div class="wrap">
        <div class="foot-top">
            <div class="foot-brand">
                <img src="{{ asset('logo.png') }}" alt="Ny Herin'ny Boky">
            </div>

            {{-- ---- inscription newsletter ---- --}}
            <div class="foot-newsletter">
                <p class="foot-newsletter-title">{{ __('home.footer_newsletter_title') }}</p>
                <p class="foot-newsletter-subtitle">{{ __('home.footer_newsletter_subtitle') }}</p>

                @if(session('newsletter_success'))
                    <p class="foot-newsletter-flash foot-newsletter-flash-ok">{{ session('newsletter_success') }}</p>
                @elseif(session('newsletter_info'))
                    <p class="foot-newsletter-flash">{{ session('newsletter_info') }}</p>
                @endif

                <form method="POST" action="{{ route('newsletter.subscribe') }}" class="foot-newsletter-form">
                    @csrf
                    <input type="email" name="newsletter_email" required
                        placeholder="{{ __('home.footer_newsletter_placeholder') }}"
                        value="{{ old('newsletter_email') }}">
                    <button type="submit">{{ __('home.footer_newsletter_button') }}</button>
                </form>
                @error('newsletter_email')
                    <p class="foot-newsletter-flash foot-newsletter-flash-error">{{ $message }}</p>
                @enderror
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