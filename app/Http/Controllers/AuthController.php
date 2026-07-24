<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * Afficher la page de connexion
     */
    public function showLogin()
    {
        // Si déjà connecté → rediriger vers le dashboard
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('Log.Login');
    }

    /**
     * Traiter la tentative de connexion
     */
    public function login(Request $request)
    {
        // 1. Validation des champs
        $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string', 'min:6'],
        ], [
            'email.required'    => 'L\'adresse e-mail est obligatoire.',
            'email.email'       => 'Veuillez saisir une adresse e-mail valide.',
            'password.required' => 'Le mot de passe est obligatoire.',
            'password.min'      => 'Le mot de passe doit contenir au moins 6 caractères.',
        ]);

        // 2. Vérification du rate limiting (max 5 tentatives / minute)
        $throttleKey = Str::lower($request->email) . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return back()->withErrors([
                'email' => "Trop de tentatives. Veuillez réessayer dans {$seconds} secondes.",
            ])->onlyInput('email');
        }

        // 3. Tentative d'authentification
        $credentials = $request->only('email', 'password');
        $remember    = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            // Succès → régénérer la session et rediriger
            $request->session()->regenerate();
            RateLimiter::clear($throttleKey);

            return redirect()->intended(route('dashboard'))
                ->with('success', 'Bienvenue, ' . Auth::user()->name . ' ! Connexion réussie.');
        }

        // 4. Échec → incrémenter le compteur et retourner l'erreur
        RateLimiter::hit($throttleKey, 60);

        $attemptsLeft = 5 - RateLimiter::attempts($throttleKey);

        return back()->withErrors([
            'email' => 'Ces identifiants ne correspondent à aucun compte enregistré.'
                . ($attemptsLeft > 0 ? " Il vous reste {$attemptsLeft} tentative(s)." : ''),
        ])->onlyInput('email');
    }

    /**
     * Déconnexion
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'Vous avez été déconnecté avec succès.');
    }
}
