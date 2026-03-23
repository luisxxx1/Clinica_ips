{{-- resources/views/medical_exams/evaluations/valoracion_medica.blade.php --}}

<div x-data="{ 
    tab: 'evaluacion',
    peso: '', 
    talla: '',
    get imc() {
        if (!this.peso || !this.talla) return 0;
        let t = this.talla / 100;
        return (this.peso / (t * t)).toFixed(2);
    },
    get clasificacion() {
        let val = this.imc;
        if (val == 0) return 'ESPERANDO DATOS';
        if (val < 18.5) return 'BAJO PESO';
        if (val < 25) return 'NORMAL';
        if (val < 30) return 'SOBREPESO';
        return 'OBESIDAD';
    }
}" class="space-y-8">

    {{-- CONTENEDOR DE EVALUACIÓN --}}
    <div x-show="tab === 'evaluacion'" class="space-y-8">
        
        <header class="mb-6">
            <h1 class="text-4xl font-black uppercase tracking-tighter text-slate-800">Valoración Médica</h1>
            <p class="text-slate-400 font-medium">Paciente: <span class="text-blue-600 font-bold">{{ $medical_exam->student->name ?? 'No especificado' }}</span></p>
        </header>

        <form action="#" method="POST" class="space-y-8">
            @csrf

            {{-- 1. SECCIÓN DE PESO Y TALLA (VISTA 1) --}}
            <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm">
                <h3 class="text-blue-600 font-black uppercase text-[10px] tracking-[0.2em] mb-6 flex items-center gap-3">
                    <span class="w-6 h-1 bg-blue-600 rounded-full"></span> 1. Antropometría e IMC
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div class="space-y-2">
                        <label class="block text-[10px] font-black uppercase text-slate-400 ml-2">Peso (kg)</label>
                        <input type="number" step="0.1" x-model="peso" name="peso" class="w-full border-none bg-slate-50 rounded-2xl p-4 font-bold text-slate-700 focus:ring-2 focus:ring-blue-500/20 shadow-sm" placeholder="0.0">
                    </div>
                    <div class="space-y-2">
                        <label class="block text-[10px] font-black uppercase text-slate-400 ml-2">Talla (cm)</label>
                        <input type="number" x-model="talla" name="talla" class="w-full border-none bg-slate-50 rounded-2xl p-4 font-bold text-slate-700 focus:ring-2 focus:ring-blue-500/20 shadow-sm" placeholder="0">
                    </div>
                    <div class="space-y-2">
                        <label class="block text-[10px] font-black uppercase text-slate-400 ml-2">IMC Calculado</label>
                        <div class="w-full bg-blue-50 text-blue-700 rounded-2xl p-4 font-black text-center border border-blue-100" x-text="imc"></div>
                    </div>
                    <div class="space-y-2">
                        <label class="block text-[10px] font-black uppercase text-slate-400 ml-2">Clasificación</label>
                        <div class="w-full rounded-2xl p-4 font-black text-center text-[10px] tracking-widest uppercase border border-slate-100" 
                             :class="imc > 0 ? 'bg-slate-900 text-white' : 'bg-white text-slate-300'"
                             x-text="clasificacion"></div>
                    </div>
                </div>
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
                                    <input type="radio" name="{{ $key }}" value="0" class="w-3 h-3 text-blue-600 border-slate-300 focus:ring-0">
                                    <span class="text-[9px] font-black uppercase text-slate-400">No</span>
                                </label>
                                <label class="flex items-center gap-1 cursor-pointer">
                                    <input type="radio" name="{{ $key }}" value="1" class="w-3 h-3 text-blue-600 border-slate-300 focus:ring-0">
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
                                <input type="checkbox" name="antecedentes[]" value="{{ Str::slug($item) }}" class="rounded text-blue-600 border-slate-200 focus:ring-0 w-3 h-3">
                                <span class="ml-2 text-[10px] font-bold text-slate-500 group-hover:text-blue-700">{{ $item }}</span>
                            </label>
                            @endforeach
                        </div>
                        
                        <div class="p-5 bg-blue-50/30 rounded-[2rem] border border-blue-100/50 space-y-4">
                            <select name="nacimiento" class="w-full border-none bg-white rounded-xl text-xs font-bold text-slate-600 p-3 shadow-sm focus:ring-2 focus:ring-blue-500/20">
                                <option value="">¿Tipo de nacimiento?</option>
                                <option value="termino">A término</option>
                                <option value="prematuro">Prematuro</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="pt-4">
                    <label class="block text-[10px] font-black uppercase text-slate-400 tracking-widest ml-4 mb-3">Observaciones Generales</label>
                    <textarea name="detalles" rows="3" class="w-full border-none bg-slate-50 rounded-[2rem] p-6 text-xs font-medium focus:ring-2 focus:ring-blue-500/10" placeholder="Escriba detalles adicionales..."></textarea>
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