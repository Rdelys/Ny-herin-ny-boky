@extends('layouts.app')

@section('meta_title', __('home.profile_client_title') . ' — ' . config('app.name'))
@section('meta_robots', 'noindex, nofollow')

@php
    $tabActif = in_array(request('tab'), ['commandes', 'profil'], true)
        ? request('tab')
        : 'commandes';

    // Rouvre l'onglet profil si une erreur de validation vient de là
    // (édition infos, mot de passe, ou suppression de compte).
    if ($errors->hasAny(['name', 'email', 'localisation', 'motif_inscription', 'types_livres_recherches', 'current_password', 'password', 'password_confirm'])) {
        $tabActif = 'profil';
    }
@endphp

@section('content')
    <section>
        <div class="wrap" style="max-width: 860px;">
            <div class="section-head">
                <div>
                    <h2>{{ __('home.profile_client_title') }}</h2>
                    <p>{{ __('home.profile_client_subtitle') }}</p>
                </div>
            </div>

            @if(session('success'))
                <p class="flash-success">{{ session('success') }}</p>
            @endif
            @if(session('error'))
                <p class="flash-error">{{ session('error') }}</p>
            @endif

            {{-- ============ IDENTITÉ (aperçu, toujours visible) ============ --}}
            <div class="profile-identity">
                <div class="profile-identity-head">
                    <div class="user-avatar profile-identity-avatar">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                    <div>
                        <h3 class="seller-name" style="color: var(--ink);">{{ $user->name }}</h3>
                        <p class="seller-meta" style="color:#7a6a5d;">{{ $user->email }}</p>
                    </div>
                </div>

                @if($profile)
                    <div class="profile-info-grid">
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

            {{-- ============ ONGLETS ============ --}}
            <div class="profile-tabs" role="tablist">
                <button type="button" class="profile-tab {{ $tabActif === 'commandes' ? 'active' : '' }}"
                        role="tab" aria-selected="{{ $tabActif === 'commandes' ? 'true' : 'false' }}"
                        aria-controls="tab-commandes" data-profile-tab="commandes">
                    {{ __('home.order_history_title') }}
                    <span class="profile-tab-count">{{ $orders->count() }}</span>
                </button>
                <button type="button" class="profile-tab {{ $tabActif === 'profil' ? 'active' : '' }}"
                        role="tab" aria-selected="{{ $tabActif === 'profil' ? 'true' : 'false' }}"
                        aria-controls="tab-profil" data-profile-tab="profil">
                    {{ __('home.profile_tab_edit') }}
                </button>
            </div>

            {{-- ---------- onglet 1 : mes commandes ---------- --}}
            <div class="profile-panel {{ $tabActif === 'commandes' ? 'active' : '' }}" id="tab-commandes" role="tabpanel">
                @include('partials.orders-table', ['orders' => $orders, 'role' => 'client'])
            </div>

            {{-- ---------- onglet 2 : mon profil ---------- --}}
            <div class="profile-panel {{ $tabActif === 'profil' ? 'active' : '' }}" id="tab-profil" role="tabpanel">

                {{-- ---- infos du compte ---- --}}
                <div class="add-book-card">
                    <h3 class="add-book-title">{{ __('home.profile_edit_title') }}</h3>

                    <form method="POST" action="{{ route('profile.client.update') }}" class="modal-form">
                        @csrf
                        @method('PUT')

                        <div class="modal-form-row">
                            <label>{{ __('home.profile_name_label') }}
                                <input type="text" name="name" value="{{ old('name', $user->name) }}" required>
                            </label>
                            <label>{{ __('home.profile_email_label') }}
                                <input type="email" name="email" value="{{ old('email', $user->email) }}" required>
                            </label>
                        </div>
                        @error('name')<p class="modal-field-error">{{ $message }}</p>@enderror
                        @error('email')<p class="modal-field-error">{{ $message }}</p>@enderror

                        <label>{{ __('home.profile_location') }}
                            <input type="text" name="localisation" value="{{ old('localisation', $profile->localisation ?? '') }}" placeholder="Antananarivo, Fianarantsoa...">
                        </label>
                        @error('localisation')<p class="modal-field-error">{{ $message }}</p>@enderror

                        <div class="modal-form-row">
                            <label>{{ __('home.profile_reason') }}
                                <select name="motif_inscription">
                                    <option value="">{{ __('home.auth_choose_placeholder') }}</option>
                                    @foreach(['auth_reason_1', 'auth_reason_2', 'auth_reason_3', 'auth_reason_4', 'auth_reason_5'] as $key)
                                        <option @selected(old('motif_inscription', $profile->motif_inscription ?? '') === __('home.' . $key))>{{ __('home.' . $key) }}</option>
                                    @endforeach
                                </select>
                            </label>
                            <label>{{ __('home.profile_books_wanted') }}
                                <select name="types_livres_recherches">
                                    <option value="">{{ __('home.auth_choose_placeholder') }}</option>
                                    @foreach(['auth_genre_1', 'auth_genre_2', 'auth_genre_3', 'auth_genre_4', 'auth_genre_5', 'auth_genre_6'] as $key)
                                        <option @selected(old('types_livres_recherches', $profile->types_livres_recherches ?? '') === __('home.' . $key))>{{ __('home.' . $key) }}</option>
                                    @endforeach
                                </select>
                            </label>
                        </div>

                        <button type="submit" class="btn-modal-primary">{{ __('home.profile_save') }}</button>
                    </form>
                </div>

                {{-- ---- mot de passe ---- --}}
                <div class="add-book-card">
                    <h3 class="add-book-title">{{ __('home.profile_change_password') }}</h3>

                    <form method="POST" action="{{ route('profile.password.update') }}" class="modal-form">
                        @csrf
                        @method('PUT')

                        <label>{{ __('home.profile_current_password') }}
                            <input type="password" name="current_password" required>
                        </label>
                        @error('current_password')<p class="modal-field-error">{{ $message }}</p>@enderror

                        <div class="modal-form-row">
                            <label>{{ __('home.profile_new_password') }}
                                <input type="password" name="password" minlength="8" required>
                            </label>
                            <label>{{ __('home.profile_new_password_confirm') }}
                                <input type="password" name="password_confirmation" minlength="8" required>
                            </label>
                        </div>
                        @error('password')<p class="modal-field-error">{{ $message }}</p>@enderror

                        <button type="submit" class="btn-modal-primary">{{ __('home.profile_change_password') }}</button>
                    </form>
                </div>

                {{-- ---- suppression du compte ---- --}}
                <div class="add-book-card" style="border-color: rgba(179,38,30,.25);">
                    <h3 class="add-book-title" style="color:#b3261e;">{{ __('home.profile_delete_account_title') }}</h3>
                    <p style="color:#7a6a5d; font-size:.88rem; margin:-10px 0 18px;">{{ __('home.profile_delete_account_text') }}</p>

                    <form method="POST" action="{{ route('profile.destroy') }}" class="modal-form"
                          onsubmit="return confirm('{{ __('home.profile_delete_account_confirm') }}');">
                        @csrf
                        @method('DELETE')

                        <label>{{ __('home.profile_delete_account_password_label') }}
                            <input type="password" name="password_confirm" required>
                        </label>
                        @error('password_confirm')<p class="modal-field-error">{{ $message }}</p>@enderror

                        <button type="submit" class="btn-danger-outline">{{ __('home.profile_delete_account_button') }}</button>
                    </form>
                </div>
            </div>

        </div>
    </section>

    <script>
        (function(){
            var tabs = document.querySelectorAll('[data-profile-tab]');

            tabs.forEach(function(tab){
                tab.addEventListener('click', function(){
                    var cible = tab.getAttribute('data-profile-tab');

                    tabs.forEach(function(autre){
                        var actif = autre === tab;
                        autre.classList.toggle('active', actif);
                        autre.setAttribute('aria-selected', actif ? 'true' : 'false');
                    });

                    document.querySelectorAll('.profile-panel').forEach(function(panel){
                        panel.classList.toggle('active', panel.id === 'tab-' + cible);
                    });

                    var url = new URL(window.location.href);
                    url.searchParams.set('tab', cible);
                    window.history.replaceState({}, '', url);
                });
            });
        })();
    </script>
@endsection