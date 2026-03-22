{{-- resources/views/medical_exams/evaluations/audiometria.blade.php --}}

<div class="mb-10 flex items-center justify-between">
    <div>
        <span class="text-[10px] font-black text-blue-500 uppercase tracking-[0.3em] mb-2 block">Especialidad Clínica</span>
        <h2 class="text-4xl font-black text-slate-900 tracking-tighter uppercase leading-none">
            Examen: <span class="text-blue-600">Audiometría</span>
        </h2>
        <div class="flex items-center mt-3 space-x-3">
            <p class="text-slate-500 font-bold text-sm uppercase tracking-tight">
                {{-- CORRECCIÓN CRÍTICA: Se usa $medical_exam para evitar el error 500 --}}
                Paciente: <span class="text-slate-800">{{ $medical_exam->student->name }}</span>
            </p>
        </div>
    </div>
</div>

<form action="{{ route('medical_exams.store_evaluation', $medical_exam) }}" method="POST" class="space-y-8">
    @csrf

    {{-- 1. Exploración Física (Otoscopia) --}}
    <div class="bg-white p-10 rounded-[3rem] shadow-sm border border-slate-100">
        <div class="flex items-center mb-8">
            <div class="w-10 h-10 bg-blue-600 text-white rounded-2xl flex items-center justify-center mr-4 shadow-lg shadow-blue-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
            </div>
            <h3 class="text-xl font-black text-slate-800 uppercase tracking-tighter">Otoscopia Preliminar</h3>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            @foreach(['oto_od' => 'Oído Derecho', 'oto_oi' => 'Oído Izquierdo'] as $name => $label)
            <div>
                <label class="text-[10px] font-black text-slate-400 uppercase mb-3 block tracking-widest">{{ $label }}</label>
                <select name="{{ $name }}" class="w-full bg-slate-50 border-none rounded-2xl text-sm font-bold py-3.5 focus:ring-4 focus:ring-blue-500/10 transition-all">
                    <option value="Normal">Conducto Integrado / Normal</option>
                    <option value="Tapón_Cerumen">Presencia de Cerumen</option>
                    <option value="Inflamado">Inflamación / Otitis</option>
                    <option value="Perforado">Membrana Perforada</option>
                </select>
            </div>
            @endforeach
        </div>
    </div>

    {{-- 2. Umbrales de Vía Aérea (dB) --}}
    <div class="bg-white p-10 rounded-[3rem] shadow-sm border border-slate-100 overflow-hidden">
        <div class="flex items-center mb-8">
            <div class="w-10 h-10 bg-slate-900 text-white rounded-2xl flex items-center justify-center mr-4">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
            </div>
            <h3 class="text-xl font-black text-slate-800 uppercase tracking-tighter">Umbrales de Audición</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-50">
                        <th class="pb-4 pl-2">Lado / Frecuencia</th>
                        @foreach([250, 500, 1000, 2000, 4000, 8000] as $hz)
                        <th class="pb-4 px-2 text-center">{{ $hz }} Hz</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="text-sm font-bold">
                    <tr class="group">
                        <td class="py-6 text-red-600 font-black italic uppercase">Derecho (OD)</td>
                        @foreach([250, 500, 1000, 2000, 4000, 8000] as $hz)
                        <td class="px-2">
                            <input type="number" name="dB_od_{{$hz}}" placeholder="0" class="w-full bg-red-50/50 border-none rounded-xl text-center text-red-700 focus:ring-2 focus:ring-red-500 py-3">
                        </td>
                        @endforeach
                    </tr>
                    <tr class="group">
                        <td class="py-6 text-blue-600 font-black italic uppercase">Izquierdo (OI)</td>
                        @foreach([250, 500, 1000, 2000, 4000, 8000] as $hz)
                        <td class="px-2">
                            <input type="number" name="dB_oi_{{$hz}}" placeholder="0" class="w-full bg-blue-50/50 border-none rounded-xl text-center text-blue-700 focus:ring-2 focus:ring-blue-500 py-3">
                        </td>
                        @endforeach
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- 3. Conclusiones SnakeDEV --}}
    <div class="bg-slate-900 p-10 rounded-[3rem] shadow-2xl shadow-slate-200">
        <div class="flex items-center mb-6">
            <div class="w-2 h-8 bg-blue-500 rounded-full mr-4"></div>
            <label class="text-xs font-black text-blue-400 uppercase tracking-[0.2em]">Observaciones Audiométricas</label>
        </div>
        
        <textarea name="observations" rows="4" required
                  class="w-full bg-slate-800/50 border-none rounded-3xl text-white text-base p-6 focus:ring-4 focus:ring-blue-500/20 placeholder:text-slate-500 transition-all" 
                  placeholder="Escriba la conducta a seguir o formula médica..."></textarea>
        
        <div class="mt-10 flex flex-col md:flex-row justify-between items-center gap-6">
            <p class="text-slate-400 text-[10px] font-bold uppercase tracking-widest max-w-xs text-center md:text-left">
                SnakeDEV System - Los datos serán almacenados en el expediente clínico digital del paciente.
            </p>
            <button type="submit" class="w-full md:w-auto bg-blue-600 text-white px-12 py-5 rounded-2xl font-black shadow-xl shadow-blue-900/20 hover:bg-blue-500 hover:-translate-y-1 active:scale-95 transition-all uppercase text-sm">
                Guardar Audiometría
            </button>
        </div>
    </div>
</form>