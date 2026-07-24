<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmation de congé — GestCongés</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Roboto', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background-color: #000000;
            color: #e8eaed;
            padding: 0;
            margin: 0;
        }

        .wrapper {
            max-width: 600px;
            margin: 0 auto;
            background-color: #000000;
            padding: 0;
        }

        /* Header expéditeur */
        .sender-block {
            display: flex;
            align-items: flex-start;
            gap: 14px;
            padding: 18px 18px 10px;
        }
        .sender-avatar {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background-color: #0d1b2a;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #c9a84c;
            font-weight: 700;
            font-size: 15px;
            flex-shrink: 0;
            text-align: center;
            line-height: 42px;
        }
        .sender-info { flex: 1; }
        .sender-name { color: #e8eaed; font-size: 16px; font-weight: 500; }
        .sender-email { color: #9aa0a6; font-size: 13px; margin-top: 2px; }
        .sender-to { color: #9aa0a6; font-size: 13px; margin-top: 2px; }

        /* Corps du mail */
        .mail-body {
            padding: 0 18px 18px;
            border-left: 2px solid #3c4043;
            margin: 0 18px 18px;
        }

        /* Logo/Titre */
        .mail-title {
            color: #ffffff;
            font-size: 26px;
            font-weight: 700;
            margin-bottom: 16px;
        }

        /* Badge statut */
        .mail-badge {
            display: inline-block;
            background-color: #1b3a23;
            color: #7ee08c;
            padding: 7px 16px;
            border-radius: 20px;
            font-size: 15px;
            font-weight: 600;
            margin-bottom: 20px;
        }

        /* Texte */
        .mail-text {
            color: #e8eaed;
            font-size: 17px;
            line-height: 1.6;
            margin-bottom: 16px;
        }
        .mail-text strong { color: #ffffff; font-weight: 700; }

        /* Tableau récapitulatif */
        .mail-card {
            background-color: #1c1c1e;
            border-left: 3px solid #c9a84c;
            border-radius: 10px;
            padding: 16px 18px;
            margin: 18px 0;
        }
        .mail-card table {
            width: 100%;
            border-collapse: collapse;
        }
        .mail-card table tr {
            border-bottom: 1px solid #2d2e30;
        }
        .mail-card table tr:last-child {
            border-bottom: none;
        }
        .mail-card table td {
            padding: 10px 0;
            font-size: 15px;
        }
        .mail-card table td:first-child {
            color: #9aa0a6;
        }
        .mail-card table td:last-child {
            color: #ffffff;
            font-weight: 700;
            text-align: right;
        }
        .date-reprise { color: #7ee08c !important; }

        /* Footer */
        .mail-footer {
            margin-top: 22px;
            padding-top: 16px;
            border-top: 1px solid #2d2e30;
            font-size: 13px;
            color: #6b6b6e;
            line-height: 1.6;
        }

        /* Boutons Répondre / Transférer */
        .reply-bar {
            display: flex;
            gap: 14px;
            padding: 20px 18px;
            align-items: center;
            border-top: 1px solid #2d2e30;
        }
        .reply-btn {
            flex: 1;
            display: inline-block;
            text-align: center;
            border: 1px solid #5f6368;
            border-radius: 24px;
            padding: 12px 0;
            color: #9aa0a6;
            font-size: 16px;
            font-weight: 500;
            text-decoration: none;
        }
    </style>
</head>
<body>
<div class="wrapper">

    {{-- Bloc expéditeur --}}
    <div class="sender-block">
        <div class="sender-avatar">RH</div>
        <div class="sender-info">
            <div class="sender-name">Direction des Ressources Humaines</div>
            <div class="sender-email">noreply@gestconges.universite.sn</div>
            <div class="sender-to">à {{ $conge->agent->email }}</div>
        </div>
    </div>

    {{-- Corps --}}
    <div class="mail-body">

        <div class="mail-title">GestCongés</div>
        <div class="mail-badge">✓ Congé validé et enregistré</div>

        <p class="mail-text">
            Bonjour {{ $conge->agent->sexe === 'F' ? 'Madame' : 'Monsieur' }}
            <strong>{{ strtoupper($conge->agent->nom) }} {{ $conge->agent->prenom }}</strong> ,
        </p>

        <p class="mail-text">
            Nous vous confirmons que votre demande de <strong>congé administratif</strong>
            a été validée par la Direction des Ressources Humaines.
            Les détails de votre congé sont récapitulés ci-dessous :
        </p>

        {{-- Tableau récapitulatif --}}
        <div class="mail-card">
            <table>
                <tr>
                    <td>Matricule</td>
                    <td>{{ $conge->agent->matricule }}</td>
                </tr>
                <tr>
                    <td>Direction</td>
                    <td>{{ $conge->agent->direction }}</td>
                </tr>
                <tr>
                    <td>Date de cessation</td>
                    <td>{{ $conge->date_cessation->format('d/m/Y') }}</td>
                </tr>
                <tr>
                    <td>Jours ouvrables</td>
                    <td>{{ $conge->jours_ouvrables }} jours</td>
                </tr>
                <tr>
                    <td>Date de reprise</td>
                    <td class="date-reprise">{{ $conge->date_reprise->format('d/m/Y') }}</td>
                </tr>
            </table>
        </div>

        <p class="mail-text">
            Merci de vous présenter à votre poste le
            <strong>{{ $conge->date_reprise->translatedFormat('l d F Y') }}</strong>.
        </p>

        <div class="mail-footer">
            Message automatique généré par GestCongés.<br>
            Université — Direction des Ressources Humaines
        </div>

    </div>

    {{-- Boutons --}}
    <div class="reply-bar">
        <a href="#" class="reply-btn">↩ Répondre</a>
        <a href="#" class="reply-btn">↪ Transférer</a>
    </div>

</div>
</body>
</html>
