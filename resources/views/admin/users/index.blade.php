@extends('layouts.admin')

@section('admin_title', 'Utilisateurs')

@section('admin_content')

    <div class="admin-filter-bar">
        <a href="{{ route('admin.users.index') }}" class="admin-rate-preset-btn {{ $roleActif === null ? 'active' : '' }}">
            Tous ({{ $compteurs->sum() }})
        </a>
        <a href="{{ route('admin.users.index', ['role' => 'client']) }}" class="admin-rate-preset-btn {{ $roleActif === 'client' ? 'active' : '' }}">
            Clients ({{ $compteurs->get('client', 0) }})
        </a>
        <a href="{{ route('admin.users.index', ['role' => 'vendeur']) }}" class="admin-rate-preset-btn {{ $roleActif === 'vendeur' ? 'active' : '' }}">
            Vendeurs ({{ $compteurs->get('vendeur', 0) }})
        </a>

        <form method="GET" action="{{ route('admin.users.index') }}" style="display:flex; gap:8px; margin-left:auto;">
            @if($roleActif)
                <input type="hidden" name="role" value="{{ $roleActif }}">
            @endif
            <input type="search" name="q" value="{{ $recherche }}" placeholder="Nom ou email…" class="admin-input">
            <button type="submit" class="admin-btn">Rechercher</button>
        </form>
    </div>

    <div class="admin-card" style="padding:0; overflow:hidden;">
        @if($users->isEmpty())
            <div class="admin-empty-state">
                <h2>Aucun utilisateur</h2>
                <p>Aucun compte ne correspond à cette recherche.</p>
            </div>
        @else
            <div class="admin-table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Nom</th>
                            <th>Email</th>
                            <th>Rôle</th>
                            <th>Livres publiés</th>
                            <th>Inscrit le</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                            <tr class="is-clickable" onclick="window.location='{{ route('admin.users.show', $user) }}'">
                                <td data-label="Nom" style="font-weight:600;">{{ $user->name }}</td>
                                <td data-label="Email" style="color:#6b5a4d;">{{ $user->email }}</td>
                                <td data-label="Rôle">
                                    <span class="admin-badge admin-badge-{{ $user->role === 'vendeur' ? 'vendeur' : 'client' }}">
                                        {{ $user->role === 'vendeur' ? 'Vendeur' : 'Client' }}
                                    </span>
                                </td>
                                <td data-label="Livres publiés">{{ $user->role === 'vendeur' ? $user->books_count : '—' }}</td>
                                <td data-label="Inscrit le" style="color:#96897d;">{{ $user->created_at->format('d/m/Y') }}</td>
                                <td data-label="">
                                    <a href="{{ route('admin.users.show', $user) }}" class="admin-btn admin-btn-ghost">
                                        {{ $user->role === 'vendeur' ? 'Voir ses livres' : 'Voir la fiche' }}
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    @if($users->hasPages())
        <div class="admin-pager">
            @if($users->onFirstPage())
                <span class="is-off">&larr; Précédent</span>
            @else
                <a href="{{ $users->previousPageUrl() }}">&larr; Précédent</a>
            @endif
            <span class="admin-pager-info">Page {{ $users->currentPage() }} / {{ $users->lastPage() }}</span>
            @if($users->hasMorePages())
                <a href="{{ $users->nextPageUrl() }}">Suivant &rarr;</a>
            @else
                <span class="is-off">Suivant &rarr;</span>
            @endif
        </div>
    @endif
@endsection
