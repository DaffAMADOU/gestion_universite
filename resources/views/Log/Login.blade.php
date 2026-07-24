<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Connexion — GestCongés</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'DM Sans', sans-serif;
            background: #0d1b2a;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-card {
            background: #ffffff;
            border-radius: 18px;
            padding: 48px 40px;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.4);
        }

        .login-logo {
            font-family: 'Playfair Display', serif;
            color: #c9a84c;
            font-size: 1.9rem;
            text-align: center;
            margin-bottom: 4px;
        }

        .login-subtitle {
            text-align: center;
            color: #6b7280;
            font-size: 0.78rem;
            margin-bottom: 32px;
            line-height: 1.5;
        }

        /* ── Alerte succès ── */
        .alert-success {
            display: flex;
            align-items: center;
            gap: 10px;
            background: #d4edda;
            border-left: 4px solid #27ae60;
            border-radius: 8px;
            padding: 12px 15px;
            margin-bottom: 20px;
            font-size: 0.84rem;
            color: #155724;
        }
        .alert-success .icon {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: #27ae60;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.9rem;
            flex-shrink: 0;
        }

        /* ── Alerte erreur ── */
        .alert-error {
            display: flex;
            align-items: center;
            gap: 10px;
            background: #f8d7da;
            border-left: 4px solid #c0392b;
            border-radius: 8px;
            padding: 12px 15px;
            margin-bottom: 20px;
            font-size: 0.84rem;
            color: #721c24;
        }
        .alert-error .icon {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: #c0392b;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.9rem;
            flex-shrink: 0;
        }

        /* ── Barre de redirection (succès) ── */
        .redirect-bar {
            height: 4px;
            background: #e5e7eb;
            border-radius: 2px;
            overflow: hidden;
            margin-top: 10px;
        }
        .redirect-fill {
            height: 100%;
            background: #27ae60;
            border-radius: 2px;
            animation: fill 2s linear forwards;
        }
        @keyframes fill { from { width: 0%; } to { width: 100%; } }

        /* ── Champs ── */
        .form-group {
            margin-bottom: 18px;
        }
        .form-label {
            display: block;
            font-size: 0.72rem;
            font-weight: 700;
            color: #0d1b2a;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            margin-bottom: 6px;
        }
        .form-input {
            width: 100%;
            padding: 11px 14px;
            border: 1.5px solid #d4c9b0;
            border-radius: 8px;
            font-family: 'DM Sans', sans-serif;
            font-size: 0.875rem;
            outline: none;
            transition: border-color 0.18s, background 0.18s;
            background: #fff;
        }
        .form-input:focus {
            border-color: #c9a84c;
        }
        .form-input.is-error {
            border-color: #c0392b;
            background: #fef5f5;
        }
        .form-input.is-success {
            border-color: #27ae60;
            background: #f6fdf8;
        }
        .field-error {
            font-size: 0.72rem;
            color: #c0392b;
            margin-top: 5px;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        /* ── Remember + forgot ── */
        .form-bottom {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 24px;
            font-size: 0.80rem;
        }
        .remember {
            display: flex;
            align-items: center;
            gap: 6px;
            cursor: pointer;
            color: #6b7280;
        }
        .remember input { cursor: pointer; accent-color: #0d1b2a; }

        /* ── Bouton ── */
        .btn-login {
            width: 100%;
            padding: 13px;
            background: #0d1b2a;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-family: 'DM Sans', sans-serif;
            font-size: 0.95rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.18s, opacity 0.18s;
        }
        .btn-login:hover { background: #1b2e45; }
        .btn-login:disabled { opacity: 0.65; cursor: not-allowed; }

        /* ── Infos de test ── */
        .demo-box {
            margin-top: 24px;
            padding: 14px 16px;
            background: #f5f0e8;
            border-radius: 8px;
            font-size: 0.74rem;
            color: #6b7280;
            line-height: 1.7;
        }
        .demo-box strong { color: #0d1b2a; }
    </style>
</head>
<body>

<div class="login-card">

    <h1 class="login-logo">GestCongés</h1>
    <p class="login-subtitle">
        Plateforme de gestion des absences et congés<br>
        Direction des Ressources Humaines
    </p>

    {{-- ── Alerte succès (connexion réussie) ── --}}
    @if(session('success'))
        <div class="alert-success">
            <div class="icon">✓</div>
            <div>
                <strong>Connexion réussie</strong><br>
                {{ session('success') }} Redirection en cours…
                <div class="redirect-bar"><div class="redirect-fill"></div></div>
            </div>
        </div>
    @endif

    {{-- ── Alerte erreur ── --}}
    @if($errors->any())
        <div class="alert-error">
            <div class="icon">✕</div>
            <div>
                <strong>Échec de la connexion</strong><br>
                {{ $errors->first('email') }}
            </div>
        </div>
    @endif

    {{-- ── Formulaire ── --}}
    <form method="POST" action="{{ route('login') }}">
        @csrf

        {{-- Email --}}
        <div class="form-group">
            <label class="form-label" for="email">Adresse e-mail</label>
            <input
                type="email"
                id="email"
                name="email"
                class="form-input {{ $errors->has('email') ? 'is-error' : '' }}"
                value="{{ old('email') }}"
                placeholder="admin@universite.sn"
                autocomplete="email"
                required
                autofocus
            >
            @error('email')
                <div class="field-error">⚠ {{ $message }}</div>
            @enderror
        </div>

        {{-- Mot de passe --}}
        <div class="form-group">
            <label class="form-label" for="password">Mot de passe</label>
            <input
                type="password"
                id="password"
                name="password"
                class="form-input {{ $errors->has('password') ? 'is-error' : '' }}"
                placeholder="••••••••"
                autocomplete="current-password"
                required
            >
            @error('password')
                <div class="field-error">⚠ {{ $message }}</div>
            @enderror
        </div>

        {{-- Se souvenir --}}
        <div class="form-bottom">
            <label class="remember">
                <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                Se souvenir de moi
            </label>
        </div>

        {{-- Bouton --}}
        <button type="submit" class="btn-login">
            Se connecter
        </button>
        <div style="text-align:center; margin-top:20px;">
    Vous n'avez pas de compte ?
    <a href="{{ route('register') }}"
       style="color:#c9a84c; text-decoration:none; font-weight:600;">
        Créer un compte
    </a>
</div>
    </form>

    {{-- Infos de test --}}
    <div class="demo-box">
        <strong>Comptes de test :</strong><br>
        Admin : amadoubocardaff@gmail.com <br>
        Gestionnaire : ibahimaba@gmail.com
    </div>

</div>

{{-- Redirection auto si succès --}}
@if(session('success'))
<script>
    setTimeout(() => {
        window.location.href = "{{ route('dashboard') }}";
    }, 2000);
</script>
@endif

</body>
</html>
