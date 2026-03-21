<x-app-layout>
    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Encabezado --}}
            <div class="mb-8 flex items-center justify-between">
                <div>
                    <h2 class="text-3xl font-black text-slate-800 tracking-tighter uppercase">
                        Valoración: <span class="text-teal-600">Optometría</span>
                    </h2>
                    <p class="text-slate-500 font-medium italic">Estudiante: {{ $medical_exam->student->full_name }}</p>
                </div>
                <div class="text-right">
                    <span class="text-[10px] font-bold text-slate-400 uppercase block">ID Estudiante</span>
                    <span class="font-mono font-bold text-slate-700">{{ $medical_exam->student->document_number }}</span>
                </div>
            </div>

            <form action="{{ route('medical_exams.store_result', $medical_exam) }}" method="POST" class="space-y-6">
                @csrf
                
                {{-- 1. Agudeza Visual --}}
                <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-100">
                    <h3 class="text-lg font-black text-slate-800 mb-6 uppercase tracking-tighter flex items-center">
                        <span class="w-7 h-7 bg-teal-100 text-teal-600 rounded-lg flex items-center justify-center mr-3 text-xs">1</span>
                        Capacidad Visual (Snellen)
                    </h3>
                    
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-separate border-spacing-y-2">
                            <thead>
                                <tr class="text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                    <th class="px-4 pb-2">Ojo</th>
                                    <th class="px-4 pb-2">Sin Corrección</th>
                                    <th class="px-4 pb-2">Con Corrección</th>
                                    <th class="px-4 pb-2">Cerca</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- Ojo Derecho --}}
                                <tr class="bg-slate-50 rounded-2xl">
                                    <td class="px-4 py-4 font-bold text-slate-700 rounded-l-2xl">Ojo Derecho (OD)</td>
                                    <td class="px-2 py-2">
                                        <input type="text" name="results[agudeza][od_sc]" placeholder="20/" class="w-full border-none bg-white rounded-xl text-sm focus:ring-2 focus:ring-teal-500">
                                    </td>
                                    <td class="px-2 py-2">
                                        <input type="text" name="results[agudeza][od_cc]" placeholder="20/" class="w-full border-none bg-white rounded-xl text-sm focus:ring-2 focus:ring-teal-500">
                                    </td>
                                    <td class="px-2 py-2 rounded-r-2xl">
                                        <input type="text" name="results[agudeza][od_cerca]" placeholder="1.0" class="w-full border-none bg-white rounded-xl text-sm focus:ring-2 focus:ring-teal-500">
                                    </td>
                                </tr>
                                {{-- Ojo Izquierdo --}}
                                <tr class="bg-slate-50 rounded-2xl">
                                    <td class="px-4 py-4 font-bold text-slate-700 rounded-l-2xl">Ojo Izquierdo (OI)</td>
                                    <td class="px-2 py-2">
                                        <input type="text" name="results[agudeza][oi_sc]" placeholder="20/" class="w-full border-none bg-white rounded-xl text-sm focus:ring-2 focus:ring-teal-500">
                                    </td>
                                    <td class="px-2 py-2">
                                        <input type="text" name="results[agudeza][oi_cc]" placeholder="20/" class="w-full border-none bg-white rounded-xl text-sm focus:ring-2 focus:ring-teal-500">
                                    </td>
                                    <td class="px-2 py-2 rounded-r-2xl">
                                        <input type="text" name="results[agudeza][oi_cerca]" placeholder="1.0" class="w-full border-none bg-white rounded-xl text-sm focus:ring-2 focus:ring-teal-500">
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- 2. Hallazgos y Diagnóstico --}}
                <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-100">
                    <h3 class="text-lg font-black text-slate-800 mb-6 uppercase tracking-tighter flex items-center">
                        <span class="w-7 h-7 bg-teal-100 text-teal-600 rounded-lg flex items-center justify-center mr-3 text-xs">2</span>
                        Evaluación Clínica
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="text-[10px] font-black text-slate-400 uppercase mb-2 block tracking-widest">Bio-Microscopía (Anexos)</label>
                            <textarea name="results[biomicroscopia]" rows="2" class="w-full bg-slate-50 border-none rounded-2xl text-sm focus:ring-2 focus:ring-teal-500" placeholder="Normal..."></textarea>
                        </div>
                        <div>
                            <label class="text-[10px] font-black text-slate-400 uppercase mb-2 block tracking-widest">Motilidad Ocular</label>
                            <textarea name="results[motilidad]" rows="2" class="w-full bg-slate-50 border-none rounded-2xl text-sm focus:ring-2 focus:ring-teal-500" placeholder="Ortoforia, versiones normales..."></textarea>
                        </div>
                    </div>
                </div>

                {{-- Cierre --}}
                <div class="bg-slate-900 p-8 rounded-[2.5rem] shadow-xl">
                    <div class="flex flex-col md:flex-row gap-6 items-start">
                        <div class="flex-1 w-full">
                            <label class="text-[10px] font-black text-teal-400 uppercase mb-4 block tracking-widest">Diagnóstico y Conducta</label>
                            <textarea name="notes" rows="3" required
                                      class="w-full bg-slate-800 border-none rounded-2xl text-white text-sm focus:ring-2 focus:ring-teal-500" 
                                      placeholder="Escriba el diagnóstico (ej: Miopía, Astigmatismo) y recomendaciones..."></textarea>
                        </div>
                        
                        <div class="w-full md:w-64">
                            <label class="text-[10px] font-black text-teal-400 uppercase mb-4 block tracking-widest">¿Usa Lentes?</label>
                            <div class="flex gap-4">
                                <label class="flex-1 cursor-pointer">
                                    <input type="radio" name="results[usa_lentes]" value="Si" class="peer hidden">
                                    <div class="py-2 border-2 border-slate-700 rounded-xl text-center text-slate-400 peer-checked:border-teal-500 peer-checked:text-teal-500 transition-all">SÍ</div>
                                </label>
                                <label class="flex-1 cursor-pointer">
                                    <input type="radio" name="results[usa_lentes]" value="No" checked class="peer hidden">
                                    <div class="py-2 border-2 border-slate-700 rounded-xl text-center text-slate-400 peer-checked:border-teal-500 peer-checked:text-teal-500 transition-all">NO</div>
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex justify-end pt-8 border-t border-slate-800 mt-6">
                        <button type="submit" class="bg-teal-600 text-white px-10 py-4 rounded-2xl font-black shadow-lg hover:bg-teal-500 hover:-translate-y-1 transition-all">
                            GUARDAR VALORACIÓN OPTOMETRÍA
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>