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

                    <label>Titre
                        <input type="text" name="titre" placeholder="Nom du livre" value="{{ old('titre') }}" required>
                    </label>
                    @error('titre')<p class="modal-field-error">{{ $message }}</p>@enderror

                    <label>Description
                        <textarea name="description" rows="3" placeholder="Mettez l'auteur, le nombre de pages, et une description brève">{{ old('description') }}</textarea>
                    </label>

                    <div class="modal-form-row">
                        <label>Prix d'achat (Ar)
                            <input type="number" name="prix_achat" min="0" value="{{ old('prix_achat') }}">
                        </label>
                        <label>Prix de location (Ar)
                            <input type="number" name="prix_location" min="0" value="{{ old('prix_location') }}">
                        </label>
                    </div>

                    <label>Catégorie
                        <select name="categorie" required>
                            <option value="">choisissez ...</option>
                            @foreach(\App\Http\Controllers\BookController::CATEGORIES as $cat)
                                <option value="{{ $cat }}" @selected(old('categorie') === $cat)>{{ $cat }}</option>
                            @endforeach
                        </select>
                    </label>
                    @error('categorie')<p class="modal-field-error">{{ $message }}</p>@enderror

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
                    <p>{{ $books->count() }} livre(s) publié(s).</p>
                </div>
            </div>

            @if($books->isEmpty())
                <p style="color:#7a6a5d;">Vous n'avez pas encore ajouté de livre.</p>
            @else
                <div class="book-grid">
                    @foreach($books as $book)
                        <article class="book-card">
                            <div class="book-cover">
                                @if($book->image_path)
                                    <img src="{{ asset('storage/'.$book->image_path) }}" alt="{{ $book->titre }}" loading="lazy">
                                @endif
                                <div class="book-cover-gradient"></div>
                                <form method="POST" action="{{ route('books.destroy', $book) }}" class="book-delete-form" onsubmit="return confirm('Supprimer ce livre ?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="book-wishlist" aria-label="Supprimer">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                            <path d="M6 6l12 12M18 6L6 18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                            <div class="book-body">
                                @if($book->categorie)
                                    <span class="book-genre">{{ $book->categorie }}</span>
                                @endif
                                <h3 class="book-title">{{ $book->titre }}</h3>
                                <div class="book-foot">
                                    <span class="book-loc">{{ $book->prix_achat ? number_format($book->prix_achat, 0, ',', ' ').' Ar' : '—' }}</span>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif
        </div>
    </section>
@endsection