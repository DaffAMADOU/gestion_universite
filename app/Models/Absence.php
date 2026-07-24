<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Absence extends Model
{
    protected $fillable = [
        'agent_id', 'date_debut', 'nombre_jours',
        'motif', 'deductible', 'annee', 'observations',
    ];

    protected $casts = [
        'date_debut'  => 'date',
        'deductible'  => 'boolean',
    ];

    /** Motifs exceptionnels non déductibles par défaut */
    public const MOTIFS_EXCEPTIONNELS = ['mariage', 'bapteme', 'deces'];

    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agent::class);
    }

    public function getMotifLabelAttribute(): string
    {
        return match($this->motif) {
            'maladie'   => 'Maladie',
            'mission'   => 'Mission officielle',
            'formation' => 'Formation',
            'mariage'   => 'Mariage',
            'bapteme'   => 'Baptême',
            'deces'     => 'Décès d\'un proche',
            default     => 'Autre',
        };
    }

    /**
     * Vérifie si le motif est exceptionnel (non déductible par défaut)
     */
    public function getIsExceptionnelAttribute(): bool
    {
        return in_array($this->motif, self::MOTIFS_EXCEPTIONNELS);
    }
}
