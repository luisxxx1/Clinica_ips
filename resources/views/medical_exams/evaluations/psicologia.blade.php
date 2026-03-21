<div class="py-2" x-data="psychoForm()">
    <div class="max-w-5xl mx-auto">
        
        {{-- Encabezado --}}
        <div class="mb-8 flex items-center justify-between">
            <div>
                <h2 class="text-3xl font-black text-slate-800 tracking-tighter uppercase">
                    Valoración: <span class="text-purple-600">Psicología</span>
                </h2>
                <p class="text-slate-500 font-medium italic underline decoration-purple-200">
                    Paciente: {{ $medical_exam->student->full_name }}
                </p>
            </div>
            <div class="text-right">
                <span class="text-[10px] font-bold text-slate-400 uppercase block">ID Estudiante</span>
                <span class="font-mono font-bold text-slate-700">{{ $medical_exam->student->document_number }}</span>
            </div>
        </div>

        <form action="{{ route('medical_exams.store_result', $medical_exam) }}" method="POST" class="space-y-6">
            @csrf
            
            {{-- 1. Esfera Cognitiva y Conductual --}}
            <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-100">
                <h3 class="text-lg font-black text-slate-800 mb-6 uppercase tracking-tighter flex items-center">
                    <span class="w-7 h-7 bg-purple-100 text-purple-600 rounded-lg flex items-center justify-center mr-3 text-xs">1</span>
                    Observación Conductual y Cognitiva
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @php
                        $aspectos = [
                            'presentacion' => 'Presentación Personal',
                            'lenguaje' => 'Lenguaje y Comunicación',
                            'afecto' => 'Estado de Ánimo / Afecto',
                            'atencion' => 'Atención y Concentración',
                            'memoria' => 'Memoria',
                            'sueño' => 'Hábitos de Sueño'
                        ];
                    @endphp

                    @foreach($aspectos as $key => $label)
                    <div class="bg-slate-50 p-4 rounded-2xl border border-transparent focus-within:border-purple-200 transition-all">
                        <label class="text-[10px] font-bold text-slate-400 uppercase mb-2 block tracking-widest">{{ $label }}</label>
                        <select name="result[{{ $key }}]" class="w-full border-none bg-transparent text-sm font-bold focus:ring-0 p-0 text-slate-700">
                            <option value="Adecuado">Adecuado / Normal</option>
                            <option value="Alterado">Alterado / Requiere observación</option>
                            <option value="No Evaluable">No Evaluable</option>
                        </select>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- 2. Entrevista / Hallazgos --}}
            <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-100">
                <h3 class="text-lg font-black text-slate-800 mb-6 flex items-center tracking-tighter uppercase">
                    <span class="w-7 h-7 bg-purple-100 text-purple-600 rounded-lg flex items-center justify-center mr-3 text-xs">2</span>
                    Descripción de Hallazgos
                </h3>
                <div class="space-y-4">
                    <div>
                        <label class="text-[10px] font-black text-slate-400 uppercase mb-2 block tracking-widest">Motivo de consulta / Antecedentes Familiares</label>
                        <textarea name="result[antecedentes_familiares]" rows="3" 
                                  class="w-full bg-slate-50 border-none rounded-2xl text-sm focus:ring-2 focus:ring-purple-500 placeholder-slate-300" 
                                  placeholder="Describa brevemente el entorno familiar y motivo de la valoración..."></textarea>
                    </div>
                    <div>
                        <label class="text-[10px] font-black text-slate-400 uppercase mb-2 block tracking-widest">Relaciones Interpersonales</label>
                        <textarea name="result[relaciones_sociales]" rows="2" 
                                  class="w-full bg-slate-50 border-none rounded-2xl text-sm focus:ring-2 focus:ring-purple-500" 
                                  placeholder="Relación con pares, figuras de autoridad, etc..."></textarea>
                    </div>
                </div>
            </div>

            {{-- Diagnóstico y Recomendaciones --}}
            <div class="bg-slate-900 p-8 rounded-[2.5rem] shadow-xl">
                <label class="text-[10px] font-black text-purple-400 uppercase mb-4 block tracking-widest">Concepto Psicológico y Recomendaciones</label>
                <textarea name="notes" rows="4" required
                          class="w-full bg-slate-800 border-none rounded-2xl text-white text-sm focus:ring-2 focus:ring-purple-500" 
                          placeholder="Escriba el diagnóstico presuntivo o plan de intervención..."></textarea>
                
                <div class="flex justify-end pt-8">
                    <button type="submit" class="bg-purple-600 text-white px-10 py-4 rounded-2xl font-black shadow-lg hover:bg-purple-500 hover:-translate-y-1 transition-all flex items-center text-sm">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        FINALIZAR VALORACIÓN PSICOLÓGICA
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    function psychoForm() {
        return {
            
        }
    }
</script>