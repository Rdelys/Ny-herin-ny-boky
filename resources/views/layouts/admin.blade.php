<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('admin_title', 'Admin') — Ny Herin'ny Boky</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,600;9..144,700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root{
            --maroon-950: #3d0b15; --maroon-900: #55101d; --maroon-800: #6c1524;
            --cream: #f6efdd; --cream-dim: #e7dcbf; --ink: #2a1210;
            --green-700: #395e26; --gold: #e9b23f; --gold-dim: #caa056;
            --serif: 'Fraunces', Georgia, serif; --sans: 'Inter', -apple-system, sans-serif;
        }
        *{ box-sizing: border-box; }
        body{
            margin: 0;
            font-family: var(--sans);
            color: var(--ink);
            background: #f4ede0;
            display: flex;
            min-height: 100vh;
        }
        a{ color: inherit; text-decoration: none; }
        ul{ list-style: none; margin: 0; padding: 0; }

        /* ---------- sidebar ---------- */
        .admin-sidebar{
            width: 240px;
            flex-shrink: 0;
            background: var(--maroon-950);
            color: var(--cream);
            display: flex;
            flex-direction: column;
            position: sticky;
            top: 0;
            height: 100vh;
        }
        .admin-brand{
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 22px 22px 18px;
            border-bottom: 1px solid rgba(246,239,221,.1);
        }
        .admin-brand img{ height: 34px; width: auto; }
        .admin-brand span{ font-family: var(--serif); font-size: 1.05rem; }
        .admin-nav{ flex: 1; padding: 18px 12px; display: flex; flex-direction: column; gap: 2px; }
        .admin-nav a{
            display: flex;
            align-items: center;
            gap: 11px;
            padding: 11px 14px;
            border-radius: 10px;
            font-size: .9rem;
            font-weight: 500;
            color: rgba(246,239,221,.7);
            transition: background .15s ease, color .15s ease;
        }
        .admin-nav a svg{ flex-shrink: 0; opacity: .8; }
        .admin-nav a:hover{ background: rgba(246,239,221,.06); color: var(--cream); }
        .admin-nav a.active{ background: var(--gold); color: var(--maroon-950); font-weight: 600; }
        .admin-nav a.active svg{ opacity: 1; }
        .admin-sidebar-foot{ padding: 16px 12px; border-top: 1px solid rgba(246,239,221,.1); }
        .admin-logout-btn{
            display: flex;
            align-items: center;
            gap: 10px;
            width: 100%;
            padding: 11px 14px;
            border-radius: 10px;
            border: 0;
            background: transparent;
            color: rgba(246,239,221,.65);
            font-family: inherit;
            font-size: .88rem;
            cursor: pointer;
            transition: background .15s ease, color .15s ease;
        }
        .admin-logout-btn:hover{ background: rgba(246,239,221,.06); color: var(--cream); }

        /* ---------- contenu ---------- */
        .admin-main{ flex: 1; min-width: 0; }
        .admin-topbar{
            background: #fffdf7;
            border-bottom: 1px solid rgba(85,16,29,.08);
            padding: 18px 32px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .admin-topbar h1{
            font-family: var(--serif);
            font-weight: 600;
            font-size: 1.3rem;
            margin: 0;
            color: var(--maroon-900);
        }
        .admin-content{ padding: 28px 32px 60px; }

        /* ---------- composants réutilisables ---------- */
        .admin-card{
            background: #fffdf7;
            border: 1px solid rgba(85,16,29,.08);
            border-radius: 16px;
            padding: 22px 24px;
        }
        .admin-empty-state{
            text-align: center;
            padding: 60px 20px;
            color: #9c8b7d;
        }
        .admin-empty-state h2{ font-family: var(--serif); color: var(--maroon-800); margin: 0 0 8px; }

        .admin-stat-grid{
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
            margin-bottom: 22px;
        }
        .admin-stat-card{
            position: relative;
            background: #fffdf7;
            border: 1px solid rgba(85,16,29,.08);
            border-radius: 16px;
            padding: 18px 20px;
            display: flex;
            flex-direction: column;
            gap: 4px;
        }
        .admin-stat-label{ font-size: .74rem; font-weight: 700; text-transform: uppercase; letter-spacing: .02em; color: #9c8b7d; }
        .admin-stat-value{ font-family: var(--serif); font-weight: 600; font-size: 1.55rem; color: var(--ink); }
        .admin-stat-sub{ font-size: .78rem; color: #96897d; }
        .admin-stat-card-highlight{
            background: linear-gradient(135deg, rgba(233,178,63,.14), rgba(233,178,63,.04));
            border-color: rgba(233,178,63,.4);
        }
        .admin-stat-card-highlight .admin-stat-value{ color: var(--maroon-800); }
        .admin-stat-card-preview{
            background: repeating-linear-gradient(135deg, rgba(85,16,29,.025), rgba(85,16,29,.025) 10px, rgba(85,16,29,.045) 10px, rgba(85,16,29,.045) 20px);
            border: 1px dashed rgba(85,16,29,.2);
        }
        .admin-preview-badge{
            position: absolute;
            top: 10px;
            right: 10px;
            font-size: .62rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .02em;
            color: var(--maroon-800);
            background: rgba(233,178,63,.28);
            padding: 2px 8px;
            border-radius: 999px;
        }
        .admin-preview-banner{
            background: rgba(233,178,63,.12);
            border: 1px dashed var(--gold);
            border-radius: 12px;
            padding: 12px 16px;
            font-size: .85rem;
            color: #6b5a4d;
            margin-bottom: 22px;
        }
        .admin-preview-banner span{
            display: inline-block;
            font-size: .68rem;
            font-weight: 700;
            text-transform: uppercase;
            color: var(--maroon-800);
            background: rgba(233,178,63,.35);
            padding: 2px 8px;
            border-radius: 999px;
            margin-right: 8px;
        }
        .admin-charts-grid{
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 18px;
        }
        .admin-card-title{
            font-family: var(--serif);
            font-size: 1.02rem;
            color: var(--maroon-900);
            margin: 0 0 16px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        @media (max-width: 480px){
            .admin-stat-grid{ grid-template-columns: 1fr 1fr; }
        }

        /* mobile : sidebar simplifiée en haut */
        @media (max-width: 860px){
            body{ flex-direction: column; }
            .admin-sidebar{ width: 100%; height: auto; position: static; flex-direction: row; align-items: center; }
            .admin-brand{ border: 0; padding: 14px 16px; }
            .admin-nav{ flex-direction: row; overflow-x: auto; padding: 10px; }
            .admin-nav a span{ display: none; }
            .admin-sidebar-foot{ border: 0; padding: 14px; }
            .admin-logout-btn span{ display: none; }
            .admin-content{ padding: 20px 16px 40px; }
            .admin-topbar{ padding: 16px; }
        }

        /* ---- À ajouter dans layouts/admin.blade.php, dans le <style> existant ---- */

.admin-settings-flash{
    background: rgba(92,138,55,.12);
    border: 1px solid rgba(92,138,55,.35);
    color: var(--green-700);
    font-size: .88rem;
    font-weight: 600;
    padding: 12px 16px;
    border-radius: 12px;
    margin: 0 0 20px;
    max-width: 560px;
}

.admin-current-rate{
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: linear-gradient(135deg, rgba(233,178,63,.16), rgba(233,178,63,.05));
    border: 1px solid rgba(233,178,63,.4);
    border-radius: 14px;
    padding: 16px 20px;
    margin-bottom: 24px;
}
.admin-current-rate span{ font-size: .82rem; color: #8a7a6d; font-weight: 600; }
.admin-current-rate strong{
    font-family: var(--serif);
    font-size: 1.8rem;
    color: var(--maroon-800);
}

.admin-form-label{
    display: block;
    font-size: .78rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .02em;
    color: #9c8b7d;
    margin-bottom: 10px;
}

.admin-rate-presets{ display: flex; flex-wrap: wrap; gap: 10px; }
.admin-rate-preset-btn{
    padding: 10px 18px;
    border-radius: 999px;
    border: 1px solid rgba(85,16,29,.18);
    background: #fffdf9;
    color: var(--maroon-800);
    font-family: inherit;
    font-weight: 600;
    font-size: .9rem;
    cursor: pointer;
    transition: background .15s ease, border-color .15s ease, transform .15s ease;
}
.admin-rate-preset-btn:hover{ background: rgba(85,16,29,.05); transform: translateY(-1px); }
.admin-rate-preset-btn.active{
    background: var(--maroon-900);
    border-color: var(--maroon-900);
    color: var(--cream);
}

.admin-rate-custom-row{
    display: flex;
    align-items: center;
    gap: 10px;
}
.admin-rate-custom-row input{
    width: 110px;
    font-family: inherit;
    font-size: .95rem;
    padding: 10px 14px;
    border-radius: 10px;
    border: 1px solid rgba(85,16,29,.18);
    background: #fffdf9;
}
.admin-rate-custom-row input:focus{ outline: none; border-color: var(--gold); box-shadow: 0 0 0 3px rgba(233,178,63,.22); }
.admin-rate-custom-row span{ font-weight: 700; color: var(--maroon-800); }
.admin-rate-custom-btn{
    background: var(--gold);
    color: var(--maroon-950);
    border: 0;
    padding: 10px 18px;
    border-radius: 999px;
    font-family: inherit;
    font-weight: 600;
    font-size: .88rem;
    cursor: pointer;
    transition: background .15s ease;
}
.admin-rate-custom-btn:hover{ background: #f0c168; }
    </style>
    @stack('admin_styles')
</head>
<body>

    <aside class="admin-sidebar">
        <div class="admin-brand">
            <img src="{{ asset('logo.png') }}" alt="Ny Herin'ny Boky">
            <span>Admin</span>
        </div>

        <nav class="admin-nav">
            <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><rect x="3" y="3" width="8" height="8" rx="2" stroke="currentColor" stroke-width="1.8"/><rect x="13" y="3" width="8" height="5" rx="2" stroke="currentColor" stroke-width="1.8"/><rect x="13" y="12" width="8" height="9" rx="2" stroke="currentColor" stroke-width="1.8"/><rect x="3" y="15" width="8" height="6" rx="2" stroke="currentColor" stroke-width="1.8"/></svg>
                <span>Dashboard</span>
            </a>
            <a href="{{ route('admin.users.index') }}" class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="9" cy="8" r="3.2" stroke="currentColor" stroke-width="1.8"/><path d="M3 20c0-3.3 2.7-6 6-6s6 2.7 6 6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/><circle cx="17" cy="7" r="2.6" stroke="currentColor" stroke-width="1.8"/><path d="M15.5 13.2c2.6.5 4.5 2.7 4.5 5.3" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                <span>Utilisateurs</span>
            </a>
            <a href="{{ route('admin.commandes') }}" class="{{ request()->routeIs('admin.commandes') ? 'active' : '' }}">
                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M6 2h9l4 4v16H6z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><path d="M9 10h6M9 14h6M9 18h4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                <span>Commandes</span>
            </a>
            <a href="{{ route('admin.paiements') }}" class="{{ request()->routeIs('admin.paiements') ? 'active' : '' }}">
                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><rect x="2" y="6" width="20" height="14" rx="2" stroke="currentColor" stroke-width="1.8"/><path d="M2 10h20" stroke="currentColor" stroke-width="1.8"/></svg>
                <span>Paiements</span>
            </a>
            <a href="{{ route('admin.parametres') }}" class="{{ request()->routeIs('admin.parametres') ? 'active' : '' }}">
                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="1.8"/><path d="M19.4 15a1.7 1.7 0 0 0 .34 1.87l.06.06a2.05 2.05 0 1 1-2.9 2.9l-.06-.06a1.7 1.7 0 0 0-1.87-.34 1.7 1.7 0 0 0-1 1.55V21a2.05 2.05 0 1 1-4.1 0v-.09A1.7 1.7 0 0 0 8.8 19.3a1.7 1.7 0 0 0-1.87.34l-.06.06a2.05 2.05 0 1 1-2.9-2.9l.06-.06a1.7 1.7 0 0 0 .34-1.87 1.7 1.7 0 0 0-1.55-1H2.7a2.05 2.05 0 1 1 0-4.1h.09A1.7 1.7 0 0 0 4.3 8.8a1.7 1.7 0 0 0-.34-1.87l-.06-.06a2.05 2.05 0 1 1 2.9-2.9l.06.06a1.7 1.7 0 0 0 1.87.34H8.8A1.7 1.7 0 0 0 9.83 2.7V2.6a2.05 2.05 0 1 1 4.1 0v.09a1.7 1.7 0 0 0 1.03 1.55 1.7 1.7 0 0 0 1.87-.34l.06-.06a2.05 2.05 0 1 1 2.9 2.9l-.06.06a1.7 1.7 0 0 0-.34 1.87V8.8a1.7 1.7 0 0 0 1.55 1h.09a2.05 2.05 0 1 1 0 4.1h-.09a1.7 1.7 0 0 0-1.55 1.03z" stroke="currentColor" stroke-width="1.3" stroke-linejoin="round"/></svg>
                <span>Paramètres</span>
            </a>
        </nav>

        <div class="admin-sidebar-foot">
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button type="submit" class="admin-logout-btn">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M15 17l5-5-5-5M20 12H9M13 21H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    <span>Se déconnecter</span>
                </button>
            </form>
        </div>
    </aside>

    <div class="admin-main">
        <div class="admin-topbar">
            <h1>@yield('admin_title', 'Dashboard')</h1>
        </div>
        <div class="admin-content">
            @yield('admin_content')
        </div>
    </div>

    @stack('admin_scripts')
</body>
</html>