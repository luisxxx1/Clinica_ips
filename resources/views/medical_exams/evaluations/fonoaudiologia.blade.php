<x-app-layout>
    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Encabezado --}}
            <div class="mb-8 flex items-center justify-between">
                <div>
                    <h2 class="text-3xl font-black text-slate-800 tracking-tighter uppercase">
                        Valoración: <span class="text-orange-600">Fonoaudiología</span>
                    </h2>
                    <p class="text-slate-500 font-medium italic">Estudiante: {{ $medical_exam->student->full_name }}</p>
                </div>
                <div class="text-right">
                    <span class="text-[10px] font-bold text-slate-400 uppercase block">Código</span>
                    <span class="font-mono font-bold text-slate-700">{{ $medical_exam->student->document_number }}</span>
                </div>
            </div>

            <form action="{{ route('medical_exams.store_result', $medical_exam) }}" method="POST" class="space-y-6">
                @csrf
                
                {{-- 1. Evaluación de Lenguaje y Habla --}}
                <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-100">
                    <h3 class="text-lg font-black text-slate-800 mb-6 uppercase tracking-tighter flex items-center">
                        <span class="w-7 h-7 bg-orange-100 text-orange-600 rounded-lg flex items-center justify-center mr-3 text-xs">1</span>
                        Componentes del Lenguaje
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @foreach([
                            'articulacion' => 'Articulación (Fonética)',
                            'fluidez' => 'Fluidez Verbal',
                            'voz' => 'Cualidades de la Voz',
                            'comprension' => 'Comprensión Auditiva'
                        ] as $key => $label)
                        <div>
                            <label class="text-[10px] font-black text-slate-400 uppercase mb-2 block tracking-widest">{{ $label }}</label>
                            <select name="results[lenguaje][{{$key}}]" class="w-full bg-slate-50 border-none rounded-2xl text-sm focus:ring-2 focus:ring-orange-500">
                                <option value="Normal">Sin alteraciones</option>
                                <option value="Alterado">Presenta alteraciones</option>
                                <option value="En_Proceso">En proceso de desarrollo</option>
                            </select>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- 2. Tamizaje Auditivo (Otoscopia y Percepción) --}}
                <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-100">
                    <h3 class="text-lg font-black text-slate-800 mb-6 uppercase tracking-tighter flex items-center">
                        <span class="w-7 h-7 bg-orange-100 text-orange-600 rounded-lg flex items-center justify-center mr-3 text-xs">2</span>
                        Función Auditiva
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        {{-- Ojo: Aunque es oído, seguimos la estructura de resultados JSON --}}
                        <div class="space-y-4">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block">Oído Derecho (OD)</label>
                            <div class="flex gap-4">
                                <label class="flex-1 cursor-pointer">
                                    <input type="radio" name="results[audicion][od]" value="Pasa" checked class="peer hidden">
                                    <div class="py-3 border-2 border-slate-100 rounded-2xl text-center peer-checked:border-orange-500 peer-checked:bg-orange-50 transition-all text-xs font-bold uppercase">Pasa</div>
                                </label>
                                <label class="flex-1 cursor-pointer">
                                    <input type="radio" name="results[audicion][od]" value="Falla" class="peer hidden">
                                    <div class="py-3 border-2 border-slate-100 rounded-2xl text-center peer-checked:border-red-500 peer-checked:bg-red-50 transition-all text-xs font-bold uppercase">Falla</div>
                                </label>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block">Oído Izquierdo (OI)</label>
                            <div class="flex gap-4">
                                <label class="flex-1 cursor-pointer">
                                    <input type="radio" name="results[audicion][oi]" value="Pasa" checked class="peer hidden">
                                    <div class="py-3 border-2 border-slate-100 rounded-2xl text-center peer-checked:border-orange-500 peer-checked:bg-orange-50 transition-all text-xs font-bold uppercase">Pasa</div>
                                </label>
                                <label class="flex-1 cursor-pointer">
                                    <input type="radio" name="results[audicion][oi]" value="Falla" class="peer hidden">
                                    <div class="py-3 border-2 border-slate-100 rounded-2xl text-center peer-checked:border-red-500 peer-checked:bg-red-50 transition-all text-xs font-bold uppercase">Falla</div>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Cierre --}}
                <div class="bg-slate-900 p-8 rounded-[2.5rem] shadow-xl">
                    <label class="text-[10px] font-black text-orange-400 uppercase mb-4 block tracking-widest">Recomendaciones Fonoaudiológicas</label>
                    <textarea name="notes" rows="3" required
                              class="w-full bg-slate-800 border-none rounded-2xl text-white text-sm focus:ring-2 focus:ring-orange-500" 
                              placeholder="Indique si requiere terapia de lenguaje o exámenes auditivos complementarios..."></textarea>
                    
                    <div class="flex justify-end pt-8">
                        <button type="submit" class="bg-orange-600 text-white px-10 py-4 rounded-2xl font-black shadow-lg hover:bg-orange-500 transition-all uppercase tracking-tighter">
                            Finalizar Registro Fonoaudiológico
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>