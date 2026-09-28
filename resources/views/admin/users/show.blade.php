@extends('layouts.admin')

@section('admin_title', $user->sellerProfile->nom_entreprise ?? $user->name)

@section('admin_content')

    <a href="{{ route('admin.users.index') }}" class="admin-back-link">&larr; Retour aux utilisateurs</a>

    {{-- ============ IDENTITÉ ============ --}}
    <div class="admin-card" style="margin-bottom:22px;">
        <div style="display:flex; flex-wrap:wrap; align-items:center; gap:16px;">
            <div style="width:56px; height:56px; flex-shrink:0; border-radius:50%; background:var(--maroon-900); color:var(--cream); display:flex; align-items:center; justify-content:center; font-family:var(--serif); font-size:1.3rem;">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
            <div style="min-width:0;">
                <h2 style="font-family:var(--serif); color:var(--maroon-900); margin:0 0 4px; font-size:1.25rem;">
                    {{ $user->sellerProfile->nom_entreprise ?? $user->name }}
                </h2>
                <p style="margin:0; color:#6b5a4d; font-size:.88rem; word-break:break-all;">{{ $user->email }}</p>
            </div>
            <span class="admin-badge admin-badge-{{ $estVendeur ? 'vendeur' : 'client' }}" style="margin-left:auto;">
                {{ $estVendeur ? 'Vendeur' : 'Client' }}
            </span>
        </div>

        <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(180px,1fr)); gap:16px; margin-top:22px;">
            <div>
                <span class="admin-stat-label">Nom du compte</span>
                <p style="margin:4px 0 0;">{{ $user->name }}</p>
            </div>
            <div>
                <span class="admin-stat-label">Inscrit le</span>
                <p style="margin:4px 0 0;">{{ $user->created_at->format('d/m/Y') }}</p>
            </div>
            @if($estVendeur)
                <div>
                    <span class="admin-stat-label">Localisation</span>
                    <p style="margin:4px 0 0;">{{ $user->sellerProfile->localisation ?: '—' }}</p>
                </div>
                <div>
                    <span class="admin-stat-label">Code postal</span>
                    <p style="margin:4px 0 0;">{{ $user->sellerProfile->code_postal ?: '—' }}</p>
                </div>
                <div>
                    <span class="admin-stat-label">Numéro de paiement</span>
                    <p style="margin:4px 0 0;">{{ $user->sellerProfile->numero_paiement ?: '—' }}</p>
                </div>
            @else
                <div>
                    <span class="admin-stat-label">Localisation</span>
                    <p style="margin:4px 0 0;">{{ $user->buyerProfile->localisation ?? '—' }}</p>
                </div>
                <div>
                    <span class="admin-stat-label">Livres recherchés</span>
                    <p style="margin:4px 0 0;">{{ $user->buyerProfile->types_livres_recherches ?? '—' }}</p>
                </div>
            @endif
        </div>
    </div>

    {{-- ============ ARGENT ============ --}}
    <div class="admin-stat-grid">
        <div class="admin-stat-card admin-stat-card-highlight">
            <span class="admin-stat-label">{{ $estVendeur ? 'Ventes encaissées' : 'Total dépensé' }}</span>
            <strong class="admin-stat-value">{{ number_format($totalCommandes, 0, ',', ' ') }} Ar</strong>
            <span class="admin-stat-sub">{{ $orders->count() }} commande(s) récente(s)</span>
        </div>
        @if($estVendeur)
            <div class="admin-stat-card">
                <span class="admin-stat-label">Argent dû</span>
                <strong class="admin-stat-value" style="color:{{ $argentDu > 0 ? '#b3261e' : 'inherit' }};">
                    {{ number_format($argentDu, 0, ',', ' ') }} Ar
                </strong>
                <span class="admin-stat-sub">
                    <a href="{{ route('admin.paiements', ['vendeur' => $user->id, 'statut' => \App\Models\Order::PAIEMENT_DU]) }}" style="color:var(--maroon-800); font-weight:600;">Reverser</a>
                </span>
            </div>
            <div class="admin-stat-card">
                <span class="admin-stat-label">Argent envoyé</span>
                <strong class="admin-stat-value">{{ number_format($argentEnvoye, 0, ',', ' ') }} Ar</strong>
            </div>
            <div class="admin-stat-card">
                <span class="admin-stat-label">Livres publiés</span>
                <strong class="admin-stat-value">{{ $books->total() }}</strong>
                <span class="admin-stat-sub">{{ (int) $user->books()->sum('quantite') }} exemplaires en stock</span>
            </div>
        @endif
    </div>

    {{-- ============ LIVRES DU VENDEUR ============ --}}
    @if($estVendeur)
        <h2 class="admin-section-title">Livres de ce vendeur</h2>

        <div class="admin-card" style="padding:0; overflow:hidden;">
            @if($books->isEmpty())
                <div class="admin-empty-state">
                    <h2>Aucun livre</h2>
                    <p>Ce vendeur n'a encore publié aucun livre.</p>
                </div>
            @else
                <div class="admin-table-wrap">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Livre</th>
                                <th>Catégorie</th>
                                <th>État</th>
                                <th>Prix vendeur</th>
                                <th>Prix client</th>
                                <th>Stock</th>
                                <th>Publié le</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($books as $book)
                                <tr>
                                    <td data-label="Livre">
                                        <div style="display:flex; align-items:center; gap:12px;">
                                            @if($book->image_path)
                                                <img src="{{ asset('storage/'.$book->image_path) }}" alt="{{ $book->titre }}" loading="lazy"
                                                     style="width:38px; height:50px; object-fit:cover; border-radius:6px; flex-shrink:0;">
                                            @endif
                                            <div>
                                                <strong>{{ $book->titre }}</strong>
                                                @if($book->auteur)<br><span class="admin-table-sub">{{ $book->auteur }}</span>@endif
                                            </div>
                                        </div>
                                    </td>
                                    <td data-label="Catégorie">{{ $book->categorie ?: '—' }}</td>
                                    <td data-label="État">{{ __('home.book_condition_' . $book->etat) }}</td>
                                    <td data-label="Prix vendeur">{{ $book->prix_achat ? number_format($book->prix_achat, 0, ',', ' ').' Ar' : '—' }}</td>
                                    <td data-label="Prix client">{{ $book->prix_achat ? number_format($book->prix_achat_client, 0, ',', ' ').' Ar' : '—' }}</td>
                                    <td data-label="Stock">{{ $book->quantite }}</td>
                                    <td data-label="Publié le" style="color:#96897d;">{{ $book->created_at->format('d/m/Y') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        @if($books->hasPages())
            <div class="admin-pager">
                @if($books->onFirstPage())
                    <span class="is-off">&larr; Précédent</span>
                @else
                    <a href="{{ $books->previousPageUrl() }}">&larr; Précédent</a>
                @endif
                <span class="admin-pager-info">Page {{ $books->currentPage() }} / {{ $books->lastPage() }}</span>
                @if($books->hasMorePages())
                    <a href="{{ $books->nextPageUrl() }}">Suivant &rarr;</a>
                @else
                    <span class="is-off">Suivant &rarr;</span>
                @endif
            </div>
        @endif
    @endif

    {{-- ============ COMMANDES ============ --}}
    <h2 class="admin-section-title">{{ $estVendeur ? 'Dernières ventes' : 'Dernières commandes' }}</h2>

    <div class="admin-card" style="padding:0; overflow:hidden;">
        @if($orders->isEmpty())
            <div class="admin-empty-state">
                <h2>Aucune commande</h2>
                <p>{{ $estVendeur ? "Ce vendeur n'a encore rien vendu." : "Ce client n'a encore rien commandé." }}</p>
            </div>
        @else
            <div class="admin-table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Référence</th>
                            <th>Livre</th>
                            <th>{{ $estVendeur ? 'Acheteur' : 'Vendeur' }}</th>
                            <th>Montant</th>
                            <th>Statut</th>
                            @if($estVendeur)<th>Reversement</th>@endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $order)
                            <tr>
                                <td data-label="Référence">
                                    <div>
                                        <strong>{{ $order->reference }}</strong><br>
                                        <span class="admin-table-sub">{{ $order->created_at->format('d/m/Y') }}</span>
                                    </div>
                                </td>
                                <td data-label="Livre">{{ $order->book_titre }}</td>
                                <td data-label="{{ $estVendeur ? 'Acheteur' : 'Vendeur' }}">
                                    @if($estVendeur)
                                        {{ $order->buyer_name }}
                                    @else
                                        {{ $order->seller->sellerProfile->nom_entreprise ?? $order->seller->name }}
                                    @endif
                                </td>
                                <td data-label="Montant">
                                    <div>
                                        <strong>{{ number_format($order->total, 0, ',', ' ') }} Ar</strong>
                                        @if($estVendeur)
                                            <br><span class="admin-table-sub">Sa part : {{ number_format($order->montant_vendeur, 0, ',', ' ') }} Ar</span>
                                        @endif
                                    </div>
                                </td>
                                <td data-label="Statut">
                                    <span class="admin-badge admin-badge-{{ $order->statut }}">{{ $order->statut_label }}</span>
                                </td>
                                @if($estVendeur)
                                    <td data-label="Reversement">
                                        <span class="admin-badge admin-badge-{{ $order->paiement_vendeur }}">{{ $order->paiement_vendeur_label }}</span>
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection
