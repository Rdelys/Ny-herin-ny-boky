<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion admin — Ny Herin'ny Boky</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,600;9..144,700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root{
            --maroon-950: #3d0b15; --maroon-900: #55101d; --maroon-800: #6c1524;
            --cream: #f6efdd; --ink: #2a1210; --gold: #e9b23f;
            --serif: 'Fraunces', Georgia, serif; --sans: 'Inter', -apple-system, sans-serif;
        }
        *{ box-sizing: border-box; }
        body{
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: var(--sans);
            background: linear-gradient(135deg, var(--maroon-950), var(--maroon-800));
            padding: 24px;
        }
        .admin-login-card{
            background: var(--cream);
            border-radius: 22px;
            width: 100%;
            max-width: 380px;
            padding: 38px 34px;
            box-shadow: 0 30px 60px -20px rgba(0,0,0,.5);
        }
        .admin-login-eyebrow{
            font-family: var(--serif);
            font-style: italic;
            color: var(--maroon-800);
            text-align: center;
            margin: 0 0 4px;
            font-size: .95rem;
        }
        .admin-login-title{
            font-family: var(--serif);
            font-weight: 600;
            font-size: 1.5rem;
            text-align: center;
            color: var(--ink);
            margin: 0 0 24px;
        }
        form{ display: flex; flex-direction: column; gap: 14px; }
        label{ display: flex; flex-direction: column; gap: 6px; font-size: .84rem; font-weight: 600; color: var(--ink); }
        input{
            font-family: inherit;
            font-size: .92rem;
            padding: 11px 13px;
            border-radius: 12px;
            border: 1px solid rgba(85,16,29,.16);
            background: #fffdf9;
        }
        input:focus{ outline: none; border-color: var(--gold); box-shadow: 0 0 0 3px rgba(233,178,63,.22); }
        button{
            margin-top: 6px;
            background: var(--maroon-900);
            color: var(--cream);
            border: 0;
            padding: 13px;
            border-radius: 999px;
            font-weight: 600;
            font-size: .95rem;
            font-family: inherit;
            cursor: pointer;
            transition: background .15s ease;
        }
        button:hover{ background: var(--maroon-800); }
        .admin-login-error{
            background: rgba(179,38,30,.1);
            border: 1px solid rgba(179,38,30,.3);
            color: #b3261e;
            font-size: .84rem;
            padding: 10px 14px;
            border-radius: 10px;
            margin: 0 0 4px;
        }
    </style>
</head>
<body>
    <div class="admin-login-card">
        <p class="admin-login-eyebrow">Ny Herin'ny Boky</p>
        <h1 class="admin-login-title">Espace administrateur</h1>

        @if ($errors->any())
            <p class="admin-login-error">{{ $errors->first() }}</p>
        @endif

        <form method="POST" action="{{ route('admin.login.submit') }}">
            @csrf
            <label>Email
                <input type="email" name="email" value="{{ old('email') }}" required autofocus>
            </label>
            <label>Mot de passe
                <input type="password" name="password" required>
            </label>
            <button type="submit">Se connecter</button>
        </form>
    </div>
</body>
</html>