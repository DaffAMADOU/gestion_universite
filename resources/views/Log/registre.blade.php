<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer un compte - GestCongés</title>

    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">

    <style>
        *{
            margin:0;
            padding:0;
            box-sizing:border-box;
        }

        body{
            font-family:'DM Sans',sans-serif;
            background:#0d1b2a;
            display:flex;
            justify-content:center;
            align-items:center;
            min-height:100vh;
            padding:20px;
        }

        .card{
            width:100%;
            max-width:450px;
            background:#fff;
            padding:40px;
            border-radius:18px;
            box-shadow:0 20px 50px rgba(0,0,0,.3);
        }

        .logo{
            text-align:center;
            font-family:'Playfair Display',serif;
            font-size:32px;
            color:#c9a84c;
            margin-bottom:8px;
        }

        .subtitle{
            text-align:center;
            color:#666;
            margin-bottom:30px;
        }

        .form-group{
            margin-bottom:18px;
        }

        label{
            display:block;
            margin-bottom:6px;
            font-size:14px;
            font-weight:600;
        }

        input{
            width:100%;
            padding:12px;
            border:1px solid #ccc;
            border-radius:8px;
            font-size:15px;
        }

        input:focus{
            outline:none;
            border-color:#c9a84c;
        }

        .btn{
            width:100%;
            padding:13px;
            border:none;
            border-radius:8px;
            background:#0d1b2a;
            color:white;
            font-size:16px;
            cursor:pointer;
            margin-top:10px;
        }

        .btn:hover{
            background:#1f324b;
        }

        .error{
            color:red;
            font-size:13px;
            margin-top:5px;
        }

        .footer{
            text-align:center;
            margin-top:25px;
            font-size:14px;
        }

        .footer a{
            color:#c9a84c;
            text-decoration:none;
            font-weight:bold;
        }

        .footer a:hover{
            text-decoration:underline;
        }
    </style>

</head>
<body>

<div class="card">

    <h1 class="logo">GestCongés</h1>

    <p class="subtitle">
        Création d'un nouveau compte
    </p>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div class="form-group">
            <label>Nom complet</label>
            <input
                type="text"
                name="name"
                value="{{ old('name') }}"
                required>

            @error('name')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label>Adresse e-mail</label>
            <input
                type="email"
                name="email"
                value="{{ old('email') }}"
                required>

            @error('email')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label>Mot de passe</label>
            <input
                type="password"
                name="password"
                required>

            @error('password')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label>Confirmer le mot de passe</label>
            <input
                type="password"
                name="password_confirmation"
                required>
        </div>

        <button class="btn">
            Créer mon compte
        </button>

    </form>

    <div class="footer">
        Vous avez déjà un compte ?
        <a href="{{ route('login') }}">
            Se connecter
        </a>
    </div>

</div>

</body>
</html>
