{{-- ============ MODALS CONNEXION / INSCRIPTION ============ --}}
{{-- Une seule overlay, 3 "vues" internes (login / inscription client / inscription
     vendeur) basculées en JS sans rechargement. En cas d'erreur de validation,
     le champ caché _auth_form permet de rouvrir automatiquement la bonne vue. --}}

<div class="modal-overlay" id="authModalOverlay">
    <div class="modal-panel" id="authModalPanel" role="dialog" aria-modal="true" aria-labelledby="authModalTitle">
        <button type="button" class="modal-close" id="authModalClose" aria-label="Fermer">&times;</button>

        {{-- ---------- CONNEXION ---------- --}}
        <div class="modal-view" id="viewLogin">
            <h2 class="modal-title" id="authModalTitle">Connexion</h2>
            <p class="modal-subtitle">Connectez-vous à votre compte</p>

            <form method="POST" action="{{ route('login') }}" class="modal-form">
                @csrf
                <input type="hidden" name="_auth_form" value="login">

                <label>Email
                    <input type="email" name="email" placeholder="exemple@gmail.com" value="{{ old('_auth_form') === 'login' ? old('email') : '' }}" required>
                </label>
                @error('email')
                    <p class="modal-field-error">{{ $message }}</p>
                @enderror

                <label>Mot de passe
                    <input type="password" name="password" placeholder="Entrez votre mot de passe" required>
                </label>

                <button type="submit" class="btn-modal-primary">Se connecter</button>
            </form>

            <p class="modal-switch">
                Pas de compte ?
                <a href="#" data-auth-switch="registerClient">S'inscrire en tant que client</a> ·
                <a href="#" data-auth-switch="registerSeller">S'inscrire en tant que vendeur</a>
            </p>
        </div>

        {{-- ---------- INSCRIPTION CLIENT ---------- --}}
        <div class="modal-view" id="viewRegisterClient" hidden>
            <h2 class="modal-title">Créer un compte</h2>
            <p class="modal-subtitle">Achetez et empruntez des livres malgaches</p>

            <div class="modal-tabs" role="tablist" aria-label="Type de compte">
                <button type="button" class="modal-tab active" data-auth-switch="registerClient" role="tab" aria-selected="true">
                    Devenir client
                </button>
                <button type="button" class="modal-tab" data-auth-switch="registerSeller" role="tab" aria-selected="false">
                    Devenir vendeur
                </button>
            </div>

            <form method="POST" action="{{ route('register.client') }}" class="modal-form">
                @csrf
                <input type="hidden" name="_auth_form" value="registerClient">

                <div class="modal-form-row">
                    <label>Prénom
                        <input type="text" name="prenom" value="{{ old('_auth_form') === 'registerClient' ? old('prenom') : '' }}" required>
                    </label>
                    <label>Nom
                        <input type="text" name="nom" value="{{ old('_auth_form') === 'registerClient' ? old('nom') : '' }}" required>
                    </label>
                </div>

                <div class="modal-form-row">
                    <label>Email
                        <input type="email" name="email" value="{{ old('_auth_form') === 'registerClient' ? old('email') : '' }}" required>
                    </label>
                    <label>Mot de passe
                        <input type="password" name="password" minlength="8" required>
                    </label>
                </div>
                @error('email')
                    <p class="modal-field-error">{{ $message }}</p>
                @enderror

                <label>Localisation
                    <input type="text" name="localisation" placeholder="Antananarivo, Fianarantsoa...">
                </label>

                <fieldset class="modal-fieldset">
                    <legend>Pourquoi vous inscrivez-vous ?</legend>

                    <div class="modal-form-row">
                        <label>Pourquoi vous inscrivez-vous ?
                            <select name="motif_inscription">
                                <option value="">choisissez ...</option>
                                <option>Apprendre et respecter les livres</option>
                                <option>Chercher des livres malgaches</option>
                                <option>Lire pour le plaisir</option>
                                <option>Études et recherches</option>
                                <option>Autre</option>
                            </select>
                        </label>

                        <label>Types de livres recherchés
                            <select name="types_livres_recherches">
                                <option value="">choisissez ...</option>
                                <option>Sciences</option>
                                <option>Malgache</option>
                                <option>Littérature</option>
                                <option>Histoire</option>
                                <option>Contemporain</option>
                                <option>Cinéma</option>
                            </select>
                        </label>
                    </div>
                </fieldset>

                <button type="submit" class="btn-modal-primary">S'inscrire</button>
            </form>

            <p class="modal-switch">Déjà un compte ? <a href="#" data-auth-switch="login">Se connecter</a></p>
        </div>

        {{-- ---------- INSCRIPTION VENDEUR ---------- --}}
        <div class="modal-view" id="viewRegisterSeller" hidden>
            <h2 class="modal-title">Créer un compte</h2>
            <p class="modal-subtitle">Vendez vos livres et gérez votre boutique</p>

            <div class="modal-tabs" role="tablist" aria-label="Type de compte">
                <button type="button" class="modal-tab" data-auth-switch="registerClient" role="tab" aria-selected="false">
                    Devenir client
                </button>
                <button type="button" class="modal-tab active" data-auth-switch="registerSeller" role="tab" aria-selected="true">
                    Devenir vendeur
                </button>
            </div>

            <form method="POST" action="{{ route('register.seller') }}" class="modal-form">
                @csrf
                <input type="hidden" name="_auth_form" value="registerSeller">

                <div class="modal-form-row">
                    <label>Nom de l'entreprise
                        <input type="text" name="nom_entreprise" value="{{ old('_auth_form') === 'registerSeller' ? old('nom_entreprise') : '' }}" required>
                    </label>
                    <label>Email
                        <input type="email" name="email" value="{{ old('_auth_form') === 'registerSeller' ? old('email') : '' }}" required>
                    </label>
                </div>
                @error('email')
                    <p class="modal-field-error">{{ $message }}</p>
                @enderror

                <div class="modal-form-row">
                    <label>Mot de passe
                        <input type="password" name="password" minlength="8" required>
                    </label>
                    <label>Localisation
                        <input type="text" name="localisation" placeholder="Antananarivo...">
                    </label>
                </div>

                <div class="modal-form-row">
                    <label>Code postal
                        <input type="text" name="code_postal">
                    </label>
                    <label>Numéro pour recevoir l'argent
                        <input type="text" name="numero_paiement" placeholder="034 xx xxx xx">
                    </label>
                </div>

                <fieldset class="modal-fieldset">
                    <legend>Mode de paiement</legend>

                    <div class="modal-radio-group">
                        <label class="modal-radio-card">
                            <input type="radio" name="mode_paiement" value="commission" checked>
                            <span>
                                <strong>Commission (-10%)</strong>
                                <small>Inscription gratuite avec commission</small>
                            </span>
                        </label>

                        <label class="modal-radio-card is-disabled">
                            <input type="radio" name="mode_paiement" value="abonnement" disabled>
                            <span>
                                <strong>Abonnement <em class="modal-badge-soon">Bientôt disponible</em></strong>
                                <small>Payer des abonnements par mois</small>
                            </span>
                        </label>
                    </div>
                </fieldset>

                <button type="submit" class="btn-modal-primary">Continuer</button>
            </form>

            <p class="modal-switch">Déjà un compte ? <a href="#" data-auth-switch="login">Se connecter</a></p>
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