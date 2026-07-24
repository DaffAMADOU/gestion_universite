@extends('layout.template')

@section('title', 'Enregistrer un congé')
@section('page-title', 'Enregistrer un congé')

@section('topbar-actions')
<a href="{{ route('conges.index') }}" class="btn-gc btn-outline-gc">
    <i class="bi bi-arrow-left"></i> Retour
</a>
@endsection

@section('content')

<div class="alert-warning-gc">
    <strong>⚠️ Règles :</strong>
    Les jours fériés et dimanches ne sont pas comptés.
    Le maximum est 72 jours cumulés.
</div>

<form method="POST" action="{{ route('conges.store') }}">
@csrf

{{-- AGENT --}}
<div class="form-section">
    <h4>Agent concerné</h4>

    <select name="agent_id" class="form-control-gc" required>
        <option value="">-- Choisir un agent --</option>
        @foreach($agents as $a)
            <option value="{{ $a->id }}">
                {{ $a->prenom ?? '' }} {{ $a->nom ?? '' }} ({{ $a->matricule }})
            </option>
        @endforeach
    </select>
</div>

{{-- CONGÉ --}}
<div class="form-section">
    <h4>Détails du congé</h4>

    <label>Date de cessation</label>
    <input type="date" name="date_cessation" class="form-control-gc" required>

    <label>Jours ouvrables</label>
    <input type="number" name="jours_ouvrables" class="form-control-gc" min="1" required>

    <label>Type</label>
    <select name="type" class="form-control-gc">
        <option value="administratif">Administratif</option>
        <option value="exceptionnel_deductible">Exceptionnel déductible</option>
        <option value="exceptionnel_non_deductible">Exceptionnel non déductible</option>
    </select>
</div>

<div style="display:flex;justify-content:flex-end;gap:10px;">
    <a href="{{ route('conges.index') }}" class="btn-gc btn-outline-gc">Annuler</a>
    <button class="btn-gc btn-primary-gc">Enregistrer</button>
</div>

</form>

@endsection
