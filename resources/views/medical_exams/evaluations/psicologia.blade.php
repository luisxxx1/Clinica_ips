<div class="bg-white rounded-[3rem] shadow-2xl shadow-emerald-100/40 border border-emerald-100 overflow-hidden">

    {{-- HEADER --}}
    <div class="p-10 border-b border-emerald-50 bg-emerald-50/30">
        <div class="flex justify-between items-start">
            <div>
                <h2 class="text-4xl font-black text-slate-900 tracking-tighter uppercase leading-none">
                    Valoración: <span class="text-emerald-700">Psicología</span>
                </h2>
                <p class="text-slate-500 font-bold text-sm uppercase tracking-tight mt-3">
                    Paciente: <span class="text-slate-800">{{ $medical_exam->student->name }}</span>
                </p>
            </div>
            <div class="text-right">
                <p class="text-[10px] font-black text-slate-300 uppercase tracking-widest">Documento</p>
                <p class="text-xl font-black text-slate-800">{{ $medical_exam->student->document_number }}</p>
            </div>
        </div>
    </div>

    {{-- FORMULARIO --}}
    <form action="{{ route('medical_exams.store_evaluation', $medical_exam) }}" method="POST" class="p-12 space-y-10">
        @csrf

        {{-- SECCIÓN 01: OBSERVACIÓN DE CONDUCTA (Basado en Vista 3) --}}
        <section>
            <div class="flex items-center gap-4 mb-8">
                <span class="w-10 h-10 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center font-black text-sm">01</span>
                <h3 class="text-xl font-black text-slate-800 uppercase tracking-tight">Evaluación de Conducta y Desarrollo</h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @php
                    $items = [
                        'atencion' => 'Atención',
                        'conducta' => 'Conducta',
                        'desarrollo' => 'Desarrollo'
                    ];
                @endphp

                @foreach($items as $key => $label)
                <div class="bg-slate-50/50 p-6 rounded-[2rem] border border-emerald-100">
                    <label class="block text-[10px] font-black text-slate-400 uppercase mb-4 tracking-widest text-center">{{ $label }}</label>
                    <div class="flex flex-col gap-3">
                        <label class="flex items-center justify-between p-3 bg-white rounded-xl cursor-pointer hover:bg-emerald-50 transition-all border border-transparent hover:border-emerald-100 group">
                            <span class="text-xs font-bold text-slate-600 group-hover:text-emerald-700">Adecuada</span>
                            <input type="radio" name="{{ $key }}" value="adecuada" class="text-emerald-600 focus:ring-0 w-4 h-4 border-slate-200">
                        </label>
                        <label class="flex items-center justify-between p-3 bg-white rounded-xl cursor-pointer hover:bg-emerald-50 transition-all border border-transparent hover:border-emerald-100 group">
                            <span class="text-xs font-bold text-slate-600 group-hover:text-emerald-700">Dispersa</span>
                            <input type="radio" name="{{ $key }}" value="dispersa" class="text-emerald-600 focus:ring-0 w-4 h-4 border-slate-200">
                        </label>
                    </div>
                </div>
                @endforeach
            </div>
        </section>

        {{-- SECCIÓN 02: CONCEPTO Y PRUEBAS --}}
        <section class="space-y-6">
            <div class="flex items-center gap-4 mb-4">
                <span class="w-10 h-10 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center font-black text-sm">02</span>
                <h3 class="text-xl font-black text-slate-800 uppercase tracking-tight">Concepto Final</h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label class="block text-[10px] font-black text-slate-400 uppercase ml-2 tracking-widest">Resultado de Aptitud</label>
                    <select name="aptitud_psicologica" class="w-full bg-slate-50 border-none rounded-2xl p-4 font-bold text-slate-700 focus:ring-2 focus:ring-emerald-500/20 shadow-sm">
                        <option value="Apto">Apto</option>
                        <option value="Apto con recomendaciones">Apto con recomendaciones</option>
                        <option value="No apto">No apto</option>
                    </select>
                </div>
                <div class="space-y-2">
                    <label class="block text-[10px] font-black text-slate-400 uppercase ml-2 tracking-widest">Pruebas Aplicadas</label>
                    <input type="text" name="pruebas" placeholder="Ej: Test de Bender" class="w-full bg-slate-50 border-none rounded-2xl p-4 font-bold text-slate-700 focus:ring-2 focus:ring-emerald-500/20 shadow-sm">
                </div>
            </div>

            <div class="bg-white rounded-[2.5rem] p-8 mt-6 border border-emerald-100 shadow-sm">
                <label class="block text-[10px] font-black text-emerald-600 uppercase mb-4 ml-2 tracking-widest">Observaciones y Diagnóstico</label>
                <textarea name="observations" rows="4"
                    placeholder="Describa los hallazgos detallados..."
                    class="w-full bg-slate-50 border border-slate-200 rounded-2xl p-6 text-slate-700 placeholder:text-slate-400 focus:ring-2 focus:ring-emerald-500/15 resize-none font-medium"></textarea>
            </div>
        </section>

        {{-- BOTÓN --}}
        <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-black py-6 rounded-[2rem] shadow-xl shadow-emerald-100 transition-all uppercase tracking-[0.2em] text-sm">
            Finalizar Evaluación de Psicología
        </button>
    </form>
</div>
