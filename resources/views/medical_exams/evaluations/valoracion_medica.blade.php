<x-app-layout>
    <div class="py-12" x-data="medForm()">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Encabezado con información del Estudiante --}}
            <div class="mb-8 flex items-center justify-between">
                <div>
                    <h2 class="text-3xl font-black text-slate-800 tracking-tighter uppercase">
                        Valoración: <span class="text-blue-600">{{ $medical_exam->student->full_name }}</span>
                    </h2>
                    <p class="text-slate-500 font-medium italic">Área actual: Medicina General</p>
                </div>
                <div class="text-right">
                    <span class="text-[10px] font-bold text-slate-400 uppercase block">Documento</span>
                    <span class="font-mono font-bold text-slate-700">{{ $medical_exam->student->document_number }}</span>
                </div>
            </div>

            <form action="{{ route('medical_exams.store_result', $medical_exam) }}" method="POST" class="space-y-6">
                @csrf
                
                {{-- 1. Medidas Antropométricas --}}
                <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-100">
                    <h3 class="text-lg font-black text-slate-800 mb-6 uppercase tracking-tighter flex items-center">
                        <span class="w-7 h-7 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center mr-3 text-xs">1</span>
                        Medidas Antropométricas
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                        <div class="bg-slate-50 p-4 rounded-2xl border border-transparent focus-within:border-blue-200 transition-all">
                            <label class="text-[10px] font-bold text-slate-400 uppercase mb-2 block tracking-widest">Peso (kg)</label>
                            <input type="number" step="0.1" name="results[antropometria][peso]" x-model.number="peso" @input="calc()" 
                                   class="w-full border-none bg-transparent text-xl font-bold focus:ring-0 p-0" required placeholder="0.0">
                        </div>
                        <div class="bg-slate-50 p-4 rounded-2xl border border-transparent focus-within:border-blue-200 transition-all">
                            <label class="text-[10px] font-bold text-slate-400 uppercase mb-2 block tracking-widest">Talla (cm)</label>
                            <input type="number" name="results[antropometria][talla]" x-model.number="talla" @input="calc()" 
                                   class="w-full border-none bg-transparent text-xl font-bold focus:ring-0 p-0" required placeholder="0">
                        </div>
                        <div class="p-4 rounded-2xl text-white text-center transition-colors duration-500" 
                             :class="imcColor">
                            <label class="text-[10px] font-bold text-white/70 uppercase mb-1 block">IMC Calculado</label>
                            <span class="text-3xl font-black" x-text="imc">0.0</span>
                            {{-- Input oculto para enviar el valor del IMC --}}
                            <input type="hidden" name="results[antropometria][imc]" :value="imc">
                        </div>
                    </div>

                    {{-- Clasificación IMC --}}
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                        <template x-for="s in ['Bajo Peso', 'Normal', 'Sobrepeso', 'Obesidad']">
                            <label class="cursor-pointer">
                                <input type="radio" name="results[antropometria][imc_status]" :value="s" x-model="status" class="peer hidden">
                                <div class="py-3 rounded-xl border-2 border-slate-100 text-center peer-checked:border-blue-500 peer-checked:bg-blue-50 transition-all">
                                    <span class="text-[10px] font-black uppercase text-slate-400 peer-checked:text-blue-600" x-text="s"></span>
                                </div>
                            </label>
                        </template>
                    </div>
                </div>

                {{-- 2. Antecedentes Médicos --}}
                <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-100">
                    <h3 class="text-lg font-black text-slate-800 mb-6 flex items-center tracking-tighter uppercase">
                        <span class="w-7 h-7 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center mr-3 text-xs">2</span>
                        Antecedentes Médicos
                    </h3>
                    <div class="space-y-4">
                        @php
                            $preguntas = [
                                'enfermedad' => '¿Tiene alguna enfermedad diagnosticada?',
                                'hospitalizado' => '¿Ha sido hospitalizado alguna vez?',
                                'cirugias' => '¿Ha tenido cirugías?',
                                'medicamentos' => '¿Toma medicamentos actualmente?',
                                'alergias' => '¿Tiene alergias conocidas?',
                                'actividad_fisica' => '¿Se cansa fácilmente al hacer actividad?',
                                'nacimiento' => '¿Nacimiento a término?',
                                'desarrollo' => '¿Desarrollo normal según pediatra?'
                            ];
                        @endphp
                        @foreach($preguntas as $key => $txt)
                        <div class="p-4 rounded-2xl border border-slate-50 hover:bg-slate-50/50 transition flex flex-col md:flex-row md:items-center gap-4">
                            <p class="flex-1 text-sm font-bold text-slate-600">{{ $loop->iteration }}. {{ $txt }}</p>
                            <div class="flex items-center gap-4">
                                <label class="flex items-center gap-2 text-xs font-bold text-slate-400 cursor-pointer">
                                    <input type="radio" name="results[antecedentes][{{$key}}][respuesta]" value="No" checked class="text-blue-600 focus:ring-blue-500"> No
                                </label>
                                <label class="flex items-center gap-2 text-xs font-bold text-slate-400 cursor-pointer">
                                    <input type="radio" name="results[antecedentes][{{$key}}][respuesta]" value="Si" class="text-blue-600 focus:ring-blue-500"> Sí →
                                </label>
                                <input type="text" name="results[antecedentes][{{$key}}][detalle]" placeholder="Detalle..." 
                                       class="border-b border-slate-200 bg-transparent text-xs focus:ring-0 focus:border-blue-500 outline-none w-40 italic">
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- 3. Exploración Física --}}
                <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-100">
                    <h3 class="text-lg font-black text-slate-800 mb-6 uppercase tracking-tighter flex items-center">
                        <span class="w-7 h-7 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center mr-3 text-xs">3</span>
                        Exploración Física
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @foreach(['Cabeza', 'Cuello', 'Torax', 'Abdomen', 'Extremidades'] as $item)
                        <div>
                            <label class="text-[10px] font-black text-slate-400 uppercase mb-2 block tracking-widest">{{ $item }}</label>
                            <textarea name="results[exploracion][{{strtolower($item)}}]" rows="2" 
                                      class="w-full bg-slate-50 border-none rounded-2xl text-sm focus:ring-2 focus:ring-blue-500 placeholder-slate-300" 
                                      placeholder="Normal, sin hallazgos patológicos..."></textarea>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- Diagnóstico y Cierre --}}
                <div class="bg-slate-900 p-8 rounded-[2.5rem] shadow-xl">
                    <label class="text-[10px] font-black text-blue-400 uppercase mb-4 block tracking-widest">Conclusión y Conducta a Seguir</label>
                    <textarea name="notes" rows="3" required
                              class="w-full bg-slate-800 border-none rounded-2xl text-white text-sm focus:ring-2 focus:ring-blue-500" 
                              placeholder="Escriba el diagnóstico final o recomendaciones..."></textarea>
                    
                    <div class="flex justify-end pt-8">
                        <button type="submit" class="bg-blue-600 text-white px-10 py-4 rounded-2xl font-black shadow-lg hover:bg-blue-500 hover:-translate-y-1 transition-all flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                            </svg>
                            GUARDAR Y FINALIZAR
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        function medForm() {
            return {
                peso: '', 
                talla: '', 
                imc: '0.0', 
                status: 'Normal',
                imcColor: 'bg-slate-400',
                calc() {
                    if(this.peso > 0 && this.talla > 0) {
                        let tallM = this.talla / 100;
                        let res = (this.peso / (tallM * tallM)).toFixed(1);
                        this.imc = res;
                        
                        if(res < 18.5) {
                            this.status = 'Bajo Peso';
                            this.imcColor = 'bg-amber-500';
                        } else if(res <= 24.9) {
                            this.status = 'Normal';
                            this.imcColor = 'bg-green-500';
                        } else if(res <= 29.9) {
                            this.status = 'Sobrepeso';
                            this.imcColor = 'bg-orange-500';
                        } else {
                            this.status = 'Obesidad';
                            this.imcColor = 'bg-red-600';
                        }
                    } else {
                        this.imc = '0.0';
                        this.imcColor = 'bg-slate-400';
                    }
                }
            }
        }
    </script>
</x-app-layout>