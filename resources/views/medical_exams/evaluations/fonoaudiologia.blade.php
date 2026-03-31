{{-- resources/views/medical_exams/evaluations/fonoaudiologia.blade.php --}}

<div class="mb-10 flex items-center justify-between">
    <div>
        <span class="text-[10px] font-black text-orange-500 uppercase tracking-[0.3em] mb-2 block">Área de Especialidad</span>
        <h2 class="text-4xl font-black text-slate-900 tracking-tighter uppercase leading-none">
            Valoración: <span class="text-orange-600">Fonoaudiología</span>
        </h2>
        <div class="flex items-center mt-3 space-x-3">
            <p class="text-slate-500 font-bold text-sm uppercase tracking-tight">
                Paciente: <span class="text-slate-800">{{ $medical_exam->student->name }}</span>
            </p>
            <span class="h-1 w-1 bg-slate-300 rounded-full"></span>
            <p class="text-slate-400 font-medium text-sm italic">{{ $medical_exam->student->document_type }}: {{ $medical_exam->student->document_number }}</p>
        </div>
    </div>
</div>

<form action="{{ route('medical_exams.store_evaluation', $medical_exam) }}" method="POST" class="space-y-8">
    @csrf
    <input type="hidden" name="evaluation_area" value="fonoaudiologia">

    {{-- 1. Componentes del Lenguaje y Habla - Extendido --}}
    <div class="bg-white p-10 rounded-[3rem] shadow-sm border border-slate-100">
        <div class="flex items-center mb-8">
            <div class="w-10 h-10 bg-orange-600 text-white rounded-2xl flex items-center justify-center mr-4 shadow-lg shadow-orange-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z" />
                </svg>
            </div>
            <h3 class="text-xl font-black text-slate-800 uppercase tracking-tighter">Desempeño Comunicativo</h3>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            @foreach([
                'articulacion' => 'Articulación y Fonética',
                'fluidez' => 'Fluidez y Ritmo',
                'voz' => 'Calidad de la Voz',
                'comprension' => 'Comprensión Verbal',
                'expresion' => 'Expresión y Vocabulario',
                'pragmatica' => 'Uso Social del Lenguaje',
                'lectura' => 'Procesos de Lectura',
                'escritura' => 'Procesos de Escritura'
            ] as $key => $label)
            <div>
                <label class="text-[10px] font-black text-slate-400 uppercase mb-3 block tracking-widest">{{ $label }}</label>
                <select name="{{$key}}" class="w-full bg-slate-50 border-none rounded-2xl text-sm font-bold py-3.5 focus:ring-4 focus:ring-orange-500/10 transition-all">
                    <option value="Normal">Normal</option>
                    <option value="Alterado">Alterado</option>
                    <option value="En_Observacion">En observación</option>
                    <option value="No_Aplica">No aplica (Edad)</option>
                </select>
            </div>
            @endforeach
        </div>
    </div>

    {{-- 2. Función Auditiva --}}
    <div class="bg-white p-10 rounded-[3rem] shadow-sm border border-slate-100">
        <div class="flex items-center mb-8">
            <div class="w-10 h-10 bg-blue-600 text-white rounded-2xl flex items-center justify-center mr-4 shadow-lg shadow-blue-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z" />
                </svg>
            </div>
            <h3 class="text-xl font-black text-slate-800 uppercase tracking-tighter">Tamizaje Auditivo</h3>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
            @foreach(['oido_derecho' => 'Oído Derecho (OD)', 'oido_izquierdo' => 'Oído Izquierdo (OI)'] as $side => $title)
            <div class="space-y-4">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block">{{ $title }}</label>
                <div class="flex gap-4">
                    <label class="flex-1 cursor-pointer">
                        <input type="radio" name="{{$side}}" value="Pasa" checked class="peer hidden">
                        <div class="py-4 border-2 border-slate-100 rounded-2xl text-center peer-checked:border-orange-500 peer-checked:bg-orange-50 peer-checked:text-orange-700 transition-all text-xs font-black uppercase">
                            Pasa
                        </div>
                    </label>
                    <label class="flex-1 cursor-pointer">
                        <input type="radio" name="{{$side}}" value="Falla" class="peer hidden">
                        <div class="py-4 border-2 border-slate-100 rounded-2xl text-center peer-checked:border-red-500 peer-checked:bg-red-50 peer-checked:text-red-700 transition-all text-xs font-black uppercase">
                            Falla
                        </div>
                    </label>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- 3. Conclusión y Recomendaciones --}}
    <div class="bg-slate-900 p-10 rounded-[3rem] shadow-2xl shadow-slate-200">
        <div class="flex items-center mb-6">
            <div class="w-2 h-8 bg-orange-500 rounded-full mr-4"></div>
            <label class="text-xs font-black text-orange-400 uppercase tracking-[0.2em]">Diagnóstico y Plan de Acción</label>
        </div>

        <textarea name="observations" rows="4"
                  class="w-full bg-slate-800/50 border-none rounded-3xl text-white text-base p-6 focus:ring-4 focus:ring-orange-500/20 placeholder:text-slate-500 transition-all"
                  placeholder="Escriba aquí la conducta a seguir..."></textarea>

        <div class="mt-10 flex flex-col md:flex-row justify-between items-center gap-6">
            <p class="text-slate-400 text-[10px] font-bold uppercase tracking-widest max-w-xs">
                SnakeDEV System v2.1 - Registro de Fonoaudiología
            </p>
            <button type="submit" class="w-full md:w-auto bg-orange-600 text-white px-12 py-5 rounded-2xl font-black shadow-xl shadow-orange-900/20 hover:bg-orange-500 hover:-translate-y-1 active:scale-95 transition-all uppercase text-sm">
                Guardar Valoración
            </button>
        </div>
    </div>
</form>
