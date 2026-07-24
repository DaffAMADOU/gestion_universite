@extends('layout.template')

@section('title', 'Calendrier Jours Fériés')
@section('page-title', 'Calendrier — Jours Fériés Sénégal')

@section('content')

<div style="display:grid;grid-template-columns:1fr 320px;gap:24px;align-items:start;">

    {{-- Grille calendrier --}}
    <div>
        <div class="card-gc" style="margin-bottom:0;">
            <div class="card-gc-header">
                <h3><i class="bi bi-calendar3 me-2"></i>Jours fériés {{ $annee }}</h3>
                <div style="display:flex;gap:8px;">
                    <a href="{{ route('feries.index', ['annee' => $annee - 1]) }}"
                       class="btn-gc btn-outline-gc btn-sm-gc">← {{ $annee - 1 }}</a>
                    <a href="{{ route('feries.index', ['annee' => $annee + 1]) }}"
                       class="btn-gc btn-outline-gc btn-sm-gc">{{ $annee + 1 }} →</a>
                </div>
            </div>
            <div style="padding:20px;">

                @php
                    $feriesDates = $feries->pluck('date')->map(fn($d) => \Carbon\Carbon::parse($d)->format('Y-m-d'))->toArray();
                    $moisNoms = ['Janvier','Février','Mars','Avril','Mai','Juin',
                                 'Juillet','Août','Septembre','Octobre','Novembre','Décembre'];
                    $today = now()->format('Y-m-d');
                @endphp

                <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:16px;">
                    @for($m = 1; $m <= 12; $m++)
                        @php
                            $firstDay = \Carbon\Carbon::create($annee, $m, 1);
                            $daysInMonth = $firstDay->daysInMonth;
                            $startDow = ($firstDay->dayOfWeek + 6) % 7; // lundi = 0
                        @endphp
                        <div class="calendar-month">
                            <div class="calendar-month-header">
                                {{ $moisNoms[$m-1] }} {{ $annee }}
                            </div>
                            <div class="calendar-grid-7" style="padding:8px;">
                                @foreach(['L','M','M','J','V','S','D'] as $j)
                                    <div class="cal-head">{{ $j }}</div>
                                @endforeach

                                @for($i = 0; $i < $startDow; $i++)
                                    <div class="cal-day"></div>
                                @endfor

                                @for($d = 1; $d <= $daysInMonth; $d++)
                                    @php
                                        $dateStr = sprintf('%d-%02d-%02d', $annee, $m, $d);
                                        $dow = \Carbon\Carbon::create($annee, $m, $d)->dayOfWeek; // 0=dim
                                        $isFerie = in_array($dateStr, $feriesDates);
                                        $isToday  = $dateStr === $today;
                                        $ferie    = $feries->firstWhere('date', fn($dd) => \Carbon\Carbon::parse($dd)->format('Y-m-d') === $dateStr);
                                        $ferieName = $feries->first(fn($f) => \Carbon\Carbon::parse($f->date)->format('Y-m-d') === $dateStr)?->designation ?? '';
                                    @endphp
                                    <div class="cal-day
                                        {{ $isToday ? 'today' : '' }}
                                        {{ $isFerie ? 'ferie' : '' }}
                                        {{ $dow === 0 ? 'dimanche' : '' }}
                                        {{ $dow === 6 ? 'samedi' : '' }}"
                                        title="{{ $ferieName }}"
                                        style="font-size:0.72rem;">
                                        {{ $d }}
                                    </div>
                                @endfor
                            </div>
                        </div>
                    @endfor
                </div>

                {{-- Légende --}}
                <div style="display:flex;gap:20px;margin-top:16px;flex-wrap:wrap;font-size:0.78rem;">
                    <div style="display:flex;align-items:center;gap:6px;">
                        <div style="width:16px;height:16px;border-radius:4px;background:#fee2e2;"></div>
                        Jour férié
                    </div>
                    <div style="display:flex;align-items:center;gap:6px;">
                        <div style="width:16px;height:16px;border-radius:4px;background:var(--navy);"></div>
                        Aujourd'hui
                    </div>
                    <div style="display:flex;align-items:center;gap:6px;">
                        <div style="width:16px;height:16px;border-radius:4px;background:#f0f0f0;"></div>
                        Dimanche (non ouvrable)
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Panneau latéral --}}
    <div style="position:sticky;top:80px;">

        {{-- Liste des jours fériés --}}
        <div class="card-gc" style="margin-bottom:16px;">
            <div class="card-gc-header">
                <h3>Liste {{ $annee }}</h3>
            </div>
            <div style="padding:12px 16px;">
                @forelse($feries as $f)
                    <div style="display:flex;justify-content:space-between;align-items:center;
                                padding:8px 0;border-bottom:1px solid var(--cream-dark);">
                        <div>
                            <span style="background:#fee2e2;color:var(--red);padding:2px 8px;
                                         border-radius:4px;font-weight:700;font-size:0.75rem;">
                                {{ \Carbon\Carbon::parse($f->date)->format('d/m') }}
                            </span>
                            <span style="margin-left:8px;font-size:0.82rem;">{{ $f->designation }}</span>
                        </div>
                        <form method="POST" action="{{ route('feries.destroy', $f) }}"
                              onsubmit="return confirm('Supprimer ce jour férié ?')">
                            @csrf @method('DELETE')
                            <button type="submit" style="background:none;border:none;cursor:pointer;
                                    color:var(--red);font-size:1rem;padding:2px 6px;">✕</button>
                        </form>
                    </div>
                @empty
                    <p style="color:var(--text-muted);font-size:0.82rem;text-align:center;padding:12px;">
                        Aucun jour férié pour {{ $annee }}
                    </p>
                @endforelse
            </div>
        </div>

        {{-- Ajouter un jour férié --}}
        <div class="card-gc">
            <div class="card-gc-header">
                <h3><i class="bi bi-plus-circle me-1"></i>Ajouter un jour férié</h3>
            </div>
            <div style="padding:16px 20px;">
                <form method="POST" action="{{ route('feries.store') }}">
                    @csrf
                    <div class="form-group" style="margin-bottom:12px;">
                        <label class="form-label">Date</label>
                        <input type="date" name="date" class="form-control-gc"
                               value="{{ old('date', $annee . '-01-01') }}" required>
                        @error('date')<span class="form-hint" style="color:var(--red)">{{ $message }}</span>@enderror
                    </div>
                    <div class="form-group" style="margin-bottom:16px;">
                        <label class="form-label">Désignation</label>
                        <input type="text" name="designation" class="form-control-gc"
                               value="{{ old('designation') }}" placeholder="Ex: Tabaski" required>
                        @error('designation')<span class="form-hint" style="color:var(--red)">{{ $message }}</span>@enderror
                    </div>
                    <button type="submit" class="btn-gc btn-gold-gc" style="width:100%;justify-content:center;">
                        <i class="bi bi-plus-lg"></i> Ajouter
                    </button>
                </form>
            </div>
        </div>
    </div>

</div>

@endsection
