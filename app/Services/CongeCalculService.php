<?php

namespace App\Services;

use App\Models\jour_feries;
use Carbon\Carbon;

class CongeCalculService
{
    /**
     * Détermine la date de début réel du décompte des congés.
     *
     * Règle : le lendemain de la cessation de service.
     * Exception : si la cessation est un vendredi, le comptage commence le lundi suivant
     *             (le samedi n'est pas considéré comme premier jour ouvrable dans ce cas).
     */
    public function getDebutDecompte(Carbon $dateCessation): Carbon
    {
        $debut = $dateCessation->copy()->addDay();

        // Si vendredi (5) : on saute au lundi
        if ($dateCessation->dayOfWeek === Carbon::FRIDAY) {
            $debut = $dateCessation->copy()->next(Carbon::MONDAY);
        }

        return $debut;
    }

    /**
     * Vérifie si une date est un jour ouvrable (lundi–samedi, hors jours fériés).
     */
    public function isJourOuvrable(Carbon $date): bool
    {
        // Dimanche = non ouvrable
        if ($date->dayOfWeek === Carbon::SUNDAY) {
            return false;
        }

        // Jour férié = non ouvrable
        if (jour_feries::isFerie($date->toDateString())) {
            return false;
        }

        return true;
    }

    /**
     * Calcule la date de reprise en ajoutant N jours ouvrables à partir d'une date de début.
     */
    public function calculerDateReprise(Carbon $debutDecompte, int $joursOuvrables): Carbon
    {
        $current = $debutDecompte->copy()->subDay(); // on commence avant pour bien compter
        $count   = 0;

        while ($count < $joursOuvrables) {
            $current->addDay();
            if ($this->isJourOuvrable($current)) {
                $count++;
            }
        }

        // La reprise est le lendemain du dernier jour de congé
        return $current->addDay();
    }

    /**
     * Compte le nombre de jours ouvrables entre deux dates (inclusif).
     */
    public function compterJoursOuvrables(Carbon $debut, Carbon $fin): int
    {
        $count   = 0;
        $current = $debut->copy();

        while ($current->lte($fin)) {
            if ($this->isJourOuvrable($current)) {
                $count++;
            }
            $current->addDay();
        }

        return $count;
    }

    /**
     * Calcule les jours acquis selon les mois de service (2 jours par mois, max 24).
     */
    public function calculerJoursAcquis(\Carbon\Carbon $datePriseService): int
    {
        $mois = $datePriseService->diffInMonths(now());
        return min($mois * 2, 24);
    }

    /**
     * Retourne un tableau récapitulatif du calcul d'un congé.
     */
    public function simuler(string $dateCessationStr, int $joursOuvrables): array
    {
        $cessation     = Carbon::parse($dateCessationStr);
        $debutDecompte = $this->getDebutDecompte($cessation);
        $reprise       = $this->calculerDateReprise($debutDecompte, $joursOuvrables);

        return [
            'date_cessation'    => $cessation->toDateString(),
            'debut_decompte'    => $debutDecompte->toDateString(),
            'jours_ouvrables'   => $joursOuvrables,
            'date_reprise'      => $reprise->toDateString(),
            'cessation_vendredi' => $cessation->dayOfWeek === Carbon::FRIDAY,
        ];
    }
}
