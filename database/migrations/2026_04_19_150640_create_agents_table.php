<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('agents', function (Blueprint $table) {
            $table->id();
            $table->string('prenom');
            $table->string('nom');
            $table->string('matricule')->unique();
            $table->string('direction'); // DRH, DAF, UFR Sciences, Rectorat, etc.
            $table->date('date_prise_service');
           
            $table->enum('sexe', ['M', 'F'])->default('M');
            $table->integer('nombre_enfants')->default(0); // +1 jour/enfant pour les femmes
            $table->integer('jours_report_n1')->default(0); // Jours reportés de N-1
            $table->integer('jours_acquis_annee')->default(24); // 24 jours max/an
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agents');
    }
};
