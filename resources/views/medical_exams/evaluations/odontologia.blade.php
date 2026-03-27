{{--
    NOTA: Se eliminó <x-app-layout> para evitar el error de "ventana doble".
    Este archivo se carga dentro de evaluar.blade.php que ya contiene el Layout.
--}}

<style>
    .tooth-container { transition: all 0.2s ease-in-out; cursor: pointer; }
    .tooth-container:hover { transform: translateY(-4px) scale(1.05); z-index: 10; }
    .tooth-label { font-size: 10px; font-weight: 900; color: #475569; letter-spacing: -0.02em; }
    #capture-area { background-color: #f8fafc; background-image: radial-gradient(#e2e8f0 1px, transparent 1px); background-size: 20px 20px; }
    .legend-item { display: flex; align-items: center; gap: 0.5rem; padding: 0.375rem 0.75rem; border-radius: 0.75rem; border-width: 1px; }
</style>

<div class="py-2 bg-transparent" x-data="odontogramaLogic()">
    <div class="max-w-7xl mx-auto">

        {{-- Encabezado Profesional --}}
        <div class="mb-10 flex flex-col md:flex-row justify-between items-start md:items-end gap-6">
            <div>
                <span class="text-[10px] font-black text-blue-500 uppercase tracking-[0.3em] mb-2 block">Módulo de Salud Oral</span>
                <h2 class="text-4xl font-black text-slate-900 tracking-tighter uppercase leading-none">
                    Valoración: <span class="text-blue-600">Odontología</span>
                </h2>
                <div class="flex items-center mt-4">
                    <div class="h-10 w-10 rounded-xl bg-slate-900 text-white flex items-center justify-center font-black text-xs shadow-lg mr-3 uppercase">
                        @php
                            $p_nombre = $exam->student->name ?? $exam->student->first_name ?? 'P';
                            $p_apellido = $exam->student->last_name ?? '';
                            echo substr($p_nombre, 0, 1) . ($p_apellido ? substr($p_apellido, 0, 1) : '');
                        @endphp
                    </div>
                    <p class="text-slate-500 font-bold uppercase text-sm tracking-tight">
                        Paciente: <span class="text-slate-800">{{ $exam->student->name ?? ($exam->student->first_name . ' ' . $exam->student->last_name) }}</span>
                        <span class="text-blue-600 ml-2">| CC: {{ $exam->student->document_number }}</span>
                    </p>
                </div>
            </div>

            {{-- Leyenda de Colores --}}
            <div class="flex flex-wrap gap-3 bg-white p-4 rounded-3xl shadow-sm border border-slate-100 items-center justify-center md:justify-start">
                <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest mr-2 w-full md:w-auto text-center">Convenciones:</span>
                <div class="legend-item bg-red-50 border-red-100"><span class="w-2.5 h-2.5 bg-red-500 rounded-full"></span><span class="text-[10px] font-black text-red-700 uppercase">Caries</span></div>
                <div class="legend-item bg-green-50 border-green-100"><span class="w-2.5 h-2.5 bg-green-500 rounded-full"></span><span class="text-[10px] font-black text-green-700 uppercase">Sellante</span></div>
                <div class="legend-item bg-blue-50 border-blue-100"><span class="w-2.5 h-2.5 bg-blue-500 rounded-full"></span><span class="text-[10px] font-black text-blue-700 uppercase">Restauración</span></div>
                <div class="legend-item bg-slate-100 border-slate-200"><span class="w-2.5 h-2.5 bg-black rounded-full"></span><span class="text-[10px] font-black text-slate-700 uppercase">Ausente</span></div>
            </div>
        </div>

        <form id="form-odontologia" action="{{ route('medical_exams.store_evaluation', $exam) }}" method="POST">
            @csrf
            {{-- Importante: El nombre dentro de results[] para el controlador --}}
            <input type="hidden" name="results[odontograma_path]" id="odontograma_imagen">

            {{-- Área del Odontograma --}}
            <div class="bg-white p-2 md:p-6 rounded-[3rem] shadow-sm border border-slate-100 mb-8 overflow-x-auto">
                <div id="capture-area" class="min-w-[800px] p-8 bg-slate-50 rounded-[2rem] border-2 border-dashed border-slate-200">
                    <div class="space-y-12">
                        <div class="flex justify-center gap-2">
                            @foreach([55,54,53,52,51,61,62,63,64,65] as $n)
                                <div class="tooth-container">
                                    <x-tooth :number="$n" />
                                    <div class="text-center mt-2 tooth-label">{{ $n }}</div>
                                </div>
                            @endforeach
                        </div>
                        <div class="flex justify-center gap-2">
                            @foreach([85,84,83,82,81,71,72,73,74,75] as $n)
                                <div class="tooth-container">
                                    <div class="text-center mb-2 tooth-label">{{ $n }}</div>
                                    <x-tooth :number="$n" />
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            {{-- Diagnóstico --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-slate-100">
                    <label class="text-[10px] font-black text-slate-400 uppercase mb-3 block tracking-[0.2em]">Higiene Oral</label>
                    <select name="results[higiene]" class="w-full border-none bg-slate-50 rounded-xl py-3 font-bold text-slate-700">
                        <option value="Buena">🟢 Buena Higiene</option>
                        <option value="Regular">🟡 Regular Higiene</option>
                        <option value="Mala">🔴 Mala Higiene</option>
                    </select>
                </div>
                <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-slate-100">
                    <label class="text-[10px] font-black text-slate-400 uppercase mb-3 block tracking-[0.2em]">Tejidos Blandos</label>
                    <input type="text" name="results[tejidos_blandos]" class="w-full border-none bg-slate-50 rounded-xl py-3 font-bold text-slate-700" placeholder="Describa hallazgos...">
                </div>
            </div>

            <div class="bg-slate-900 p-8 rounded-[2.5rem] shadow-xl">
                <textarea name="notes" id="notes" rows="3" required class="w-full bg-slate-800/50 border-none rounded-2xl text-white p-5" placeholder="Observaciones..."></textarea>
                <div class="flex justify-between items-center pt-8">
                    <p class="text-slate-400 text-[9px] font-bold uppercase tracking-widest max-w-xs">Se capturará el estado actual del odontograma.</p>
                    <button type="submit" id="btn-save" class="bg-blue-600 text-white px-10 py-4 rounded-xl font-black uppercase text-xs hover:bg-blue-500 transition-all">
                        Finalizar Registro
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script src="https://html2canvas.hertzen.com/dist/html2canvas.min.js"></script>
<script>
    function odontogramaLogic() { return { } }

    document.getElementById('form-odontologia').addEventListener('submit', function(e) {
        e.preventDefault();
        const btn = document.getElementById('btn-save');
        const form = this;
        btn.disabled = true;
        btn.innerHTML = `PROCESANDO...`;

        html2canvas(document.querySelector("#capture-area"), {
            backgroundColor: '#f8fafc',
            scale: 2,
            useCORS: true
        }).then(canvas => {
            document.getElementById('odontograma_imagen').value = canvas.toDataURL('image/png');
            form.submit();
        });
    });
</script>