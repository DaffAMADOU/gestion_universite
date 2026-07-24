<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('absences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agent_id')->constrained('agents')->onDelete('cascade');
            $table->date('date_debut');
            $table->integer('nombre_jours');
            $table->string('motif'); // maladie, mission, formation, mariage, bapteme, deces, autre
            $table->boolean('deductible')->default(true); // false pour absences exceptionnelles
            $table->integer('annee');
            $table->text('observations')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('absences');
    }
};
