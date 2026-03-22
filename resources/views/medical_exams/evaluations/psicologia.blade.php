{{-- NOTA: Eliminamos layouts y sidebars manuales para evitar el doble slide --}}
<div class="bg-white rounded-[3rem] shadow-2xl shadow-slate-200/50 border border-slate-100 overflow-hidden">
    
    {{-- HEADER DE PSICOLOGÍA --}}
    <div class="p-10 border-b border-slate-50 bg-slate-50/30">
        <div class="flex justify-between items-start">
            <div>
                <h2 class="text-4xl font-black text-slate-900 tracking-tighter uppercase leading-none">
                    Valoración: <span class="text-purple-600">Psicología</span>
                </h2>
                <div class="flex items-center mt-3 space-x-3">
                    <p class="text-slate-500 font-bold text-sm uppercase tracking-tight">
                        Paciente: <span class="text-slate-800">{{ $medical_exam->student->name }}</span>
                    </p>
                </div>
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

        {{-- SECCIÓN: OBSERVACIONES CLÍNICAS --}}
        <section>
            <div class="flex items-center gap-4 mb-8">
                <span class="w-10 h-10 bg-purple-50 text-purple-600 rounded-xl flex items-center justify-center font-black text-sm">01</span>
                <h3 class="text-xl font-black text-slate-800 uppercase tracking-tight">Concepto de Aptitud Psicológica</h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase mb-3 ml-2 tracking-widest">Resultado</label>
                    <select name="aptitud_psicologica" class="w-full bg-slate-50 border border-slate-100 rounded-2xl p-4 font-bold text-slate-700 focus:ring-2 focus:ring-purple-500/20">
                        <option value="Apto">Apto</option>
                        <option value="Apto con recomendaciones">Apto con recomendaciones</option>
                        <option value="No apto">No apto</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase mb-3 ml-2 tracking-widest">Pruebas Aplicadas</label>
                    <input type="text" name="pruebas" placeholder="Ej: Test de Bender, Figuras complejas" class="w-full bg-slate-50 border border-slate-100 rounded-2xl p-4 font-bold text-slate-700 focus:ring-2 focus:ring-purple-500/20">
                </div>
            </div>

            <div class="bg-slate-50/50 rounded-[2.5rem] p-8 border border-slate-100">
                <label class="block text-[10px] font-black text-slate-400 uppercase mb-4 ml-2 tracking-widest">Observaciones y Recomendaciones</label>
                <textarea name="observations" rows="6" 
                    placeholder="Describa los hallazgos de la evaluación psicológica..." 
                    class="w-full bg-white border border-slate-100 rounded-2xl p-6 text-slate-700 placeholder:text-slate-400 focus:ring-2 focus:ring-purple-500/20 shadow-sm resize-none font-medium"></textarea>
            </div>
        </section>

        {{-- BOTÓN --}}
        <div class="pt-6">
            <button type="submit" class="w-full bg-purple-600 hover:bg-purple-700 text-white font-black py-6 rounded-[2rem] shadow-xl shadow-purple-100 transition-all uppercase tracking-[0.2em] text-sm">
                Finalizar Evaluación de Psicología
            </button>
        </div>
    </form>
</div>