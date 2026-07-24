<div class="topbar">

    {{-- TITRE DE LA PAGE (défini dans chaque vue avec @section('page-title')) --}}
    <h2>@yield('page-title', 'Tableau de bord')</h2>

    {{-- ACTIONS À DROITE (boutons, date, etc.) --}}
    <div class="topbar-actions">

        {{-- Boutons spécifiques à chaque page (ex: "+ Nouvel agent") --}}
        @yield('topbar-actions')

        {{-- Date du jour --}}
        <span class="current-date">
            {{ now()->translatedFormat('l d F Y') }}
        </span>

    </div>

</div>
