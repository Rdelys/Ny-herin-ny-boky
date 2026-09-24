<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    {{-- ============ SEO : titre, description, robots ============ --}}
    <title>@yield('meta_title', __('home.meta_title'))</title>
    <meta name="description" content="@yield('meta_description', __('home.meta_description'))">
    <meta name="robots" content="@yield('meta_robots', 'index, follow')">
    <meta name="theme-color" content="#55101d">

    <link rel="canonical" href="{{ url()->current() }}">
    <link rel="alternate" hreflang="mg" href="{{ url('/') }}">
    <link rel="alternate" hreflang="fr" href="{{ url('/fr') }}">
    <link rel="alternate" hreflang="en" href="{{ url('/en') }}">
    <link rel="alternate" hreflang="x-default" href="{{ url('/') }}">

    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Ny Herin'ny Boky">
    <meta property="og:title" content="@yield('meta_title', __('home.meta_title'))">
    <meta property="og:description" content="@yield('meta_description', __('home.meta_description'))">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:image" content="{{ asset('logo.png') }}">
    <meta property="og:locale" content="{{ ['fr' => 'fr_FR', 'mg' => 'mg_MG', 'en' => 'en_US'][app()->getLocale()] }}">
    <meta property="og:locale:alternate" content="fr_FR">
    <meta property="og:locale:alternate" content="mg_MG">
    <meta property="og:locale:alternate" content="en_US">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('meta_title', __('home.meta_title'))">
    <meta name="twitter:description" content="@yield('meta_description', __('home.meta_description'))">
    <meta name="twitter:image" content="{{ asset('logo.png') }}">

    <script type="application/ld+json">
    {
        "@@context": "https://schema.org",
        "@@type": "Organization",
        "name": "Ny Herin'ny Boky",
        "url": "{{ url('/') }}",
        "logo": "{{ asset('logo.png') }}"
    }
    </script>
    <script type="application/ld+json">
    {
        "@@context": "https://schema.org",
        "@@type": "WebSite",
        "name": "Ny Herin'ny Boky",
        "url": "{{ url('/') }}",
        "inLanguage": ["fr", "mg", "en"]
    }
    </script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,500;9..144,600;9..144,700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flag-icons/7.2.3/css/flag-icons.min.css">

    <style>
        :root{
            --maroon-950: #3d0b15;
            --maroon-900: #55101d;
            --maroon-800: #6c1524;
            --maroon-700: #832032;
            --cream: #f6efdd;
            --cream-dim: #e7dcbf;
            --ink: #2a1210;
            --green-700: #395e26;
            --green-500: #5c8a37;
            --green-300: #86b357;
            --gold: #e9b23f;
            --gold-dim: #caa056;

            --serif: 'Fraunces', Georgia, serif;
            --sans: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;

            --radius: 14px;
            --container: 1180px;
        }

        *{ box-sizing: border-box; }
        html{ scroll-behavior: smooth; }
        body{
            margin: 0;
            font-family: var(--sans);
            color: var(--ink);
            background: var(--cream);
            -webkit-font-smoothing: antialiased;
            overflow-x: hidden;
        }
        img{ max-width: 100%; display: block; }
        a{ color: inherit; text-decoration: none; }
        ul{ list-style: none; margin: 0; padding: 0; }
        button{ font-family: inherit; cursor: pointer; }

        .wrap{
            max-width: var(--container);
            margin: 0 auto;
            padding: 0 24px;
        }

        .site-header{
            position: sticky;
            top: 0;
            z-index: 40;
            background: var(--maroon-900);
            border-bottom: 1px solid rgba(246,239,221,.12);
        }
        .site-header .wrap{
            display: flex;
            align-items: center;
            gap: 20px;
            padding-top: 14px;
            padding-bottom: 14px;
        }
        .brand{
            display: flex;
            align-items: center;
            flex-shrink: 0;
            margin-right: auto;
        }
        .brand img{ height: 42px; width: auto; }

        .header-search{
            display: flex;
            align-items: center;
            gap: 8px;
            background: rgba(246,239,221,.08);
            border: 1px solid rgba(246,239,221,.16);
            border-radius: 999px;
            padding: 8px 14px;
            color: rgba(246,239,221,.6);
            flex: 1 1 260px;
            max-width: 320px;
            margin: 0 8px;
            transition: border-color .15s ease, background .15s ease;
        }
        .header-search:focus-within{
            border-color: rgba(246,239,221,.4);
            background: rgba(246,239,221,.12);
        }
        .header-search svg{ flex-shrink: 0; }
        .header-search input{
            border: 0;
            background: transparent;
            outline: none;
            color: var(--cream);
            font-family: inherit;
            font-size: .88rem;
            width: 100%;
        }
        .header-search input::placeholder{ color: rgba(246,239,221,.5); }
        .header-search-mobile{ display: none; }

        .catalog-filters{
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            align-items: center;
            margin-bottom: 32px;
        }
        .catalog-filters input[type="search"]{
            flex: 1 1 220px;
            padding: 11px 16px;
            border-radius: 999px;
            border: 1px solid rgba(85,16,29,.16);
            background: #fffdf9;
            font-family: inherit;
            font-size: .9rem;
        }
        .catalog-filters select{
            padding: 11px 16px;
            border-radius: 999px;
            border: 1px solid rgba(85,16,29,.16);
            background: #fffdf9;
            font-family: inherit;
            font-size: .9rem;
            max-width: 220px;
        }
        .catalog-filters input:focus, .catalog-filters select:focus{
            outline: none;
            border-color: var(--gold);
            box-shadow: 0 0 0 3px rgba(233,178,63,.2);
        }

        .main-nav{
            display: flex;
            align-items: center;
            gap: 22px;
        }
        .main-nav > ul{ display: flex; gap: 30px; }
        .main-nav > ul a{
            color: var(--cream-dim);
            font-size: .97rem;
            font-weight: 500;
            padding: 6px 2px;
            border-bottom: 2px solid transparent;
            transition: color .15s ease, border-color .15s ease;
        }
        .main-nav > ul a:hover, .main-nav > ul a:focus-visible{
            color: var(--cream);
            border-color: var(--gold);
        }

        .lang-dropdown{ position: relative; flex-shrink: 0; }
        .lang-toggle{
            display: flex;
            align-items: center;
            gap: 7px;
            background: rgba(246,239,221,.08);
            border: 1px solid rgba(246,239,221,.16);
            color: var(--cream);
            font-size: .82rem;
            font-weight: 600;
            letter-spacing: .02em;
            padding: 9px 14px;
            border-radius: 999px;
            transition: background .15s ease, border-color .15s ease;
        }
        .lang-toggle:hover{ background: rgba(246,239,221,.14); }
        .lang-toggle[aria-expanded="true"]{ background: rgba(246,239,221,.18); border-color: rgba(246,239,221,.32); }
        .lang-flag{
            width: 18px;
            height: 13px;
            border-radius: 2px;
            flex-shrink: 0;
            background-size: cover;
            background-position: center;
            box-shadow: 0 0 0 1px rgba(0,0,0,.15);
        }
        .lang-chevron{ transition: transform .2s ease; color: var(--gold); }
        .lang-toggle[aria-expanded="true"] .lang-chevron{ transform: rotate(180deg); }

        .lang-menu{
            position: absolute;
            top: calc(100% + 8px);
            right: 0;
            min-width: 168px;
            background: var(--maroon-800);
            border: 1px solid rgba(246,239,221,.16);
            border-radius: 12px;
            padding: 6px;
            box-shadow: 0 20px 40px -16px rgba(0,0,0,.55);
            opacity: 0;
            transform: translateY(-6px);
            pointer-events: none;
            transition: opacity .15s ease, transform .15s ease;
            z-index: 60;
        }
        .lang-menu.open{ opacity: 1; transform: translateY(0); pointer-events: auto; }
        .lang-menu li + li{ margin-top: 2px; }
        .lang-menu a{
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            padding: 10px 12px;
            border-radius: 8px;
            font-size: .88rem;
            color: var(--cream-dim);
            border-bottom: 0;
        }
        .lang-menu a:hover, .lang-menu a:focus-visible{ background: rgba(246,239,221,.09); color: var(--cream); }
        .lang-menu a.active{ color: var(--gold); font-weight: 600; }
        .lang-menu a.active::after{ content: '✓'; font-size: .78rem; }
        .lang-option{ display: flex; align-items: center; gap: 9px; }

        .auth-actions{
            display: flex;
            align-items: center;
            gap: 10px;
            flex-shrink: 0;
        }
        .btn-auth{
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 9px 18px;
            border-radius: 999px;
            font-size: .87rem;
            font-weight: 600;
            line-height: 1.3;
            white-space: nowrap;
            border: 1px solid transparent;
            transition: background .15s ease, border-color .15s ease, box-shadow .15s ease, transform .15s ease;
        }
        .btn-auth:hover{ transform: translateY(-1px); }
        .btn-login{
            color: var(--cream);
            border-color: rgba(246,239,221,.35);
            background: transparent;
        }
        .btn-login:hover{ border-color: var(--cream); background: rgba(246,239,221,.06); }
        .btn-register{
            background: var(--gold);
            border-color: var(--gold);
            color: var(--maroon-950);
        }
        .btn-register:hover{ background: #f0c168; box-shadow: 0 8px 18px -8px rgba(233,178,63,.6); }

        .user-dropdown{ position: relative; flex-shrink: 0; }
        .user-toggle{
            display: flex;
            align-items: center;
            gap: 9px;
            background: rgba(246,239,221,.08);
            border: 1px solid rgba(246,239,221,.16);
            color: var(--cream);
            padding: 6px 14px 6px 6px;
            border-radius: 999px;
            transition: background .15s ease, border-color .15s ease;
        }
        .user-toggle:hover{ background: rgba(246,239,221,.14); }
        .user-toggle[aria-expanded="true"]{ background: rgba(246,239,221,.18); border-color: rgba(246,239,221,.32); }
        .user-avatar{
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--gold), var(--gold-dim));
            color: var(--maroon-950);
            font-family: var(--serif);
            font-weight: 700;
            font-size: .85rem;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .user-name{
            font-size: .88rem;
            font-weight: 600;
            max-width: 130px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .user-toggle .lang-chevron{ color: var(--gold); transition: transform .2s ease; }
        .user-toggle[aria-expanded="true"] .lang-chevron{ transform: rotate(180deg); }

        .user-menu{
            position: absolute;
            top: calc(100% + 8px);
            right: 0;
            min-width: 220px;
            background: var(--maroon-800);
            border: 1px solid rgba(246,239,221,.16);
            border-radius: 12px;
            padding: 6px;
            box-shadow: 0 20px 40px -16px rgba(0,0,0,.55);
            opacity: 0;
            transform: translateY(-6px);
            pointer-events: none;
            transition: opacity .15s ease, transform .15s ease;
            z-index: 60;
        }
        .user-menu.open{ opacity: 1; transform: translateY(0); pointer-events: auto; }
        .user-menu-divider{ height: 1px; background: rgba(246,239,221,.12); margin: 6px 4px; }
        .user-menu-link{
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 12px;
            border-radius: 8px;
            font-size: .88rem;
            color: var(--cream-dim);
            white-space: nowrap;
            transition: background .15s ease, color .15s ease;
        }
        .user-menu-link:hover, .user-menu-link:focus-visible{ background: rgba(246,239,221,.09); color: var(--cream); }
        .user-menu-link svg{ flex-shrink: 0; color: var(--gold); }
        .user-menu-logout{
            display: flex;
            align-items: center;
            gap: 10px;
            width: 100%;
            text-align: left;
            background: transparent;
            border: 0;
            padding: 10px 12px;
            border-radius: 8px;
            font-size: .88rem;
            font-family: inherit;
            color: var(--cream-dim);
            cursor: pointer;
            white-space: nowrap;
            transition: background .15s ease, color .15s ease;
        }
        .user-menu-logout svg{ flex-shrink: 0; color: var(--gold); }
        .user-menu-logout:hover{ background: rgba(246,239,221,.09); color: var(--cream); }

        .modal-overlay{
            position: fixed;
            inset: 0;
            background: rgba(20,4,7,.55);
            backdrop-filter: blur(3px);
            -webkit-backdrop-filter: blur(3px);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            opacity: 0;
            pointer-events: none;
            transition: opacity .2s ease;
            z-index: 100;
        }
        .modal-overlay.open{ opacity: 1; pointer-events: auto; }
        .modal-panel{
            position: relative;
            background: var(--cream);
            border-radius: 22px;
            width: 100%;
            max-width: 440px;
            max-height: 90vh;
            overflow-y: auto;
            overflow-x: hidden;
            padding: 34px 30px 30px;
            box-shadow: 0 30px 60px -20px rgba(0,0,0,.5);
            transform: translateY(14px) scale(.98);
            transition: transform .22s cubic-bezier(.2,.8,.2,1), max-width .2s ease;
        }
        .modal-overlay.open .modal-panel{ transform: translateY(0) scale(1); }
        .modal-close{
            position: absolute;
            top: 16px;
            right: 16px;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            border: 0;
            background: rgba(85,16,29,.08);
            color: var(--maroon-900);
            font-size: 1.3rem;
            line-height: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background .15s ease;
        }
        .modal-close:hover{ background: rgba(85,16,29,.16); }
        .modal-title{
            font-family: var(--serif);
            font-weight: 600;
            font-size: 1.5rem;
            text-align: center;
            margin: 0 0 5px;
            color: var(--ink);
        }
        .modal-subtitle{
            text-align: center;
            color: #7a6a5d;
            font-size: .92rem;
            margin: 0 0 18px;
        }

        .modal-tabs{
            display: flex;
            gap: 6px;
            background: rgba(85,16,29,.06);
            padding: 5px;
            border-radius: 999px;
            margin-bottom: 18px;
        }
        .modal-tab{
            flex: 1;
            text-align: center;
            padding: 9px 12px;
            border: 0;
            border-radius: 999px;
            background: transparent;
            font-size: .84rem;
            font-weight: 600;
            color: #8a7a6d;
            transition: background .15s ease, color .15s ease;
        }
        .modal-tab:hover{ color: var(--maroon-800); }
        .modal-tab.active{
            background: var(--maroon-900);
            color: var(--cream);
            box-shadow: 0 4px 10px -4px rgba(85,16,29,.4);
        }

        .modal-form{ display: flex; flex-direction: column; gap: 13px; }

        .modal-form-row{
            display: grid;
            grid-template-columns: minmax(0,1fr) minmax(0,1fr);
            gap: 13px;
        }
        .modal-form label{
            display: flex;
            flex-direction: column;
            gap: 6px;
            font-size: .84rem;
            font-weight: 600;
            color: var(--ink);
            min-width: 0;
        }
        .modal-form input[type="text"],
        .modal-form input[type="email"],
        .modal-form input[type="password"],
        .modal-form input[type="number"],
        .modal-form select,
        .modal-form textarea{
            font-family: inherit;
            font-size: .92rem;
            font-weight: 400;
            padding: 11px 13px;
            border-radius: 12px;
            border: 1px solid rgba(85,16,29,.16);
            background: #fffdf9;
            color: var(--ink);
            width: 100%;
            transition: border-color .15s ease, box-shadow .15s ease;
        }
        .modal-form textarea{ resize: vertical; min-height: 90px; }
        .modal-form input[type="file"]{
            font-size: .82rem;
            color: #7a6a5d;
        }
        .modal-form input[type="file"]::file-selector-button{
            background: var(--maroon-900);
            color: var(--cream);
            border: 0;
            padding: 9px 16px;
            border-radius: 999px;
            font-weight: 600;
            font-size: .82rem;
            margin-right: 10px;
            cursor: pointer;
            transition: background .15s ease;
        }
        .modal-form input[type="file"]::file-selector-button:hover{ background: var(--maroon-800); }
        .modal-form input:focus, .modal-form select:focus, .modal-form textarea:focus{
            outline: none;
            border-color: var(--gold);
            box-shadow: 0 0 0 3px rgba(233,178,63,.22);
        }
        .modal-fieldset{
            border: 1px solid rgba(85,16,29,.14);
            border-radius: 14px;
            padding: 14px 14px 14px;
            display: flex;
            flex-direction: column;
            gap: 12px;
            margin: 0;
        }
        .modal-fieldset legend{
            padding: 0 8px;
            font-size: .78rem;
            font-weight: 700;
            color: var(--maroon-800);
        }
        .modal-fieldset > .modal-form-row{ margin: 0; }

        .modal-panel.modal-panel--wide{ max-width: 760px; padding: 38px 44px 34px; }
        .modal-panel--wide .modal-radio-group{ flex-direction: row; flex-wrap: wrap; }
        .modal-panel--wide .modal-radio-group .modal-radio-card{ flex: 1; min-width: 140px; }

        .modal-panel--wide .modal-radio-group .modal-radio-card[data-cash="1"]{
            flex: 1 1 100%;
            margin-top: 4px;
        }

        .modal-radio-group{ display: flex; flex-direction: column; gap: 10px; }
        .modal-radio-card{
            display: flex;
            align-items: flex-start;
            gap: 10px;
            border: 1px solid rgba(85,16,29,.16);
            border-radius: 12px;
            padding: 12px 14px;
            cursor: pointer;
            transition: border-color .15s ease, background .15s ease;
        }
        .modal-radio-card input{ margin-top: 3px; accent-color: var(--maroon-800); flex-shrink: 0; }
        .modal-radio-card small{ display: block; color: #8a7a6d; font-weight: 400; margin-top: 2px; }
        .modal-radio-card:has(input:checked){
            border-color: var(--maroon-800);
            background: rgba(85,16,29,.06);
        }
        .modal-radio-card.is-disabled{
            cursor: not-allowed;
            opacity: .55;
            background: rgba(85,16,29,.03);
        }
        .modal-radio-card.is-disabled:hover{ border-color: rgba(85,16,29,.16); }
        .modal-badge-soon{
            display: inline-block;
            font-style: normal;
            font-size: .64rem;
            font-weight: 700;
            letter-spacing: .02em;
            text-transform: uppercase;
            color: var(--maroon-800);
            background: rgba(233,178,63,.28);
            padding: 2px 8px;
            border-radius: 999px;
            margin-left: 6px;
            vertical-align: middle;
        }
        .btn-modal-primary{
            background: var(--maroon-900);
            color: var(--cream);
            border: 0;
            padding: 13px;
            border-radius: 999px;
            font-weight: 600;
            font-size: .96rem;
            transition: background .15s ease, transform .15s ease;
        }
        .btn-modal-primary:hover{ background: var(--maroon-800); transform: translateY(-1px); }
        .modal-switch{
            text-align: center;
            font-size: .85rem;
            color: #8a7a6d;
            margin: 16px 0 0;
        }
        .modal-switch a{ color: var(--maroon-800); font-weight: 600; }
        .modal-field-error{
            color: #b3261e;
            font-size: .8rem;
            margin: -8px 0 0;
        }

        .order-book{
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 20px;
        }
        .order-book-cover{
            width: 56px;
            height: 74px;
            border-radius: 10px;
            overflow: hidden;
            flex-shrink: 0;
            background: var(--cream-dim);
        }
        .order-book-cover img{ width: 100%; height: 100%; object-fit: cover; }
        .order-book-seller{ font-size: .82rem; color: #8a7a6d; margin: 0; }
        .order-book-author{ font-size: .82rem; color: #7a6a5d; margin: 2px 0 8px; }
        .order-book-badges{ display: flex; gap: 6px; flex-wrap: wrap; }
        .order-book-description{
            font-size: .9rem;
            line-height: 1.6;
            color: #4a3a30;
            background: rgba(85,16,29,.03);
            border-radius: 12px;
            padding: 12px 14px;
            margin: 0 0 18px;
        }

        .order-login-prompt{ text-align: center; padding: 8px 0 4px; }
        .order-login-prompt p{ color: #6b5a4d; font-size: .92rem; margin: 0 0 16px; }
        .order-login-actions{ display: flex; gap: 10px; justify-content: center; flex-wrap: wrap; }
        .order-login-btn{
            padding: 11px 22px;
            border-radius: 999px;
            font-weight: 600;
            font-size: .9rem;
            transition: background .15s ease, box-shadow .15s ease, transform .15s ease;
        }
        .order-login-btn:hover{ transform: translateY(-1px); }
        .order-login-btn-ghost{
            background: transparent;
            color: var(--maroon-800);
            border: 1px solid rgba(85,16,29,.25);
        }
        .order-login-btn-ghost:hover{ background: rgba(85,16,29,.05); }
        .order-login-btn-primary{
            background: var(--gold);
            color: var(--maroon-950);
            border: 1px solid var(--gold);
        }
        .order-login-btn-primary:hover{ box-shadow: 0 8px 18px -8px rgba(233,178,63,.6); }

        .order-summary{
            background: rgba(85,16,29,.04);
            border-radius: 14px;
            padding: 14px 16px;
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-bottom: 16px;
        }
        .order-summary-row{
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: .9rem;
            color: var(--ink);
        }
        .order-summary-total{
            padding-top: 10px;
            border-top: 1px dashed rgba(85,16,29,.16);
            font-size: 1rem;
        }
        .order-summary-total strong{ color: var(--maroon-800); font-family: var(--serif); font-size: 1.15rem; }

        .order-qty-stepper{
            display: flex;
            align-items: center;
            gap: 0;
            border: 1px solid rgba(85,16,29,.18);
            border-radius: 999px;
            overflow: hidden;
        }
        .order-qty-stepper button{
            width: 32px;
            height: 32px;
            border: 0;
            background: rgba(85,16,29,.06);
            color: var(--maroon-800);
            font-size: 1.1rem;
            line-height: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background .15s ease;
        }
        .order-qty-stepper button:hover{ background: rgba(85,16,29,.12); }
        .order-qty-stepper input{
            width: 44px;
            border: 0;
            text-align: center;
            font-family: inherit;
            font-size: .92rem;
            font-weight: 600;
            color: var(--ink);
            -moz-appearance: textfield;
        }
        .order-qty-stepper input::-webkit-outer-spin-button,
        .order-qty-stepper input::-webkit-inner-spin-button{ -webkit-appearance: none; margin: 0; }

        .order-payment-group .modal-radio-card{ justify-content: center; text-align: center; }
        .order-payment-group .modal-radio-card span{ width: 100%; }

        .order-payment-number{
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            flex-wrap: wrap;
            background: rgba(233,178,63,.14);
            border: 1px dashed var(--gold);
            border-radius: 12px;
            padding: 12px 16px;
            margin: 16px 0;
            font-size: .9rem;
        }
        .order-payment-number > div{ display: flex; flex-direction: column; gap: 3px; }
        .order-payment-owner{ text-align: right; }
        .order-payment-number span{ font-size: .78rem; color: #8a7a6d; }
        .order-payment-number strong{
            font-family: var(--serif);
            font-size: 1.1rem;
            color: var(--maroon-900);
            letter-spacing: .02em;
        }

        .order-reference-field{
            display: flex;
            flex-direction: column;
            gap: 6px;
            font-size: .84rem;
            font-weight: 600;
            color: var(--ink);
            margin-bottom: 4px;
        }
        .order-reference-field input,
        .order-reference-field select{
            font-family: inherit;
            font-size: .92rem;
            font-weight: 400;
            padding: 11px 13px;
            border-radius: 12px;
            border: 1px solid rgba(85,16,29,.16);
            background: #fffdf9;
            color: var(--ink);
            transition: border-color .15s ease, box-shadow .15s ease;
        }
        .order-reference-field select{
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            padding-right: 34px;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='6' viewBox='0 0 10 6' fill='none'%3E%3Cpath d='M1 1L5 5L9 1' stroke='%236c1524' stroke-width='1.6' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 13px center;
            cursor: pointer;
        }
        .order-reference-field input:focus,
        .order-reference-field select:focus{
            outline: none;
            border-color: var(--gold);
            box-shadow: 0 0 0 3px rgba(233,178,63,.22);
        }
        .order-reference-field input.has-error{ border-color: #b3261e; }

        .order-static-note{
            text-align: center;
            font-size: .78rem;
            color: #9c8b7d;
            margin: 12px 0 0;
        }

        @media (max-width: 480px){
            .order-summary-row{ font-size: .86rem; }
        }

        @media (max-height: 700px){
            .modal-overlay{ align-items: flex-start; padding-top: 24px; padding-bottom: 24px; }
            .modal-panel{ max-height: calc(100vh - 48px); }
        }

        .menu-toggle{
            display: none;
            border: 0;
            background: transparent;
            width: 38px;
            height: 32px;
            padding: 0;
            position: relative;
            flex-shrink: 0;
        }
        .menu-toggle span, .menu-toggle span::before, .menu-toggle span::after{
            content: '';
            position: absolute;
            left: 4px;
            right: 4px;
            height: 2px;
            background: var(--cream);
            transition: transform .2s ease, opacity .2s ease;
        }
        .menu-toggle span{ top: 15px; }
        .menu-toggle span::before{ top: -8px; }
        .menu-toggle span::after{ top: 8px; }
        .menu-toggle[aria-expanded="true"] span{ background: transparent; }
        .menu-toggle[aria-expanded="true"] span::before{ transform: translateY(8px) rotate(45deg); }
        .menu-toggle[aria-expanded="true"] span::after{ transform: translateY(-8px) rotate(-45deg); }

        .hero{
            position: relative;
            background-image:
                linear-gradient(180deg, rgba(61,11,21,.72) 0%, rgba(61,11,21,.85) 100%),
                url('{{ asset('hero.jpg') }}');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            color: var(--cream);
            padding: 90px 0 0;
            overflow: hidden;
        }
        .hero .wrap{
            display: flex;
            flex-direction: column;
        }
        .hero-inner{
            max-width: 640px;
        }
        .hero-eyebrow{
            font-family: var(--serif);
            font-style: italic;
            font-size: 1.05rem;
            color: var(--green-300);
            margin: 0 0 18px;
        }
        .hero h1{
            font-family: var(--serif);
            font-weight: 600;
            font-size: clamp(2.1rem, 5.5vw, 3.6rem);
            line-height: 1.08;
            margin: 0 0 22px;
            max-width: 15ch;
        }
        .hero p{
            font-size: clamp(.98rem, 1.6vw, 1.08rem);
            line-height: 1.6;
            color: var(--cream-dim);
            max-width: 46ch;
            margin: 0 0 32px;
        }
        .hero-actions{
            display: flex;
            flex-wrap: wrap;
            gap: 14px;
            margin-bottom: 56px;
        }
        .btn{
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 14px 26px;
            border-radius: 999px;
            font-weight: 600;
            font-size: .97rem;
            transition: transform .15s ease, background .15s ease, box-shadow .15s ease;
            border: 1px solid transparent;
            white-space: nowrap;
        }
        .btn:hover{ transform: translateY(-1px); }
        .btn-primary{
            background: var(--gold);
            color: var(--maroon-950);
        }
        .btn-primary:hover{ box-shadow: 0 10px 24px -8px rgba(233,178,63,.55); }
        .btn-ghost{
            background: rgba(61,11,21,.25);
            border-color: rgba(246,239,221,.4);
            color: var(--cream);
        }
        .btn-ghost:hover{ border-color: var(--cream); }

        .ribbon-divider{
            display: block;
            width: 100%;
            height: auto;
        }

        section{ padding: 76px 0; }
        .section-head{
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 20px;
            margin-bottom: 40px;
            flex-wrap: wrap;
        }
        .section-head h2{
            font-family: var(--serif);
            font-weight: 600;
            font-size: clamp(1.6rem, 3.2vw, 2.3rem);
            margin: 0 0 6px;
            color: var(--maroon-900);
        }
        .section-head p{
            margin: 0;
            color: #6b5a4d;
            font-size: 1rem;
            max-width: 52ch;
        }
        .see-all{
            font-weight: 600;
            font-size: .93rem;
            color: var(--maroon-800);
            border-bottom: 1px solid var(--maroon-800);
            padding-bottom: 2px;
            white-space: nowrap;
        }

        .book-grid{
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(210px, 1fr));
            gap: 26px;
        }
        .book-card{
            position: relative;
            background: #fffdf7;
            border-radius: 20px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            box-shadow: 0 1px 2px rgba(61,11,21,.06), 0 12px 24px -18px rgba(61,11,21,.25);
            transition: transform .25s cubic-bezier(.2,.8,.2,1), box-shadow .25s ease;
        }
        .book-card::after{
            content: '';
            position: absolute;
            inset: 0;
            border-radius: inherit;
            box-shadow: inset 0 0 0 1.5px rgba(233,178,63,.65);
            opacity: 0;
            transition: opacity .25s ease;
            pointer-events: none;
        }
        .book-card:hover{
            transform: translateY(-6px);
            box-shadow: 0 24px 40px -20px rgba(61,11,21,.4);
        }
        .book-card:hover::after{ opacity: 1; }

        .book-cover{
            position: relative;
            aspect-ratio: 3/4;
            overflow: hidden;
            background: var(--cream-dim);
        }
        .book-cover img{
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform .5s cubic-bezier(.2,.8,.2,1);
        }
        .book-card:hover .book-cover img{ transform: scale(1.08); }
        .book-cover-gradient{
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, rgba(0,0,0,0) 55%, rgba(20,4,7,.55) 100%);
            pointer-events: none;
        }

        .book-tag{
            position: absolute;
            top: 12px;
            left: 12px;
            font-size: .68rem;
            font-weight: 700;
            letter-spacing: .02em;
            padding: 5px 11px;
            border-radius: 999px;
            background: rgba(92,138,55,.95);
            color: var(--cream);
            box-shadow: 0 4px 10px -4px rgba(0,0,0,.4);
        }
        .book-tag.occasion{ background: rgba(233,178,63,.95); color: #4a3208; }

        .book-wishlist{
            position: absolute;
            top: 10px;
            right: 10px;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: rgba(255,253,247,.92);
            color: var(--maroon-800);
            border: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background .18s ease, color .18s ease, transform .18s ease;
        }
        .book-wishlist:hover{ background: var(--gold); color: var(--maroon-950); transform: scale(1.08); }

        .book-price-float{
            position: absolute;
            left: 12px;
            bottom: 12px;
            font-family: var(--serif);
            font-weight: 600;
            font-size: .96rem;
            color: var(--cream);
            background: rgba(61,11,21,.55);
            backdrop-filter: blur(6px);
            -webkit-backdrop-filter: blur(6px);
            padding: 6px 13px;
            border-radius: 999px;
            box-shadow: 0 6px 14px -6px rgba(0,0,0,.5);
        }

        .book-out-of-stock{
            position: absolute;
            top: 12px;
            left: 12px;
            z-index: 2;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: .68rem;
            font-weight: 700;
            letter-spacing: .02em;
            padding: 5px 11px;
            border-radius: 999px;
            background: rgba(179,38,30,.92);
            color: #fff;
            box-shadow: 0 4px 10px -4px rgba(0,0,0,.4);
        }
        .book-card.is-out-of-stock .book-cover img{ filter: grayscale(.5); opacity: .7; }

        .book-quickview{
            position: absolute;
            right: 12px;
            bottom: 12px;
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: .72rem;
            font-weight: 700;
            color: var(--maroon-950);
            background: var(--gold);
            padding: 7px 13px;
            border-radius: 999px;
            border: 0;
            opacity: 0;
            transform: translateY(8px);
            transition: opacity .2s ease, transform .2s ease;
        }
        .book-card:hover .book-quickview{ opacity: 1; transform: translateY(0); }

        .book-body{
            padding: 16px 18px 18px;
            display: flex;
            flex-direction: column;
        }
        .book-genre{
            display: inline-block;
            align-self: flex-start;
            font-size: .68rem;
            font-weight: 700;
            letter-spacing: .04em;
            text-transform: uppercase;
            color: var(--green-700);
            background: rgba(92,138,55,.12);
            padding: 3px 10px;
            border-radius: 999px;
            margin-bottom: 9px;
        }
        .book-title{
            font-family: var(--serif);
            font-size: 1.05rem;
            line-height: 1.25;
            margin: 0 0 4px;
            color: var(--ink);
        }
        .book-author{
            font-size: .86rem;
            color: #7a6a5d;
            margin: 0 0 4px;
        }
        .book-seller{
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: .76rem;
            color: #b09c8c;
            margin: 0 0 8px;
        }
        .book-seller svg{ flex-shrink: 0; color: var(--gold-dim); }

        .book-delivery-badge{
            display: inline-block;
            font-size: .72rem;
            font-weight: 600;
            color: var(--green-700);
            background: rgba(92,138,55,.1);
            padding: 3px 10px;
            border-radius: 999px;
            margin: 0 0 14px;
        }

        .book-foot{
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            padding-top: 12px;
            border-top: 1px dashed rgba(85,16,29,.14);
        }
        .book-loc{
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: .78rem;
            color: #96897d;
            white-space: nowrap;
        }
        .book-loc svg{ flex-shrink: 0; color: var(--maroon-700); }
        .book-add{
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: var(--maroon-900);
            color: var(--cream);
            border: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            transition: background .18s ease, transform .25s ease;
        }
        .book-add:hover{ background: var(--green-700); transform: rotate(90deg); }

        .flash-success{
            background: rgba(92,138,55,.12);
            border: 1px solid rgba(92,138,55,.35);
            color: var(--green-700);
            font-size: .88rem;
            font-weight: 600;
            padding: 12px 16px;
            border-radius: 12px;
            margin: 0 0 24px;
        }
        .flash-error{
            background: rgba(179,38,30,.09);
            border: 1px solid rgba(179,38,30,.3);
            color: #b3261e;
            font-size: .88rem;
            font-weight: 600;
            padding: 12px 16px;
            border-radius: 12px;
            margin: 0 0 24px;
        }

        .order-status-badge{
            display: inline-block;
            font-size: .72rem;
            font-weight: 700;
            letter-spacing: .02em;
            padding: 4px 11px;
            border-radius: 999px;
            white-space: nowrap;
        }
        .order-status-en_attente_livraison{ background: rgba(233,178,63,.2); color: #8a5f14; }
        .order-status-en_livraison{ background: rgba(60,110,200,.14); color: #2c4f8a; }
        .order-status-livree{ background: rgba(92,138,55,.14); color: var(--green-700); }
        .order-status-annulee{ background: rgba(179,38,30,.1); color: #b3261e; }
        .add-book-card{
            background: #fffdf7;
            border: 1px solid rgba(85,16,29,.09);
            border-radius: 20px;
            padding: 28px 26px;
            margin-bottom: 44px;
            max-width: 560px;
        }
        .add-book-title{
            font-family: var(--serif);
            font-size: 1.2rem;
            color: var(--maroon-900);
            margin: 0 0 18px;
        }
        .add-book-card .field-hint{
            font-size: .74rem;
            color: #9c8b7d;
            margin: -8px 0 0;
        }
        .book-delete-form{ position: absolute; top: 10px; right: 10px; z-index: 2; }
        .book-delete-form .book-wishlist{ position: static; }

        .profile-identity{
            background: #fffdf7;
            border: 1px solid rgba(85,16,29,.09);
            border-radius: 20px;
            padding: 26px;
            margin-bottom: 34px;
        }
        .profile-identity-head{ display: flex; align-items: center; gap: 16px; flex-wrap: wrap; }
        .profile-identity-avatar{ width: 56px; height: 56px; font-size: 1.3rem; flex-shrink: 0; }
        .profile-info-grid{
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(190px, 1fr));
            gap: 18px;
            margin-top: 24px;
            padding-top: 22px;
            border-top: 1px solid rgba(85,16,29,.08);
        }
        .profile-info-grid p{ margin: 0; }

        .profile-section-title{
            font-family: var(--serif);
            font-size: 1.15rem;
            color: var(--maroon-900);
            margin: 0 0 16px;
        }
        .profile-stat-grid{
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(190px, 1fr));
            gap: 14px;
            margin-bottom: 36px;
        }
        .profile-stat-card{
            background: #fffdf7;
            border: 1px solid rgba(85,16,29,.09);
            border-radius: 16px;
            padding: 18px 20px;
            display: flex;
            flex-direction: column;
            gap: 4px;
        }
        .profile-stat-label{
            font-size: .72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .03em;
            color: #9c8b7d;
        }
        .profile-stat-value{
            font-family: var(--serif);
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--ink);
            line-height: 1.2;
            word-break: break-word;
        }
        .profile-stat-sub{ font-size: .76rem; color: #96897d; }
        .profile-stat-card-pending{
            background: linear-gradient(135deg, rgba(233,178,63,.16), rgba(233,178,63,.04));
            border-color: rgba(233,178,63,.42);
        }
        .profile-stat-card-pending .profile-stat-value{ color: var(--maroon-800); }
        .profile-stat-card-paid{
            background: linear-gradient(135deg, rgba(92,138,55,.14), rgba(92,138,55,.03));
            border-color: rgba(92,138,55,.34);
        }
        .profile-stat-card-paid .profile-stat-value{ color: var(--green-700); }

        .profile-tabs{
            display: flex;
            gap: 8px;
            margin-bottom: 26px;
            border-bottom: 1px solid rgba(85,16,29,.12);
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: none;
        }
        .profile-tabs::-webkit-scrollbar{ display: none; }
        .profile-tab{
            display: inline-flex;
            align-items: center;
            gap: 8px;
            flex-shrink: 0;
            border: 0;
            background: none;
            font-family: inherit;
            font-size: .92rem;
            font-weight: 600;
            color: #8a7a6d;
            padding: 12px 16px;
            border-bottom: 3px solid transparent;
            margin-bottom: -1px;
            cursor: pointer;
            white-space: nowrap;
            transition: color .15s ease, border-color .15s ease;
        }
        .profile-tab:hover{ color: var(--maroon-800); }
        .profile-tab.active{ color: var(--maroon-900); border-bottom-color: var(--gold); }
        .profile-tab-count{
            font-size: .72rem;
            font-weight: 700;
            background: rgba(85,16,29,.08);
            color: var(--maroon-800);
            padding: 2px 8px;
            border-radius: 999px;
        }
        .profile-tab.active .profile-tab-count{ background: rgba(233,178,63,.28); }

        .profile-panel{ display: none; }
        .profile-panel.active{ display: block; animation: profileFade .18s ease; }
        @keyframes profileFade{ from{ opacity: 0; transform: translateY(4px); } to{ opacity: 1; transform: none; } }
        .profile-panel .add-book-card{ max-width: 620px; }

        @media (max-width: 600px){
            .profile-identity{ padding: 20px; }
            .profile-stat-grid{ grid-template-columns: 1fr 1fr; gap: 10px; }
            .profile-stat-card{ padding: 14px; }
            .profile-stat-value{ font-size: 1.2rem; }
            .profile-tab{ padding: 11px 12px; font-size: .86rem; }
        }
        @media (max-width: 380px){
            .profile-stat-grid{ grid-template-columns: 1fr; }
        }

        .table-scroll{ overflow-x: auto; border-radius: 16px; border: 1px solid rgba(85,16,29,.09); }
        .seller-table{
            width: 100%;
            border-collapse: collapse;
            background: #fffdf7;
            font-size: .88rem;
        }
        .seller-table thead th{
            text-align: left;
            font-size: .72rem;
            font-weight: 700;
            letter-spacing: .04em;
            text-transform: uppercase;
            color: #8a7a6d;
            padding: 12px 16px;
            border-bottom: 1px solid rgba(85,16,29,.1);
            white-space: nowrap;
        }
        .seller-table tbody td{
            padding: 10px 16px;
            border-bottom: 1px solid rgba(85,16,29,.06);
            vertical-align: middle;
        }
        .seller-table-row{ cursor: pointer; transition: background .12s ease; }
        .seller-table-row:hover{ background: rgba(233,178,63,.08); }
        .seller-table-row:last-child td{ border-bottom: 0; }
        .seller-table-thumb{ width: 52px; }
        .seller-table-thumb img{
            width: 44px; height: 58px; object-fit: cover; border-radius: 6px;
        }
        .seller-table-thumb-empty{
            width: 44px; height: 58px; border-radius: 6px; background: var(--cream-dim);
        }
        .seller-table-sub{ font-size: .78rem; color: #96897d; }
        .seller-table-actions{ display: flex; gap: 12px; white-space: nowrap; }
        .table-action-link{
            background: none; border: 0; padding: 0; font: inherit;
            color: var(--maroon-800); font-weight: 600; font-size: .82rem; cursor: pointer;
        }
        .table-action-link:hover{ text-decoration: underline; }
        .table-action-danger{ color: #b3261e; }

        .pager{
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 16px;
            margin-top: 22px;
        }
        .pager-btn{
            font-size: .85rem;
            font-weight: 600;
            color: var(--maroon-800);
            padding: 8px 14px;
            border-radius: 999px;
            border: 1px solid rgba(85,16,29,.16);
            transition: background .15s ease;
        }
        .pager-btn:hover{ background: rgba(85,16,29,.06); }
        .pager-btn.disabled{ color: #b8a99b; border-color: rgba(85,16,29,.08); pointer-events: none; }
        .pager-info{ font-size: .82rem; color: #8a7a6d; }

        .seller-grid-light{
            grid-template-columns: repeat(auto-fill, 260px);
            justify-content: start;
        }
        .seller-grid-light .seller-card{
            background: #fffdf7;
            border: 1px solid rgba(85,16,29,.09);
            transition: background .15s ease, border-color .15s ease, transform .15s ease;
        }
        .seller-grid-light .seller-card:hover{
            background: #fffdf7;
            border-color: var(--gold);
            transform: translateY(-3px);
        }
        .seller-grid-light .seller-name{ color: var(--ink); }
        .seller-grid-light .seller-meta{ color: #7a6a5d; }
        .seller-grid-light .seller-stats{ color: var(--maroon-800); }

        .page-banner{
            background: linear-gradient(135deg, var(--maroon-800), var(--maroon-950));
            color: var(--cream);
            padding: 64px 0 52px;
        }
        .page-banner-eyebrow{
            font-family: var(--serif);
            font-style: italic;
            color: var(--green-300);
            font-size: 1rem;
            margin: 0 0 14px;
        }
        .page-banner h1{
            font-family: var(--serif);
            font-weight: 600;
            font-size: clamp(1.9rem, 4vw, 2.7rem);
            margin: 0 0 14px;
            max-width: 20ch;
        }
        .page-banner p{
            color: var(--cream-dim);
            font-size: 1.02rem;
            max-width: 60ch;
            margin: 0 0 10px;
            line-height: 1.6;
        }
        .page-banner-meta{
            display: inline-block;
            font-size: .8rem;
            color: rgba(246,239,221,.6);
            margin-top: 8px;
        }
        .page-banner-actions{ display: flex; flex-wrap: wrap; gap: 14px; margin-top: 26px; }

        .content-card{
            background: #fffdf7;
            border: 1px solid rgba(85,16,29,.09);
            border-radius: 20px;
            padding: 28px 30px;
            margin-bottom: 22px;
        }
        .content-card h3{
            font-family: var(--serif);
            font-size: 1.2rem;
            color: var(--maroon-900);
            margin: 0 0 14px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .content-card p{ color: #4a3a30; line-height: 1.7; margin: 0 0 12px; }
        .content-card p:last-child{ margin-bottom: 0; }
        .content-list{ margin: 0; padding: 0; display: flex; flex-direction: column; gap: 9px; }
        .content-list li{
            list-style: none;
            padding-left: 22px;
            position: relative;
            color: #4a3a30;
            line-height: 1.6;
        }
        .content-list li::before{
            content: '';
            position: absolute;
            left: 0;
            top: 9px;
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: var(--gold);
        }

        .numbered-card{ display: flex; gap: 22px; }
        .numbered-card .step-number{
            font-family: var(--serif);
            font-weight: 600;
            font-size: 1.3rem;
            color: var(--gold-dim);
            flex-shrink: 0;
            line-height: 1;
        }
        .numbered-card .content-card{ flex: 1; margin-bottom: 0; }

        .chip-list{ display: flex; flex-wrap: wrap; gap: 10px; margin-top: 6px; }
        .chip{
            display: inline-block;
            background: rgba(92,138,55,.1);
            color: var(--green-700);
            font-size: .82rem;
            font-weight: 600;
            padding: 7px 15px;
            border-radius: 999px;
        }

        .feature-grid, .steps-grid{
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 22px;
        }
        .feature-card, .step-card{
            background: #fffdf7;
            border: 1px solid rgba(85,16,29,.09);
            border-radius: 18px;
            padding: 26px;
        }
        .step-card .step-number{
            font-family: var(--serif);
            font-weight: 700;
            font-size: 1.6rem;
            color: rgba(85,16,29,.18);
            margin-bottom: 8px;
        }
        .feature-card h4, .step-card h4{
            font-family: var(--serif);
            font-size: 1.05rem;
            color: var(--maroon-900);
            margin: 0 0 8px;
        }
        .feature-card p, .step-card p{ color: #6b5a4d; font-size: .92rem; line-height: 1.6; margin: 0; }

        .stat-grid{ display: flex; flex-wrap: wrap; gap: 40px; margin-top: 22px; }
        .stat-item .stat-num{
            font-family: var(--serif);
            font-weight: 700;
            font-size: 2.1rem;
            color: var(--maroon-800);
            line-height: 1;
        }
        .stat-item .stat-label{ font-size: .85rem; color: #8a7a6d; margin-top: 6px; }

        .contact-grid{
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 18px;
            margin-top: 8px;
        }
        .contact-item{ display: flex; align-items: flex-start; gap: 12px; }
        .contact-item svg{ color: var(--maroon-800); flex-shrink: 0; margin-top: 3px; }
        .contact-item a, .contact-item span{ color: #4a3a30; font-size: .92rem; line-height: 1.5; }
        .contact-item a:hover{ color: var(--maroon-800); text-decoration: underline; }

        .sellers{ background: var(--maroon-950); color: var(--cream); }
        .sellers .section-head h2{ color: var(--cream); }
        .sellers .section-head p{ color: rgba(246,239,221,.65); }
        .sellers .see-all{ color: var(--gold); border-color: var(--gold); }

        .seller-grid{
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
            gap: 22px;
        }
        .seller-card{
            background: rgba(246,239,221,.05);
            border: 1px solid rgba(246,239,221,.14);
            border-radius: var(--radius);
            padding: 26px;
            display: flex;
            gap: 16px;
            align-items: flex-start;
            transition: background .18s ease, border-color .18s ease;
        }
        .seller-card:hover{
            background: rgba(246,239,221,.09);
            border-color: rgba(246,239,221,.3);
        }
        .seller-avatar{
            width: 52px;
            height: 52px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--green-500), var(--green-700));
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: var(--serif);
            font-weight: 600;
            font-size: 1.1rem;
            flex-shrink: 0;
        }
        .seller-name{
            font-family: var(--serif);
            font-size: 1.05rem;
            margin: 0 0 4px;
        }
        .seller-meta{
            font-size: .85rem;
            color: rgba(246,239,221,.6);
            margin: 0 0 10px;
        }
        .seller-stats{
            display: flex;
            gap: 16px;
            font-size: .8rem;
            color: var(--gold);
        }

        .cta-band{
            background: linear-gradient(120deg, var(--green-700), var(--green-500));
            color: var(--cream);
            border-radius: 20px;
            margin: 0 auto 0;
            padding: 48px 44px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 30px;
            flex-wrap: wrap;
        }
        .cta-band h3{
            font-family: var(--serif);
            font-weight: 600;
            font-size: clamp(1.4rem, 2.4vw, 1.9rem);
            margin: 0 0 8px;
            max-width: 22ch;
        }
        .cta-band p{
            margin: 0;
            color: rgba(246,239,221,.85);
            max-width: 44ch;
        }
        .cta-band .btn-primary{
            background: var(--cream);
            color: var(--green-700);
        }

        footer{
            background: var(--maroon-950);
            color: rgba(246,239,221,.7);
            padding: 48px 0 28px;
            margin-top: 76px;
        }
        footer .foot-top{
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 24px;
            flex-wrap: wrap;
            padding-bottom: 28px;
            border-bottom: 1px solid rgba(246,239,221,.12);
        }
        footer .foot-brand{
            display: flex;
            align-items: center;
            gap: 10px;
        }
        footer .foot-brand img{ height: 30px; }
        footer .foot-brand span{
            font-family: var(--serif);
            font-size: 1.05rem;
            color: var(--cream);
        }
        footer .foot-links{
            display: flex;
            gap: 26px;
            flex-wrap: wrap;
        }
        footer .foot-links a{
            font-size: .88rem;
            color: rgba(246,239,221,.7);
            transition: color .15s ease;
        }
        footer .foot-links a:hover{ color: var(--gold); }
        footer .foot-bottom{
            padding-top: 22px;
            font-size: .83rem;
            line-height: 1.7;
            max-width: 68ch;
        }

        @media (max-width: 980px){
            .seller-grid{ grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); }
            .hero{ padding-top: 72px; }
        }

        @media (max-width: 900px){
            .main-nav{
                position: fixed;
                inset: 68px 12px auto 12px;
                background: var(--maroon-800);
                border: 1px solid rgba(246,239,221,.14);
                border-radius: 16px;
                box-shadow: 0 20px 40px -16px rgba(0,0,0,.5);
                flex-direction: column;
                align-items: stretch;
                gap: 6px;
                padding: 14px;
                transform: translateY(-8px);
                opacity: 0;
                pointer-events: none;
                transition: opacity .18s ease, transform .18s ease;
                z-index: 50;
            }
            .main-nav.open{
                opacity: 1;
                pointer-events: auto;
                transform: translateY(0);
            }
            .main-nav > ul{ flex-direction: column; gap: 2px; }
            .main-nav > ul a{
                display: block;
                padding: 13px 10px;
                font-size: 1.02rem;
                border-bottom: 1px solid rgba(246,239,221,.1);
            }
            .main-nav .lang-dropdown{ width: 100%; margin-top: 10px; }
            .main-nav .lang-toggle{ width: 100%; justify-content: space-between; }
            .main-nav .lang-menu{ right: 0; left: auto; }

            .main-nav .user-dropdown{ width: 100%; margin-top: 10px; }
            .main-nav .user-toggle{ width: 100%; justify-content: space-between; }
            .main-nav .user-name{
                max-width: none;
                flex: 1;
                text-align: left;
                margin-left: 2px;
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
                min-width: 0;
            }
            .main-nav .user-menu{ right: 0; left: auto; }
            .main-nav .lang-menu a, .main-nav .user-menu-link, .main-nav .user-menu-logout{
                padding: 13px 14px;
                font-size: .92rem;
            }

            .main-nav .auth-actions{
                flex-direction: column;
                width: 100%;
                gap: 8px;
                margin-top: 14px;
                padding-top: 14px;
                border-top: 1px solid rgba(246,239,221,.12);
            }
            .main-nav .btn-auth{ width: 100%; text-align: center; padding: 12px; }

            .menu-toggle{ display: block; }

            .book-grid{ grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 16px; }
            .book-quickview, .book-price-float{ opacity: 1; transform: none; }
            .seller-grid{ grid-template-columns: 1fr; }
            .cta-band{ padding: 30px 22px; }
            section{ padding: 52px 0; }
            .hero{ padding-top: 58px; }
            .header-search{ display: none; }
            .header-search-mobile{ display: flex; flex: none; width: 100%; margin: 0 0 12px; max-width: none; }
        }

        @media (max-width: 480px){
            .wrap{ padding: 0 16px; }
            .seller-grid-light{ grid-template-columns: 1fr; }
            .hero h1{ max-width: 100%; }
            .hero p{ max-width: 100%; }
            .hero-actions .btn{ flex: 1 1 auto; justify-content: center; }
            .book-grid{ grid-template-columns: repeat(2, 1fr); gap: 12px; }
            .book-body{ padding: 12px 14px 16px; }
            .book-title{ font-size: .96rem; }
            .book-quickview{ padding: 6px 10px; font-size: .66rem; }
            .book-price-float{ font-size: .84rem; padding: 5px 10px; }
            .cta-band{ flex-direction: column; align-items: flex-start; }

            .modal-overlay{ padding: 12px; align-items: flex-end; }
            .modal-panel{ padding: 26px 20px 22px; border-radius: 20px 20px 0 0; max-height: 92vh; }
            .modal-tabs{ gap: 4px; }
            .modal-tab{ padding: 9px 8px; font-size: .8rem; }
        }

        @media (max-width: 600px){
            .modal-form-row{ grid-template-columns: 1fr; gap: 16px; }

            .numbered-card{ flex-direction: column; gap: 10px; }
            .numbered-card .step-number{ font-size: 1.15rem; }

            .catalog-filters{ flex-direction: column; align-items: stretch; }
            .catalog-filters select{ max-width: none; }

            .stat-grid{ gap: 24px; }

            .content-card, .feature-card, .step-card{ padding: 20px; }
            .page-banner{ padding: 48px 0 40px; }

            .seller-table th:nth-child(3), .seller-table td:nth-child(3){ display: none; }
        }

        @media (max-width: 360px){
            .book-grid{ grid-template-columns: 1fr 1fr; gap: 10px; }
            .book-quickview{ display: none; }
        }

        @media (prefers-reduced-motion: reduce){
            *{ animation: none !important; transition: none !important; }
            html{ scroll-behavior: auto; }
        }
        :focus-visible{
            outline: 2px solid var(--gold);
            outline-offset: 2px;
        }

.commission-sticker{
    display: inline-flex;
    align-items: center;
    gap: 10px;
    background: var(--gold);
    color: var(--maroon-950);
    padding: 10px 20px 10px 16px;
    border-radius: 10px;
    font-size: .88rem;
    box-shadow: 0 10px 20px -8px rgba(233,178,63,.55), 0 2px 0 rgba(0,0,0,.08);
    transform: rotate(-1.5deg);
    margin-bottom: 26px;
    position: relative;
}
.commission-sticker-pin{
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: var(--maroon-800);
    box-shadow: 0 0 0 2px rgba(255,255,255,.5);
    flex-shrink: 0;
}
.commission-sticker-label{
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: .02em;
    font-size: .74rem;
}
.commission-sticker-rate{
    font-family: var(--serif);
    font-weight: 700;
    font-size: 1.15rem;
    margin-left: 2px;
}

@media (max-width: 480px){
    .commission-sticker{ font-size: .8rem; padding: 9px 16px 9px 14px; }
    .commission-sticker-rate{ font-size: 1.02rem; }
}

.commission-sticker-tiers{
    flex-wrap: wrap;
    row-gap: 4px;
}
.commission-sticker-tier{
    font-size: .82rem;
    padding-left: 10px;
    border-left: 1px solid rgba(61,11,21,.25);
}
.commission-sticker-tier strong{
    font-family: var(--serif);
}
@media (max-width: 480px){
    .commission-sticker-tiers{ flex-direction: column; align-items: flex-start; }
    .commission-sticker-tier{ border-left: 0; padding-left: 0; }
}

.foot-newsletter{ flex: 1 1 260px; max-width: 340px; }
.foot-newsletter-title{
    font-family: var(--serif);
    font-size: 1rem;
    color: var(--cream);
    margin: 0 0 4px;
}
.foot-newsletter-subtitle{
    font-size: .82rem;
    color: rgba(246,239,221,.6);
    margin: 0 0 12px;
}
.foot-newsletter-form{ display: flex; gap: 8px; }
.foot-newsletter-form input{
    flex: 1;
    min-width: 0;
    font-family: inherit;
    font-size: .86rem;
    padding: 9px 13px;
    border-radius: 999px;
    border: 1px solid rgba(246,239,221,.2);
    background: rgba(246,239,221,.06);
    color: var(--cream);
}
.foot-newsletter-form input::placeholder{ color: rgba(246,239,221,.45); }
.foot-newsletter-form input:focus{
    outline: none;
    border-color: var(--gold);
    background: rgba(246,239,221,.1);
}
.foot-newsletter-form button{
    flex-shrink: 0;
    background: var(--gold);
    color: var(--maroon-950);
    border: 0;
    padding: 9px 16px;
    border-radius: 999px;
    font-weight: 600;
    font-size: .84rem;
    transition: background .15s ease;
}
.foot-newsletter-form button:hover{ background: #f0c168; }
.foot-newsletter-flash{
    font-size: .78rem;
    color: var(--green-300);
    margin: 0 0 10px;
}
.foot-newsletter-flash-error{ color: #e79a94; }

@media (max-width: 600px){
    .foot-newsletter{ max-width: none; width: 100%; }
    .foot-newsletter-form{ flex-direction: column; }
    .foot-newsletter-form button{ width: 100%; }
}

.btn-danger-outline{
    background: transparent;
    color: #b3261e;
    border: 1px solid rgba(179,38,30,.35);
    padding: 12px 20px;
    border-radius: 999px;
    font-weight: 600;
    font-size: .92rem;
    transition: background .15s ease, transform .15s ease;
}
.btn-danger-outline:hover{ background: rgba(179,38,30,.07); transform: translateY(-1px); }

    </style>
    @stack('styles')
    @stack('head')
</head>
<body>

    @include('partials.header')

    @yield('content')

    @include('partials.footer')

    @include('partials.order-modal')

    <script>
        (function(){
            var toggle = document.getElementById('menuToggle');
            var nav = document.getElementById('mainNav');
            toggle.addEventListener('click', function(){
                var open = nav.classList.toggle('open');
                toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
            });
            nav.querySelectorAll('a').forEach(function(a){
                a.addEventListener('click', function(){
                    nav.classList.remove('open');
                    toggle.setAttribute('aria-expanded', 'false');
                });
            });
            document.addEventListener('click', function(e){
                if (!nav.classList.contains('open')) return;
                if (nav.contains(e.target) || toggle.contains(e.target)) return;
                nav.classList.remove('open');
                toggle.setAttribute('aria-expanded', 'false');
            });

            var langToggle = document.getElementById('langToggle');
            var langMenu = document.getElementById('langMenu');
            if (langToggle && langMenu) {
                langToggle.addEventListener('click', function(){
                    var open = langMenu.classList.toggle('open');
                    langToggle.setAttribute('aria-expanded', open ? 'true' : 'false');
                });
                document.addEventListener('click', function(e){
                    if (!langMenu.classList.contains('open')) return;
                    if (langMenu.contains(e.target) || langToggle.contains(e.target)) return;
                    langMenu.classList.remove('open');
                    langToggle.setAttribute('aria-expanded', 'false');
                });
                document.addEventListener('keydown', function(e){
                    if (e.key === 'Escape') {
                        langMenu.classList.remove('open');
                        langToggle.setAttribute('aria-expanded', 'false');
                    }
                });
            }

            var userToggle = document.getElementById('userToggle');
            var userMenu = document.getElementById('userMenu');
            if (userToggle && userMenu) {
                userToggle.addEventListener('click', function(){
                    var open = userMenu.classList.toggle('open');
                    userToggle.setAttribute('aria-expanded', open ? 'true' : 'false');
                });
                document.addEventListener('click', function(e){
                    if (!userMenu.classList.contains('open')) return;
                    if (userMenu.contains(e.target) || userToggle.contains(e.target)) return;
                    userMenu.classList.remove('open');
                    userToggle.setAttribute('aria-expanded', 'false');
                });
            }

            // ---- modals connexion / inscription ----
            var authOverlay = document.getElementById('authModalOverlay');
            var authPanel = document.getElementById('authModalPanel');
            var authClose = document.getElementById('authModalClose');
            var authViews = {
                login: document.getElementById('viewLogin'),
                registerClient: document.getElementById('viewRegisterClient'),
                registerSeller: document.getElementById('viewRegisterSeller')
            };

            function openAuthModal(view){
                if (!authOverlay) return;
                var orderOverlayEl = document.getElementById('orderModalOverlay');
                if (orderOverlayEl) {
                    orderOverlayEl.classList.remove('open');
                }
                Object.keys(authViews).forEach(function(key){
                    if (!authViews[key]) return;
                    authViews[key].hidden = (key !== view);
                });
                document.querySelectorAll('.modal-tab').forEach(function(tab){
                    var isActive = tab.getAttribute('data-auth-switch') === view;
                    tab.classList.toggle('active', isActive);
                    tab.setAttribute('aria-selected', isActive ? 'true' : 'false');
                });
                if (authPanel) {
                    authPanel.classList.toggle('modal-panel--wide', view !== 'login');
                }
                authOverlay.classList.add('open');
                document.body.style.overflow = 'hidden';
                nav.classList.remove('open');
                toggle.setAttribute('aria-expanded', 'false');
            }
            window.openAuthModal = openAuthModal;

            function closeAuthModal(){
                if (!authOverlay) return;
                authOverlay.classList.remove('open');
                document.body.style.overflow = '';
            }

            document.querySelectorAll('[data-auth-open]').forEach(function(btn){
                btn.addEventListener('click', function(){
                    openAuthModal(btn.getAttribute('data-auth-open'));
                });
            });
            document.querySelectorAll('[data-auth-switch]').forEach(function(link){
                link.addEventListener('click', function(e){
                    e.preventDefault();
                    openAuthModal(link.getAttribute('data-auth-switch'));
                });
            });
            if (authClose) authClose.addEventListener('click', closeAuthModal);
            if (authOverlay) {
                authOverlay.addEventListener('click', function(e){
                    if (e.target === authOverlay) closeAuthModal();
                });
            }
            document.addEventListener('keydown', function(e){
                if (e.key === 'Escape' && authOverlay && authOverlay.classList.contains('open')) {
                    closeAuthModal();
                }
            });

            // ==========================================================
            // ---- modal de commande ----
            // ==========================================================
            var orderOverlay = document.getElementById('orderModalOverlay');
            var orderClose = document.getElementById('orderModalClose');
            var orderTitle = document.getElementById('orderModalTitle');
            var orderImage = document.getElementById('orderBookImage');
            var orderSeller = document.getElementById('orderBookSeller');
            var orderUnitPrice = document.getElementById('orderUnitPrice');
            var orderTotalPrice = document.getElementById('orderTotalPrice');
            var orderQtyInput = document.getElementById('orderQtyInput');
            var orderQtyMinus = document.getElementById('orderQtyMinus');
            var orderQtyPlus = document.getElementById('orderQtyPlus');
            var orderPaymentNumber = document.getElementById('orderPaymentNumber');
            var orderPaymentName = document.getElementById('orderPaymentName');
            var orderConfirmButton = document.getElementById('orderConfirmButton');
            var orderStaticNote = document.querySelector('.order-static-note');
            var orderAuthor = document.getElementById('orderBookAuthor');
            var orderCategory = document.getElementById('orderBookCategory');
            var orderCondition = document.getElementById('orderBookCondition');
            var orderDescription = document.getElementById('orderBookDescription');
            var orderAvailableQty = document.getElementById('orderAvailableQty');
            var orderBookId = document.getElementById('orderBookId');
            var orderDeliveryEstimate = document.getElementById('orderDeliveryEstimate');
            var orderBookDelivery = document.getElementById('orderBookDelivery');
            var orderVilleSelect = document.getElementById('orderVilleSelect');
            var orderReferenceWrap = document.getElementById('orderReferenceWrap');
            var orderPaymentReference = document.getElementById('orderPaymentReference');
            var orderPaymentReferenceError = document.getElementById('orderPaymentReferenceError');
            var orderForm = document.getElementById('orderForm');

            var currentUnitPrice = 0;

            function formatAr(n){
                return Math.round(n).toLocaleString('fr-FR') + ' Ar';
            }

            function updateOrderTotal(){
                if (!orderQtyInput || !orderTotalPrice) return;
                var qty = parseInt(orderQtyInput.value, 10) || 1;
                orderTotalPrice.textContent = formatAr(currentUnitPrice * qty);
            }

            // La modal d'inscription vendeur utilise elle aussi un groupe de
            // radios name="mode_paiement" (commission / abonnement) : toutes
            // les recherches ci-dessous sont limitées à la modal de commande.
            function orderPaymentRadios(){
                return orderOverlay ? orderOverlay.querySelectorAll('input[name="mode_paiement"]') : [];
            }

            function updateOrderPaymentNumber(){
                var checked = orderOverlay ? orderOverlay.querySelector('input[name="mode_paiement"]:checked') : null;
                if (orderPaymentNumber) {
                    orderPaymentNumber.textContent = checked ? (checked.getAttribute('data-payment-number') || '—') : '—';
                }
                if (orderPaymentName) {
                    orderPaymentName.textContent = checked ? (checked.getAttribute('data-payment-name') || '—') : '—';
                }
            }

            // Version unique : masque à la fois le bloc "Number to contact /
            // Account holder name" ET le champ de référence de paiement
            // quand "Espèces" est sélectionné.
            function toggleReferenceRequirement(){
                var checked = orderOverlay ? orderOverlay.querySelector('input[name="mode_paiement"]:checked') : null;
                var isCash = checked && checked.value === 'especes';

                var orderPaymentNumberRow = document.getElementById('orderPaymentNumberRow');
                if (orderPaymentNumberRow) orderPaymentNumberRow.style.display = isCash ? 'none' : '';

                if (orderReferenceWrap) orderReferenceWrap.style.display = isCash ? 'none' : '';
                if (orderPaymentReference) orderPaymentReference.required = !isCash;
            }

            // Ville => filtre la disponibilité de l'option "Espèces".
            function updateCashAvailability(){
                if (!orderVilleSelect) return;
                var option = orderVilleSelect.options[orderVilleSelect.selectedIndex];
                var cashAllowed = option && option.getAttribute('data-cash-allowed') === '1';

                document.querySelectorAll('[data-payment-option][data-cash="1"]').forEach(function(label){
                    label.style.display = cashAllowed ? '' : 'none';
                    var radio = label.querySelector('input[type="radio"]');
                    if (!cashAllowed && radio.checked) {
                        // Si "espèces" était choisi et n'est plus valide (ville changée),
                        // on retombe sur le premier moyen mobile disponible.
                        var fallback = document.querySelector('[data-payment-option][data-cash="0"] input[type="radio"]');
                        if (fallback) fallback.checked = true;
                        updateOrderPaymentNumber();
                        toggleReferenceRequirement();
                    }
                });
            }

            function setOptionalText(el, value){
                if (!el) return;
                if (value) {
                    el.textContent = value;
                    el.style.display = '';
                } else {
                    el.textContent = '';
                    el.style.display = 'none';
                }
            }

            window.openOrderModal = function(trigger){
                if (!orderOverlay || !trigger) return;

                currentUnitPrice = parseFloat(trigger.getAttribute('data-book-price')) || 0;
                var maxQty = parseInt(trigger.getAttribute('data-book-max'), 10) || 0;

                if (orderBookId) {
                    orderBookId.value = trigger.getAttribute('data-book-id') || '';
                }

                orderTitle.textContent = trigger.getAttribute('data-book-title') || '';
                orderSeller.textContent = trigger.getAttribute('data-book-seller') || '';
                orderImage.src = trigger.getAttribute('data-book-image') || '';
                orderImage.alt = trigger.getAttribute('data-book-title') || '';
                orderUnitPrice.textContent = formatAr(currentUnitPrice);

                setOptionalText(orderAuthor, trigger.getAttribute('data-book-author'));
                setOptionalText(orderCategory, trigger.getAttribute('data-book-category'));
                setOptionalText(orderCondition, trigger.getAttribute('data-book-condition'));
                setOptionalText(orderDescription, trigger.getAttribute('data-book-description'));

                var deliveryLabel = trigger.getAttribute('data-book-delivery') || '—';
                if (orderDeliveryEstimate) orderDeliveryEstimate.textContent = deliveryLabel;
                setOptionalText(orderBookDelivery, deliveryLabel);

                if (orderAvailableQty) {
                    orderAvailableQty.textContent = maxQty;
                }

                if (orderQtyInput) {
                    orderQtyInput.value = 1;
                    orderQtyInput.max = maxQty || 99;
                    updateOrderTotal();
                }

                if (orderVilleSelect) {
                    orderVilleSelect.value = '';
                }
                updateCashAvailability();

                var firstPayment = orderPaymentRadios()[0];
                if (firstPayment) firstPayment.checked = true;
                updateOrderPaymentNumber();
                toggleReferenceRequirement();

                if (orderPaymentReference) {
                    orderPaymentReference.value = '';
                    orderPaymentReference.classList.remove('has-error');
                }
                if (orderPaymentReferenceError) orderPaymentReferenceError.style.display = 'none';

                orderOverlay.classList.add('open');
                document.body.style.overflow = 'hidden';
            };

            function closeOrderModal(){
                if (!orderOverlay) return;
                orderOverlay.classList.remove('open');
                document.body.style.overflow = '';
            }

            document.querySelectorAll('[data-book-order]').forEach(function(btn){
                btn.addEventListener('click', function(){
                    window.openOrderModal(btn);
                });
            });

            if (orderQtyInput) {
                orderQtyInput.addEventListener('input', function(){
                    var max = parseInt(orderQtyInput.max, 10) || 99;
                    var val = parseInt(orderQtyInput.value, 10) || 1;
                    if (val < 1) val = 1;
                    if (val > max) val = max;
                    orderQtyInput.value = val;
                    updateOrderTotal();
                });
            }
            if (orderQtyMinus) {
                orderQtyMinus.addEventListener('click', function(){
                    orderQtyInput.value = Math.max(1, (parseInt(orderQtyInput.value, 10) || 1) - 1);
                    updateOrderTotal();
                });
            }
            if (orderQtyPlus) {
                orderQtyPlus.addEventListener('click', function(){
                    var max = parseInt(orderQtyInput.max, 10) || 99;
                    orderQtyInput.value = Math.min(max, (parseInt(orderQtyInput.value, 10) || 1) + 1);
                    updateOrderTotal();
                });
            }
            orderPaymentRadios().forEach(function(radio){
                radio.addEventListener('change', updateOrderPaymentNumber);
                radio.addEventListener('change', toggleReferenceRequirement);
            });

            if (orderVilleSelect) {
                orderVilleSelect.addEventListener('change', function(){
                    updateCashAvailability();
                });
            }

            if (orderForm) {
                orderForm.addEventListener('submit', function(e){
                    var checked = orderOverlay.querySelector('input[name="mode_paiement"]:checked');
                    var isCash = checked && checked.value === 'especes';

                    if (!isCash && orderPaymentReference && orderPaymentReference.value.trim() === '') {
                        e.preventDefault();
                        orderPaymentReference.classList.add('has-error');
                        if (orderPaymentReferenceError) orderPaymentReferenceError.style.display = '';
                        orderPaymentReference.focus();
                        return;
                    }
                    if (orderPaymentReference) orderPaymentReference.classList.remove('has-error');
                    if (orderPaymentReferenceError) orderPaymentReferenceError.style.display = 'none';
                    if (orderConfirmButton) orderConfirmButton.disabled = true;
                });
            }
            if (orderPaymentReference) {
                orderPaymentReference.addEventListener('input', function(){
                    orderPaymentReference.classList.remove('has-error');
                    if (orderPaymentReferenceError) orderPaymentReferenceError.style.display = 'none';
                });
            }

            if (orderClose) orderClose.addEventListener('click', closeOrderModal);
            if (orderOverlay) {
                orderOverlay.addEventListener('click', function(e){
                    if (e.target === orderOverlay) closeOrderModal();
                });
            }
            document.addEventListener('keydown', function(e){
                if (e.key === 'Escape' && orderOverlay && orderOverlay.classList.contains('open')) {
                    closeOrderModal();
                }
            });
        })();
    </script>

    @stack('scripts')
</body>
</html>