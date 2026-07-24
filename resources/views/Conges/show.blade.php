@extends('layout.template')

@section('title', 'Détail congé')
@section('page-title', 'Détail du congé')

@section('topbar-actions')
    <a href="{{ route('conges.index') }}" class="btn-gc btn-outline-gc">
        <i class="bi bi-arrow-left"></i> Retour
    </a>

    <a href="{{ route('conges.edit', $conge->id) }}" class="btn-gc btn-primary-gc">
        <i class="bi bi-pencil"></i> Modifier
    </a>
@endsection

@section('content')

<div class="card-gc">

    <h4>Informations agent</h4>

    <div class="form-grid">
        <div>
            <strong>Nom :</strong>
            {{ $conge->agent?->nom ?? '-' }}
        </div>

        <div>
            <strong>Prénom :</strong>
            {{ $conge->agent?->prenom ?? '-' }}
        </div>

        <div>
            <strong>Matricule :</strong>
            {{ $conge->agent?->matricule ?? '-' }}
        </div>

        <div>
            <strong>Direction :</strong>
            {{ $conge->agent?->direction ?? '-' }}
        </div>
    </div>

    <hr>

    <h4>Détails du congé</h4>

    <div class="form-grid">

        <div>
            <strong>Date cessation :</strong>
            {{ $conge->date_cessation?->format('d/m/Y') }}
        </div>

        <div>
            <strong>Date reprise :</strong>
            {{ $conge->date_reprise?->format('d/m/Y') }}
        </div>

        <div>
            <strong>Jours pris :</strong>
            {{ $conge->jours_ouvrables }}
        </div>

        <div>
            <strong>Année :</strong>
            {{ $conge->annee }}
        </div>

        <div>
            <strong>Type :</strong>
            {{ $conge->type }}
        </div>

    </div>

    <hr>

    <div>
        <strong>Observations :</strong>
        <p>{{ $conge->observations ?? 'Aucune observation' }}</p>
    </div>

</div>

@endsection
