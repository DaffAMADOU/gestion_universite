@extends('layout.template')

@section('title', 'Nouvel agent')
@section('page-title', 'Ajouter un agent')

@section('topbar-actions')
    <a href="{{ route('agents.index') }}" class="btn-gc btn-outline-gc">
        <i class="bi bi-arrow-left"></i> Retour
    </a>
@endsection

@section('content')

<div class="container-fluid">

<form method="POST" action="{{ route('agents.store') }}">
    @csrf

    {{-- ================= IDENTITÉ ================= --}}
    <div class="form-section">
        <h4><i class="bi bi-person-fill me-2"></i>Identité de l'agent</h4>

        <div class="form-grid">

            <div class="form-group">
                <label class="form-label">Prénom *</label>
                <input type="text" name="prenom" class="form-control-gc"
                       value="{{ old('prenom') }}" required>
                @error('prenom')<span class="form-hint text-danger">{{ $message }}</span>@enderror
            </div>

            <div class="form-group">
                <label class="form-label">Nom *</label>
                <input type="text" name="nom" class="form-control-gc"
                       value="{{ old('nom') }}" required>
                @error('nom')<span class="form-hint text-danger">{{ $message }}</span>@enderror
            </div>

            <div class="form-group">
                <label class="form-label">Matricule *</label>
                <input type="text" name="matricule" class="form-control-gc"
                       value="{{ old('matricule') }}" required>
                @error('matricule')<span class="form-hint text-danger">{{ $message }}</span>@enderror
            </div>

            <div class="form-group">
                <label class="form-label">Direction *</label>
                <select name="direction" class="form-control-gc" required>
                    <option value="">-- Sélectionner --</option>
                    @foreach($directions as $dir)
                        <option value="{{ $dir }}" {{ old('direction') == $dir ? 'selected' : '' }}>
                            {{ $dir }}
                        </option>
                    @endforeach
                </select>
                @error('direction')<span class="form-hint text-danger">{{ $message }}</span>@enderror
            </div>

            <div class="form-group">
                <label class="form-label">Sexe *</label>
                <select name="sexe" id="sexe" class="form-control-gc" required onchange="toggleEnfants()">
                    <option value="M" {{ old('sexe') == 'M' ? 'selected' : '' }}>Masculin</option>
                    <option value="F" {{ old('sexe') == 'F' ? 'selected' : '' }}>Féminin</option>
                </select>
            </div>

            <div class="form-group" id="bloc-enfants" style="display:none;">
                <label class="form-label">Nombre d'enfants</label>
                <input type="number" name="nombre_enfants" class="form-control-gc"
                       value="{{ old('nombre_enfants', 0) }}" min="0" max="20">
                <span class="form-hint">+1 jour par enfant (femmes uniquement)</span>
            </div>

        </div>
    </div>

    {{-- ================= SERVICE ================= --}}
    <div class="form-section">
        <h4><i class="bi bi-briefcase-fill me-2"></i>Service</h4>

        <div class="form-grid">

            <div class="form-group">
                <label class="form-label">Date de prise de service *</label>
                <input type="date" name="date_prise_service"
                       class="form-control-gc"
                       value="{{ old('date_prise_service') }}"
                       max="{{ date('Y-m-d') }}" required>

                <span class="form-hint">
                    Congé uniquement après 12 mois de service
                </span>

                @error('date_prise_service')<span class="form-hint text-danger">{{ $message }}</span>@enderror
            </div>

            <div class="form-group">
                <label class="form-label">Type de contrat</label>
                <select name="type_contrat" class="form-control-gc">
                    <option value="titulaire" {{ old('type_contrat') == 'titulaire' ? 'selected' : '' }}>Titulaire</option>
                    <option value="contractuel" {{ old('type_contrat') == 'contractuel' ? 'selected' : '' }}>Contractuel</option>
                </select>
            </div>

        </div>
    </div>

    {{-- ================= CONGÉS ================= --}}
    <div class="form-section">
        <h4><i class="bi bi-calendar2-week-fill me-2"></i>Congés</h4>

        <div class="form-grid">

            <div class="form-group">
                <label class="form-label">Jours reportés N-1</label>
                <input type="number" name="jours_report_n1"
                       class="form-control-gc"
                       value="{{ old('jours_report_n1', 0) }}"
                       min="0" max="72">
            </div>

            <div class="form-group">
                <label class="form-label">Jours acquis cette année</label>
                <input type="number" name="jours_acquis_annee"
                       class="form-control-gc"
                       value="{{ old('jours_acquis_annee', 24) }}"
                       min="0" max="24">
            </div>

        </div>
    </div>

    {{-- ================= ACTIONS ================= --}}
    <div style="display:flex;gap:12px;justify-content:flex-end;">
        <a href="{{ route('agents.index') }}" class="btn-gc btn-outline-gc">Annuler</a>

        <button type="submit" class="btn-gc btn-primary-gc">
            <i class="bi bi-check-lg"></i> Enregistrer
        </button>
    </div>

</form>

</div>

@endsection

@push('scripts')
<script>
function toggleEnfants() {
    const sexe = document.getElementById('sexe').value;
    const bloc = document.getElementById('bloc-enfants');
    const input = document.querySelector('[name="nombre_enfants"]');

    if (sexe === 'F') {
        bloc.style.display = 'block';
    } else {
        bloc.style.display = 'none';
        if (input) input.value = 0;
    }
}

document.addEventListener('DOMContentLoaded', function () {
    toggleEnfants();
});
</script>
@endpush
