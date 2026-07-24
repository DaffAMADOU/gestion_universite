@extends('layout.template')

@section('title', 'Agents')
@section('page-title', 'Gestion des Agents')

@section('topbar-actions')
    <a href="{{ route('agents.create') }}" class="btn-gc btn-primary-gc">
        <i class="bi bi-plus-lg"></i> Nouvel agent
    </a>
@endsection

@section('content')

<div class="card-gc">

    {{-- ================= FILTRES ================= --}}
    <form method="GET" action="{{ route('agents.index') }}" class="search-bar">

        <input type="text"
               name="search"
               class="search-input flex-1"
               placeholder="🔍 Rechercher par nom, prénom ou matricule..."
               value="{{ request('search') }}">

        <select name="direction" class="search-input" style="width:200px;">
            <option value="">Toutes les directions</option>
            @foreach($directions as $dir)
                <option value="{{ $dir }}" {{ request('direction') == $dir ? 'selected' : '' }}>
                    {{ $dir }}
                </option>
            @endforeach
        </select>

        <button type="submit" class="btn-gc btn-outline-gc btn-sm-gc">
            <i class="bi bi-funnel"></i> Filtrer
        </button>

        @if(request('search') || request('direction'))
            <a href="{{ route('agents.index') }}" class="btn-gc btn-outline-gc btn-sm-gc">
                <i class="bi bi-x"></i> Réinitialiser
            </a>
        @endif

    </form>

    {{-- ================= TABLE ================= --}}
    <div style="overflow-x:auto;">

        <table class="table-gc">

            <thead>
                <tr>
                    <th>Matricule</th>
                    <th>Nom & Prénom</th>
                    <th>Direction / UFR</th>
                    <th style="text-align:center;">Jours dus</th>
                    <th style="text-align:center;">Jours pris</th>
                    <th style="text-align:center;">Absences</th>
                    <th style="text-align:center;">Restants</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>

                @forelse($agents as $agent)

                    <tr>

                        <td><strong>{{ $agent->matricule }}</strong></td>

                        <td>
                            {{ $agent->prenom }} {{ $agent->nom }}

                            @if($agent->sexe === 'F' && $agent->nombre_enfants > 0)
                                <span style="font-size:0.75rem;color:var(--text-muted);">
                                    +{{ $agent->nombre_enfants }} enfant(s)
                                </span>
                            @endif
                        </td>

                        <td>
                            <span class="badge-gc badge-blue">
                                {{ $agent->direction }}
                            </span>
                        </td>

                        <td style="text-align:center;font-weight:700;">
                            {{ $agent->jours_dus }}
                        </td>

                        <td style="text-align:center;">
                            {{ $agent->jours_pris }}
                        </td>

                        <td style="text-align:center;">
                            {{ $agent->absences_deductibles }}
                        </td>

                        <td style="text-align:center;">
                            <strong style="color:{{ $agent->jours_restants < 5 ? 'var(--red)' : 'var(--green)' }}">
                                {{ $agent->jours_restants }}
                            </strong>
                        </td>

                        <td>
                            @if($agent->en_conge)
                                <span class="badge-gc badge-orange">En congé</span>
                            @else
                                <span class="badge-gc badge-green">Actif</span>
                            @endif
                        </td>

                        <td>
                            <div style="display:flex;gap:6px;flex-wrap:wrap;">

                                <a href="{{ route('agents.show', $agent) }}"
                                   class="btn-gc btn-outline-gc btn-sm-gc">
                                    <i class="bi bi-eye"></i>
                                </a>

                                <a href="{{ route('agents.edit', $agent) }}"
                                   class="btn-gc btn-outline-gc btn-sm-gc">
                                    <i class="bi bi-pencil"></i>
                                </a>

                                <form method="POST"
                                      action="{{ route('agents.destroy', $agent) }}"
                                      onsubmit="return confirm('Supprimer cet agent ?')">

                                    @csrf
                                    @method('DELETE')

                                    <button type="submit"
                                            class="btn-gc btn-danger-gc btn-sm-gc">
                                        <i class="bi bi-trash"></i>
                                    </button>

                                </form>

                            </div>
                        </td>

                    </tr>

                @empty

                    <tr>
                        <td colspan="9"
                            style="text-align:center;padding:40px;color:var(--text-muted);">
                            Aucun agent enregistré.
                            <a href="{{ route('agents.create') }}">Ajouter un agent</a>
                        </td>
                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

</div>

@endsection
