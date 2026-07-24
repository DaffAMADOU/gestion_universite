@extends('layout.template')

@section('title', 'Modifier congé')
@section('page-title', 'Modifier le congé')

@section('topbar-actions')
    <a href="{{ route('conges.index') }}" class="btn-gc btn-outline-gc">
        <i class="bi bi-arrow-left"></i> Retour
    </a>
@endsection

@section('content')

<form method="POST" action="{{ route('conges.update', $conge->id) }}">
    @csrf
    @method('PUT')

    <div class="card-gc">

        <h4>Agent</h4>

        <select name="agent_id" class="form-control-gc" required>
            @foreach($agents as $a)
                <option value="{{ $a->id }}"
                    {{ $conge->agent_id == $a->id ? 'selected' : '' }}>
                    {{ $a->prenom }} {{ $a->nom }} ({{ $a->matricule }})
                </option>
            @endforeach
        </select>

        <hr>

        <h4>Congé</h4>

        <label>Date cessation</label>
        <input type="date"
               name="date_cessation"
               class="form-control-gc"
               value="{{ $conge->date_cessation?->format('Y-m-d') }}"
               required>

        <label>Jours ouvrables</label>
        <input type="number"
               name="jours_ouvrables"
               class="form-control-gc"
               value="{{ $conge->jours_ouvrables }}"
               min="1"
               required>

        <label>Type</label>
        <select name="type" class="form-control-gc">
            <option value="administratif" {{ $conge->type == 'administratif' ? 'selected' : '' }}>
                Administratif
            </option>

            <option value="exceptionnel_deductible" {{ $conge->type == 'exceptionnel_deductible' ? 'selected' : '' }}>
                Exceptionnel déductible
            </option>

            <option value="exceptionnel_non_deductible" {{ $conge->type == 'exceptionnel_non_deductible' ? 'selected' : '' }}>
                Exceptionnel non déductible
            </option>
        </select>

        <label>Observations</label>
        <textarea name="observations" class="form-control-gc" rows="3">
            {{ $conge->observations }}
        </textarea>

        <br>

        <div style="display:flex;justify-content:flex-end;gap:10px;">
            <a href="{{ route('conges.index') }}" class="btn-gc btn-outline-gc">
                Annuler
            </a>

            <button type="submit" class="btn-gc btn-primary-gc">
                Modifier
            </button>
        </div>

    </div>

</form>

@endsection
