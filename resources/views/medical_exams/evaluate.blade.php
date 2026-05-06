<x-app-layout>
    <div class="min-h-screen bg-slate-50/50">
        <header class="bg-white border-b border-emerald-100 p-6 flex items-center justify-between sticky top-0 z-40">
            <div class="space-y-1">
                <p class="text-[10px] font-black text-emerald-600 uppercase tracking-[0.35em]">Circuito clínico</p>
                <h1 class="text-base md:text-lg font-black text-slate-900 uppercase tracking-tight">Evaluación clínica del paciente</h1>
            </div>
            <div class="flex items-center gap-4">
                <span class="text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-100 px-3 py-1 rounded-full uppercase">Registro institucional</span>
                <img src="https://i.ibb.co/XfRzV8R/logo-ips.png" class="h-10 w-auto" alt="Logo IPS">
            </div>
        </header>

        <div class="p-4 md:p-10">
            <div class="max-w-6xl mx-auto space-y-6">
                <div class="app-panel-strong rounded-[2.5rem] p-6 md:p-8 overflow-hidden border border-emerald-100">
                    <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-6">
                        <div class="space-y-3">
                            <span class="inline-flex items-center bg-emerald-50 text-emerald-700 border border-emerald-100 text-[10px] font-black uppercase tracking-[0.28em] px-3 py-2 rounded-full">Circuito clínico</span>
                            <h2 class="app-display text-3xl md:text-4xl font-black text-slate-900 tracking-tight">
                                Evaluación <span class="text-emerald-700">clínica</span> del paciente
                            </h2>
                            <p class="max-w-2xl text-sm md:text-base text-slate-500 leading-6">
                                Formulario institucional para registrar la valoración clínica, consultar hallazgos previos y consolidar el seguimiento del paciente.
                            </p>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="px-4 py-3 rounded-2xl bg-white/90 border border-emerald-100 shadow-sm">
                                <span class="block text-[10px] font-black uppercase tracking-[0.24em] text-slate-400">Estado</span>
                                <span class="block text-sm font-bold text-slate-800">Activo</span>
                            </div>
                            <div class="px-4 py-3 rounded-2xl bg-white/90 border border-emerald-100 shadow-sm">
                                <span class="block text-[10px] font-black uppercase tracking-[0.24em] text-slate-400">Vista</span>
                                <span class="block text-sm font-bold text-slate-800">{{ strtoupper($area ?? 'general') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

            <div class="max-w-5xl mx-auto">
                @php
                    /** * SOPORTE DINÁMICO SNAKEDEV:
                     * Si el controlador manda 'exam', lo asignamos a $medical_exam para que el resto no falle.
                     */
                    $medical_exam = $medical_exam ?? $exam;

                    $role = Auth::user()->role->name;
                    $area = $userArea ?? Str::slug($role, '_');

                    $view = match(true) {
                        str_contains($area, 'medica') || str_contains($area, 'medico')
                            => 'medical_exams.evaluations.valoracion_medica',

                        str_contains($area, 'psico')
                            => 'medical_exams.evaluations.psicologia',

                        str_contains($area, 'fono')
                            => 'medical_exams.evaluations.fonoaudiologia',

                        str_contains($area, 'opto')
                            => 'medical_exams.evaluations.optometria',

                        str_contains($area, 'audio')
                            => 'medical_exams.evaluations.audiometria',

                        str_contains($area, 'odonto')
                            => 'medical_exams.evaluations.odontologia',

                        default => "medical_exams.evaluations.{$area}"
                    };
                @endphp

                @if(view()->exists($view) || ($showBothAudioExams ?? false))
                    {{-- Contenedor principal con bordes suavizados SnakeDEV --}}
                    <div class="app-panel-strong p-6 md:p-10 rounded-[3rem]">
                        {{-- LÓGICA UNIFICADA: Si se deben mostrar ambos exámenes de audio --}}
                        @if($showBothAudioExams ?? false)
                            {{-- Mostrar vista unificada con Audiometría + Fonoaudiología --}}
                            @include('medical_exams.evaluations.audio_combined', [
                                'exam' => $medical_exam,
                                'medical_exam' => $medical_exam,
                                'area' => 'audiometria'
                            ])
                        @else
                            {{--
                                CORRECCIÓN FINAL:
                                Enviamos ambas llaves para que cualquier sub-vista funcione sin importar
                                si pide $exam o $medical_exam.
                            --}}
                            @include($view, [
                                'exam' => $medical_exam,
                                'medical_exam' => $medical_exam,
                                'area' => $area
                            ])
                        @endif
                    </div>

                    <script>
                        document.addEventListener('DOMContentLoaded', function () {
                            const existingData = @json($existingEvaluationData ?? []);
                            const existingNotes = @json($existingEvaluationNotes ?? null);
                            const existingAudioNotes = @json($existingAudioNotes ?? null);
                            const existingFonoNotes = @json($existingFonoNotes ?? null);

                            const fillFieldByName = (name, value) => {
                                const fields = document.querySelectorAll(`[name="${name}"]`);
                                if (!fields.length) return false;

                                fields.forEach((field) => {
                                    const type = (field.type || '').toLowerCase();

                                    if (type === 'checkbox') {
                                        if (Array.isArray(value)) {
                                            field.checked = value.map(String).includes(String(field.value));
                                        } else {
                                            field.checked = [true, 1, '1', 'true', 'on', String(field.value)].includes(value);
                                        }
                                        return;
                                    }

                                    if (type === 'radio') {
                                        field.checked = String(field.value) === String(value);
                                        return;
                                    }

                                    field.value = Array.isArray(value) ? value.join(', ') : (value ?? '');
                                });

                                return true;
                            };

                            const fillValue = (key, value) => {
                                const candidates = [
                                    key,
                                    `${key}[]`,
                                    `results[${key}]`,
                                    `results[${key}][]`,
                                ];

                                for (const candidate of candidates) {
                                    if (fillFieldByName(candidate, value)) {
                                        return;
                                    }
                                }
                            };

                            Object.entries(existingData || {}).forEach(([key, value]) => {
                                if (value !== null && typeof value === 'object' && !Array.isArray(value)) {
                                    Object.entries(value).forEach(([childKey, childValue]) => {
                                        fillValue(`${key}[${childKey}]`, childValue);
                                    });
                                    return;
                                }

                                fillValue(key, value);
                            });

                            if (typeof existingNotes === 'string' && existingNotes.trim() !== '') {
                                ['notes', 'observations', 'detalles'].forEach((name) => {
                                    const field = document.querySelector(`[name="${name}"]`);
                                    if (field && String(field.value || '').trim() === '') {
                                        field.value = existingNotes;
                                    }
                                });
                            }

                            if (typeof existingAudioNotes === 'string' && existingAudioNotes.trim() !== '') {
                                const notesField = document.querySelector('[name="notes"]');
                                if (notesField && String(notesField.value || '').trim() === '') {
                                    notesField.value = existingAudioNotes;
                                }
                            }

                            if (typeof existingFonoNotes === 'string' && existingFonoNotes.trim() !== '') {
                                const observationsField = document.querySelector('[name="observations"]');
                                if (observationsField && String(observationsField.value || '').trim() === '') {
                                    observationsField.value = existingFonoNotes;
                                }
                            }
                        });
                    </script>
                @else
                    <div class="bg-white p-12 rounded-[3.5rem] shadow-sm border border-slate-100 text-center">
                        <div class="w-20 h-20 bg-red-50 text-red-500 rounded-3xl flex items-center justify-center mx-auto mb-6">
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <h3 class="text-2xl font-black text-slate-800 uppercase tracking-tighter">Vista no encontrada</h3>
                        <p class="text-slate-500 font-medium">
                            No existe un formulario para el área: <span class="text-red-600 font-bold">"{{ $area }}"</span>.
                            <br>
                            <span class="text-xs text-slate-400">Ruta intentada: resources/views/{{ str_replace('.', '/', $view) }}.blade.php</span>
                        </p>
                        <div class="mt-8">
                            <a href="{{ route('medical_exams.index') }}" class="text-xs font-black text-slate-400 uppercase tracking-widest hover:text-slate-900 transition-colors">← Volver a la bandeja</a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
