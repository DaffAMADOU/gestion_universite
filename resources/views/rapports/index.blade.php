@extends('layout.template')

@section('title', 'Rapports')
@section('page-title', 'Génération de Rapports')

@section('topbar-actions')
    @if(isset($parDirection) && $parDirection->isNotEmpty())
        <a href="{{ route('rapports.pdf', array_merge(request()->query(), ['annee' => $annee ?? now()->year])) }}"
           class="btn-gc btn-danger-gc no-print">
            <i class="bi bi-file-pdf"></i> Exporter PDF
        </a>
    @endif
@endsection

@section('content')

{{-- Formulaire de génération --}}
<div class="card-gc no-print" style="margin-bottom:24px;">
    <div class="card-gc-header">
        <h3><i class="bi bi-sliders me-2"></i>Paramètres du rapport</h3>
    </div>
    <div style="padding:20px 24px;">
        <form method="GET" action="{{ route('rapports.generer') }}" style="display:flex;gap:16px;align-items:flex-end;flex-wrap:wrap;">
            <div class="form-group" style="min-width:140px;">
                <label class="form-label">Année</label>
                <select name="annee" class="form-control-gc">
                    @foreach($annees as $a)
                        <option value="{{ $a }}" {{ ($annee ?? now()->year) == $a ? 'selected' : '' }}>{{ $a }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group" style="min-width:200px;">
                <label class="form-label">Structure</label>
                <select name="direction" class="form-control-gc">
                    <option value="">Toutes les structures</option>
                    @foreach($directions as $dir)
                        <option value="{{ $dir }}" {{ (request('direction') ?? ($direction ?? '')) == $dir ? 'selected' : '' }}>
                            {{ $dir }}
                        </option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn-gc btn-gold-gc">
                <i class="bi bi-table"></i> Générer les tableaux
            </button>
        </form>
    </div>
</div>

{{-- Tableaux générés --}}
@isset($parDirection)
    @if($parDirection->isEmpty())
        <div style="text-align:center;padding:48px;color:var(--text-muted);">
            Aucun agent trouvé pour cette sélection.
        </div>
    @else
        {{-- En-tête d'impression --}}
        <div style="display:none;" class="print-header">
            <h2 style="font-family:'Playfair Display',serif;">Université — Direction des Ressources Humaines</h2>
            <p>Rapport des congés — Année {{ $annee ?? now()->year }}</p>
            <p>Généré le {{ now()->format('d/m/Y à H:i') }}</p>
            <hr>
        </div>

        @foreach($parDirection as $dir => $agentsDir)
            <div class="rapport-block">
                <div class="rapport-block-title">
                    <i class="bi bi-building"></i>
                    {{ $dir }}
                    <span style="font-size:0.75rem;color:var(--text-muted);font-family:'DM Sans',sans-serif;font-weight:400;">
                        ({{ $agentsDir->count() }} agent{{ $agentsDir->count() > 1 ? 's' : '' }})
                    </span>
                    <span style="flex:1;height:1px;background:var(--border);"></span>
                    <button onclick="imprimerSection('section-{{ Str::slug($dir) }}')"
                            class="btn-gc btn-outline-gc btn-sm-gc no-print">
                        <i class="bi bi-printer"></i> Imprimer
                    </button>
                </div>

                <div id="section-{{ Str::slug($dir) }}">
                    <div style="overflow-x:auto;">
                        <table class="table-gc">
                            <thead>
                                <tr>
                                    <th style="width:30px;">N°</th>
                                    <th>Nom & Prénom</th>
                                    <th>Matricule</th>
                                    <th style="text-align:center;">Jours dus</th>
                                    <th style="text-align:center;">Jours pris</th>
                                    <th style="text-align:center;">Absences</th>
                                    <th style="text-align:center;">Restants</th>
                                    <th>Type</th>
                                    <th>Observations</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($agentsDir as $i => $agent)
                                    <tr style="{{ $i % 2 == 0 ? 'background:var(--cream);' : '' }}">
                                        <td>{{ $i + 1 }}</td>
                                        <td><strong>{{ $agent->nom }} {{ $agent->prenom }}</strong></td>
                                        <td>{{ $agent->matricule }}</td>
                                        <td style="text-align:center;font-weight:700;">{{ $agent->jours_dus }}</td>
                                        <td style="text-align:center;">{{ $agent->jours_pris }}</td>
                                        <td style="text-align:center;">{{ $agent->absences_deductibles }}</td>
                                        <td style="text-align:center;font-weight:700;
                                            color:{{ $agent->jours_restants < 5 ? 'var(--red)' : 'var(--green)' }}">
                                            {{ $agent->jours_restants }}
                                        </td>
                                        <td style="font-size:0.78rem;">
                                            {{ $agent->type_contrat == 'contractuel' ? 'Contractuel' : 'Titulaire' }}
                                        </td>
                                        <td style="font-size:0.75rem;color:var(--text-muted);">
                                            @if($agent->en_conge) En congé @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3">TOTAL — {{ $agentsDir->count() }} agents</td>
                                    <td style="text-align:center;">
                                        {{ $agentsDir->sum(fn($a) => $a->jours_dus) }}
                                    </td>
                                    <td style="text-align:center;">
                                        {{ $agentsDir->sum(fn($a) => $a->jours_pris) }}
                                    </td>
                                    <td style="text-align:center;">
                                        {{ $agentsDir->sum(fn($a) => $a->absences_deductibles) }}
                                    </td>
                                    <td style="text-align:center;">
                                        {{ $agentsDir->sum(fn($a) => $a->jours_restants) }}
                                    </td>
                                    <td colspan="2"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        @endforeach
    @endif
@else
    <div style="text-align:center;padding:60px;color:var(--text-muted);">
        <i class="bi bi-table" style="font-size:3rem;display:block;margin-bottom:12px;opacity:0.3;"></i>
        Sélectionnez les paramètres et cliquez sur <strong>"Générer les tableaux"</strong>
        pour afficher les rapports par structure.
    </div>
@endisset

@endsection

@push('scripts')
<script>
function imprimerSection(id) {
    const el = document.getElementById(id);
    const titre = el.closest('.rapport-block').querySelector('.rapport-block-title').textContent.trim();
    const w = window.open('', '_blank');
    w.document.write(`
        <html><head><title>Rapport — ${titre}</title>
        <style>
            body { font-family: Arial, sans-serif; padding: 20px; font-size: 12px; }
            h2 { color: #0d1b2a; font-size: 16px; margin-bottom: 4px; }
            p  { color: #666; font-size: 11px; margin-bottom: 16px; }
            table { width: 100%; border-collapse: collapse; }
            thead th { background: #0d1b2a; color: white; padding: 8px 10px; text-align: left; font-size: 10px; text-transform: uppercase; }
            tbody td { padding: 7px 10px; border-bottom: 1px solid #e5e5e5; }
            tbody tr:nth-child(even) { background: #f9f6f0; }
            tfoot td { background: #1b2e45; color: white; font-weight: bold; padding: 8px 10px; }
        </style>
        </head>
        <body>
            <h2>Université — Direction des Ressources Humaines</h2>
            <p>Rapport des congés — ${titre} | Édité le ${new Date().toLocaleDateString('fr-FR')}</p>
            ${el.innerHTML}
        </body></html>`);
    w.document.close();
    w.print();
}
</script>
@endpush
