{{-- NOTA: Este archivo NO debe tener layouts, solo el div principal --}}
<div class="bg-white rounded-[3rem] shadow-2xl shadow-slate-200/50 border border-slate-100 overflow-hidden">
    
    {{-- HEADER DEL FORMULARIO --}}
    <div class="p-10 border-b border-slate-50 bg-slate-50/30">
        <div class="flex justify-between items-start">
            <div>
                <h2 class="text-4xl font-black text-slate-800 tracking-tighter uppercase mb-2">
                    Valoración: <span class="text-teal-500">Optometría</span>
                </h2>
                {{-- Validamos el nombre del paciente --}}
                <p class="text-slate-500 font-bold italic">Paciente: {{ $medical_exam->student->name ?? $medical_exam->student->first_name }}</p>
            </div>
            <div class="text-right">
                <p class="text-[10px] font-black text-slate-300 uppercase tracking-widest">Documento</p>
                <p class="text-xl font-black text-slate-800">{{ $medical_exam->student->document_number }}</p>
            </div>
        </div>
    </div>

    {{-- Formulario con la ruta maestra corregida --}}
    <form action="{{ route('medical_exams.store_evaluation', $medical_exam) }}" method="POST" class="p-12 space-y-12">
        @csrf

        {{-- SECCIÓN 01: AGUDEZA VISUAL --}}
        <section>
            <div class="flex items-center gap-4 mb-10">
                <span class="w-10 h-10 bg-teal-50 text-teal-500 rounded-xl flex items-center justify-center font-black text-sm">01</span>
                <h3 class="text-xl font-black text-slate-800 uppercase tracking-tight">Agudeza Visual (Snellen)</h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                {{-- OJO DERECHO --}}
                <div class="bg-slate-50/50 p-8 rounded-[2.5rem] border border-slate-100">
                    <p class="text-center text-[10px] font-black text-teal-600 uppercase tracking-[0.2em] mb-6">Ojo Derecho (OD)</p>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[9px] font-black text-slate-400 uppercase mb-2 ml-2">Lejana</label>
                            <input type="text" name="od_lejana" value="20/" class="w-full bg-white border-none rounded-2xl p-4 font-bold text-slate-700 focus:ring-2 focus:ring-teal-500/20 shadow-sm">
                        </div>
                        <div>
                            <label class="block text-[9px] font-black text-slate-400 uppercase mb-2 ml-2">Próxima</label>
                            <input type="text" name="od_proxima" placeholder="0.50" class="w-full bg-white border-none rounded-2xl p-4 font-bold text-slate-700 focus:ring-2 focus:ring-teal-500/20 shadow-sm">
                        </div>
                    </div>
                </div>

                {{-- OJO IZQUIERDO --}}
                <div class="bg-slate-50/50 p-8 rounded-[2.5rem] border border-slate-100">
                    <p class="text-center text-[10px] font-black text-teal-600 uppercase tracking-[0.2em] mb-6">Ojo Izquierdo (OI)</p>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[9px] font-black text-slate-400 uppercase mb-2 ml-2">Lejana</label>
                            <input type="text" name="oi_lejana" value="20/" class="w-full bg-white border-none rounded-2xl p-4 font-bold text-slate-700 focus:ring-2 focus:ring-teal-500/20 shadow-sm">
                        </div>
                        <div>
                            <label class="block text-[9px] font-black text-slate-400 uppercase mb-2 ml-2">Próxima</label>
                            <input type="text" name="oi_proxima" placeholder="0.50" class="w-full bg-white border-none rounded-2xl p-4 font-bold text-slate-700 focus:ring-2 focus:ring-teal-500/20 shadow-sm">
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- SECCIÓN 02: HALLAZGOS --}}
        <section>
            <div class="flex items-center gap-4 mb-8">
                <span class="w-10 h-10 bg-teal-50 text-teal-500 rounded-xl flex items-center justify-center font-black text-sm">02</span>
                <h3 class="text-xl font-black text-slate-800 uppercase tracking-tight">Hallazgos Clínicos</h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase mb-3 ml-2 tracking-widest">Corrección Óptica</label>
                    <select name="correccion_optica" class="w-full bg-slate-50 border-none rounded-2xl p-4 font-bold text-slate-700 focus:ring-2 focus:ring-teal-500/20">
                        <option value="No usa">No usa</option>
                        <option value="Usa permanentemente">Usa permanentemente</option>
                        <option value="Usa para lectura">Usa para lectura</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase mb-3 ml-2 tracking-widest">Diagnóstico (CIE-10)</label>
                    <input type="text" name="diagnostico" placeholder="Ej: H52.1 - Miopía" class="w-full bg-slate-50 border-none rounded-2xl p-4 font-bold text-slate-700 focus:ring-2 focus:ring-teal-500/20">
                </div>
            </div>

            <div class="bg-slate-900 rounded-[2.5rem] p-8">
                <label class="block text-[10px] font-black text-teal-400 uppercase mb-4 ml-2 tracking-widest">Observaciones y Recomendaciones</label>
                {{-- Nombre 'observations' para que el controlador lo reciba correctamente --}}
                <textarea name="observations" rows="4" placeholder="Escriba la conducta a seguir o formula médica..." 
                          class="w-full bg-slate-800 border-none rounded-2xl p-6 text-white placeholder:text-slate-500 focus:ring-2 focus:ring-teal-500/40 resize-none font-medium" required></textarea>
            </div>
        </section>

        {{-- BOTÓN DE GUARDADO --}}
        <div class="pt-6">
            <button type="submit" class="w-full bg-teal-500 hover:bg-teal-600 text-white font-black py-6 rounded-[2rem] shadow-xl shadow-teal-100 transition-all uppercase tracking-[0.2em] text-sm">
                Finalizar Evaluación de Optometría
            </button>
        </div>
    </form>
</div>