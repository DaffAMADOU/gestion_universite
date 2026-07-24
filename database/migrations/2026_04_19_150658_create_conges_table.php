<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('conges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agent_id')->constrained('agents')->onDelete('cascade');
            $table->date('date_cessation');    // Jour où l'agent cesse service
            $table->date('date_reprise');      // Jour calculé de reprise
            $table->integer('jours_ouvrables'); // Nombre de jours pris
            $table->enum('type', [
                'administratif',
                'exceptionnel_deductible',
                'exceptionnel_non_deductible'
            ])->default('administratif');
            $table->text('observations')->nullable();
            $table->integer('annee'); // Année du congé
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conges');
    }
};
