@extends('layout.template')

@section('title', 'Tableau de bord')
@section('page-title', 'Tableau de bord')

@section('content')

{{-- 📊 STATS --}}
<div class="stats-grid">

    <div class="stat-card blue">
        <div class="stat-label">Total Agents</div>
        <div class="stat-value">{{ $stats['total_agents'] }}</div>
        <div class="stat-sub">Personnel enregistré</div>
    </div>

    <div class="stat-card gold">
        <div class="stat-label">En Congé</div>
        <div class="stat-value">{{ $stats['en_conge'] }}</div>
        <div class="stat-sub">Actuellement en congé</div>
    </div>

    <div class="stat-card green">
        <div class="stat-label">Absences (mois)</div>
        <div class="stat-value">{{ $stats['absences_mois'] }}</div>
        <div class="stat-sub">{{ now()->translatedFormat('F Y') }}</div>
    </div>

    <div class="stat-card red">
        <div class="stat-label">Alertes</div>
        <div class="stat-value">{{ $stats['alertes'] }}</div>
        <div class="stat-sub">Solde congé critique</div>
    </div>

</div>

{{-- 📌 CONTENU --}}
<div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;margin-top:20px;">

    {{-- 🟡 CONGÉS EN COURS --}}
    <div class="card-gc">

        <div class="card-gc-header">
            <h3>Agents en congé</h3>
        </div>

        @if($enCours->isEmpty())
            <p style="padding:20px;text-align:center;color:var(--text-muted);">
                Aucun agent en congé actuellement
            </p>
        @else
            @foreach($enCours as $conge)
                <div style="padding:12px;border-bottom:1px solid #eee;display:flex;justify-content:space-between;">

                    <div>
                        <strong>{{ $conge->agent->prenom }} {{ $conge->agent->nom }}</strong>
                        <div style="font-size:12px;color:gray;">
                            {{ $conge->agent->direction }}
                        </div>
                    </div>

                    <div style="text-align:right;">
                        <span class="badge-gc badge-orange">
                            {{ $conge->jours_ouvrables ?? '-' }} j
                        </span>

                        <div style="font-size:11px;color:gray;">
                            Reprise :
                            {{ optional($conge->date_reprise)->format('d/m/Y') }}
                        </div>
                    </div>

                </div>
            @endforeach
        @endif

    </div>

    {{-- 📊 PAR DIRECTION --}}
    <div class="card-gc">

        <div class="card-gc-header">
            <h3>Jours de congé par direction</h3>
        </div>

        <div style="padding:15px;">

            @forelse($parDirection as $dir => $data)

                <div style="margin-bottom:15px;">

                    <div style="display:flex;justify-content:space-between;">
                        <strong>{{ $dir }}</strong>

                        <span style="font-size:12px;color:gray;">
                            {{ $data['count'] }} agents — {{ $data['jours_dus'] }} jours
                        </span>
                    </div>

                    <div class="progress-bar-gc">
                        <div class="progress-fill-gc"
                             style="width:{{ min(($data['jours_pris'] / max($data['jours_dus'],1))*100,100) }}%;">
                        </div>
                    </div>

                </div>

            @empty
                <p style="text-align:center;color:gray;">
                    Aucune donnée disponible
                </p>
            @endforelse

        </div>

    </div>

</div>

{{-- 📌 INFO --}}
<div class="alert-info-gc" style="margin-top:20px;">
    <strong>Rappel :</strong>
    Congés calculés du lundi au samedi, hors jours fériés.
    Maximum 72 jours cumulables.
</div>

@endsection
