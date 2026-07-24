<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Conge extends Model
{
    protected $fillable = [
        'agent_id', 'date_cessation', 'date_reprise',
        'jours_ouvrables', 'type', 'observations', 'annee',
    ];

    protected $casts = [
        'date_cessation' => 'date',
        'date_reprise'   => 'date',
    ];

    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agent::class);
    }

    /**
     * Statut du congé : en_cours, termine, a_venir
     */
    public function getStatutAttribute(): string
    {
        $today = now()->toDateString();
        if ($this->date_cessation->toDateString() <= $today && $this->date_reprise->toDateString() >= $today) {
            return 'en_cours';
        }
        if ($this->date_reprise->toDateString() < $today) {
            return 'termine';
        }
        return 'a_venir';
    }

    public function getStatutLabelAttribute(): string
    {
        return match($this->statut) {
            'en_cours' => 'En cours',
            'termine'  => 'Terminé',
            'a_venir'  => 'À venir',
        };
    }

    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            'administratif'                 => 'Administratif',
            'exceptionnel_deductible'       => 'Exceptionnel (déductible)',
            'exceptionnel_non_deductible'   => 'Exceptionnel (non déductible)',
            default => $this->type,
        };
    }
}
