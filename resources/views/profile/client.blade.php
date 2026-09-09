@extends('layouts.app')

@section('meta_title', __('home.profile_client_title') . ' — ' . config('app.name'))

@section('content')
    <section>
        <div class="wrap" style="max-width: 720px;">
            <div class="section-head">
                <div>
                    <h2>{{ __('home.profile_client_title') }}</h2>
                    <p>{{ __('home.profile_client_subtitle') }}</p>
                </div>
            </div>

            <div class="seller-card" style="background:#fffdf7; border:1px solid rgba(85,16,29,.09); color: var(--ink); flex-direction: column; align-items: flex-start; gap: 18px; padding: 30px;">
                <div style="display:flex; align-items:center; gap:16px;">
                    <div class="user-avatar" style="width:56px; height:56px; font-size:1.3rem;">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                    <div>
                        <h3 class="seller-name" style="color: var(--ink);">{{ $user->name }}</h3>
                        <p class="seller-meta" style="color:#7a6a5d;">{{ $user->email }}</p>
                    </div>
                </div>

                @if($profile)
                    <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(200px,1fr)); gap:16px; width:100%;">
                        <div>
                            <p class="book-genre" style="margin-bottom:4px;">{{ __('home.profile_location') }}</p>
                            <p>{{ $profile->localisation ?: '—' }}</p>
                        </div>
                        <div>
                            <p class="book-genre" style="margin-bottom:4px;">{{ __('home.profile_reason') }}</p>
                            <p>{{ $profile->motif_inscription ?: '—' }}</p>
                        </div>
                        <div>
                            <p class="book-genre" style="margin-bottom:4px;">{{ __('home.profile_books_wanted') }}</p>
                            <p>{{ $profile->types_livres_recherches ?: '—' }}</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </section>
@endsection