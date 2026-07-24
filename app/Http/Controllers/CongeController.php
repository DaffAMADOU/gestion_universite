<?php

namespace App\Http\Controllers;

use App\Models\Agent;
use App\Models\Conge;
use App\Services\CongeCalculService;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Mail\CongeValide;
use Illuminate\Support\Facades\Mail;

class CongeController extends Controller
{
    private $calcService;

    public function __construct(CongeCalculService $calcService)
    {
        $this->calcService = $calcService;
    }

    /*
    |--------------------------------------------------------------------------
    | LISTE DES CONGÉS
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $query = Conge::with('agent')
            ->orderByDesc('date_cessation');

        /*
        |--------------------------------------------------------------------------
        | RECHERCHE
        |--------------------------------------------------------------------------
        */

        if ($request->filled('search')) {

            $search = $request->search;

            $query->whereHas('agent', function ($q) use ($search) {

                $q->where('nom', 'like', "%{$search}%")
                  ->orWhere('prenom', 'like', "%{$search}%")
                  ->orWhere('matricule', 'like', "%{$search}%");
            });
        }

        /*
        |--------------------------------------------------------------------------
        | FILTRE DIRECTION
        |--------------------------------------------------------------------------
        */

        if ($request->filled('direction')) {

            $query->whereHas('agent', function ($q) use ($request) {

                $q->where('direction', $request->direction);
            });
        }

        /*
        |--------------------------------------------------------------------------
        | FILTRE STATUT
        |--------------------------------------------------------------------------
        */

        if ($request->filled('statut')) {

            $today = now()->toDateString();

            if ($request->statut == 'en_cours') {

                $query->where('date_cessation', '<=', $today)
                      ->where('date_reprise', '>=', $today);
            }

            elseif ($request->statut == 'termine') {

                $query->where('date_reprise', '<', $today);
            }

            elseif ($request->statut == 'a_venir') {

                $query->where('date_cessation', '>', $today);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | DONNÉES
        |--------------------------------------------------------------------------
        */

        $conges = $query->paginate(10);

        $agents = Agent::orderBy('nom')->get();

        $directions = Agent::select('direction')
            ->distinct()
            ->pluck('direction');

        return view(
            'conges.index',
            compact('conges', 'agents', 'directions')
        );
    }

    /*
    |--------------------------------------------------------------------------
    | FORMULAIRE AJOUT
    |--------------------------------------------------------------------------
    */

    public function create()
    {
        $agents = Agent::orderBy('nom')->get();

        return view('conges.create', compact('agents'));

    }

    /*
    |--------------------------------------------------------------------------
    | ENREGISTREMENT
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $validated = $request->validate([

            'agent_id' => 'required|exists:agents,id',

            'date_cessation' => 'required|date',

            'jours_ouvrables' => 'required|integer|min:1',

            'type' => 'required|in:administratif,exceptionnel_deductible,exceptionnel_non_deductible',

            'observations' => 'nullable|string|max:500',
        ]);

        /*
        |--------------------------------------------------------------------------
        | AGENT
        |--------------------------------------------------------------------------
        */

        $agent = Agent::findOrFail($validated['agent_id']);

        /*
        |--------------------------------------------------------------------------
        | INTERDICTION SI PRISE DE SERVICE CETTE ANNÉE
        |--------------------------------------------------------------------------
        */

        $anneePriseService =
            Carbon::parse($agent->date_prise_service)->year;

        $anneeActuelle = now()->year;

        if ($anneePriseService == $anneeActuelle) {

            return back()
                ->withInput()
                ->withErrors([

                    'agent_id' =>
                    "Cet agent ne peut pas prendre de congé cette année car il a pris service en {$anneeActuelle}."
                ]);
        }

        /*
        |--------------------------------------------------------------------------
        | VÉRIFICATION SOLDE
        |--------------------------------------------------------------------------
        */

        if ($validated['type'] != 'exceptionnel_non_deductible') {

            if ($validated['jours_ouvrables'] > $agent->jours_restants) {

                return back()
                    ->withInput()
                    ->withErrors([

                        'jours_ouvrables' =>

                        "Impossible : l'agent ne possède que {$agent->jours_restants} jour(s) restant(s)."
                    ]);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | CALCUL DATE REPRISE
        |--------------------------------------------------------------------------
        */

        $simulation = $this->calcService->simuler(

            $validated['date_cessation'],

            $validated['jours_ouvrables']
        );

        /*
        |--------------------------------------------------------------------------
        | ENREGISTREMENT
        |--------------------------------------------------------------------------
        */

        Conge::create([

            'agent_id' => $validated['agent_id'],

            'date_cessation' => $simulation['date_cessation'],

            'date_reprise' => $simulation['date_reprise'],

            'jours_ouvrables' => $validated['jours_ouvrables'],

            'type' => $validated['type'],

            'observations' => $validated['observations'] ?? null,

            'annee' => now()->year,
        ]);
            if ($agent->email) {
    Mail::to($agent->email)->send(new CongeValide($conge));}
        /*
        |--------------------------------------------------------------------------
        | REDIRECTION
        |--------------------------------------------------------------------------
        */

        return redirect()
            ->route('conges.index')
            ->with(

                'success',

                'Congé enregistré avec succès. '
                . 'Reprise prévue le '
                . Carbon::parse(
                    $simulation['date_reprise']
                )->format('d/m/Y')
            );
    }

    /*
    |--------------------------------------------------------------------------
    | SIMULATION
    |--------------------------------------------------------------------------
    */
public function show(Conge $conge)
{
    $conge->load('agent');

    return view('conges.show', compact('conge'));
}
    public function simuler(Request $request)
    {
        $request->validate([

            'date_cessation' => 'required|date',

            'jours_ouvrables' => 'required|integer|min:1',
        ]);

        $result = $this->calcService->simuler(

            $request->date_cessation,

            (int) $request->jours_ouvrables
        );

        return response()->json($result);
    }

    /*
    |--------------------------------------------------------------------------
    | SUPPRESSION
    |--------------------------------------------------------------------------
    */

    public function destroy(Conge $conge)
    {
        $conge->delete();

        return redirect()
            ->route('conges.index')
            ->with('success', 'Congé supprimé.');
    }
}
