{{-- ============ MODALS CONNEXION / INSCRIPTION ============ --}}
{{-- Une seule overlay, 3 "vues" internes (login / inscription client / inscription
     vendeur) basculées en JS sans rechargement. En cas d'erreur de validation,
     le champ caché _auth_form permet de rouvrir automatiquement la bonne vue.
     Tous les textes viennent de lang/{locale}/home.php (clés auth_*). --}}

<div class="modal-overlay" id="authModalOverlay">
    <div class="modal-panel" id="authModalPanel" role="dialog" aria-modal="true" aria-labelledby="authModalTitle">
        <button type="button" class="modal-close" id="authModalClose" aria-label="Fermer">&times;</button>

        {{-- ---------- CONNEXION ---------- --}}
        <div class="modal-view" id="viewLogin">
            <h2 class="modal-title" id="authModalTitle">{{ __('home.auth_login_title') }}</h2>
            <p class="modal-subtitle">{{ __('home.auth_login_subtitle') }}</p>

            <form method="POST" action="{{ route('login') }}" class="modal-form">
                @csrf
                <input type="hidden" name="_auth_form" value="login">

                <label>{{ __('home.auth_email') }}
                    <input type="email" name="email" placeholder="{{ __('home.auth_email_placeholder') }}" value="{{ old('_auth_form') === 'login' ? old('email') : '' }}" required>
                </label>
                @error('email')
                    <p class="modal-field-error">{{ $message }}</p>
                @enderror

                <label>{{ __('home.auth_password') }}
                    <input type="password" name="password" placeholder="{{ __('home.auth_password_placeholder') }}" required>
                </label>

                <button type="submit" class="btn-modal-primary">{{ __('home.auth_login_submit') }}</button>
            </form>

            <p class="modal-switch">
                {{ __('home.auth_no_account') }}
                <a href="#" data-auth-switch="registerClient">{{ __('home.auth_signup_as_client') }}</a> ·
                <a href="#" data-auth-switch="registerSeller">{{ __('home.auth_signup_as_seller') }}</a>
            </p>
        </div>

        {{-- ---------- INSCRIPTION CLIENT ---------- --}}
        <div class="modal-view" id="viewRegisterClient" hidden>
            <h2 class="modal-title">{{ __('home.auth_create_account_title') }}</h2>
            <p class="modal-subtitle">{{ __('home.auth_client_subtitle') }}</p>

            <div class="modal-tabs" role="tablist" aria-label="Type de compte">
                <button type="button" class="modal-tab active" data-auth-switch="registerClient" role="tab" aria-selected="true">
                    {{ __('home.auth_tab_client') }}
                </button>
                <button type="button" class="modal-tab" data-auth-switch="registerSeller" role="tab" aria-selected="false">
                    {{ __('home.auth_tab_seller') }}
                </button>
            </div>

            <form method="POST" action="{{ route('register.client') }}" class="modal-form">
                @csrf
                <input type="hidden" name="_auth_form" value="registerClient">

                <div class="modal-form-row">
                    <label>{{ __('home.auth_firstname') }}
                        <input type="text" name="prenom" value="{{ old('_auth_form') === 'registerClient' ? old('prenom') : '' }}" required>
                    </label>
                    <label>{{ __('home.auth_lastname') }}
                        <input type="text" name="nom" value="{{ old('_auth_form') === 'registerClient' ? old('nom') : '' }}" required>
                    </label>
                </div>

                <div class="modal-form-row">
                    <label>{{ __('home.auth_email') }}
                        <input type="email" name="email" value="{{ old('_auth_form') === 'registerClient' ? old('email') : '' }}" required>
                    </label>
                    <label>{{ __('home.auth_password') }}
                        <input type="password" name="password" minlength="8" required>
                    </label>
                </div>
                @error('email')
                    <p class="modal-field-error">{{ $message }}</p>
                @enderror

                <label>{{ __('home.auth_location') }}
                    <input type="text" name="localisation" placeholder="{{ __('home.auth_location_placeholder') }}">
                </label>

                <fieldset class="modal-fieldset">
                    <legend>{{ __('home.auth_reason_legend') }}</legend>

                    <div class="modal-form-row">
                        <label>{{ __('home.auth_reason_legend') }}
                            <select name="motif_inscription">
                                <option value="">{{ __('home.auth_choose_placeholder') }}</option>
                                <option>{{ __('home.auth_reason_1') }}</option>
                                <option>{{ __('home.auth_reason_2') }}</option>
                                <option>{{ __('home.auth_reason_3') }}</option>
                                <option>{{ __('home.auth_reason_4') }}</option>
                                <option>{{ __('home.auth_reason_5') }}</option>
                            </select>
                        </label>

                        <label>{{ __('home.auth_books_wanted_label') }}
                            <select name="types_livres_recherches">
                                <option value="">{{ __('home.auth_choose_placeholder') }}</option>
                                <option>{{ __('home.auth_genre_1') }}</option>
                                <option>{{ __('home.auth_genre_2') }}</option>
                                <option>{{ __('home.auth_genre_3') }}</option>
                                <option>{{ __('home.auth_genre_4') }}</option>
                                <option>{{ __('home.auth_genre_5') }}</option>
                                <option>{{ __('home.auth_genre_6') }}</option>
                            </select>
                        </label>
                    </div>
                </fieldset>

                <button type="submit" class="btn-modal-primary">{{ __('home.auth_submit_register') }}</button>
            </form>

            <p class="modal-switch">{{ __('home.auth_already_account') }} <a href="#" data-auth-switch="login">{{ __('home.auth_login_link') }}</a></p>
        </div>

        {{-- ---------- INSCRIPTION VENDEUR ---------- --}}
        <div class="modal-view" id="viewRegisterSeller" hidden>
            <h2 class="modal-title">{{ __('home.auth_create_account_title') }}</h2>
            <p class="modal-subtitle">{{ __('home.auth_seller_subtitle') }}</p>

            <div class="modal-tabs" role="tablist" aria-label="Type de compte">
                <button type="button" class="modal-tab" data-auth-switch="registerClient" role="tab" aria-selected="false">
                    {{ __('home.auth_tab_client') }}
                </button>
                <button type="button" class="modal-tab active" data-auth-switch="registerSeller" role="tab" aria-selected="true">
                    {{ __('home.auth_tab_seller') }}
                </button>
            </div>

            <form method="POST" action="{{ route('register.seller') }}" class="modal-form">
                @csrf
                <input type="hidden" name="_auth_form" value="registerSeller">

                <div class="modal-form-row">
                    <label>{{ __('home.auth_company_name') }}
                        <input type="text" name="nom_entreprise" value="{{ old('_auth_form') === 'registerSeller' ? old('nom_entreprise') : '' }}" required>
                    </label>
                    <label>{{ __('home.auth_email') }}
                        <input type="email" name="email" value="{{ old('_auth_form') === 'registerSeller' ? old('email') : '' }}" required>
                    </label>
                </div>
                @error('email')
                    <p class="modal-field-error">{{ $message }}</p>
                @enderror

                <div class="modal-form-row">
                    <label>{{ __('home.auth_password') }}
                        <input type="password" name="password" minlength="8" required>
                    </label>
                    <label>{{ __('home.auth_location') }}
                        <input type="text" name="localisation" placeholder="{{ __('home.auth_location_placeholder') }}">
                    </label>
                </div>

                <div class="modal-form-row">
                    <label>{{ __('home.auth_postal_code') }}
                        <input type="text" name="code_postal">
                    </label>
                    <label>{{ __('home.auth_payment_number') }}
                        <input type="text" name="numero_paiement" placeholder="{{ __('home.auth_payment_number_placeholder') }}">
                    </label>
                </div>

                <fieldset class="modal-fieldset">
                    <legend>{{ __('home.auth_payment_mode_legend') }}</legend>

                    <div class="modal-radio-group">
                        <label class="modal-radio-card">
                            <input type="radio" name="mode_paiement" value="commission" checked>
                            <span>
                                <strong>{{ __('home.auth_payment_commission_title', ['rate' => rtrim(rtrim(number_format(\App\Models\Setting::commissionRate(), 2, ',', ' '), '0'), ',')]) }}</strong>
                                <small>{{ __('home.auth_payment_commission_desc') }}</small>
                            </span>
                        </label>

                        <label class="modal-radio-card is-disabled">
                            <input type="radio" name="mode_paiement" value="abonnement" disabled>
                            <span>
                                <strong>{{ __('home.auth_payment_subscription_title') }} <em class="modal-badge-soon">{{ __('home.auth_payment_soon') }}</em></strong>
                                <small>{{ __('home.auth_payment_subscription_desc') }}</small>
                            </span>
                        </label>
                    </div>
                </fieldset>

                <button type="submit" class="btn-modal-primary">{{ __('home.auth_continue_submit') }}</button>
            </form>

            <p class="modal-switch">{{ __('home.auth_already_account') }} <a href="#" data-auth-switch="login">{{ __('home.auth_login_link') }}</a></p>
        </div>
    </div>
</div>

@if ($errors->any())
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (window.openAuthModal) {
                window.openAuthModal(@json(old('_auth_form', 'login')));
            }
        });
    </script>
@endif