@extends('layouts.admin')

@section('admin_title', 'Livreurs')

@section('admin_content')

    @if(session('success'))
        <div class="admin-settings-flash">{{ session('success') }}</div>
    @endif

    {{-- ---- ajouter un livreur ---- --}}
    <div class="admin-card" style="max-width: 560px; margin-bottom: 24px;">
        <h3 class="admin-card-title">Ajouter un livreur</h3>
        <form method="POST" action="{{ route('admin.livreurs.store') }}">
            @csrf
            <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(160px,1fr)); gap:12px; margin-bottom:12px;">
                <label>
                    <span class="admin-form-label">Nom</span>
                    <input type="text" name="nom" value="{{ old('nom') }}" required class="admin-input" style="width:100%;">
                </label>
                <label>
                    <span class="admin-form-label">Téléphone</span>
                    <input type="text" name="telephone" value="{{ old('telephone') }}" required class="admin-input" style="width:100%;">
                </label>
                <label>
                    <span class="admin-form-label">Zone (optionnel)</span>
                    <input type="text" name="zone" value="{{ old('zone') }}" class="admin-input" style="width:100%;">
                </label>
            </div>
            @error('nom')<p style="color:#b3261e; font-size:.82rem; margin:0 0 12px;">{{ $message }}</p>@enderror
            @error('telephone')<p style="color:#b3261e; font-size:.82rem; margin:0 0 12px;">{{ $message }}</p>@enderror
            <button type="submit" class="admin-btn">Ajouter</button>
        </form>
    </div>

    {{-- ---- liste des livreurs ---- --}}
    <div class="admin-card" style="padding:0; overflow:hidden;">
        @if($livreurs->isEmpty())
            <div class="admin-empty-state">
                <h2>Aucun livreur</h2>
                <p>Ajoutez un livreur ci-dessus : il apparaîtra ensuite dans la liste proposée depuis « Commandes » quand une commande passe « En livraison ».</p>
            </div>
        @else
            <div class="admin-table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Nom</th>
                            <th>Téléphone</th>
                            <th>Zone</th>
                            <th>Livraisons</th>
                            <th>Actif</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($livreurs as $livreur)
                            <tr>
                                <td data-label="Nom" style="font-weight:600;">{{ $livreur->nom }}</td>
                                <td data-label="Téléphone" style="color:#6b5a4d;">{{ $livreur->telephone }}</td>
                                <td data-label="Zone" style="color:#6b5a4d;">{{ $livreur->zone ?: '—' }}</td>
                                <td data-label="Livraisons">{{ $livreur->orders_count }}</td>
                                <td data-label="Actif">
                                    <span class="admin-badge {{ $livreur->actif ? 'admin-badge-envoye' : 'admin-badge-du' }}">
                                        {{ $livreur->actif ? 'Actif' : 'Inactif' }}
                                    </span>
                                </td>
                                <td data-label="">
                                    <div class="admin-row-actions">
                                        <form method="POST" action="{{ route('admin.livreurs.update', $livreur) }}">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="nom" value="{{ $livreur->nom }}">
                                            <input type="hidden" name="telephone" value="{{ $livreur->telephone }}">
                                            <input type="hidden" name="zone" value="{{ $livreur->zone }}">
                                            <input type="hidden" name="actif" value="{{ $livreur->actif ? '0' : '1' }}">
                                            <button type="submit" class="admin-btn admin-btn-ghost">{{ $livreur->actif ? 'Désactiver' : 'Activer' }}</button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.livreurs.destroy', $livreur) }}" onsubmit="return confirm('Supprimer ce livreur ?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="admin-btn admin-btn-danger">Supprimer</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection
