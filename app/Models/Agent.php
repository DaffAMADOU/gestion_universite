<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Agent extends Model
{
    protected $fillable = [
        'prenom', 'nom', 'matricule', 'direction',
        'date_prise_service', 'sexe',
        'nombre_enfants', 'jours_report_n1', 'jours_acquis_annee',
    ];

    protected $casts = [
        'date_prise_service' => 'date',
    ];

    // ── Relations ─────────────────────────────────────────────

    public function conges(): HasMany
    {
        return $this->hasMany(Conge::class);
    }

    public function absences(): HasMany
    {
        return $this->hasMany(Absence::class);
    }

    // ── Accesseurs calculés ───────────────────────────────────

    /**
     * Nom complet de l'agent
     */
    public function getNomCompletAttribute(): string
    {
        return "{$this->prenom} {$this->nom}";
    }

    /**
     * Bonus jours congé pour enfants (femmes uniquement)
     */
    public function getBonusEnfantsAttribute(): int
    {
        if ($this->sexe === 'F') {
            return (int) $this->nombre_enfants;
        }
        return 0;
    }

    /**
     * Total jours dus = report_N1 + acquis + bonus_enfants - absences_deductibles
     * Plafonné à 72 jours (max légal)
     */
    public function getJoursDusAttribute(): int
    {
        $absencesDed = $this->absences()
            ->where('deductible', true)
            ->where('annee', now()->year)
            ->sum('nombre_jours');

        $total = $this->jours_report_n1
               + $this->jours_acquis_annee
               + $this->bonus_enfants
               - $absencesDed;

        return min(max($total, 0), 72);
    }

    /**
     * Jours déjà pris en congé (hors non-déductibles)
     */
    public function getJoursPrisAttribute(): int
    {
        return (int) $this->conges()
            ->where('type', '!=', 'exceptionnel_non_deductible')
            ->where('annee', now()->year)
            ->sum('jours_ouvrables');
    }

    /**
     * Absences déductibles de l'année
     */
    public function getAbsencesDeductiblesAttribute(): int
    {
        return (int) $this->absences()
            ->where('deductible', true)
            ->where('annee', now()->year)
            ->sum('nombre_jours');
    }

    /**
     * Jours restants = jours_dus - jours_pris
     */
    public function getJoursRestantsAttribute(): int
    {
        return max($this->jours_dus - $this->jours_pris, 0);
    }

    /**
     * Vérifie si l'agent est actuellement en congé
     */
    public function getEnCongeAttribute(): bool
    {
        $today = now()->toDateString();
        return $this->conges()
            ->where('date_cessation', '<=', $today)
            ->where('date_reprise', '>=', $today)
            ->exists();
    }

    /**
     * Vérifie si l'agent a droit aux congés (min 12 mois de service)
     */
    public function getADroitCongeAttribute(): bool
    {
        if (!$this->date_prise_service) return false;
        return $this->date_prise_service->diffInMonths(now()) >= 12;
    }

    /**
     * Statut d'alerte si jours restants < 5
     */
    public function getAlerteAttribute(): bool
    {
        return $this->jours_restants < 5 && $this->jours_dus > 0;
    }
}
