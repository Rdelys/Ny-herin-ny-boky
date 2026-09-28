@extends('layouts.app')

@section('meta_title', __('home.terms_meta_title'))
@section('meta_description', __('home.terms_intro'))

@section('content')

    <section class="page-banner">
        <div class="wrap">
            <p class="page-banner-eyebrow">{{ __('home.terms_eyebrow') }}</p>
            <h1>{{ __('home.terms_title') }}</h1>
            <p>{{ __('home.terms_intro') }}</p>
            <span class="page-banner-meta">{{ __('home.terms_updated') }}</span>
        </div>
    </section>

    <section>
        <div class="wrap" style="max-width: 860px;">

            <div class="numbered-card">
                <div class="step-number">01</div>
                <div class="content-card">
                    <h3>{{ __('home.terms_s1_title') }}</h3>
                    <p>{{ __('home.terms_s1_p1') }}</p>
                    <p>{{ __('home.terms_s1_p2') }}</p>
                </div>
            </div>

            <div class="numbered-card" style="margin-top: 22px;">
                <div class="step-number">02</div>
                <div class="content-card">
                    <h3>{{ __('home.terms_s2_title') }}</h3>
                    <p>{{ __('home.terms_s2_text') }}</p>
                    <div class="chip-list">
                        @foreach(range(1, 4) as $i)
                            <span class="chip">{{ __('home.terms_pay_' . $i) }}</span>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="numbered-card" style="margin-top: 22px;">
                <div class="step-number">03</div>
                <div class="content-card">
                    <h3>{{ __('home.terms_s3_title') }}</h3>
                    <p>{{ __('home.terms_s3_text') }}</p>
                    <div class="table-scroll">
                        <table class="seller-table">
                            <thead>
                                <tr><th>{{ __('home.terms_commission_col_price') }}</th><th>{{ __('home.terms_commission_col_rate') }}</th></tr>
                            </thead>
                            <tbody>
                                <tr><td>{{ __('home.terms_commission_row1') }}</td><td><strong>8%</strong></td></tr>
                                <tr><td>{{ __('home.terms_commission_row2') }}</td><td><strong>7%</strong></td></tr>
                                <tr><td>{{ __('home.terms_commission_row3') }}</td><td><strong>5%</strong></td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="numbered-card" style="margin-top: 22px;">
                <div class="step-number">04</div>
                <div class="content-card">
                    <h3>{{ __('home.terms_s4_title') }}</h3>
                    <ul class="content-list">
                        <li>{{ __('home.terms_s4_1') }}</li>
                        <li>{{ __('home.terms_s4_2') }}</li>
                        <li>{{ __('home.terms_s4_3') }}</li>
                    </ul>
                </div>
            </div>

            <div class="numbered-card" style="margin-top: 22px;">
                <div class="step-number">05</div>
                <div class="content-card">
                    <h3>{{ __('home.terms_s5_title') }}</h3>
                    <p>{{ __('home.terms_s5_p1') }}</p>
                    <p>{{ __('home.terms_s5_p2') }}</p>
                    <p>{{ __('home.terms_s5_p3') }}</p>
                </div>
            </div>

        </div>
    </section>

    <section>
        <div class="wrap">
            <div class="cta-band">
                <div>
                    <h3>{{ __('home.terms_cta_title') }}</h3>
                    <p>{{ __('home.terms_cta_text') }}</p>
                </div>
                <a href="mailto:{{ __('home.terms_contact_email') }}" class="btn btn-primary">{{ __('home.terms_cta_button') }}</a>
            </div>
        </div>
    </section>

@endsection