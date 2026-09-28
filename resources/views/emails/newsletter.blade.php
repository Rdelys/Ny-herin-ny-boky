<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <style>
        body{ margin:0; background:#f4ede0; font-family: Arial, sans-serif; color:#2a1210; }
        .wrap{ max-width:560px; margin:0 auto; padding:32px 20px; }
        .card{ background:#fffdf7; border-radius:16px; padding:30px 26px; }
        .brand{ font-size:1.1rem; font-weight:700; color:#55101d; margin:0 0 20px; }
        .content{ font-size:.95rem; line-height:1.7; white-space: pre-line; }
        .foot{ text-align:center; font-size:.76rem; color:#9c8b7d; margin-top:24px; }
    </style>
</head>
<body>
    <div class="wrap">
        <div class="card">
            <p class="brand">Ny Herin'ny Boky</p>
            <div class="content">{{ $corps }}</div>
        </div>
        <p class="foot">Vous recevez cet email car vous êtes inscrit à la newsletter de Ny Herin'ny Boky.</p>
    </div>
</body>
</html>