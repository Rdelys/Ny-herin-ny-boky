@extends('layouts.app')

@section('meta_title', __('home.about_meta_title'))
@section('meta_description', __('home.about_intro'))

@section('content')

    <section class="page-banner">
        <div class="wrap">
            <p class="page-banner-eyebrow">{{ __('home.about_eyebrow') }}</p>
            <h1>{{ __('home.about_title') }}</h1>
            <p>{{ __('home.about_intro') }}</p>
            <div class="page-banner-actions">
                <a href="{{ url('/#livres') }}" class="btn btn-primary">{{ __('home.about_cta_browse') }}</a>
                <button type="button" class="btn btn-ghost" data-auth-open="registerSeller">{{ __('home.about_cta_sell') }}</button>
            </div>
        </div>
    </section>

    <section>
        <div class="wrap">
            <div class="section-head">
                <div>
                    <h2>{{ __('home.about_stats_heading') }}</h2>
                    <p>{{ __('home.about_stats_sub') }}</p>
                </div>
            </div>
            <div class="stat-grid">
                <div class="stat-item">
                    <div class="stat-num">{{ __('home.about_stat1_num') }}</div>
                    <div class="stat-label">{{ __('home.about_stat1_label') }}</div>
                </div>
                <div class="stat-item">
                    <div class="stat-num">{{ __('home.about_stat2_num') }}</div>
                    <div class="stat-label">{{ __('home.about_stat2_label') }}</div>
                </div>
            </div>
        </div>
    </section>

    <section>
        <div class="wrap">
            <div class="section-head">
                <div><h2>{{ __('home.about_why_heading') }}</h2></div>
            </div>
            <div class="feature-grid">
                <div class="feature-card">
                    <h4>{{ __('home.about_why1_title') }}</h4>
                    <p>{{ __('home.about_why1_text') }}</p>
                </div>
                <div class="feature-card">
                    <h4>{{ __('home.about_why2_title') }}</h4>
                    <p>{{ __('home.about_why2_text') }}</p>
                </div>
                <div class="feature-card">
                    <h4>{{ __('home.about_why3_title') }}</h4>
                    <p>{{ __('home.about_why3_text') }}</p>
                </div>
            </div>
        </div>
    </section>

    <section class="sellers">
        <div class="wrap">
            <div class="section-head">
                <div>
                    <h2>{{ __('home.about_how_heading') }}</h2>
                    <p style="color: rgba(246,239,221,.75); max-width: 68ch;">{{ __('home.about_how_intro') }}</p>
                </div>
            </div>
            <div class="steps-grid">
                <div class="step-card">
                    <div class="step-number">01</div>
                    <h4>{{ __('home.about_step1_title') }}</h4>
                    <p>{{ __('home.about_step1_text') }}</p>
                </div>
                <div class="step-card">
                    <div class="step-number">02</div>
                    <h4>{{ __('home.about_step2_title') }}</h4>
                    <p>{{ __('home.about_step2_text') }}</p>
                </div>
                <div class="step-card">
                    <div class="step-number">03</div>
                    <h4>{{ __('home.about_step3_title') }}</h4>
                    <p>{{ __('home.about_step3_text') }}</p>
                </div>
            </div>
        </div>
    </section>

    <section>
        <div class="wrap">
            <div class="section-head">
                <div>
                    <h2>{{ __('home.about_delivery_heading') }}</h2>
                    <p>{{ __('home.about_delivery_sub') }}</p>
                </div>
            </div>
            <p style="color:#6b5a4d; max-width: 68ch; margin: -20px 0 22px;">{{ __('home.about_delivery_text') }}</p>
            <div class="chip-list">
                @foreach(range(1, 8) as $i)
                    <span class="chip">{{ __('home.about_city_' . $i) }}</span>
                @endforeach
            </div>
        </div>
    </section>

    <section>
        <div class="wrap">
            <div class="section-head">
                <div>
                    <h2>{{ __('home.about_contact_heading') }}</h2>
                    <p>{{ __('home.about_contact_sub') }}</p>
                </div>
            </div>
            <p style="color:#6b5a4d; max-width: 68ch; margin: -20px 0 22px;">{{ __('home.about_contact_text') }}</p>
            <div class="contact-grid">
                <div class="contact-item">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.8 19.8 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.8 19.8 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.13.96.36 1.9.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.9.34 1.85.57 2.81.7A2 2 0 0 1 22 16.92z" stroke="currentColor" stroke-width="1.6"/></svg>
                    <a href="tel:+261384126644">{{ __('home.about_contact_phone') }}</a>
                </div>
                <div class="contact-item">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z" stroke="currentColor" stroke-width="1.6"/></svg>
                    <a href="https://wa.me/261342174639" target="_blank" rel="noopener">{{ __('home.about_contact_whatsapp') }}</a>
                </div>
                <div class="contact-item">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M4 4h16v16H4z" stroke="currentColor" stroke-width="1.6"/><path d="m4 6 8 7 8-7" stroke="currentColor" stroke-width="1.6"/></svg>
                    <a href="mailto:{{ __('home.about_contact_email') }}">{{ __('home.about_contact_email') }}</a>
                </div>
                <div class="contact-item">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M12 22s7-7.58 7-13A7 7 0 1 0 5 9c0 5.42 7 13 7 13z" stroke="currentColor" stroke-width="1.6"/><circle cx="12" cy="9" r="2.4" stroke="currentColor" stroke-width="1.6"/></svg>
                    <span>{{ __('home.about_contact_address') }}</span>
                </div>
            </div>
        </div>
    </section>

    <section>
        <div class="wrap">
            <div class="cta-band">
                <div>
                    <h3>{{ __('home.about_cta2_title') }}</h3>
                    <p>{{ __('home.about_cta2_text') }}</p>
                </div>
                <button type="button" class="btn btn-primary" data-auth-open="registerSeller">{{ __('home.about_cta2_button') }}</button>
            </div>
        </div>
    </section>

@endsection