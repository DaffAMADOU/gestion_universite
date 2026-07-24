<?php

namespace App\Http\Controllers;

use App\Models\Agent;
use App\Models\Absence;
use Illuminate\Http\Request;

class AbsenceController extends Controller
{
    public function index(Request $request)
    {
        $query = Absence::with('agent')->orderByDesc('date_debut');

        if ($request->filled('search')) {
            $q = $request->search;
            $query->whereHas('agent', function ($q2) use ($q) {
                $q2->where('nom', 'like', "%{$q}%")
                   ->orWhere('prenom', 'like', "%{$q}%")
                   ->orWhere('matricule', 'like', "%{$q}%");
            });
        }

        if ($request->filled('motif')) {
            $query->where('motif', $request->motif);
        }

        $absences = $query->paginate(20);
        $agents   = Agent::orderBy('nom')->get();

        return view('absences.index', compact('absences', 'agents'));
    }

    public function create()
    {
        $agents = Agent::orderBy('nom')->get();
        return view('absences.create', compact('agents'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'agent_id'     => 'required|exists:agents,id',
            'date_debut'   => 'required|date',
            'nombre_jours' => 'required|integer|min:1',
            'motif'        => 'required|string|max:100',
            'deductible'   => 'nullable|boolean',
            'observations' => 'nullable|string|max:500',
        ]);

        // Absences exceptionnelles = non déductibles par défaut
        $motifsExceptionnels = ['mariage', 'bapteme', 'deces'];
        $deductible = isset($validated['deductible'])
            ? (bool) $validated['deductible']
            : !in_array($validated['motif'], $motifsExceptionnels);

        Absence::create([
            'agent_id'     => $validated['agent_id'],
            'date_debut'   => $validated['date_debut'],
            'nombre_jours' => $validated['nombre_jours'],
            'motif'        => $validated['motif'],
            'deductible'   => $deductible,
            'annee'        => now()->year,
            'observations' => $validated['observations'] ?? null,
        ]);

        return redirect()->route('absences.index')
            ->with('success', 'Absence enregistrée. Le solde de l\'agent a été mis à jour.');
    }

    public function destroy(Absence $absence)
    {
        $absence->delete();
        return redirect()->route('absences.index')
            ->with('success', 'Absence supprimée.');
    }
}
