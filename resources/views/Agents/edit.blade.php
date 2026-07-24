@extends('layout.template')

@section('title', 'Modifier agent')
@section('page-title', 'Modifier — ' . $agent->nom_complet)

@section('topbar-actions')
    <a href="{{ route('agents.index') }}" class="btn-gc btn-outline-gc">
        <i class="bi bi-arrow-left"></i> Retour
    </a>
@endsection

@section('content')

<form method="POST" action="{{ route('agents.update', $agent) }}">
    @csrf
    @method('PUT')

    {{-- ================= IDENTITÉ ================= --}}
    <div class="form-section">
        <h4><i class="bi bi-person-fill me-2"></i>Identité de l'agent</h4>

        <div class="form-grid">

            <div class="form-group">
                <label class="form-label">Prénom *</label>
                <input type="text" name="prenom" class="form-control-gc"
                       value="{{ old('prenom', $agent->prenom) }}" required>
            </div>

            <div class="form-group">
                <label class="form-label">Nom *</label>
                <input type="text" name="nom" class="form-control-gc"
                       value="{{ old('nom', $agent->nom) }}" required>
            </div>

            <div class="form-group">
                <label class="form-label">Matricule *</label>
                <input type="text" name="matricule" class="form-control-gc"
                       value="{{ old('matricule', $agent->matricule) }}" required>
            </div>

            <div class="form-group">
                <label class="form-label">Direction *</label>
                <select name="direction" class="form-control-gc" required>
                    @foreach($directions as $dir)
                        <option value="{{ $dir }}"
                            {{ old('direction', $agent->direction) == $dir ? 'selected' : '' }}>
                            {{ $dir }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Sexe *</label>
                <select name="sexe" id="sel-sexe" class="form-control-gc" onchange="toggleEnfants()" required>
                    <option value="M" {{ old('sexe', $agent->sexe) == 'M' ? 'selected' : '' }}>Masculin</option>
                    <option value="F" {{ old('sexe', $agent->sexe) == 'F' ? 'selected' : '' }}>Féminin</option>
                </select>
            </div>

            <div class="form-group" id="enfants-group" style="display:none;">
                <label class="form-label">Nombre d'enfants</label>
                <input type="number" name="nombre_enfants" class="form-control-gc"
                       value="{{ old('nombre_enfants', $agent->nombre_enfants) }}" min="0" max="20">
                <span class="form-hint">+1 jour de congé par enfant (femmes)</span>
            </div>

        </div>
    </div>

    {{-- ================= SERVICE ================= --}}
    <div class="form-section">
        <h4><i class="bi bi-briefcase-fill me-2"></i>Informations de service</h4>

        <div class="form-grid">

            <div class="form-group">
                <label class="form-label">Date de prise de service *</label>
                <input type="date" name="date_prise_service" class="form-control-gc"
                       value="{{ old('date_prise_service', optional($agent->date_prise_service)->format('Y-m-d')) }}"
                       required>
            </div>

            <div class="form-group">
                <label class="form-label">Type de contrat</label>
                <select name="type_contrat" class="form-control-gc">
                    <option value="titulaire"
                        {{ old('type_contrat', $agent->type_contrat) == 'titulaire' ? 'selected' : '' }}>
                        Titulaire
                    </option>
                    <option value="contractuel"
                        {{ old('type_contrat', $agent->type_contrat) == 'contractuel' ? 'selected' : '' }}>
                        Contractuel
                    </option>
                </select>
            </div>

        </div>
    </div>

    {{-- ================= CONGÉS ================= --}}
    <div class="form-section">
        <h4><i class="bi bi-calendar2-week-fill me-2"></i>Solde de congés</h4>

        <div class="form-grid">

            <div class="form-group">
                <label class="form-label">Jours reportés N-1</label>
                <input type="number" name="jours_report_n1" class="form-control-gc"
                       value="{{ old('jours_report_n1', $agent->jours_report_n1) }}" min="0" max="72">
            </div>

            <div class="form-group">
                <label class="form-label">Jours acquis cette année</label>
                <input type="number" name="jours_acquis_annee" class="form-control-gc"
                       value="{{ old('jours_acquis_annee', $agent->jours_acquis_annee) }}" min="0" max="24">
            </div>

        </div>
    </div>

    {{-- ================= ACTIONS ================= --}}
    <div style="display:flex;gap:12px;justify-content:flex-end;">
        <a href="{{ route('agents.index') }}" class="btn-gc btn-outline-gc">
            Annuler
        </a>

        <button type="submit" class="btn-gc btn-primary-gc">
            <i class="bi bi-check-lg"></i> Mettre à jour
        </button>
    </div>

</form>

@endsection

@push('scripts')
<script>
function toggleEnfants() {
    const sexe = document.getElementById('sel-sexe').value;
    const bloc = document.getElementById('enfants-group');
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
