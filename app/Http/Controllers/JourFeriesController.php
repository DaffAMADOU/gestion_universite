<?php

namespace App\Http\Controllers;

use App\Models\jour_feries;
use Illuminate\Http\Request;

class JourFeriesController extends Controller
{
    public function index(Request $request)
    {
        $annee = $request->get('annee', now()->year);
     $feries = jour_feries::where('annee', $annee)
            ->orderBy('date')
            ->get();

        $annees = range(now()->year - 1, now()->year + 2);

        return view('calendrier.index', compact('feries', 'annee', 'annees'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'date'        => 'required|date|unique:jours_feries,date',
            'designation' => 'required|string|max:150',
            'recurrent'   => 'nullable|boolean',
        ]);

        jour_feries::create([
            'date'        => $validated['date'],
            'designation' => $validated['designation'],
            'annee'       => (int) substr($validated['date'], 0, 4),
            'recurrent'   => $validated['recurrent'] ?? false,
        ]);

        return back()->with('success', 'Jour férié ajouté avec succès.');
    }

    public function destroy(jour_feries $jourFerie)
    {
        $jourFerie->delete();

        return back()->with('success', 'Jour férié supprimé.');
    }
}
