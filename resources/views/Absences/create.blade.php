@extends('layout.template')

@section('title', 'Enregistrer une absence')
@section('page-title', 'Enregistrer une absence')

@section('topbar-actions')
    <a href="{{ route('absences.index') }}" class="btn-gc btn-outline-gc">
        <i class="bi bi-arrow-left"></i> Retour
    </a>
@endsection

@section('content')

<form method="POST" action="{{ route('absences.store') }}">
    @csrf

    {{-- AGENT --}}
    <div class="form-section">
        <h4><i class="bi bi-person-fill me-2"></i>Agent concerné</h4>

        <div class="form-group">
            <label class="form-label">Agent *</label>

            <select name="agent_id" class="form-control-gc" required>
                <option value="">-- Sélectionner un agent --</option>

                @foreach($agents as $a)
                    <option value="{{ $a->id }}"
                        {{ old('agent_id') == $a->id ? 'selected' : '' }}>
                        {{ $a->prenom }} {{ $a->nom }}
                        ({{ $a->matricule }} — {{ $a->direction }})
                    </option>
                @endforeach
            </select>

            @error('agent_id')
                <span class="form-hint" style="color:var(--red)">{{ $message }}</span>
            @enderror
        </div>
    </div>

    {{-- DETAILS --}}
    <div class="form-section">
        <h4><i class="bi bi-calendar-x me-2"></i>Détails de l'absence</h4>

        <div class="form-grid">

            <div class="form-group">
                <label class="form-label">Date de début *</label>

                <input type="date"
                       name="date_debut"
                       class="form-control-gc"
                       value="{{ old('date_debut') }}"
                       required>

                @error('date_debut')
                    <span class="form-hint" style="color:var(--red)">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Nombre de jours *</label>

                <input type="number"
                       name="nombre_jours"
                       class="form-control-gc"
                       value="{{ old('nombre_jours', 1) }}"
                       min="1"
                       required>

                @error('nombre_jours')
                    <span class="form-hint" style="color:var(--red)">{{ $message }}</span>
                @enderror
            </div>

            {{-- MOTIF --}}
            <div class="form-group full">
                <label class="form-label">Motif *</label>

                <select name="motif" class="form-control-gc" required>
                    <option value="">-- Sélectionner --</option>

                    <option value="maladie" {{ old('motif')=='maladie'?'selected':'' }}>Maladie</option>
                    <option value="mission" {{ old('motif')=='mission'?'selected':'' }}>Mission officielle</option>
                    <option value="formation" {{ old('motif')=='formation'?'selected':'' }}>Formation</option>

                    <option value="mariage" {{ old('motif')=='mariage'?'selected':'' }}>Mariage (exceptionnel)</option>
                    <option value="bapteme" {{ old('motif')=='bapteme'?'selected':'' }}>Baptême (exceptionnel)</option>
                    <option value="deces" {{ old('motif')=='deces'?'selected':'' }}>Décès (exceptionnel)</option>

                    <option value="autre" {{ old('motif')=='autre'?'selected':'' }}>Autre</option>
                </select>

                @error('motif')
                    <span class="form-hint" style="color:var(--red)">{{ $message }}</span>
                @enderror
            </div>

            {{-- DEDUCTIBLE (IMPORTANT: contrôlé côté serveur uniquement) --}}
            <div class="form-group full">
                <label style="display:flex;align-items:center;gap:8px;cursor:pointer;">
                    <input type="checkbox"
                           name="deductible"
                           value="1"
                           {{ old('deductible', 1) ? 'checked' : '' }}>

                    <span>Déduire cette absence des congés</span>
                </label>

                <span class="form-hint">
                    Le calcul des absences exceptionnelles sera géré automatiquement par le système.
                </span>
            </div>

            {{-- OBSERVATIONS --}}
            <div class="form-group full">
                <label class="form-label">Observations</label>

                <textarea name="observations"
                          class="form-control-gc"
                          rows="2">{{ old('observations') }}</textarea>
            </div>

        </div>
    </div>

    {{-- ACTIONS --}}
    <div style="display:flex;gap:12px;justify-content:flex-end;">
        <a href="{{ route('absences.index') }}" class="btn-gc btn-outline-gc">Annuler</a>

        <button type="submit" class="btn-gc btn-primary-gc">
            <i class="bi bi-check-lg"></i> Enregistrer
        </button>
    </div>

</form>

@endsection
