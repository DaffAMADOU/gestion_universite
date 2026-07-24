<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| MODELS
|--------------------------------------------------------------------------
*/
use App\Models\Agent;
use App\Models\Conge;
use App\Models\Absence;

/*
|--------------------------------------------------------------------------
| CONTROLLERS
|--------------------------------------------------------------------------
*/
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AgentController;
use App\Http\Controllers\CongeController;
use App\Http\Controllers\AbsenceController;
use App\Http\Controllers\JourFeriesController;
use App\Http\Controllers\RapportController;
use App\Http\Controllers\Auth\RegisterController;

Route::get('/login',   [AuthController::class, 'showLogin'])->name('login');
Route::post('/login',  [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/register', [RegisterController::class, 'create'])->name('register');
Route::post('/register', [RegisterController::class, 'store']);



Route::middleware('auth')->group(function () {
/*
|--------------------------------------------------------------------------
| DASHBOARD
|--------------------------------------------------------------------------
*/
Route::get('/', function () {

    return view('dashboard', [

        'stats' => [
            'total_agents' => Agent::count(),

            'en_conge' => Conge::whereDate('date_cessation', '<=', now())
                                ->whereDate('date_reprise', '>=', now())
                                ->count(),

            'absences_mois' => Absence::whereMonth('created_at', now()->month)->count(),

            'alertes' => Agent::all()->filter(function ($agent) {
                return (
                    $agent->jours_acquis_annee
                    + $agent->jours_report_n1
                    - $agent->jours_pris
                    - $agent->absences_deductibles
                ) < 5;
            })->count(),
        ],

        'enCours' => Conge::with('agent')
            ->whereDate('date_cessation', '<=', now())
            ->whereDate('date_reprise', '>=', now())
            ->get(),

        'parDirection' => Agent::all()
            ->groupBy('direction')
            ->map(function ($group) {
                return [
                    'count' => $group->count(),
                    'jours_dus' => $group->sum('jours_acquis_annee'),
                    'jours_pris' => $group->sum('jours_pris'),
                ];
            }),
    ]);

})->name('dashboard');


/*
|--------------------------------------------------------------------------
| AGENTS
|--------------------------------------------------------------------------
*/
Route::get('/agents', [AgentController::class, 'index'])->name('agents.index');
Route::get('/agents/create', [AgentController::class, 'create'])->name('agents.create');
Route::post('/agents/store', [AgentController::class, 'store'])->name('agents.store');
Route::get('/agents/show/{agent}', [AgentController::class, 'show'])->name('agents.show');
Route::get('/agents/edit/{agent}', [AgentController::class, 'edit'])->name('agents.edit');
Route::put('/agents/update/{agent}', [AgentController::class, 'update'])->name('agents.update');
Route::delete('/agents/delete/{agent}', [AgentController::class, 'destroy'])->name('agents.destroy');


/*
|--------------------------------------------------------------------------
| CONGÉS
|--------------------------------------------------------------------------
*/
Route::get('/conges', [CongeController::class, 'index'])->name('conges.index');
Route::get('/conges/create', [CongeController::class, 'create'])->name('conges.create');
Route::post('/conges/store', [CongeController::class, 'store'])->name('conges.store');
Route::get('/conges/show/{conge}', [CongeController::class, 'show'])->name('conges.show');
Route::get('/conges/edit/{conge}', [CongeController::class, 'edit'])->name('conges.edit');
Route::put('/conges/update/{conge}', [CongeController::class, 'update'])->name('conges.update');
Route::delete('/conges/delete/{conge}', [CongeController::class, 'destroy'])->name('conges.destroy');


/*
|--------------------------------------------------------------------------
| ABSENCES
|--------------------------------------------------------------------------
*/
Route::get('/absences', [AbsenceController::class, 'index'])->name('absences.index');
Route::get('/absences/create', [AbsenceController::class, 'create'])->name('absences.create');
Route::post('/absences/store', [AbsenceController::class, 'store'])->name('absences.store');
Route::get('/absences/show/{absence}', [AbsenceController::class, 'show'])->name('absences.show');
Route::get('/absences/edit/{absence}', [AbsenceController::class, 'edit'])->name('absences.edit');
Route::put('/absences/update/{absence}', [AbsenceController::class, 'update'])->name('absences.update');
Route::delete('/absences/delete/{absence}', [AbsenceController::class, 'destroy'])->name('absences.destroy');


/*
|--------------------------------------------------------------------------
| JOURS FÉRIÉS
|--------------------------------------------------------------------------
*/
    Route::get('/calendrier',                   [JourFeriesController::class, 'index'])->name('feries.index');
    Route::post('/calendrier',                  [JourFeriesController::class, 'store'])->name('feries.store');
    Route::delete('/calendrier/{jourFerie}',    [JourFeriesController::class, 'destroy'])->name('feries.destroy');

/*
|--------------------------------------------------------------------------
| RAPPORTS
|--------------------------------------------------------------------------
*/
Route::get('/rapports', [RapportController::class, 'index'])->name('rapports.index');
Route::get('/rapports/generer', [RapportController::class, 'generer'])->name('rapports.generer');
Route::get('/rapports/pdf', [RapportController::class, 'exportPdf'])->name('rapports.pdf');



});
