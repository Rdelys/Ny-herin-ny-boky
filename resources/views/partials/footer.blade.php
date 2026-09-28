{{-- ============ FOOTER ============ --}}
@php
    // Placez l'APK dans public/downloads/ (voir instructions). Le bouton
    // n'apparaît que si le fichier existe.
    $apkFile = 'downloads/ny-herin-ny-boky.apk';
    $apkExists = file_exists(public_path($apkFile));
    $apkSize = $apkExists ? round(filesize(public_path($apkFile)) / 1048576, 1) : null;
@endphp
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

            {{-- ---- téléchargement direct de l'app Android (APK) ---- --}}
            @if($apkExists)
                <div class="foot-app">
                    <p class="foot-app-title">{{ __('home.footer_app_title') }}</p>
                    <a href="{{ asset($apkFile) }}?v={{ filemtime(public_path($apkFile)) }}"
                       class="apk-badge" download="Ny-Herin-ny-Boky.apk"
                       aria-label="{{ __('home.footer_app_download') }} — Android APK">
                        <span class="apk-badge-icon" aria-hidden="true">
                            <svg width="30" height="30" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M6 9.5h12v8a1.5 1.5 0 0 1-1.5 1.5h-9A1.5 1.5 0 0 1 6 17.5v-8Z" fill="currentColor"/>
                                <path d="M6.2 8.5a5.8 5.8 0 0 1 11.6 0H6.2Z" fill="currentColor"/>
                                <path d="M8.3 4.2 7.2 2.6M15.7 4.2l1.1-1.6" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/>
                                <circle cx="9.6" cy="6.6" r=".9" fill="#3d0b15"/>
                                <circle cx="14.4" cy="6.6" r=".9" fill="#3d0b15"/>
                                <rect x="2.6" y="9.8" width="2" height="6.4" rx="1" fill="currentColor"/>
                                <rect x="19.4" y="9.8" width="2" height="6.4" rx="1" fill="currentColor"/>
                                <rect x="8.6" y="18.6" width="2" height="3" rx="1" fill="currentColor"/>
                                <rect x="13.4" y="18.6" width="2" height="3" rx="1" fill="currentColor"/>
                            </svg>
                        </span>
                        <span class="apk-badge-text">
                            <small>{{ __('home.footer_app_direct') }}</small>
                            <strong>{{ __('home.footer_app_download') }}</strong>
                        </span>
                        <span class="apk-badge-arrow" aria-hidden="true">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12 4v11m0 0-4.5-4.5M12 15l4.5-4.5M5 19.5h14" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </span>
                    </a>
                    <p class="foot-app-meta">Android · APK · {{ $apkSize }} Mo</p>
                    <p class="foot-app-hint">{{ __('home.footer_app_hint') }}</p>
                </div>
            @endif
        </div>
        <p class="foot-bottom">{{ __('home.footer_copyright') }}</p>
    </div>
</footer>