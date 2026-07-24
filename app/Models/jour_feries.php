<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class jour_feries extends Model
{
    protected $table = 'jours_feries';

    protected $fillable = [
        'date',
        'designation',
        'annee',
        'recurrent'
    ];

    protected $casts = [
        'date'      => 'date',
        'recurrent' => 'boolean',
    ];

    /**
     * Liste des jours fériés d'une année
     */
    public static function getDatesForYear(int $year): array
    {
        return self::where('annee', $year)
            ->pluck('date')
            ->map(fn($d) => Carbon::parse($d)->toDateString())
            ->toArray();
    }

    /**
     * Vérifie si une date est fériée
     */
    public static function isFerie(string $date): bool
    {
         $year = (int) substr($date, 0, 4);
        return self::where('annee', $year)->where('date', $date)->exists();
    }
}
