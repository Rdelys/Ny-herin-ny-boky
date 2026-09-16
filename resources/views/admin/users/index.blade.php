@extends('layouts.admin')

@section('admin_title', 'Utilisateurs')

@section('admin_content')
    <div class="admin-card" style="padding:0; overflow:hidden;">
        <div style="overflow-x:auto;">
            <table style="width:100%; border-collapse:collapse; font-size:.9rem;">
                <thead>
                    <tr style="background: rgba(85,16,29,.03);">
                        <th style="text-align:left; padding:14px 20px; font-size:.72rem; text-transform:uppercase; letter-spacing:.03em; color:#9c8b7d;">Nom</th>
                        <th style="text-align:left; padding:14px 20px; font-size:.72rem; text-transform:uppercase; letter-spacing:.03em; color:#9c8b7d;">Email</th>
                        <th style="text-align:left; padding:14px 20px; font-size:.72rem; text-transform:uppercase; letter-spacing:.03em; color:#9c8b7d;">Rôle</th>
                        <th style="text-align:left; padding:14px 20px; font-size:.72rem; text-transform:uppercase; letter-spacing:.03em; color:#9c8b7d;">Livres publiés</th>
                        <th style="text-align:left; padding:14px 20px; font-size:.72rem; text-transform:uppercase; letter-spacing:.03em; color:#9c8b7d;">Inscrit le</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                        <tr style="border-top:1px solid rgba(85,16,29,.06);">
                            <td style="padding:12px 20px; font-weight:600;">{{ $user->name }}</td>
                            <td style="padding:12px 20px; color:#6b5a4d;">{{ $user->email }}</td>
                            <td style="padding:12px 20px;">
                                @if($user->role === 'vendeur')
                                    <span style="background:rgba(233,178,63,.18); color:#8a5f14; font-size:.76rem; font-weight:700; padding:3px 10px; border-radius:999px;">Vendeur</span>
                                @else
                                    <span style="background:rgba(92,138,55,.14); color:#395e26; font-size:.76rem; font-weight:700; padding:3px 10px; border-radius:999px;">Client</span>
                                @endif
                            </td>
                            <td style="padding:12px 20px;">{{ $user->role === 'vendeur' ? $user->books_count : '—' }}</td>
                            <td style="padding:12px 20px; color:#96897d;">{{ $user->created_at->format('d/m/Y') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    @if($users->hasPages())
        <div style="display:flex; align-items:center; justify-content:center; gap:16px; margin-top:22px;">
            @if($users->onFirstPage())
                <span style="color:#b8a99b; font-size:.85rem;">&larr; Précédent</span>
            @else
                <a href="{{ $users->previousPageUrl() }}" style="color:var(--maroon-800); font-weight:600; font-size:.85rem;">&larr; Précédent</a>
            @endif
            <span style="font-size:.82rem; color:#8a7a6d;">Page {{ $users->currentPage() }} / {{ $users->lastPage() }}</span>
            @if($users->hasMorePages())
                <a href="{{ $users->nextPageUrl() }}" style="color:var(--maroon-800); font-weight:600; font-size:.85rem;">Suivant &rarr;</a>
            @else
                <span style="color:#b8a99b; font-size:.85rem;">Suivant &rarr;</span>
            @endif
        </div>
    @endif
@endsection