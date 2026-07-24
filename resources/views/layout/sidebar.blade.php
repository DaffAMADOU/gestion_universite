<div id="sidebar">

    {{-- LOGO --}}
    <div class="sidebar-logo">
        <h1>GestCongés</h1>
        <p>Université_RH</p>
    </div>

    {{-- NAVIGATION --}}
    <nav class="sidebar-nav">

        <div class="nav-section">Navigation</div>

        <a href="{{ route('dashboard') }}"
           class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="bi bi-grid-1x2-fill"></i>
            Tableau de bord
        </a>

        <a href="{{ route('agents.index') }}"
           class="nav-item {{ request()->routeIs('agents.*') ? 'active' : '' }}">
            <i class="bi bi-people-fill"></i>
            Agents
        </a>

        <a href="{{ route('conges.index') }}"
           class="nav-item {{ request()->routeIs('conges.*') ? 'active' : '' }}">
            <i class="bi bi-calendar2-check-fill"></i>
            Congés
        </a>

        <a href="{{ route('absences.index') }}"
           class="nav-item {{ request()->routeIs('absences.*') ? 'active' : '' }}">
            <i class="bi bi-exclamation-circle-fill"></i>
            Absences
        </a>

        <div class="nav-section">Outils</div>

        <a href="{{ route('feries.index') }}"
           class="nav-item {{ request()->routeIs('feries.*') ? 'active' : '' }}">
            <i class="bi bi-calendar3"></i>
            Jours Fériés
        </a>

        <a href="{{ route('rapports.index') }}"
           class="nav-item {{ request()->routeIs('rapports.*') ? 'active' : '' }}">
            <i class="bi bi-file-earmark-bar-graph-fill"></i>
            Rapports
        </a>

    </nav>

    {{-- PIED DU SIDEBAR : profil + déconnexion --}}


</div>
