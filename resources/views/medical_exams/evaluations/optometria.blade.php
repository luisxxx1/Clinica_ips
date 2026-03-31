{{-- NOTA: Este archivo NO debe tener layouts, solo el div principal --}}
<div class="bg-white rounded-[3rem] shadow-2xl shadow-slate-200/50 border border-slate-100 overflow-hidden">

    {{-- HEADER DEL FORMULARIO --}}
    <div class="p-10 border-b border-slate-50 bg-slate-50/30 text-center md:text-left">
        <div class="flex flex-col md:flex-row justify-between items-center gap-4">
            <div>
                <span class="text-[10px] font-black text-teal-500 uppercase tracking-[0.4em] mb-2 block text-center md:text-left">Módulo Especializado</span>
                <h2 class="text-4xl font-black text-slate-800 tracking-tighter uppercase mb-2">
                    Valoración: <span class="text-teal-500">Optometría</span>
                </h2>
                <p class="text-slate-500 font-bold italic">Paciente: {{ $medical_exam->student->name ?? $medical_exam->student->first_name }}</p>
            </div>
            <div class="bg-white px-8 py-4 rounded-3xl border border-slate-100 shadow-sm">
                <p class="text-[10px] font-black text-slate-300 uppercase tracking-widest mb-1 text-center">Documento</p>
                <p class="text-xl font-black text-slate-800">{{ $medical_exam->student->document_number }}</p>
            </div>
        </div>
    </div>

    <form action="{{ route('medical_exams.store_evaluation', $medical_exam) }}" method="POST" class="p-8 md:p-12 space-y-16">
        @csrf

        {{-- SECCIÓN 01: ANTECEDENTES Y SÍNTOMAS --}}
        <section>
            <div class="flex items-center gap-4 mb-8">
                <span class="w-10 h-10 bg-teal-50 text-teal-500 rounded-xl flex items-center justify-center font-black text-sm uppercase">01</span>
                <h3 class="text-xl font-black text-slate-800 uppercase tracking-tight">Antecedentes y Sintomatología</h3>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-4">
                    <label class="block text-[10px] font-black text-slate-400 uppercase ml-2">Motivo de Consulta / Síntomas</label>
                    <textarea name="motivo_consulta" rows="2" placeholder="Ej: Cefalea, visión borrosa, ardor..." class="w-full bg-slate-50 border-none rounded-2xl p-4 font-medium text-slate-700 focus:ring-2 focus:ring-teal-500/20"></textarea>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label class="block text-[9px] font-black text-slate-400 uppercase ml-2 italic">Cefalea</label>
                        <select name="sintoma_cefalea" class="w-full bg-slate-50 border-none rounded-xl p-3 text-xs font-bold text-slate-600">
                            <option value="No">No</option>
                            <option value="Ocasional">Ocasional</option>
                            <option value="Frecuente">Frecuente</option>
                        </select>
                    </div>
                    <div class="space-y-2">
                        <label class="block text-[9px] font-black text-slate-400 uppercase ml-2 italic">Astenopia</label>
                        <select name="sintoma_astenopia" class="w-full bg-slate-50 border-none rounded-xl p-3 text-xs font-bold text-slate-600">
                            <option value="No">No</option>
                            <option value="Si">Si</option>
                        </select>
                    </div>
                </div>
            </div>
        </section>

        {{-- SECCIÓN 02: AGUDEZA VISUAL --}}
        <section>
            <div class="flex items-center gap-4 mb-10">
                <span class="w-10 h-10 bg-teal-50 text-teal-500 rounded-xl flex items-center justify-center font-black text-sm uppercase">02</span>
                <h3 class="text-xl font-black text-slate-800 uppercase tracking-tight">Agudeza Visual (Snellen)</h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                {{-- OJO DERECHO --}}
                <div class="bg-slate-50/50 p-8 rounded-[2.5rem] border border-slate-100">
                    <p class="text-center text-[10px] font-black text-teal-600 uppercase tracking-[0.2em] mb-6">Ojo Derecho (OD)</p>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[9px] font-black text-slate-400 uppercase mb-2 ml-2 tracking-tighter">V. Lejana (Sin corr.)</label>
                            <input type="text" name="od_lejana" value="20/" class="w-full bg-white border-none rounded-2xl p-4 font-bold text-slate-700 focus:ring-2 focus:ring-teal-500/20 shadow-sm">
                        </div>
                        <div>
                            <label class="block text-[9px] font-black text-slate-400 uppercase mb-2 ml-2 tracking-tighter">V. Próxima (Sin corr.)</label>
                            <input type="text" name="od_proxima" placeholder="0.50" class="w-full bg-white border-none rounded-2xl p-4 font-bold text-slate-700 focus:ring-2 focus:ring-teal-500/20 shadow-sm">
                        </div>
                    </div>
                </div>

                {{-- OJO IZQUIERDO --}}
                <div class="bg-slate-50/50 p-8 rounded-[2.5rem] border border-slate-100">
                    <p class="text-center text-[10px] font-black text-teal-600 uppercase tracking-[0.2em] mb-6">Ojo Izquierdo (OI)</p>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[9px] font-black text-slate-400 uppercase mb-2 ml-2 tracking-tighter">V. Lejana (Sin corr.)</label>
                            <input type="text" name="oi_lejana" value="20/" class="w-full bg-white border-none rounded-2xl p-4 font-bold text-slate-700 focus:ring-2 focus:ring-teal-500/20 shadow-sm">
                        </div>
                        <div>
                            <label class="block text-[9px] font-black text-slate-400 uppercase mb-2 ml-2 tracking-tighter">V. Próxima (Sin corr.)</label>
                            <input type="text" name="oi_proxima" placeholder="0.50" class="w-full bg-white border-none rounded-2xl p-4 font-bold text-slate-700 focus:ring-2 focus:ring-teal-500/20 shadow-sm">
                        </div>
                    </div>
                </div>

                {{-- VISIÓN DE CERCA GENERAL --}}
                <div class="bg-teal-50/40 p-8 rounded-[2.5rem] border border-teal-100">
                    <p class="text-center text-[10px] font-black text-teal-600 uppercase tracking-[0.2em] mb-6">Visión de Cerca</p>
                    <div class="flex justify-center">
                        <div class="w-full max-w-xs">
                            <label class="block text-[9px] font-black text-slate-400 uppercase mb-2 ml-2 tracking-tighter">VP (Sin Corrección)</label>
                            <input type="text" name="vp" placeholder="Ej: 0.50, 0.60, 0.70" class="w-full bg-white border-none rounded-2xl p-4 font-bold text-slate-700 focus:ring-2 focus:ring-teal-500/20 shadow-sm">
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- SECCIÓN 03: MOTILIDAD Y COLOR --}}
        <section class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="bg-slate-50/30 p-6 rounded-[2rem] border border-slate-50">
                <div class="flex items-center gap-3 mb-6">
                    <span class="w-8 h-8 bg-white text-teal-500 rounded-lg flex items-center justify-center font-black text-xs shadow-sm">03</span>
                    <h3 class="text-sm font-black text-slate-800 uppercase italic">Motilidad</h3>
                </div>
                <div class="space-y-4">
                    <select name="motilidad_ocular" class="w-full bg-white border-none rounded-xl p-3 text-xs font-bold text-slate-600 shadow-sm">
                        <option value="Normal">Normal (Eutropia)</option>
                        <option value="Endoforia">Endoforia</option>
                        <option value="Exoforia">Exoforia</option>
                    </select>
                    <input type="text" name="ppc" placeholder="PPC (cm)" class="w-full bg-white border-none rounded-xl p-3 text-xs font-bold shadow-sm">
                </div>
            </div>

            <div class="bg-slate-50/30 p-6 rounded-[2rem] border border-slate-50">
                <div class="flex items-center gap-3 mb-6">
                    <span class="w-8 h-8 bg-white text-teal-500 rounded-lg flex items-center justify-center font-black text-xs shadow-sm">04</span>
                    <h3 class="text-sm font-black text-slate-800 uppercase italic">Color (Ishihara)</h3>
                </div>
                <select name="test_color" class="w-full bg-white border-none rounded-xl p-3 text-xs font-bold text-slate-600 shadow-sm">
                    <option value="Normal">Normal</option>
                    <option value="Deficiente">Deficiente / Daltonismo</option>
                </select>
            </div>

            <div class="bg-slate-50/30 p-6 rounded-[2rem] border border-slate-50">
                <div class="flex items-center gap-3 mb-6">
                    <span class="w-8 h-8 bg-white text-teal-500 rounded-lg flex items-center justify-center font-black text-xs shadow-sm">05</span>
                    <h3 class="text-sm font-black text-slate-800 uppercase italic">Examen Externo</h3>
                </div>
                <select name="examen_externo" class="w-full bg-white border-none rounded-xl p-3 text-xs font-bold text-slate-600 shadow-sm">
                    <option value="Sanos">Anexos Sanos</option>
                    <option value="Conjuntivitis">Conjuntivitis</option>
                    <option value="Blefaritis">Blefaritis</option>
                    <option value="Pterigio">Pterigio</option>
                </select>
            </div>
        </section>

        {{-- SECCIÓN FINAL: DIAGNÓSTICO --}}
        <section class="space-y-8">
            <div class="flex items-center gap-4">
                <span class="w-10 h-10 bg-slate-100 text-slate-500 rounded-xl flex items-center justify-center font-black text-sm">06</span>
                <h3 class="text-xl font-black text-slate-800 uppercase tracking-tight">Cierre de Evaluación</h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase mb-3 ml-2 tracking-widest">Corrección Óptica Actual</label>
                    <select name="correccion_optica" class="w-full bg-slate-50 border-none rounded-2xl p-4 font-bold text-slate-700">
                        <option value="No usa">No usa</option>
                        <option value="Monofocal">Monofocal</option>
                        <option value="Bifocal">Bifocal</option>
                        <option value="Progresivo">Progresivo</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase mb-3 ml-2 tracking-widest">Diagnóstico Principal (CIE-10)</label>
                    <input type="text" name="diagnostico" placeholder="Ej: H52.1 Miopía / H52.2 Astigmatismo" class="w-full bg-slate-50 border-none rounded-2xl p-4 font-bold text-slate-700 shadow-sm">
                </div>
            </div>

            <div class="bg-slate-900 rounded-[2.5rem] p-8 shadow-2xl">
                <label class="block text-[10px] font-black text-teal-400 uppercase mb-4 ml-2 tracking-widest text-center md:text-left">Plan de Manejo y Recomendaciones Médicas</label>
                <textarea name="observations" rows="5" placeholder="Indique el tratamiento, remisión o formula óptica necesaria..."
                          class="w-full bg-slate-800 border-none rounded-2xl p-6 text-white placeholder:text-slate-500 focus:ring-2 focus:ring-teal-500/40 resize-none font-medium"></textarea>
            </div>
        </section>

        {{-- BOTÓN DE ACCIÓN --}}
        <div class="pt-6">
            <button type="submit" class="w-full bg-gradient-to-r from-teal-400 to-teal-600 hover:scale-[1.02] text-white font-black py-8 rounded-[2.5rem] shadow-2xl shadow-teal-500/20 transition-all uppercase tracking-[0.3em] text-sm">
                Guardar Historia de Optometría
            </button>
            <div class="flex justify-center items-center gap-2 mt-8 opacity-40">
                <div class="h-[1px] w-12 bg-slate-300"></div>
                <p class="text-[9px] font-black text-slate-400 uppercase tracking-[0.4em]">Snake_DEV Technology</p>
                <div class="h-[1px] w-12 bg-slate-300"></div>
            </div>
        </div>
    </form>
</div>
