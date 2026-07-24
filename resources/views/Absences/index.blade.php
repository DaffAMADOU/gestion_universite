@extends('layout.template')

@section('title', 'Absences')
@section('page-title', 'Gestion des Absences')

@section('topbar-actions')
    <a href="{{ route('absences.create') }}" class="btn-gc btn-primary-gc">
        <i class="bi bi-plus-lg"></i> Enregistrer une absence
    </a>
@endsection

@section('content')

<div class="alert-info-gc">
    <strong><i class="bi bi-info-circle-fill me-1"></i>Note :</strong>
    Les absences exceptionnelles (<strong>mariage, baptême, décès d'un proche</strong>) ne sont pas déduites
    des jours de congé. Elles sont indiquées comme <em>non déductibles</em>.
</div>

<div class="card-gc">
    <form method="GET" action="{{ route('absences.index') }}" class="search-bar">
        <input type="text" name="search" class="search-input flex-1"
               placeholder="🔍 Rechercher agent..." value="{{ request('search') }}">
        <select name="motif" class="search-input" style="width:180px;">
            <option value="">Tous motifs</option>
            <option value="maladie" {{ request('motif')=='maladie'?'selected':'' }}>Maladie</option>
            <option value="mission" {{ request('motif')=='mission'?'selected':'' }}>Mission</option>
            <option value="formation" {{ request('motif')=='formation'?'selected':'' }}>Formation</option>
            <option value="mariage" {{ request('motif')=='mariage'?'selected':'' }}>Mariage</option>
            <option value="bapteme" {{ request('motif')=='bapteme'?'selected':'' }}>Baptême</option>
            <option value="deces" {{ request('motif')=='deces'?'selected':'' }}>Décès</option>
        </select>
        <button type="submit" class="btn-gc btn-outline-gc btn-sm-gc">
            <i class="bi bi-funnel"></i> Filtrer
        </button>
    </form>

    <div style="overflow-x:auto;">
        <table class="table-gc">
            <thead>
                <tr>
                    <th>Agent</th>
                    <th>Matricule</th>
                    <th>Direction</th>
                    <th>Date</th>
                    <th style="text-align:center;">Jours</th>
                    <th>Motif</th>
                    <th>Déductible</th>
                    <th>Observations</th>
                    <th class="no-print">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($absences as $ab)
                    <tr>
                        <td><strong>{{ $ab->agent->nom_complet }}</strong></td>
                        <td>{{ $ab->agent->matricule }}</td>
                        <td><span class="badge-gc badge-blue">{{ $ab->agent->direction }}</span></td>
                        <td>{{ $ab->date_debut->format('d/m/Y') }}</td>
                        <td style="text-align:center;font-weight:700;">{{ $ab->nombre_jours }}</td>
                        <td>{{ $ab->motif_label }}</td>
                        <td>
                            @if($ab->deductible)
                                <span class="badge-gc badge-red">Oui — déduit</span>
                            @else
                                <span class="badge-gc badge-green">Non — exceptionnel</span>
                            @endif
                        </td>
                        <td style="font-size:0.78rem;color:var(--text-muted);">{{ $ab->observations ?? '—' }}</td>
                        <td class="no-print">
                            <form method="POST" action="{{ route('absences.destroy', $ab) }}"
                                  onsubmit="return confirm('Supprimer cette absence ?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-gc btn-danger-gc btn-sm-gc">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" style="text-align:center;padding:40px;color:var(--text-muted);">
                            Aucune absence enregistrée.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="padding:16px 20px;">
        {{ $absences->appends(request()->query())->links() }}
    </div>
</div>

@endsection
