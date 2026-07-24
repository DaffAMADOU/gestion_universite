@extends('layout.template')


@section('title', 'Fiche — ' . $agent->nom_complet)
@section('page-title', 'Fiche Agent')

@section('topbar-actions')
    <a href="{{ route('agents.index') }}" class="btn-gc btn-outline-gc no-print">
        <i class="bi bi-arrow-left"></i> Retour
    </a>
    <button onclick="window.print()" class="btn-gc btn-gold-gc no-print">
        <i class="bi bi-printer"></i> Imprimer
    </button>
@endsection

@section('content')

@php
    $dus      = $agent->jours_dus;
    $pris     = $agent->jours_pris;
    $restants = $agent->jours_restants;
    $pct      = $dus > 0 ? min(round(($pris / $dus) * 100), 100) : 0;
@endphp

{{-- En-tête fiche --}}
<div class="card-gc" style="margin-bottom:20px;">
    <div style="padding:24px 28px;display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:16px;">
        <div style="display:flex;align-items:center;gap:16px;">
            <div style="width:60px;height:60px;border-radius:50%;background:var(--navy);display:flex;align-items:center;justify-content:center;font-size:1.4rem;color:var(--gold);font-family:'Playfair Display',serif;font-weight:700;flex-shrink:0;">
                {{ strtoupper(substr($agent->prenom, 0, 1) . substr($agent->nom, 0, 1)) }}
            </div>

            <div>
                <h3 style="font-family:'Playfair Display',serif;font-size:1.4rem;color:var(--navy);">
                    {{ $agent->prenom }} {{ $agent->nom }}
                </h3>

                <div style="display:flex;gap:8px;margin-top:4px;flex-wrap:wrap;">
                    <span class="badge-gc badge-blue">{{ $agent->direction }}</span>

                    {{-- ❌ type_contrat supprimé (n’existe pas dans ton model) --}}
                    <span class="badge-gc badge-navy">
                        Agent
                    </span>

                    @if($agent->en_conge)
                        <span class="badge-gc badge-orange">En congé actuellement</span>
                    @else
                        <span class="badge-gc badge-green">Actif</span>
                    @endif
                </div>
            </div>
        </div>

        <div style="text-align:right;">
            <div style="font-size:0.72rem;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.08em;">Matricule</div>
            <div style="font-family:'Playfair Display',serif;font-size:1.3rem;color:var(--navy);">
                {{ $agent->matricule }}
            </div>
        </div>
    </div>
</div>

{{-- Détails en grille --}}
<div class="detail-grid" style="margin-bottom:20px;">
    <div class="detail-item">
        <div class="detail-label">Date de prise de service</div>
        <div class="detail-val">{{ $agent->date_prise_service?->format('d/m/Y') ?? 'N/A' }}</div>
    </div>

    <div class="detail-item">
        <div class="detail-label">Sexe & Enfants</div>
        <div class="detail-val">
            {{ $agent->sexe == 'M' ? 'Masculin' : 'Féminin' }}

            @if($agent->sexe == 'F')
                — {{ $agent->nombre_enfants }} enfant(s) (+{{ $agent->bonus_enfants }} j)
            @endif
        </div>
    </div>

    <div class="detail-item">
        <div class="detail-label">Jours de congé dus</div>
        <div class="detail-val" style="color:var(--green);font-size:1.3rem;font-weight:700;">
            {{ $dus }} jours
        </div>
    </div>

    <div class="detail-item">
        <div class="detail-label">Jours déjà pris</div>
        <div class="detail-val">{{ $pris }} jours</div>
    </div>

    <div class="detail-item">
        <div class="detail-label">Absences déductibles</div>
        <div class="detail-val">{{ $agent->absences_deductibles }} jours</div>
    </div>

    <div class="detail-item">
        <div class="detail-label">Jours restants</div>
        <div class="detail-val" style="color:{{ $restants < 5 ? 'var(--red)' : 'var(--navy)' }};font-size:1.3rem;font-weight:700;">
            {{ $restants }} jours
            @if($restants < 5) <span style="font-size:0.75rem;"> ⚠️ Critique</span> @endif
        </div>
    </div>
</div>

{{-- Barre de progression --}}
<div class="card-gc" style="padding:20px 24px;margin-bottom:20px;">
    <div style="display:flex;justify-content:space-between;font-size:0.82rem;color:var(--text-muted);margin-bottom:6px;">
        <span>Utilisation des congés {{ now()->year }}</span>
        <span>{{ $pris }} / {{ $dus }} jours ({{ $pct }}%)</span>
    </div>
    <div class="progress-bar-gc" style="height:12px;">
        <div class="progress-fill-gc" style="width:{{ $pct }}%;background:{{ $pct > 80 ? 'var(--red)' : ($pct > 50 ? 'var(--gold)' : 'var(--green)') }};"></div>
    </div>
</div>

{{-- Historique congés --}}
<div class="card-gc" style="margin-bottom:20px;">
    <div class="card-gc-header">
        <h3><i class="bi bi-calendar2-week me-2"></i>Historique des congés</h3>
        <a href="{{ route('conges.create') }}?agent_id={{ $agent->id }}" class="btn-gc btn-primary-gc btn-sm-gc no-print">
            + Ajouter un congé
        </a>
    </div>

    @if($agent->conges->isEmpty())
        <p style="padding:24px;text-align:center;color:var(--text-muted);">Aucun congé enregistré</p>
    @else
        <div style="overflow-x:auto;">
            <table class="table-gc">
                <thead>
                <tr>
                    <th>Date cessation</th>
                    <th>Date reprise</th>
                    <th>Jours pris</th>
                    <th>Type</th>
                    <th>Statut</th>
                    <th>Observations</th>
                </tr>
                </thead>
                <tbody>
                @foreach($agent->conges as $c)
                    <tr>
                        <td>{{ $c->date_cessation->format('d/m/Y') }}</td>
                        <td>{{ $c->date_reprise->format('d/m/Y') }}</td>
                        <td><strong>{{ $c->jours_ouvrables }}</strong></td>
                        <td>{{ $c->type_label }}</td>
                        <td>
                            @if($c->statut == 'en_cours')
                                <span class="badge-gc badge-orange">En cours</span>
                            @elseif($c->statut == 'termine')
                                <span class="badge-gc badge-green">Terminé</span>
                            @else
                                <span class="badge-gc badge-blue">À venir</span>
                            @endif
                        </td>
                        <td style="font-size:0.78rem;color:var(--text-muted);">{{ $c->observations ?? '—' }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

{{-- Historique absences --}}
<div class="card-gc">
    <div class="card-gc-header">
        <h3><i class="bi bi-exclamation-circle me-2"></i>Historique des absences</h3>
        <a href="{{ route('absences.create') }}?agent_id={{ $agent->id }}" class="btn-gc btn-primary-gc btn-sm-gc no-print">
            + Enregistrer une absence
        </a>
    </div>

    @if($agent->absences->isEmpty())
        <p style="padding:24px;text-align:center;color:var(--text-muted);">Aucune absence enregistrée</p>
    @else
        <div style="overflow-x:auto;">
            <table class="table-gc">
                <thead>
                <tr>
                    <th>Date</th>
                    <th>Nbre jours</th>
                    <th>Motif</th>
                    <th>Déductible</th>
                    <th>Observations</th>
                </tr>
                </thead>
                <tbody>
                @foreach($agent->absences as $ab)
                    <tr>
                        <td>{{ $ab->date_debut->format('d/m/Y') }}</td>
                        <td><strong>{{ $ab->nombre_jours }}</strong></td>
                        <td>{{ $ab->motif_label }}</td>
                        <td>
                            @if($ab->deductible)
                                <span class="badge-gc badge-red">Oui</span>
                            @else
                                <span class="badge-gc badge-green">Non</span>
                            @endif
                        </td>
                        <td style="font-size:0.78rem;color:var(--text-muted);">{{ $ab->observations ?? '—' }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

@endsection
