@extends('layout.template')

@section('title', 'Congés')
@section('page-title', 'Gestion des Congés')

@section('topbar-actions')
<a href="{{ route('conges.create') }}" class="btn-gc btn-primary-gc">
    <i class="bi bi-plus-lg"></i> Enregistrer un congé
</a>
@endsection

@section('content')

<div class="alert-warning-gc">
    <strong><i class="bi bi-info-circle-fill me-1"></i>Règle :</strong>
    Congés comptés du <strong>lundi au samedi</strong>, hors jours fériés.
    Si la cessation de service est un <strong>vendredi</strong>, le décompte commence le <strong>lundi suivant</strong>.
</div>

<div class="card-gc">

    {{-- FILTRES --}}
    <form method="GET" action="{{ route('conges.index') }}" class="search-bar">

        <input type="text"
               name="search"
               class="search-input flex-1"
               placeholder="🔍 Rechercher agent..."
               value="{{ request('search') }}">

        <select name="direction" class="search-input" style="width:180px;">
            <option value="">Toutes directions</option>
            @foreach($directions as $dir)
                <option value="{{ $dir }}" {{ request('direction') == $dir ? 'selected' : '' }}>
                    {{ $dir }}
                </option>
            @endforeach
        </select>

        <select name="statut" class="search-input" style="width:150px;">
            <option value="">Tous statuts</option>
            <option value="en_cours" {{ request('statut') == 'en_cours' ? 'selected' : '' }}>En cours</option>
            <option value="a_venir" {{ request('statut') == 'a_venir' ? 'selected' : '' }}>À venir</option>
            <option value="termine" {{ request('statut') == 'termine' ? 'selected' : '' }}>Terminé</option>
        </select>

        <button type="submit" class="btn-gc btn-outline-gc btn-sm-gc">
            <i class="bi bi-funnel"></i> Filtrer
        </button>

        @if(request()->hasAny(['search','direction','statut']))
            <a href="{{ route('conges.index') }}" class="btn-gc btn-outline-gc btn-sm-gc">✕</a>
        @endif
    </form>

    {{-- TABLE --}}
    <div style="overflow-x:auto;">
        <table class="table-gc">

            <thead>
                <tr>
                    <th>Agent</th>
                    <th>Matricule</th>
                    <th>Direction</th>
                    <th>Date cessation</th>
                    <th>Date reprise</th>
                    <th style="text-align:center;">Jours</th>
                    <th>Type</th>
                    <th>Statut</th>
                    <th class="no-print">Actions</th>
                </tr>
            </thead>

            <tbody>

                @forelse($conges as $c)
                    <tr>

                        {{-- Agent SAFE (évite crash si agent supprimé) --}}
                        <td>
                            <strong>{{ $c->agent?->nom_complet ?? 'Agent supprimé' }}</strong>
                        </td>

                        <td>{{ $c->agent?->matricule ?? '-' }}</td>

                        <td>
                            <span class="badge-gc badge-blue">
                                {{ $c->agent?->direction ?? '-' }}
                            </span>
                        </td>

                        {{-- Dates SAFE --}}
                        <td>
                            {{ $c->date_cessation ? \Carbon\Carbon::parse($c->date_cessation)->format('d/m/Y') : '-' }}
                        </td>

                        <td>
                            {{ $c->date_reprise ? \Carbon\Carbon::parse($c->date_reprise)->format('d/m/Y') : '-' }}
                        </td>

                        <td style="text-align:center;font-weight:700;">
                            {{ $c->jours_ouvrables }}
                        </td>

                        <td style="font-size:0.78rem;">
                            {{ $c->type }}
                        </td>

                        {{-- STATUT --}}
                        <td>
                            @if($c->date_cessation && $c->date_reprise && now()->between($c->date_cessation, $c->date_reprise))
                                <span class="badge-gc badge-orange">En cours</span>

                            @elseif($c->date_reprise && $c->date_reprise < now())
                                <span class="badge-gc badge-green">Terminé</span>

                            @else
                                <span class="badge-gc badge-blue">À venir</span>
                            @endif
                        </td>

                        {{-- ACTIONS --}}
                        <td class="no-print">

                            <a href="{{ route('conges.show', $c) }}"
                               class="btn-gc btn-outline-gc btn-sm-gc">
                                <i class="bi bi-eye"></i>
                            </a>

                            <a href="{{ route('conges.edit', $c) }}"
                               class="btn-gc btn-primary-gc btn-sm-gc">
                                <i class="bi bi-pencil"></i>
                            </a>

                            <form method="POST"
                                  action="{{ route('conges.destroy', $c) }}"
                                  style="display:inline"
                                  onsubmit="return confirm('Supprimer ce congé ?')">
                                @csrf
                                @method('DELETE')

                                <button type="submit" class="btn-gc btn-danger-gc btn-sm-gc">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>

                        </td>

                    </tr>
                @empty
                    <tr>
                        <td colspan="9" style="text-align:center;padding:40px;color:var(--text-muted);">
                            Aucun congé enregistré.
                        </td>
                    </tr>
                @endforelse

            </tbody>

        </table>
    </div>

    {{-- PAGINATION --}}
    <div style="padding:16px 20px;">
        {{ $conges->appends(request()->query())->links() }}
    </div>

</div>

@endsection
