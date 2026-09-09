@extends('layouts.app')

@section('meta_title', __('home.privacy_meta_title'))

@section('content')

    <section class="page-banner">
        <div class="wrap">
            <h1>{{ __('home.privacy_title') }}</h1>
            <span class="page-banner-meta">{{ __('home.privacy_updated') }}</span>
        </div>
    </section>

    <section>
        <div class="wrap" style="max-width: 820px;">

            <div class="content-card">
                <h3>{{ __('home.privacy_data_heading') }}</h3>
                <p>{{ __('home.privacy_data_intro') }}</p>
                <ul class="content-list">
                    @foreach(range(1, 5) as $i)
                        <li>{{ __('home.privacy_data_' . $i) }}</li>
                    @endforeach
                </ul>
            </div>

            <div class="content-card">
                <h3>{{ __('home.privacy_why_heading') }}</h3>
                <ul class="content-list">
                    @foreach(range(1, 4) as $i)
                        <li>{{ __('home.privacy_why_' . $i) }}</li>
                    @endforeach
                </ul>
            </div>

            <div class="content-card">
                <h3>{{ __('home.privacy_nosell_heading') }}</h3>
                <p>{{ __('home.privacy_nosell_text') }}</p>
            </div>

            <div class="content-card">
                <h3>{{ __('home.privacy_cookies_heading') }}</h3>
                <p>{{ __('home.privacy_cookies_text') }}</p>
            </div>

            <div class="content-card">
                <h3>{{ __('home.privacy_rights_heading') }}</h3>
                <ul class="content-list" style="margin-bottom: 14px;">
                    @foreach(range(1, 3) as $i)
                        <li>{{ __('home.privacy_rights_' . $i) }}</li>
                    @endforeach
                </ul>
                <p>{!! str_replace(
                    ':email',
                    '<a href="mailto:' . __('home.privacy_contact_email') . '" style="color: var(--maroon-800); font-weight: 600;">' . __('home.privacy_contact_email') . '</a>',
                    __('home.privacy_rights_contact')
                ) !!}</p>
            </div>

        </div>
    </section>

@endsection