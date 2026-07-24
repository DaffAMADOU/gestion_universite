<?php

namespace App\Http\Controllers;

use App\Models\Agent;
use App\Models\Conge;
use App\Models\Absence;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class RapportController extends Controller
{
    /**
     * Page principale des rapports
     */
    public function index()
    {
        $directions = Agent::select('direction')->distinct()->pluck('direction');
        $annees     = range(now()->year - 2, now()->year + 1);
        return view('rapports.index', compact('directions', 'annees'));
    }

    /**
     * Génère et affiche les tableaux par direction/UFR
     */
    public function generer(Request $request)
    {
        $annee     = $request->get('annee', now()->year);
        $direction = $request->get('direction'); // null = toutes

        $query = Agent::with(['conges', 'absences'])->orderBy('nom');
        if ($direction) {
            $query->where('direction', $direction);
        }

        $agents = $query->get();

        // Grouper par direction
        $parDirection = $agents->groupBy('direction');

        $directions = Agent::select('direction')->distinct()->pluck('direction');
        $annees     = range(now()->year - 2, now()->year + 1);

        return view('rapports.index', compact('parDirection', 'directions', 'annees', 'annee', 'direction'));
    }

    /**
     * Export PDF d'une direction
     */
    public function exportPdf(Request $request)
    {
        $annee     = $request->get('annee', now()->year);
        $direction = $request->get('direction');

        $query = Agent::with(['conges', 'absences'])->orderBy('nom');
        if ($direction) {
            $query->where('direction', $direction);
        }

        $agents       = $query->get();
        $parDirection = $agents->groupBy('direction');

        $pdf = Pdf::loadView('rapports.pdf', compact('parDirection', 'annee', 'direction'))
            ->setPaper('a4', 'landscape');

        $filename = 'rapport_conges_' . ($direction ?? 'toutes') . '_' . $annee . '.pdf';
        return $pdf->download($filename);
    }

    /**
     * Dashboard statistiques
     */
    public function dashboard()
    {
        $annee  = now()->year;
        $today  = now()->toDateString();

        $stats = [
            'total_agents'     => Agent::count(),
            'en_conge'         => Conge::where('date_cessation', '<=', $today)
                                       ->where('date_reprise', '>=', $today)->count(),
            'absences_mois'    => Absence::where('annee', $annee)
                                         ->whereMonth('date_debut', now()->month)
                                         ->sum('nombre_jours'),
            'alertes'          => 0, // calculé ci-dessous
            'conges_this_year' => Conge::where('annee', $annee)->count(),
        ];

        // Agents avec jours restants < 5
        $agents = Agent::with(['conges', 'absences'])->get();
        $stats['alertes'] = $agents->filter(fn($a) => $a->jours_restants < 5 && $a->jours_dus > 0)->count();

        // Congés en cours
        $enCours = Conge::with('agent')
            ->where('date_cessation', '<=', $today)
            ->where('date_reprise', '>=', $today)
            ->get();

        // Stats par direction
        $parDirection = $agents->groupBy('direction')->map(fn($grp) => [
            'count'      => $grp->count(),
            'jours_dus'  => $grp->sum(fn($a) => $a->jours_dus),
            'jours_pris' => $grp->sum(fn($a) => $a->jours_pris),
        ]);

        return view('dashboard.index', compact('stats', 'enCours', 'parDirection', 'annee'));
    }
}
