<?php

namespace App\Http\Controllers;

use App\Models\Agent;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AgentController extends Controller
{
    public function index(Request $request)
    {
        $query = Agent::with(['conges', 'absences']);

        if ($request->filled('search')) {

            $search = $request->search;

            $query->where(function ($q) use ($search) {

                $q->where('nom', 'like', "%{$search}%")
                  ->orWhere('prenom', 'like', "%{$search}%")
                  ->orWhere('matricule', 'like', "%{$search}%");
            });
        }

        if ($request->filled('direction')) {
            $query->where('direction', $request->direction);
        }

        $agents = $query->orderBy('nom')->get();

        $directions = $this->getDirections();

        return view('agents.index', compact('agents', 'directions'));
    }

    public function create()
    {
        $directions = $this->getDirections();

        return view('agents.create', compact('directions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([

            'prenom' => 'required|string|max:100',

            'nom' => 'required|string|max:100',

            'matricule' => 'required|string|max:50|unique:agents,matricule',

            'direction' => 'required|string|max:100',

            'date_prise_service' => 'required|date',

            'sexe' => 'required|in:M,F',

            'nombre_enfants' => 'nullable|integer|min:0|max:20',

            'jours_report_n1' => 'nullable|integer|min:0|max:72',

            'jours_acquis_annee' => 'nullable|integer|min:0|max:24',
        ]);

        $validated['nombre_enfants'] =
            $validated['nombre_enfants'] ?? 0;

        $validated['jours_report_n1'] =
            $validated['jours_report_n1'] ?? 0;

        $validated['jours_acquis_annee'] =
            $validated['jours_acquis_annee'] ?? 24;

        // Homme → pas de bonus enfants
        if ($validated['sexe'] == 'M') {
            $validated['nombre_enfants'] = 0;
        }

        Agent::create($validated);

        return redirect()
            ->route('agents.index')
            ->with('success', 'Agent enregistré avec succès.');
    }

    public function show(Agent $agent)
    {
        $agent->load([
            'conges' => fn($q) => $q->orderByDesc('date_cessation'),
            'absences' => fn($q) => $q->orderByDesc('date_debut'),
        ]);

        return view('agents.show', compact('agent'));
    }

    public function edit(Agent $agent)
    {
        $directions = $this->getDirections();

        return view('agents.edit', compact('agent', 'directions'));
    }

    public function update(Request $request, Agent $agent)
    {
        $validated = $request->validate([

            'prenom' => 'required|string|max:100',

            'nom' => 'required|string|max:100',

            'matricule' => [
                'required',
                'string',
                'max:50',
                Rule::unique('agents')->ignore($agent->id)
            ],

            'direction' => 'required|string|max:100',

            'date_prise_service' => 'required|date',

            'sexe' => 'required|in:M,F',

            'nombre_enfants' => 'nullable|integer|min:0|max:20',

            'jours_report_n1' => 'nullable|integer|min:0|max:72',

            'jours_acquis_annee' => 'nullable|integer|min:0|max:24',
        ]);

        if ($validated['sexe'] == 'M') {
            $validated['nombre_enfants'] = 0;
        }

        $agent->update($validated);

        return redirect()
            ->route('agents.index')
            ->with('success', 'Agent modifié avec succès.');
    }

    public function destroy(Agent $agent)
    {
        $agent->delete();

        return redirect()
            ->route('agents.index')
            ->with('success', 'Agent supprimé.');
    }

    private function getDirections(): array
    {
        return [

            'DRH',
            'DAF',
            'DCI',
            'DSI',
            'DEP',

            'Rectorat',
            'Vice-Rectorat',

            'UFR Sciences',
            'UFR Lettres',
            'UFR Droit',
            'UFR Médecine',
            'UFR Économie',
            'UFR Technologie',
        ];
    }
}
