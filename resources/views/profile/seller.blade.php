@extends('layouts.app')

@section('meta_title', 'Mon espace vendeur — ' . config('app.name'))

@section('content')
    <section>
        <div class="wrap">
            <div class="section-head">
                <div>
                    <h2>Mon espace vendeur</h2>
                    <p>{{ $profile->nom_entreprise ?? $user->name }}</p>
                </div>
            </div>

            @if(session('success'))
                <p class="flash-success">{{ session('success') }}</p>
            @endif

            <div class="seller-card" style="background:#fffdf7; border:1px solid rgba(85,16,29,.09); color: var(--ink); flex-direction: column; align-items: flex-start; gap: 18px; padding: 30px; margin-bottom: 40px;">
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
                            <p class="book-genre" style="margin-bottom:4px;">Localisation</p>
                            <p>{{ $profile->localisation ?: '—' }}</p>
                        </div>
                        <div>
                            <p class="book-genre" style="margin-bottom:4px;">Code postal</p>
                            <p>{{ $profile->code_postal ?: '—' }}</p>
                        </div>
                        <div>
                            <p class="book-genre" style="margin-bottom:4px;">Numéro pour recevoir l'argent</p>
                            <p>{{ $profile->numero_paiement ?: '—' }}</p>
                        </div>
                        <div>
                            <p class="book-genre" style="margin-bottom:4px;">Mode de paiement</p>
                            <p>
                                {{ $profile->mode_paiement === 'abonnement' ? 'Abonnement' : 'Commission' }}
                                @if($profile->mode_paiement !== 'abonnement' && $profile->commission_status)
                                    <span class="book-tag" style="position:static; display:inline-block; margin-left:6px;">{{ $profile->commission_status }}</span>
                                @endif
                            </p>
                        </div>
                    </div>
                @endif
            </div>

            {{-- ============ AJOUTER UN LIVRE ============ --}}
            <div class="add-book-card">
                <h3 class="add-book-title">Ajouter un livre</h3>

                <form method="POST" action="{{ route('books.store') }}" enctype="multipart/form-data" class="modal-form">
                    @csrf

                    <div class="modal-form-row">
                        <label>Titre
                            <input type="text" name="titre" placeholder="Nom du livre" value="{{ old('titre') }}" required>
                        </label>
                        <label>Auteur
                            <input type="text" name="auteur" placeholder="Nom de l'auteur" value="{{ old('auteur') }}">
                        </label>
                    </div>
                    @error('titre')<p class="modal-field-error">{{ $message }}</p>@enderror

                    <label>Description
                        <textarea name="description" rows="3" placeholder="Nombre de pages, résumé bref...">{{ old('description') }}</textarea>
                    </label>

                    <div class="modal-form-row">
                        <label>Prix d'achat (Ar)
                            <input type="number" name="prix_achat" min="0" value="{{ old('prix_achat') }}">
                        </label>
                        <label>Prix de location (Ar)
                            <input type="number" name="prix_location" min="0" value="{{ old('prix_location') }}">
                        </label>
                    </div>

                    <div class="modal-form-row">
                        <label>Catégorie
                            <select name="categorie" required>
                                <option value="">choisissez ...</option>
                                @foreach(\App\Http\Controllers\BookController::CATEGORIES as $cat)
                                    <option value="{{ $cat }}" @selected(old('categorie') === $cat)>{{ $cat }}</option>
                                @endforeach
                            </select>
                        </label>
                        <label>État
                            <select name="etat" required>
                                <option value="neuf" @selected(old('etat') === 'neuf')>Neuf</option>
                                <option value="occasion" @selected(old('etat', 'occasion') === 'occasion')>Occasion</option>
                            </select>
                        </label>
                    </div>
                    @error('categorie')<p class="modal-field-error">{{ $message }}</p>@enderror

                    <fieldset class="modal-fieldset">
                        <legend>Livraison</legend>
                        <label class="modal-radio-card" id="shippingToggleCard">
                            <input type="checkbox" name="livraison_disponible" value="1" id="shippingToggle" {{ old('livraison_disponible') ? 'checked' : '' }}>
                            <span>
                                <strong>Livraison disponible</strong>
                                <small>Cochez si vous proposez la livraison pour ce livre</small>
                            </span>
                        </label>
                        <label id="shippingFeeField" style="{{ old('livraison_disponible') ? '' : 'display:none;' }}">
                            Frais de livraison (Ar)
                            <input type="number" name="frais_livraison" min="0" placeholder="0 = livraison gratuite" value="{{ old('frais_livraison') }}">
                        </label>
                    </fieldset>

                    <label>Image du livre
                        <input type="file" name="image" accept="image/*">
                    </label>
                    <p class="field-hint">Image (4 Mo max)</p>
                    @error('image')<p class="modal-field-error">{{ $message }}</p>@enderror

                    <button type="submit" class="btn-modal-primary">Ajouter</button>
                </form>
            </div>

            <div class="section-head">
                <div>
                    <h2>Mes livres</h2>
                    <p>{{ $books->total() }} livre(s) publié(s).</p>
                </div>
            </div>

            @if($books->isEmpty())
                <p style="color:#7a6a5d;">Vous n'avez pas encore ajouté de livre.</p>
            @else
                <div class="table-scroll">
                    <table class="seller-table">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Titre</th>
                                <th>Catégorie</th>
                                <th>Prix</th>
                                <th>Livraison</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($books as $book)
                                <tr class="seller-table-row" onclick="window.location='{{ route('books.edit', $book) }}'">
                                    <td class="seller-table-thumb">
                                        @if($book->image_path)
                                            <img src="{{ asset('storage/'.$book->image_path) }}" alt="{{ $book->titre }}" loading="lazy">
                                        @else
                                            <div class="seller-table-thumb-empty"></div>
                                        @endif
                                    </td>
                                    <td>
                                        <strong>{{ $book->titre }}</strong>
                                        @if($book->auteur)<br><span class="seller-table-sub">{{ $book->auteur }}</span>@endif
                                    </td>
                                    <td><span class="book-genre" style="margin:0;">{{ $book->categorie }}</span></td>
                                    <td>{{ $book->prix_achat ? number_format($book->prix_achat, 0, ',', ' ').' Ar' : '—' }}</td>
                                    <td>
                                        @if($book->livraison_disponible)
                                            <span class="book-tag" style="position:static;">Oui{{ $book->frais_livraison ? ' · '.number_format($book->frais_livraison, 0, ',', ' ').' Ar' : ' · gratuite' }}</span>
                                        @else
                                            <span style="color:#96897d;">Non</span>
                                        @endif
                                    </td>
                                    <td class="seller-table-actions" onclick="event.stopPropagation();">
                                        <a href="{{ route('books.edit', $book) }}" class="table-action-link">Modifier</a>
                                        <form method="POST" action="{{ route('books.destroy', $book) }}" onsubmit="return confirm('Supprimer ce livre ?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="table-action-link table-action-danger">Supprimer</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($books->hasPages())
                    <div class="pager">
                        @if($books->onFirstPage())
                            <span class="pager-btn disabled">&larr; Précédent</span>
                        @else
                            <a href="{{ $books->previousPageUrl() }}" class="pager-btn">&larr; Précédent</a>
                        @endif
                        <span class="pager-info">Page {{ $books->currentPage() }} / {{ $books->lastPage() }}</span>
                        @if($books->hasMorePages())
                            <a href="{{ $books->nextPageUrl() }}" class="pager-btn">Suivant &rarr;</a>
                        @else
                            <span class="pager-btn disabled">Suivant &rarr;</span>
                        @endif
                    </div>
                @endif
            @endif
        </div>
    </section>

    <script>
        (function(){
            var toggle = document.getElementById('shippingToggle');
            var feeField = document.getElementById('shippingFeeField');
            if (toggle && feeField) {
                toggle.addEventListener('change', function(){
                    feeField.style.display = toggle.checked ? '' : 'none';
                });
            }
        })();
    </script>
@endsection