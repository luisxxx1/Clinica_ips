<div class="bg-white rounded-[3rem] shadow-2xl shadow-slate-200/50 border border-slate-100 overflow-hidden">
    
    {{-- HEADER PROFESIONAL --}}
    <div class="p-10 border-b border-slate-50 bg-slate-50/30 text-center md:text-left">
        <div class="flex flex-col md:flex-row justify-between items-center gap-4">
            <div>
                <span class="text-[10px] font-black text-blue-500 uppercase tracking-[0.4em] mb-2 block">Módulo de Especialidad</span>
                <h2 class="text-4xl font-black text-slate-900 tracking-tighter uppercase leading-none">
                    Examen: <span class="text-blue-600">Audiometría</span>
                </h2>
                <p class="text-slate-500 font-bold text-sm uppercase tracking-tight mt-3">
                    Paciente: <span class="text-slate-800">{{ $medical_exam->student->name }}</span>
                </p>
            </div>
            <div class="bg-white px-8 py-4 rounded-3xl border border-slate-100 shadow-sm">
                <p class="text-[10px] font-black text-slate-300 uppercase tracking-widest mb-1 text-center">ID Historia</p>
                <p class="text-xl font-black text-slate-800 text-center">{{ $medical_exam->student->document_number }}</p>
            </div>
        </div>
    </div>

    {{-- FORMULARIO UNIFICADO --}}
    <form action="{{ route('medical_exams.store_evaluation', $medical_exam) }}" method="POST" class="p-8 md:p-12 space-y-12">
        @csrf

        {{-- 1. OTOSCOPIA PRELIMINAR --}}
        <section>
            <div class="flex items-center gap-4 mb-8">
                <div class="w-10 h-10 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center font-black text-sm uppercase text-center">01</div>
                <h3 class="text-xl font-black text-slate-800 uppercase tracking-tight">Exploración Física (Otoscopia)</h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                @foreach(['oto_od' => 'Oído Derecho (OD)', 'oto_oi' => 'Oído Izquierdo (OI)'] as $name => $label)
                <div class="space-y-3">
                    <label class="block text-[10px] font-black text-slate-400 uppercase ml-2 tracking-widest">{{ $label }}</label>
                    <select name="{{ $name }}" class="w-full bg-slate-50 border-none rounded-2xl p-4 font-bold text-slate-700 focus:ring-4 focus:ring-blue-500/10 transition-all">
                        <option value="Normal">Conducto Integrado / Normal</option>
                        <option value="Tapon_Cerumen">Presencia de Cerumen</option>
                        <option value="Inflamado">Inflamación / Otitis</option>
                        <option value="Perforado">Membrana Perforada</option>
                    </select>
                </div>
                @endforeach
            </div>
        </section>

        {{-- 2. TABLA DE FRECUENCIAS --}}
        <section>
            <div class="flex items-center gap-4 mb-8">
                <div class="w-10 h-10 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center font-black text-sm uppercase text-center">02</div>
                <h3 class="text-xl font-black text-slate-800 uppercase tracking-tight">Matriz de Umbrales (Vía Aérea dB)</h3>
            </div>

            <div class="overflow-hidden border border-slate-100 rounded-[2.5rem] shadow-sm bg-slate-50/30">
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="bg-slate-900">
                            <th class="p-6 text-[10px] font-black text-slate-400 uppercase tracking-widest text-left">Frecuencia (Hz)</th>
                            @foreach([250, 500, 1000, 2000, 4000, 8000] as $hz)
                            <th class="p-6 text-[10px] font-black text-white uppercase tracking-widest text-center border-l border-slate-800">{{ $hz }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        {{-- OÍDO DERECHO --}}
                        <tr class="bg-white group">
                            <td class="p-6">
                                <span class="text-xs font-black text-red-600 uppercase italic tracking-tighter">Oído Derecho (OD)</span>
                            </td>
                            @foreach([250, 500, 1000, 2000, 4000, 8000] as $hz)
                            <td class="p-3 border-l border-slate-50 group-hover:bg-red-50/30 transition-colors">
                                <input type="number" name="dB_od_{{$hz}}" placeholder="dB" 
                                    class="w-full bg-transparent border-none text-center font-black text-red-700 placeholder:text-red-200 focus:ring-0 text-lg" min="0" max="120">
                            </td>
                            @endforeach
                        </tr>
                        {{-- OÍDO IZQUIERDO --}}
                        <tr class="bg-white group">
                            <td class="p-6">
                                <span class="text-xs font-black text-blue-600 uppercase italic tracking-tighter">Oído Izquierdo (OI)</span>
                            </td>
                            @foreach([250, 500, 1000, 2000, 4000, 8000] as $hz)
                            <td class="p-3 border-l border-slate-50 group-hover:bg-blue-50/30 transition-colors">
                                <input type="number" name="dB_oi_{{$hz}}" placeholder="dB" 
                                    class="w-full bg-transparent border-none text-center font-black text-blue-700 placeholder:text-blue-200 focus:ring-0 text-lg" min="0" max="120">
                            </td>
                            @endforeach
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>

        {{-- 3. GRÁFICA INTERACTIVA --}}
        <section class="bg-white p-8 md:p-12 rounded-[3rem] border border-slate-100 shadow-sm transition-all overflow-hidden">
            <div class="flex items-center gap-4 mb-8">
                <div class="w-10 h-10 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center font-black text-sm uppercase text-center">03</div>
                <h3 class="text-xl font-black text-slate-800 uppercase tracking-tight">Curva Audiométrica Dinámica</h3>
            </div>
            
            <div class="relative w-full h-[450px] bg-slate-50/50 rounded-[2rem] p-6 border border-dashed border-slate-200">
                <canvas id="audiogramChart"></canvas>
            </div>
        </section>

        {{-- 4. RESULTADOS Y CONDUCTA --}}
        <section class="bg-slate-900 rounded-[3rem] p-8 md:p-12 shadow-2xl shadow-blue-900/20">
            <div class="flex items-center gap-4 mb-8">
                <div class="w-10 h-10 bg-blue-500/20 text-blue-400 rounded-xl flex items-center justify-center font-black text-sm italic uppercase text-center">S_D</div>
                <h3 class="text-xl font-black text-white uppercase tracking-tight">Interpretación y Conducta</h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                <div class="space-y-3">
                    <label class="block text-[10px] font-black text-blue-400 uppercase ml-2 tracking-widest">Diagnóstico Sugerido</label>
                    <select name="diagnostico" class="w-full bg-slate-800 border-none rounded-2xl p-4 font-bold text-white focus:ring-4 focus:ring-blue-500/20 transition-all">
                        <option value="Normal">Audición Normal</option>
                        <option value="Hipoacusia_Leve">Hipoacusia Leve</option>
                        <option value="Hipoacusia_Moderada">Hipoacusia Moderada</option>
                        <option value="Hipoacusia_Severa">Hipoacusia Severa</option>
                        <option value="Anacusia">Anacusia / Sordera</option>
                    </select>
                </div>
                <div class="space-y-3">
                    <label class="block text-[10px] font-black text-blue-400 uppercase ml-2 tracking-widest">Uso de Protección Auditiva</label>
                    <div class="flex gap-4">
                        <label for="prot_si" class="flex-1 flex items-center justify-center p-4 rounded-2xl bg-slate-800 border border-transparent hover:border-blue-500/50 cursor-pointer transition-all group">
                            <input type="radio" id="prot_si" name="proteccion" value="1" class="hidden peer">
                            <span class="text-xs font-black text-slate-500 peer-checked:text-blue-400 uppercase tracking-widest transition-colors">Recomendado</span>
                        </label>
                        <label for="prot_no" class="flex-1 flex items-center justify-center p-4 rounded-2xl bg-slate-800 border border-transparent hover:border-slate-500/50 cursor-pointer transition-all group">
                            <input type="radio" id="prot_no" name="proteccion" value="0" class="hidden peer">
                            <span class="text-xs font-black text-slate-500 peer-checked:text-slate-300 uppercase tracking-widest transition-colors">No Requerido</span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="space-y-3">
                <label class="block text-[10px] font-black text-blue-400 uppercase ml-2 tracking-widest">Observaciones Clínicas Detalladas</label>
                <textarea name="observations" rows="5" required
                    class="w-full bg-slate-800/50 border-none rounded-[2rem] p-8 text-white text-base focus:ring-4 focus:ring-blue-500/20 placeholder:text-slate-600 transition-all resize-none" 
                    placeholder="Describa la curva audiométrica, simetría y recomendaciones finales..."></textarea>
            </div>

            <div class="mt-12 flex flex-col md:flex-row justify-between items-center gap-8 border-t border-slate-800 pt-10">
                <div class="text-center md:text-left">
                    <p class="text-[9px] font-black text-slate-500 uppercase tracking-[0.3em] mb-1">Snake_DEV Technology</p>
                    <p class="text-[10px] font-bold text-slate-400">Sistema Certificado de Historias Clínicas Unificadas</p>
                </div>
                <button type="submit" class="w-full md:w-auto bg-blue-600 hover:bg-blue-500 text-white px-16 py-6 rounded-2xl font-black uppercase text-sm tracking-widest shadow-2xl shadow-blue-900/40 hover:-translate-y-1 transition-all">
                    Guardar Audiometría
                </button>
            </div>
        </section>
    </form>
</div>

{{-- SCRIPTS --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('audiogramChart').getContext('2d');
        const audiogramChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['250', '500', '1000', '2000', '4000', '8000'],
                datasets: [
                    {
                        label: 'Oído Derecho (OD)',
                        borderColor: '#ef4444',
                        backgroundColor: '#ef4444',
                        data: [null, null, null, null, null, null],
                        borderWidth: 3,
                        pointStyle: 'circle',
                        pointRadius: 7,
                        spanGaps: true
                    },
                    {
                        label: 'Oído Izquierdo (OI)',
                        borderColor: '#2563eb',
                        backgroundColor: '#2563eb',
                        data: [null, null, null, null, null, null],
                        borderWidth: 3,
                        pointStyle: 'crossRot',
                        pointRadius: 8,
                        spanGaps: true
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: { reverse: true, min: -10, max: 120, ticks: { stepSize: 10, font: { weight: 'bold' } } },
                    x: { ticks: { font: { weight: 'bold' } } }
                }
            }
        });

        function syncData() {
            const freqs = [250, 500, 1000, 2000, 4000, 8000];
            audiogramChart.data.datasets[0].data = freqs.map(f => document.getElementsByName(`dB_od_${f}`)[0].value || null);
            audiogramChart.data.datasets[1].data = freqs.map(f => document.getElementsByName(`dB_oi_${f}`)[0].value || null);
            audiogramChart.update();
        }

        document.querySelectorAll('input[type="number"]').forEach(input => input.addEventListener('input', syncData));
    });
</script>