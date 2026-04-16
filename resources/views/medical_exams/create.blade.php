<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Valoración Médica Activa - Odontología') }}
            </h2>
            <span class="px-4 py-1 bg-blue-100 text-blue-700 rounded-full text-sm font-bold shadow-sm border border-blue-200">
                Estudiante: {{ $student->full_name }}
            </span>
        </div>
    </x-slot>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">

                {{-- Sidebar --}}
                <div class="lg:col-span-1">
                    <div class="app-panel-strong p-6 rounded-[2rem] sticky top-6 text-center">
                                <div class="h-20 w-20 bg-teal-700 text-white rounded-full flex items-center justify-center text-3xl font-bold mx-auto mb-2 shadow-inner">
                            {{ substr($student->first_name, 0, 1) }}{{ substr($student->last_name, 0, 1) }}
                        </div>
                        <h3 class="font-bold text-gray-900 leading-tight">{{ $student->full_name }}</h3>
                        <p class="text-xs text-gray-500 uppercase tracking-tighter">{{ $student->document_type }}: {{ $student->document_number }}</p>
                        <hr class="my-4 border-gray-100">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold {{ $medicalExam->status === 'en_proceso' ? 'bg-amber-100 text-amber-800 border border-amber-200' : 'bg-teal-100 text-teal-800 border border-teal-200' }}">
                            {{ ucfirst(str_replace('_', ' ', $medicalExam->status)) }}
                        </span>
                    </div>
                </div>

                {{-- Panel Principal --}}
                <div class="lg:col-span-3">
                    <div class="app-panel-strong overflow-hidden rounded-[2rem]">
                        <div class="p-8">
                            <form id="form-odontologia" action="{{ route('medical_exams.store_result', $medicalExam) }}" method="POST">
                                @csrf
                                <input type="hidden" name="odontograma_imagen" id="odontograma_imagen">

                                <div class="space-y-10">
                                    {{-- ODONTOGRAMA --}}
                                    <div id="capture-area" class="bg-slate-50 p-8 rounded-[2rem] border border-slate-200 shadow-inner">
                                        <h4 class="text-slate-500 font-black mb-10 text-xs uppercase text-center tracking-[0.2em]">Odontograma Interactivo</h4>

                                        {{-- Leyenda --}}
                                        <div class="flex flex-wrap justify-center gap-6 mb-12 border-b border-slate-200 pb-8" data-html2canvas-ignore>
                                            @foreach(['white' => 'Sano', 'red' => 'Caries', 'blue' => 'Obturado', 'green' => 'Sellante', 'gray' => 'Ausente'] as $color => $label)
                                                <div class="flex items-center gap-2">
                                                    <div class="w-5 h-5 rounded border border-gray-400 {{ $color === 'white' ? 'bg-white' : ($color === 'red' ? 'bg-red-600' : ($color === 'blue' ? 'bg-blue-600' : ($color === 'green' ? 'bg-green-500' : 'bg-gray-800'))) }}"></div>
                                                    <span class="text-[11px] font-bold text-slate-600 uppercase">{{ $label }}</span>
                                                </div>
                                            @endforeach
                                        </div>

                                        <script>
                                            function toothLogic(toothNumber) {
                                                return {
                                                    faces: { top: 'white', bottom: 'white', left: 'white', right: 'white', center: 'white' },
                                                    toggle(face) {
                                                        const colors = ['white', 'red', 'blue', 'green', 'gray'];
                                                        this.faces[face] = colors[(colors.indexOf(this.faces[face]) + 1) % colors.length];
                                                    },
                                                    getColor(face) {
                                                        return { 'white':'#ffffff', 'red':'#dc2626', 'blue':'#2563eb', 'green':'#16a34a', 'gray':'#1f2937' }[this.faces[face]];
                                                    }
                                                }
                                            }
                                        </script>

                                        <div class="flex flex-col items-center space-y-10">
                                            {{-- Renderizado de cuadrantes --}}
                                            @php
                                                $rows = [
                                                    ['list' => [18,17,16,15,14,13,12,11, 21,22,23,24,25,26,27,28], 'label' => 'Superiores Permanentes'],
                                                    ['list' => [55,54,53,52,51, 61,62,63,64,65], 'label' => 'Superiores Temporales'],
                                                    ['list' => [85,84,83,82,81, 71,72,73,74,75], 'label' => 'Inferiores Temporales'],
                                                    ['list' => [48,47,46,45,44,43,42,41, 31,32,33,34,35,36,37,38], 'label' => 'Inferiores Permanentes']
                                                ];
                                            @endphp

                                            @foreach($rows as $index => $row)
                                                <div class="flex flex-wrap justify-center gap-1">
                                                    @foreach($row['list'] as $n)
                                                        <div x-data="toothLogic({{ $n }})" class="flex flex-col items-center">
                                                            <svg width="32" height="32" viewBox="0 0 100 100" class="cursor-pointer transition hover:opacity-80">
                                                                <path @click="toggle('top')" :fill="getColor('top')" d="M0,0 L100,0 L70,30 L30,30 Z" stroke="#333" stroke-width="3" />
                                                                <path @click="toggle('right')" :fill="getColor('right')" d="M100,0 L100,100 L70,70 L70,30 Z" stroke="#333" stroke-width="3" />
                                                                <path @click="toggle('bottom')" :fill="getColor('bottom')" d="M0,100 L100,100 L70,70 L30,70 Z" stroke="#333" stroke-width="3" />
                                                                <path @click="toggle('left')" :fill="getColor('left')" d="M0,0 L0,100 L30,70 L30,30 Z" stroke="#333" stroke-width="3" />
                                                                <rect @click="toggle('center')" :fill="getColor('center')" x="30" y="30" width="40" height="40" stroke="#333" stroke-width="3" />
                                                            </svg>
                                                            <span class="text-[10px] font-bold mt-1 text-slate-700">{{ $n }}</span>
                                                            {{-- Inputs ocultos organizados para el controlador --}}
                                                            <template x-for="(val, face) in faces">
                                                                <input type="hidden" :name="`odontograma[${ {{ $n }} }][${face}]`" :value="val">
                                                            </template>
                                                        </div>
                                                    @endforeach
                                                </div>
                                                @if($index === 1) <div class="w-full border-t-2 border-slate-200 border-dashed"></div> @endif
                                            @endforeach
                                        </div>
                                    </div>

                                    {{-- ANTECEDENTES --}}
                                    <div class="bg-teal-50/50 p-6 rounded-[1.5rem] border border-teal-100">
                                        <h4 class="text-teal-700 font-bold mb-6 text-xs uppercase tracking-widest flex items-center gap-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
                                            Antecedentes y Hábitos
                                        </h4>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            @foreach(['cepillado' => '¿Se cepilla los dientes diariamente?', 'caries' => '¿Ha tenido caries anteriormente?', 'tratamiento' => '¿Ha recibido tratamiento dental?', 'visita' => '¿Visitó al odontólogo el último año?'] as $key => $pregunta)
                                                <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm">
                                                    <p class="text-sm font-semibold text-gray-700 mb-3">{{ $pregunta }}</p>
                                                    <div class="flex gap-4">
                                                        <label class="inline-flex items-center">
                                                            <input type="radio" name="habitos[{{ $key }}]" value="si" class="text-teal-600 focus:ring-teal-500" required>
                                                            <span class="ml-2 text-sm">Sí</span>
                                                        </label>
                                                        <label class="inline-flex items-center">
                                                            <input type="radio" name="habitos[{{ $key }}]" value="no" class="text-teal-600 focus:ring-teal-500" required>
                                                            <span class="ml-2 text-sm">No</span>
                                                        </label>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>

                                    {{-- Observaciones --}}
                                    <div class="bg-white p-6 rounded-[1.5rem] border border-slate-200">
                                        <label class="block text-sm font-bold text-slate-700 mb-2 uppercase">Observaciones / Recomendaciones</label>
                                        <textarea name="notes" rows="4" class="w-full border-slate-300 rounded-2xl focus:ring-teal-500 focus:border-teal-500 text-sm" placeholder="Diagnóstico..."></textarea>
                                    </div>
                                </div>

                                <div class="mt-8 flex justify-end">
                                    <button type="submit" id="btn-save" class="bg-teal-700 hover:bg-teal-600 text-white font-black py-3 px-10 rounded-full text-[10px] uppercase tracking-[0.22em] shadow-lg transition transform hover:scale-105">
                                        GUARDAR DIAGNÓSTICO
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('form-odontologia').addEventListener('submit', function(e) {
            e.preventDefault();
            const form = this;
            const btn = document.getElementById('btn-save');

            btn.disabled = true;
            btn.innerHTML = "PROCESANDO...";

            const captureArea = document.getElementById('capture-area');

            // Pequeña espera para asegurar que Alpine terminó de renderizar cualquier cambio visual
            setTimeout(() => {
                html2canvas(captureArea, {
                    backgroundColor: '#f8fafc',
                    scale: 2, // Mejor resolución para el PDF posterior
                    logging: false,
                    useCORS: true
                }).then(canvas => {
                    document.getElementById('odontograma_imagen').value = canvas.toDataURL('image/png');
                    form.submit();
                }).catch(err => {
                    console.error("Error:", err);
                    form.submit(); // Intentar enviar aunque falle la imagen
                });
            }, 300);
        });
    </script>
</x-app-layout>
