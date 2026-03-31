{{-- resources/views/medical_exams/evaluations/valoracion_medica.blade.php --}}

<div x-data="{
    tab: 'evaluacion',
    edad: {{ (int) ($medical_exam->student->age ?? 0) }},
    sexo: '{{ Str::lower($medical_exam->student->gender ?? '') }}',
    peso: '',
    talla: '',
    tablasPercentil: {
        nino: {
            5: { p5: 13.8, p85: 17.4, p95: 18.9 },
            6: { p5: 13.9, p85: 17.8, p95: 19.4 },
            7: { p5: 14.0, p85: 18.2, p95: 20.0 },
            8: { p5: 14.2, p85: 18.8, p95: 20.8 },
            9: { p5: 14.4, p85: 19.4, p95: 21.7 },
            10: { p5: 14.6, p85: 20.1, p95: 22.8 },
            11: { p5: 14.9, p85: 20.9, p95: 24.0 },
            12: { p5: 15.2, p85: 21.8, p95: 25.3 },
            13: { p5: 15.6, p85: 22.6, p95: 26.4 },
            14: { p5: 16.0, p85: 23.3, p95: 27.2 },
            15: { p5: 16.4, p85: 23.8, p95: 27.8 },
            16: { p5: 16.8, p85: 24.2, p95: 28.2 },
            17: { p5: 17.1, p85: 24.6, p95: 28.6 },
            18: { p5: 17.4, p85: 25.0, p95: 29.0 }
        },
        nina: {
            5: { p5: 13.6, p85: 17.1, p95: 18.6 },
            6: { p5: 13.7, p85: 17.6, p95: 19.2 },
            7: { p5: 13.9, p85: 18.3, p95: 20.0 },
            8: { p5: 14.1, p85: 19.0, p95: 21.0 },
            9: { p5: 14.4, p85: 19.8, p95: 22.1 },
            10: { p5: 14.8, p85: 20.7, p95: 23.2 },
            11: { p5: 15.2, p85: 21.6, p95: 24.4 },
            12: { p5: 15.7, p85: 22.5, p95: 25.5 },
            13: { p5: 16.1, p85: 23.2, p95: 26.2 },
            14: { p5: 16.4, p85: 23.8, p95: 26.8 },
            15: { p5: 16.6, p85: 24.2, p95: 27.1 },
            16: { p5: 16.8, p85: 24.5, p95: 27.4 },
            17: { p5: 16.9, p85: 24.7, p95: 27.6 },
            18: { p5: 17.0, p85: 24.9, p95: 27.8 }
        }
    },
    get imc() {
        if (!this.peso || !this.talla) return 0;
        let t = this.talla / 100;
        return (this.peso / (t * t)).toFixed(2);
    },
    get sexoNormalizado() {
        if (this.sexo.includes('f') || this.sexo.includes('mujer')) return 'nina';
        return 'nino';
    },
    interpolar(x1, y1, x2, y2, x) {
        if (x2 === x1) return y1;
        return y1 + ((x - x1) * (y2 - y1)) / (x2 - x1);
    },
    get puntosCorte() {
        const edad = Number(this.edad);
        const tabla = this.tablasPercentil[this.sexoNormalizado];
        if (!edad || !tabla) return null;

        const minEdad = 5;
        const maxEdad = 18;
        const edadAjustada = Math.min(Math.max(edad, minEdad), maxEdad);
        const edadBase = Math.floor(edadAjustada);
        const edadTope = Math.ceil(edadAjustada);

        if (edadBase === edadTope) return tabla[edadBase];

        const a = tabla[edadBase];
        const b = tabla[edadTope];

        return {
            p5: this.interpolar(edadBase, a.p5, edadTope, b.p5, edadAjustada),
            p85: this.interpolar(edadBase, a.p85, edadTope, b.p85, edadAjustada),
            p95: this.interpolar(edadBase, a.p95, edadTope, b.p95, edadAjustada),
        };
    },
    get percentil() {
        const imc = Number(this.imc);
        const cortes = this.puntosCorte;
        if (!imc || !cortes) return 0;

        if (imc < cortes.p5) {
            const p = (imc / cortes.p5) * 5;
            return Math.max(1, Math.round(p));
        }

        if (imc < cortes.p85) {
            const p = 5 + ((imc - cortes.p5) / (cortes.p85 - cortes.p5)) * 80;
            return Math.round(p);
        }

        if (imc < cortes.p95) {
            const p = 85 + ((imc - cortes.p85) / (cortes.p95 - cortes.p85)) * 10;
            return Math.round(p);
        }

        const extra = Math.min(((imc - cortes.p95) / 5) * 4, 4);
        return Math.min(99, Math.round(95 + extra));
    },
    get etiquetaPercentil() {
        if (!this.percentil) return 'PENDIENTE';
        return 'P' + this.percentil;
    },
    get clasificacion() {
        let val = this.percentil;
        if (val == 0) return 'ESPERANDO DATOS';
        if (val < 5) return 'BAJO PESO';
        if (val < 85) return 'NORMAL';
        if (val < 95) return 'SOBREPESO';
        return 'OBESIDAD';
    }
}" class="space-y-8">

    {{-- CONTENEDOR DE EVALUACIÓN --}}
    <div x-show="tab === 'evaluacion'" class="space-y-8">

        <header class="mb-6">
            <h1 class="text-4xl font-black uppercase tracking-tighter text-slate-800">Valoración Médica</h1>
            <p class="text-slate-400 font-medium">Paciente: <span class="text-blue-600 font-bold">{{ $medical_exam->student->name ?? 'No especificado' }}</span></p>
        </header>

        <form action="{{ route('medical_exams.store_evaluation', $medical_exam) }}" method="POST" class="space-y-8">
            @csrf

            {{-- 1. SECCIÓN DE PESO Y TALLA (VISTA 1) --}}
            <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm">
                <h3 class="text-blue-600 font-black uppercase text-[10px] tracking-[0.2em] mb-6 flex items-center gap-3">
                    <span class="w-6 h-1 bg-blue-600 rounded-full"></span> 1. Antropometría e IMC
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-5 gap-6">
                    <div class="space-y-2">
                        <label class="block text-[10px] font-black uppercase text-slate-400 ml-2">Peso (kg)</label>
                        <input type="number" step="0.1" x-model="peso" name="results[peso]" class="w-full border-none bg-slate-50 rounded-2xl p-4 font-bold text-slate-700 focus:ring-2 focus:ring-blue-500/20 shadow-sm" placeholder="0.0">
                    </div>
                    <div class="space-y-2">
                        <label class="block text-[10px] font-black uppercase text-slate-400 ml-2">Talla (cm)</label>
                        <input type="number" x-model="talla" name="results[talla]" class="w-full border-none bg-slate-50 rounded-2xl p-4 font-bold text-slate-700 focus:ring-2 focus:ring-blue-500/20 shadow-sm" placeholder="0">
                    </div>
                    <div class="space-y-2">
                        <label class="block text-[10px] font-black uppercase text-slate-400 ml-2">IMC</label>
                        <div class="w-full bg-blue-50 text-blue-700 rounded-2xl p-4 font-black text-center border border-blue-100" x-text="imc"></div>
                    </div>
                    <div class="space-y-2">
                        <label class="block text-[10px] font-black uppercase text-slate-400 ml-2">Clasificación</label>
                        <div class="w-full rounded-2xl p-4 font-black text-center text-[10px] tracking-widest uppercase border border-slate-100"
                             :class="imc > 0 ? 'bg-slate-900 text-white' : 'bg-white text-slate-300'"
                             x-text="clasificacion"></div>
                    </div>
                    <div class="space-y-2">
                        <label class="block text-[10px] font-black uppercase text-slate-400 ml-2">Percentil</label>
                        <div class="w-full bg-emerald-50 text-emerald-700 rounded-2xl p-4 font-black text-center border border-emerald-100" x-text="etiquetaPercentil"></div>
                    </div>
                </div>

                <input type="hidden" name="results[imc_calculado]" :value="imc">
                <input type="hidden" name="results[imc_clasificacion]" :value="clasificacion">
                <input type="hidden" name="results[imc_percentil]" :value="percentil">
                <input type="hidden" name="results[imc_referencia]" value="percentil_imc_edad_sexo_estimado">
            </div>

            {{-- 2. SECCIÓN DE CUESTIONARIO (VISTA 2) --}}
            <div class="bg-white p-8 md:p-10 rounded-[2.5rem] border border-slate-100 shadow-sm space-y-8">
                <h3 class="text-blue-600 font-black uppercase text-[10px] tracking-[0.2em] mb-6 flex items-center gap-3">
                    <span class="w-6 h-1 bg-blue-600 rounded-full"></span> 2. Antecedentes Médicos
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-12 gap-y-8">
                    {{-- Preguntas Sí/No --}}
                    <div class="space-y-4">
                        @foreach(['hospitalizado' => '¿Ha sido hospitalizado?', 'cirugias' => '¿Ha tenido cirugías?', 'medicamentos' => '¿Toma medicamentos?', 'alergias' => '¿Tiene alergias?'] as $key => $label)
                        <div class="flex items-center justify-between p-4 bg-slate-50/50 rounded-2xl border border-transparent hover:border-slate-100 transition-all group">
                            <span class="text-xs font-bold text-slate-600 group-hover:text-slate-900">{{ $label }}</span>
                            <div class="flex gap-4">
                                <label class="flex items-center gap-1 cursor-pointer">
                                    <input type="radio" name="results[{{ $key }}]" value="0" class="w-3 h-3 text-blue-600 border-slate-300 focus:ring-0">
                                    <span class="text-[9px] font-black uppercase text-slate-400">No</span>
                                </label>
                                <label class="flex items-center gap-1 cursor-pointer">
                                    <input type="radio" name="results[{{ $key }}]" value="1" class="w-3 h-3 text-blue-600 border-slate-300 focus:ring-0">
                                    <span class="text-[9px] font-black uppercase text-slate-400">Sí</span>
                                </label>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    {{-- Checkboxes y Desarrollo --}}
                    <div class="space-y-6">
                        <div class="grid grid-cols-2 gap-3">
                            @foreach(['Asma', 'Diabetes', 'Convulsiones', 'Corazón'] as $item)
                            <label class="flex items-center p-3 bg-white border border-slate-100 rounded-xl cursor-pointer hover:bg-blue-50/50 transition-all group">
                                <input type="checkbox" name="results[antecedentes][]" value="{{ Str::slug($item) }}" class="rounded text-blue-600 border-slate-200 focus:ring-0 w-3 h-3">
                                <span class="ml-2 text-[10px] font-bold text-slate-500 group-hover:text-blue-700">{{ $item }}</span>
                            </label>
                            @endforeach
                        </div>

                        <div class="p-5 bg-blue-50/30 rounded-[2rem] border border-blue-100/50 space-y-4">
                            <select name="results[nacimiento]" class="w-full border-none bg-white rounded-xl text-xs font-bold text-slate-600 p-3 shadow-sm focus:ring-2 focus:ring-blue-500/20">
                                <option value="">¿Tipo de nacimiento?</option>
                                <option value="termino">A término</option>
                                <option value="prematuro">Prematuro</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="pt-4">
                    <label class="block text-[10px] font-black uppercase text-slate-400 tracking-widest ml-4 mb-3">Observaciones Generales</label>
                    <textarea name="notes" rows="3" class="w-full border-none bg-slate-50 rounded-[2rem] p-6 text-xs font-medium focus:ring-2 focus:ring-blue-500/10" placeholder="Escriba detalles adicionales..."></textarea>
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="bg-blue-600 text-white px-12 py-5 rounded-3xl font-black uppercase text-[10px] tracking-widest shadow-2xl shadow-blue-200 hover:scale-105 transition-all">
                    Finalizar Valoración Médica
                </button>
            </div>
        </form>
    </div>

</div>
