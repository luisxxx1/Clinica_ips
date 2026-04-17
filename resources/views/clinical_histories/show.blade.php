<x-app-layout>
    @php
        $roleName = mb_strtolower(auth()->user()->role->name ?? '');
        $canDownloadClinicalPdf = in_array($roleName, ['administrador', 'admisión', 'admision']);
    @endphp

    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h2 class="font-semibold text-xl text-slate-800 leading-tight">
                    Historial Clínico del Paciente
                </h2>
                <p class="text-xs font-medium text-slate-400 uppercase tracking-[0.12em]">
                    {{ $student->first_name }} {{ $student->last_name }} | {{ $student->document_type }}: {{ $student->document_number }}
                </p>
            </div>
            <div class="flex flex-col sm:flex-row gap-2 w-full md:w-auto">
                @if($canDownloadClinicalPdf)
                    <a href="{{ route('clinical_histories.pdf', $student) }}" class="inline-flex items-center justify-center px-4 py-2 rounded-xl bg-emerald-600 text-white text-xs font-semibold uppercase tracking-[0.08em] hover:bg-emerald-700 transition w-full sm:w-auto">
                        Descargar PDF
                    </a>
                @endif

                <a href="{{ route('clinical_histories.index') }}" class="inline-flex items-center justify-center px-4 py-2 rounded-xl bg-slate-100 text-slate-700 text-xs font-semibold uppercase tracking-[0.08em] hover:bg-slate-200 transition w-full sm:w-auto">
                    Volver al Listado
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-6xl mx-auto grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5 lg:sticky lg:top-6">
                    <h3 class="text-sm font-semibold text-slate-800 uppercase tracking-[0.08em] mb-4">Nueva Entrada</h3>

                    @if($errors->any())
                        <div class="mb-4 rounded-xl bg-red-50 border border-red-100 p-3">
                            @foreach($errors->all() as $error)
                                <p class="text-xs font-bold text-red-700">{{ $error }}</p>
                            @endforeach
                        </div>
                    @endif

                    <form method="POST" action="{{ route('clinical_histories.store', $student) }}" class="space-y-4">
                        @csrf

                        <div>
                            <label class="block text-[11px] font-semibold uppercase tracking-[0.08em] text-slate-500 mb-2">Área que registra</label>
                            @if($canSelectEntryArea)
                                <select id="new_entry_area" name="area" class="w-full rounded-xl border-slate-200 text-sm font-medium text-slate-700 focus:border-teal-500 focus:ring-teal-500">
                                    @foreach($entryAreaOptions as $areaKey => $areaLabel)
                                        <option value="{{ $areaKey }}" {{ old('area', $defaultEntryArea ?? 'valoracion_medica') === $areaKey ? 'selected' : '' }}>{{ $areaLabel }}</option>
                                    @endforeach
                                </select>
                            @else
                                <input type="hidden" name="area" value="{{ old('area', $defaultEntryArea ?? '') }}">
                                <input type="text" value="{{ $currentAreaLabel }}" readonly class="w-full rounded-xl border-slate-200 bg-slate-50 text-sm font-medium text-slate-600">
                            @endif
                        </div>

                        <div>
                            <label for="title" class="block text-[11px] font-semibold uppercase tracking-[0.08em] text-slate-500 mb-2">Título</label>
                            <input id="title" name="title" value="{{ old('title', $defaultClinicalTitle ?? '') }}" type="text" maxlength="150" placeholder="Ej. Evolución inicial" class="w-full rounded-xl border-slate-200 text-sm font-medium text-slate-700 focus:border-teal-500 focus:ring-teal-500">
                        </div>

                        <div>
                            <label for="recorded_at" class="block text-[11px] font-semibold uppercase tracking-[0.08em] text-slate-500 mb-2">Fecha y hora</label>
                            <input id="recorded_at" name="recorded_at" value="{{ old('recorded_at', now('America/Bogota')->format('Y-m-d\TH:i')) }}" type="datetime-local" class="w-full rounded-xl border-slate-200 text-sm font-medium text-slate-700 focus:border-teal-500 focus:ring-teal-500">
                        </div>

                        <div>
                            <label for="entry" class="block text-[11px] font-semibold uppercase tracking-[0.08em] text-slate-500 mb-2">Nota clínica</label>
                            <textarea id="entry" name="entry" rows="8" required maxlength="3000" placeholder="Escribe la observación clínica de esta área..." class="w-full rounded-xl border-slate-200 text-sm font-medium text-slate-700 focus:border-teal-500 focus:ring-teal-500">{{ old('entry', $defaultClinicalEntry ?? '') }}</textarea>
                        </div>

                        <button type="submit" class="w-full px-4 py-3 rounded-xl bg-teal-700 text-white text-xs font-semibold uppercase tracking-[0.08em] hover:bg-teal-600 transition">
                            Guardar Entrada
                        </button>
                    </form>
                </div>
            </div>

            <div class="lg:col-span-2 space-y-4">
                @if(session('success'))
                    <div class="rounded-2xl bg-emerald-50 border border-emerald-200 p-4">
                        <p class="text-sm font-black text-emerald-700 uppercase tracking-wider">{{ session('success') }}</p>
                    </div>
                @endif

                @forelse($student->clinicalHistories as $entry)
                    <article class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5">
                        <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-3 mb-4">
                            <div>
                                <h3 class="text-sm font-semibold text-slate-800">
                                    {{ $entry->title ?: 'Nota clínica sin título' }}
                                </h3>
                                <p class="text-[11px] font-medium text-teal-700 uppercase tracking-[0.08em] mt-1">
                                    Área: {{ str_replace('_', ' ', $entry->area) }}
                                </p>
                            </div>
                            <div class="text-left md:text-right">
                                <p class="text-[11px] font-medium text-slate-500 uppercase tracking-[0.08em]">
                                    {{ optional($entry->recorded_at)->timezone('America/Bogota')->format('d/m/Y H:i') }}
                                </p>
                                <p class="text-[11px] font-medium text-slate-400 uppercase tracking-[0.08em] mt-1">
                                    {{ $entry->specialist->name ?? 'Especialista' }}
                                </p>
                            </div>
                        </div>

                        <div class="rounded-2xl bg-slate-50 border border-slate-100 p-4">
                            <p class="text-sm leading-relaxed text-slate-700 whitespace-pre-line">{{ $entry->entry }}</p>
                        </div>

                        @php
                            $isAdmin = mb_strtolower(auth()->user()->role->name ?? '') === 'administrador';
                            $canEdit = $isAdmin || ((int) auth()->id() === (int) $entry->user_id);
                        @endphp

                        @if($canEdit)
                            <details class="mt-4 rounded-2xl border border-slate-200 bg-white">
                                <summary class="cursor-pointer list-none px-4 py-3 text-xs font-semibold uppercase tracking-[0.08em] text-amber-600 hover:text-amber-700">
                                    Editar Nota Guardada
                                </summary>

                                <div class="px-4 pb-4 pt-1 border-t border-slate-100">
                                    <form method="POST" action="{{ route('clinical_histories.update', [$student, $entry]) }}" class="space-y-4">
                                        @csrf
                                        @method('PATCH')

                                        <div>
                                            <label class="block text-[11px] font-semibold uppercase tracking-[0.08em] text-slate-500 mb-2">Área</label>
                                            @if($isAdmin)
                                                <select name="area" class="w-full rounded-xl border-slate-200 text-sm font-medium text-slate-700 focus:border-amber-500 focus:ring-amber-500">
                                                    @foreach($availableAreas as $areaKey => $areaLabel)
                                                        <option value="{{ $areaKey }}" {{ old('area', $entry->area) === $areaKey ? 'selected' : '' }}>{{ $areaLabel }}</option>
                                                    @endforeach
                                                </select>
                                            @else
                                                <input type="hidden" name="area" value="{{ $entry->area }}">
                                                <input type="text" value="{{ $availableAreas[$entry->area] ?? str_replace('_', ' ', $entry->area) }}" readonly class="w-full rounded-xl border-slate-200 bg-slate-50 text-sm font-medium text-slate-600">
                                            @endif
                                        </div>

                                        <div>
                                            <label class="block text-[11px] font-semibold uppercase tracking-[0.08em] text-slate-500 mb-2">Título</label>
                                            <input type="text" name="title" maxlength="150" value="{{ old('title', $entry->title) }}" class="w-full rounded-xl border-slate-200 text-sm font-medium text-slate-700 focus:border-amber-500 focus:ring-amber-500">
                                        </div>

                                        <div>
                                            <label class="block text-[11px] font-semibold uppercase tracking-[0.08em] text-slate-500 mb-2">Fecha y hora</label>
                                            <input type="datetime-local" name="recorded_at" value="{{ old('recorded_at', optional($entry->recorded_at)->timezone('America/Bogota')->format('Y-m-d\TH:i')) }}" class="w-full rounded-xl border-slate-200 text-sm font-medium text-slate-700 focus:border-amber-500 focus:ring-amber-500">
                                        </div>

                                        <div>
                                            <label class="block text-[11px] font-semibold uppercase tracking-[0.08em] text-slate-500 mb-2">Nota clínica</label>
                                            <textarea name="entry" rows="7" required maxlength="3000" class="w-full rounded-xl border-slate-200 text-sm font-medium text-slate-700 focus:border-amber-500 focus:ring-amber-500">{{ old('entry', $entry->entry) }}</textarea>
                                        </div>

                                        <button type="submit" class="px-4 py-2 rounded-xl bg-amber-500 text-white text-xs font-semibold uppercase tracking-[0.08em] hover:bg-amber-600 transition w-full sm:w-auto">
                                            Guardar Cambios
                                        </button>
                                    </form>
                                </div>
                            </details>
                        @endif
                    </article>
                @empty
                    <div class="bg-white rounded-3xl shadow-xl shadow-slate-200/50 border-2 border-dashed border-slate-200 p-14 text-center">
                        <h3 class="text-lg font-black text-slate-700 uppercase tracking-tight">Sin entradas registradas</h3>
                        <p class="text-sm font-semibold text-slate-500 mt-2">Este paciente aún no tiene historial clínico en el nuevo módulo.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    @if($canSelectEntryArea)
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const areaSelect = document.getElementById('new_entry_area');
                const titleInput = document.getElementById('title');
                const entryInput = document.getElementById('entry');
                const defaultTitles = @json($defaultClinicalTitlesByArea ?? []);
                const defaultEntries = @json($defaultClinicalEntriesByArea ?? []);

                if (!areaSelect || !titleInput || !entryInput) {
                    return;
                }

                const applyDefaultContentByArea = () => {
                    const selectedArea = areaSelect.value;
                    const suggestedTitle = defaultTitles[selectedArea] ?? '';
                    const suggestedEntry = defaultEntries[selectedArea] ?? '';

                    const hasCustomTitle = String(titleInput.value || '').trim() !== '';
                    const hasCustomEntry = String(entryInput.value || '').trim() !== '';

                    if (hasCustomTitle || hasCustomEntry) {
                        const shouldReplace = window.confirm(
                            'Ya hay información escrita. ¿Deseas reemplazar el título y la nota por la plantilla del área seleccionada?'
                        );

                        if (!shouldReplace) {
                            return;
                        }

                        titleInput.value = suggestedTitle;
                        entryInput.value = suggestedEntry;
                        return;
                    }

                    titleInput.value = suggestedTitle;
                    entryInput.value = suggestedEntry;
                };

                areaSelect.addEventListener('change', applyDefaultContentByArea);
            });
        </script>
    @endif
</x-app-layout>
