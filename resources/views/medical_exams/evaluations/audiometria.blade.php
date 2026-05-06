<div class="bg-white rounded-[3rem] shadow-2xl shadow-emerald-100/40 border border-emerald-100 overflow-hidden">

    {{-- HEADER PROFESIONAL --}}
    <div class="p-10 border-b border-emerald-50 bg-emerald-50/30 text-center md:text-left">
        <div class="flex flex-col md:flex-row justify-between items-center gap-4">
            <div>
                <span class="text-[10px] font-black text-emerald-600 uppercase tracking-[0.4em] mb-2 block">Módulo de Especialidad</span>
                <h2 class="text-4xl font-black text-slate-900 tracking-tighter uppercase leading-none">
                    Examen: <span class="text-emerald-700">Audiometría</span>
                </h2>
                <p class="text-slate-500 font-bold text-sm uppercase tracking-tight mt-3">
                    Paciente: <span class="text-slate-800">{{ $medical_exam->student->first_name }} {{ $medical_exam->student->last_name }}</span>
                </p>
            </div>
            <div class="bg-white px-8 py-4 rounded-3xl border border-emerald-100 shadow-sm">
                <p class="text-[10px] font-black text-slate-300 uppercase tracking-widest mb-1 text-center">ID Historia</p>
                <p class="text-xl font-black text-slate-800 text-center">{{ $medical_exam->student->document_number }}</p>
            </div>
        </div>
    </div>

    {{-- FORMULARIO UNIFICADO --}}
    <form action="{{ route('medical_exams.store_evaluation', $medical_exam) }}" method="POST" id="audiometriaForm" class="p-8 md:p-12 space-y-12">
        @csrf
        <input type="hidden" name="evaluation_area" value="audiometria">
        {{-- Campo oculto para la imagen de la gráfica --}}
        <input type="hidden" name="audiogram_base64" id="audiogram_base64">

        {{-- 1. OTOSCOPIA PRELIMINAR --}}
        <section>
            <div class="flex items-center gap-4 mb-8">
                <div class="w-10 h-10 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center font-black text-sm uppercase text-center">01</div>
                <h3 class="text-xl font-black text-slate-800 uppercase tracking-tight">Exploración Física (Otoscopia)</h3>
            </div>

            @php
                $otoscopiaOptions = [
                    'conducto' => [
                        'label' => 'Conducto Auditivo Externo (CAE)',
                        'name_od' => 'results[oto_od_cae]',
                        'name_oi' => 'results[oto_oi_cae]',
                        'options' => [
                            '' => 'Seleccione una opción',
                            'Normal' => 'Normal',
                            'Inflamacion' => 'Inflamación',
                            'Erosion' => 'Erosión',
                            'Tapon_Cerumen_Total' => 'Tapón de Cerumen Total',
                            'Tapon_Cerumen_Parcial' => 'Tapón de Cerumen Parcial',
                            'Obliteracion_Completa_o_Parcial' => 'Obliteración completa o parcial',
                            'Atresia' => 'Atresia',
                            'Cuerpo_Extrano' => 'Presencia de cuerpo extraño',
                            'Secrecion' => 'Secreción',
                            'Resequedad' => 'Resequedad',
                        ],
                    ],
                    'cerumen' => [
                        'label' => 'Coloración del Cerumen',
                        'name_od' => 'results[oto_od_cerumen]',
                        'name_oi' => 'results[oto_oi_cerumen]',
                        'options' => [
                            '' => 'Seleccione una opción',
                            'No_Presenta' => 'No presenta',
                            'Marron' => 'Marrón',
                            'Amarillo_Ambar' => 'Amarillo ámbar',
                        ],
                    ],
                    'membrana' => [
                        'label' => 'Membrana Timpánica',
                        'name_od' => 'results[oto_od_membrana]',
                        'name_oi' => 'results[oto_oi_membrana]',
                        'options' => [
                            '' => 'Seleccione una opción',
                            'Normal' => 'Normal',
                            'No_Se_Aprecia' => 'No se aprecia',
                            'Retraida' => 'Retraída',
                            'Abombada' => 'Abombada',
                            'Irritada' => 'Irritada',
                            'Perforada' => 'Perforada',
                        ],
                    ],
                ];
            @endphp

            <div class="grid grid-cols-1 xl:grid-cols-2 gap-8">
                @foreach([
                    'od' => 'Oído Derecho (OD)',
                    'oi' => 'Oído Izquierdo (OI)',
                ] as $earKey => $earLabel)
                    <div class="rounded-[2.25rem] border border-emerald-100 bg-white p-6 space-y-5 shadow-sm">
                        <div class="flex items-center justify-between gap-4">
                            <div>
                                <p class="text-[10px] font-black text-emerald-600 uppercase tracking-[0.35em]">{{ $earLabel }}</p>
                                <h5 class="text-lg font-black text-slate-800 uppercase tracking-tight mt-1">Hallazgos Otoscópicos</h5>
                            </div>
                            <span class="inline-flex items-center justify-center rounded-2xl bg-emerald-50 px-4 py-2 text-[10px] font-black uppercase tracking-[0.25em] text-emerald-700 border border-emerald-100">{{ strtoupper($earKey) }}</span>
                        </div>

                        @foreach($otoscopiaOptions as $field)
                            @php
                                $fieldName = $earKey === 'od' ? $field['name_od'] : $field['name_oi'];
                                $oldValue = old(str_replace(['[', ']'], ['', ''], $fieldName));
                            @endphp
                            <div class="space-y-3">
                                <label class="block text-[10px] font-black text-slate-400 uppercase ml-2 tracking-widest">{{ $field['label'] }}</label>
                                <select name="{{ $fieldName }}" class="w-full bg-white border border-slate-200 rounded-2xl p-4 font-bold text-slate-700 focus:ring-4 focus:ring-emerald-500/10 transition-all">
                                    @foreach($field['options'] as $value => $label)
                                        <option value="{{ $value }}" {{ (string) $oldValue === (string) $value ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>
        </section>

        {{-- 2. TABLA DE FRECUENCIAS --}}
        <section>
            <div class="flex items-center gap-4 mb-8">
                <div class="w-10 h-10 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center font-black text-sm uppercase text-center">02</div>
                <h3 class="text-xl font-black text-slate-800 uppercase tracking-tight">Matriz de Umbrales (Vía Aérea dB)</h3>
            </div>

            <div class="overflow-hidden border border-emerald-100 rounded-[2.5rem] shadow-sm bg-emerald-50/20">
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="bg-emerald-700">
                            <th class="p-6 text-[10px] font-black text-emerald-100 uppercase tracking-widest text-left">Frecuencia (Hz)</th>
                            @foreach([250, 500, 1000, 2000, 4000, 8000] as $hz)
                            <th class="p-6 text-[10px] font-black text-white uppercase tracking-widest text-center border-l border-emerald-800">{{ $hz }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr class="bg-white group">
                            <td class="p-6 text-xs font-black text-emerald-700 uppercase italic tracking-tighter">Oído Derecho (OD)</td>
                            @foreach([250, 500, 1000, 2000, 4000, 8000] as $hz)
                            <td class="p-3 border-l border-slate-50 group-hover:bg-emerald-50/30">
                                <input type="number" name="results[dB_od_{{$hz}}]" placeholder="dB" class="dB-input w-full bg-transparent border-none text-center font-black text-emerald-700 text-lg" min="-10" max="120">
                            </td>
                            @endforeach
                        </tr>
                        <tr class="bg-white group">
                            <td class="p-6 text-xs font-black text-emerald-700 uppercase italic tracking-tighter">Oído Izquierdo (OI)</td>
                            @foreach([250, 500, 1000, 2000, 4000, 8000] as $hz)
                            <td class="p-3 border-l border-slate-50 group-hover:bg-emerald-50/30">
                                <input type="number" name="results[dB_oi_{{$hz}}]" placeholder="dB" class="dB-input w-full bg-transparent border-none text-center font-black text-emerald-700 text-lg" min="-10" max="120">
                            </td>
                            @endforeach
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>

        {{-- 3. GRÁFICA INTERACTIVA --}}
        <section class="bg-white p-8 md:p-12 rounded-[3rem] border border-slate-100 shadow-sm">
            <div class="flex items-center gap-4 mb-8">
                <div class="w-10 h-10 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center font-black text-sm uppercase text-center">03</div>
                <h3 class="text-xl font-black text-slate-800 uppercase tracking-tight">Curva Audiométrica Dinámica</h3>
            </div>

            {{-- Leyenda de símbolos --}}
            <div class="flex gap-8 mb-6 p-4 bg-emerald-50 rounded-2xl border border-emerald-100">
                <div class="flex items-center gap-3">
                    <svg width="30" height="30" viewBox="0 0 30 30" style="display: inline-block;">
                        <circle cx="15" cy="15" r="8" fill="none" stroke="#ef4444" stroke-width="2.5"/>
                    </svg>
                    <span class="text-sm font-bold text-emerald-700 uppercase">Oído Derecho (OD)</span>
                </div>
                <div class="flex items-center gap-3">
                    <svg width="30" height="30" viewBox="0 0 30 30" style="display: inline-block;">
                        <line x1="8" y1="8" x2="22" y2="22" stroke="#2563eb" stroke-width="3" stroke-linecap="round"/>
                        <line x1="22" y1="8" x2="8" y2="22" stroke="#2563eb" stroke-width="3" stroke-linecap="round"/>
                    </svg>
                    <span class="text-sm font-bold text-emerald-700 uppercase">Oído Izquierdo (OI)</span>
                </div>
            </div>

            <div class="relative w-full h-[500px] bg-white rounded-[2rem] p-6 border border-emerald-100">
                <canvas id="audiogramChart"></canvas>
            </div>

            <div class="flex justify-center mt-8 gap-4">
                <button type="button" id="btn-capture-audiogram" class="bg-emerald-600 hover:bg-emerald-700 text-white px-8 py-4 rounded-xl font-black uppercase text-xs tracking-widest transition-all shadow-lg shadow-emerald-200/60">
                    📸 Capturar Audiograma
                </button>
            </div>

            {{-- Vista previa del audiograma capturado --}}
            <div id="capture-preview-audio" style="display:none; margin-top: 20px; text-align: center;">
                <p class="text-sm font-bold text-slate-700 mb-3">Vista previa del audiograma:</p>
                <img id="preview-img-audio" style="max-width: 100%; max-height: 300px; border: 2px solid #10b981; border-radius: 1rem;">
            </div>
        </section>

        {{-- 4. RESULTADOS Y CONDUCTA --}}
        <section class="bg-white rounded-[3rem] p-8 md:p-12 shadow-sm border border-emerald-100">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                <div class="space-y-3">
                    <label class="block text-[10px] font-black text-emerald-600 uppercase tracking-widest">Diagnóstico Sugerido</label>
                    <select name="results[diagnostico]" class="w-full bg-slate-50 border border-slate-200 rounded-2xl p-4 font-bold text-slate-700 focus:ring-4 focus:ring-emerald-500/10">
                        <option value="Normal">Audición Normal</option>
                        <option value="Hipoacusia_Leve">Hipoacusia Leve</option>
                        <option value="Hipoacusia_Moderada">Hipoacusia Moderada</option>
                    </select>
                </div>
                <div class="space-y-3">
                    <label class="block text-[10px] font-black text-emerald-600 uppercase tracking-widest">Uso de Protección Auditiva</label>
                    <div class="flex gap-4">
                        <label class="flex-1 flex items-center justify-center p-4 rounded-2xl bg-slate-50 border border-slate-200 cursor-pointer">
                            <input type="radio" name="results[proteccion]" value="1" class="mr-2">
                            <span class="text-xs font-black text-slate-600 uppercase">Recomendado</span>
                        </label>
                        <label class="flex-1 flex items-center justify-center p-4 rounded-2xl bg-slate-50 border border-slate-200 cursor-pointer">
                            <input type="radio" name="results[proteccion]" value="0" class="mr-2" checked>
                            <span class="text-xs font-black text-slate-600 uppercase">No Requerido</span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="space-y-3">
                <label class="block text-[10px] font-black text-emerald-600 uppercase tracking-widest">Observaciones Clínicas</label>
                <textarea name="notes" rows="5" class="w-full bg-slate-50 border border-slate-200 rounded-[2rem] p-8 text-slate-700 focus:ring-4 focus:ring-emerald-500/10" placeholder="Describa los hallazgos..."></textarea>
            </div>

            <div class="mt-12 flex justify-end">
                <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white px-16 py-6 rounded-2xl font-black uppercase text-sm tracking-widest transition-all">
                    Guardar Audiometría
                </button>
            </div>
        </section>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('audiogramChart').getContext('2d');
        const form = document.getElementById('audiometriaForm');

        const audiogramChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['250', '500', '1000', '2000', '4000', '8000'],
                datasets: [
                    { label: 'OD ○', borderColor: '#ef4444', backgroundColor: 'rgba(0,0,0,0)', pointBorderColor: '#ef4444', pointBackgroundColor: 'rgba(0,0,0,0)', pointBorderWidth: 2, data: [null,null,null,null,null,null], pointStyle: 'circle', pointRadius: 8, spanGaps: true },
                    { label: 'OI ✕', borderColor: '#2563eb', backgroundColor: '#2563eb', data: [null,null,null,null,null,null], pointStyle: 'crossRot', pointRadius: 10, spanGaps: true }
                ]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                scales: {
                    y: { reverse: true, min: -10, max: 120, ticks: { stepSize: 10 } }
                },
                animation: false // Desactivado para asegurar captura de imagen correcta
            }
        });

        function syncData() {
            const freqs = [250, 500, 1000, 2000, 4000, 8000];
            audiogramChart.data.datasets[0].data = freqs.map(f => {
                const el = document.getElementsByName(`results[dB_od_${f}]`)[0];
                return el && el.value !== "" ? parseFloat(el.value) : null;
            });
            audiogramChart.data.datasets[1].data = freqs.map(f => {
                const el = document.getElementsByName(`results[dB_oi_${f}]`)[0];
                return el && el.value !== "" ? parseFloat(el.value) : null;
            });
            audiogramChart.update('none');
        }

        document.querySelectorAll('.dB-input').forEach(input => input.addEventListener('input', syncData));

        // Captura explícita del audiograma
        let capturedAudiogramBase64 = null;
        document.getElementById('btn-capture-audiogram').addEventListener('click', function(e) {
            e.preventDefault();
            const btn = document.getElementById('btn-capture-audiogram');
            const originalText = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = `⏳ Capturando...`;

            setTimeout(() => {
                try {
                    // Capturar imagen del gráfico
                    capturedAudiogramBase64 = audiogramChart.toBase64Image();

                    // Mostrar vista previa
                    document.getElementById('preview-img-audio').src = capturedAudiogramBase64;
                    document.getElementById('capture-preview-audio').style.display = 'block';

                    // Guardar en input oculto
                    document.getElementById('audiogram_base64').value = capturedAudiogramBase64;

                    btn.disabled = false;
                    btn.innerHTML = `✅ ${originalText}`;
                    btn.classList.remove('bg-emerald-600', 'hover:bg-emerald-700');
                    btn.classList.add('bg-emerald-700', 'hover:bg-emerald-600');

                    setTimeout(() => {
                        btn.innerHTML = originalText;
                        btn.classList.remove('bg-emerald-700', 'hover:bg-emerald-600');
                        btn.classList.add('bg-emerald-600', 'hover:bg-emerald-700');
                    }, 2000);
                } catch (err) {
                    console.error('Error capturando audiograma:', err);
                    btn.disabled = false;
                    btn.innerHTML = `❌ Error en captura`;
                    setTimeout(() => {
                        btn.innerHTML = originalText;
                    }, 2000);
                }
            }, 150);
        });

        form.addEventListener('submit', function(e) {
            if (!capturedAudiogramBase64) {
                e.preventDefault();
                alert('⚠️ Debes capturar el audiograma primero antes de guardar.');
                return;
            }
            document.getElementById('audiogram_base64').value = capturedAudiogramBase64;
        });
    });
</script>
