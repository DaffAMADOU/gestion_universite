<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Rapport Congés {{ $annee }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 10px; color: #1a1a2e; }

        .page-header {
            text-align: center; padding: 16px 0 12px;
            border-bottom: 2px solid #0d1b2a; margin-bottom: 16px;
        }
        .page-header h1 { font-size: 15px; color: #0d1b2a; }
        .page-header h2 { font-size: 12px; color: #6b7280; font-weight: normal; margin-top: 4px; }
        .page-header p  { font-size: 9px; color: #9ca3af; margin-top: 4px; }

        .section { margin-bottom: 20px; page-break-inside: avoid; }
        .section-title {
            background: #0d1b2a; color: white;
            padding: 8px 12px; font-size: 11px; font-weight: bold;
            border-radius: 4px 4px 0 0;
        }

        table { width: 100%; border-collapse: collapse; }
        thead th {
            background: #1b2e45; color: white;
            padding: 7px 8px; text-align: left;
            font-size: 8px; text-transform: uppercase; letter-spacing: 0.05em;
        }
        tbody td { padding: 6px 8px; border-bottom: 1px solid #ede5d4; }
        tbody tr:nth-child(even) { background: #f9f6f0; }
        tfoot td {
            background: #243854; color: white;
            font-weight: bold; padding: 7px 8px;
        }

        .badge-green  { color: #155724; font-weight: bold; }
        .badge-red    { color: #c0392b; font-weight: bold; }

        .page-footer {
            position: fixed; bottom: 0; left: 0; right: 0;
            text-align: center; font-size: 8px; color: #9ca3af;
            padding: 6px; border-top: 1px solid #ede5d4;
        }
    </style>
</head>
<body>

<div class="page-header">
    <h1>Université — Direction des Ressources Humaines</h1>
    <h2>Rapport des congés administratifs — Année {{ $annee }}
        @if($direction) | {{ $direction }} @endif
    </h2>
    <p>Édité le {{ now()->format('d/m/Y à H:i') }}</p>
</div>

@foreach($parDirection as $dir => $agentsDir)
<div class="section">
    <div class="section-title">
        {{ $dir }} — {{ $agentsDir->count() }} agent(s)
    </div>
    <table>
        <thead>
            <tr>
                <th width="4%">N°</th>
                <th width="22%">Nom & Prénom</th>
                <th width="14%">Matricule</th>
                <th width="9%" style="text-align:center;">Jours dus</th>
                <th width="9%" style="text-align:center;">Jours pris</th>
                <th width="9%" style="text-align:center;">Absences</th>
                <th width="9%" style="text-align:center;">Restants</th>
                <th width="10%">Type contrat</th>
                <th width="14%">Observations</th>
            </tr>
        </thead>
        <tbody>
            @foreach($agentsDir as $i => $agent)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td><strong>{{ $agent->nom }} {{ $agent->prenom }}</strong></td>
                <td>{{ $agent->matricule }}</td>
                <td style="text-align:center;font-weight:bold;">{{ $agent->jours_dus }}</td>
                <td style="text-align:center;">{{ $agent->jours_pris }}</td>
                <td style="text-align:center;">{{ $agent->absences_deductibles }}</td>
                <td style="text-align:center;" class="{{ $agent->jours_restants < 5 ? 'badge-red' : 'badge-green' }}">
                    {{ $agent->jours_restants }}
                </td>
                <td>{{ $agent->type_contrat == 'contractuel' ? 'Contractuel' : 'Titulaire' }}</td>
                <td style="font-size:9px;color:#6b7280;">{{ $agent->en_conge ? 'En congé' : '' }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3">TOTAL — {{ $agentsDir->count() }} agents</td>
                <td style="text-align:center;">{{ $agentsDir->sum(fn($a) => $a->jours_dus) }}</td>
                <td style="text-align:center;">{{ $agentsDir->sum(fn($a) => $a->jours_pris) }}</td>
                <td style="text-align:center;">{{ $agentsDir->sum(fn($a) => $a->absences_deductibles) }}</td>
                <td style="text-align:center;">{{ $agentsDir->sum(fn($a) => $a->jours_restants) }}</td>
                <td colspan="2"></td>
            </tr>
        </tfoot>
    </table>
</div>
@endforeach

<div class="page-footer">
    Plateforme GestCongés — Université | Document confidentiel — Réservé à la DRH
</div>

</body>
</html>
