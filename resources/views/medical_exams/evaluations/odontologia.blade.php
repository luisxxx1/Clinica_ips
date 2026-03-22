<x-app-layout>
    {{-- Estilos Refinados para Odontología SnakeDEV --}}
    <style>
        .tooth-container { transition: all 0.2s ease-in-out; cursor: pointer; }
        .tooth-container:hover { transform: translateY(-4px) scale(1.05); z-index: 10; }
        .tooth-label { font-size: 10px; font-weight: 900; color: #475569; letter-spacing: -0.02em; }
        #capture-area { background-image: radial-gradient(#e2e8f0 1px, transparent 1px); background-size: 20px 20px; }
    </style>

    <div class="py-12 bg-slate-50/50" x-data="odontogramaLogic()">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Encabezado Profesional --}}
            <div class="mb-10 flex flex-col md:flex-row justify-between items-start md:items-end gap-6">
                <div>
                    <span class="text-[10px] font-black text-blue-500 uppercase tracking-[0.3em] mb-2 block">Módulo de Salud Oral</span>
                    <h2 class="text-4xl font-black text-slate-900 tracking-tighter uppercase leading-none">
                        Valoración: <span class="text-blue-600">Odontología</span>
                    </h2>
                    <p class="text-slate-500 font-bold mt-3 uppercase text-sm tracking-tight">
                        Paciente: <span class="text-slate-800">{{ $exam->student->name }}</span>
                    </p>
                </div>

                {{-- Leyenda de Colores Estilizada --}}
                <div class="flex gap-4 bg-white p-4 rounded-3xl shadow-sm border border-slate-100 items-center">
                    <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest mr-2">Convenciones:</span>
                    <div class="flex items-center gap-2 px-3 py-1.5 bg-red-50 rounded-xl border border-red-100">
                        <span class="w-2.5 h-2.5 bg-red-500 rounded-full animate-pulse"></span> 
                        <span class="text-[10px] font-black text-red-700 uppercase">Caries</span>
                    </div>
                    <div class="flex items-center gap-2 px-3 py-1.5 bg-blue-50 rounded-xl border border-blue-100">
                        <span class="w-2.5 h-2.5 bg-blue-500 rounded-full"></span> 
                        <span class="text-[10px] font-black text-blue-700 uppercase">Obturado</span>
                    </div>
                    <div class="flex items-center gap-2 px-3 py-1.5 bg-green-50 rounded-xl border border-green-100">
                        <span class="w-2.5 h-2.5 bg-green-500 rounded-full"></span> 
                        <span class="text-[10px] font-black text-green-700 uppercase">Sellante</span>
                    </div>
                </div>
            </div>

            <form id="form-odontologia" action="{{ route('medical_exams.store_evaluation', $exam) }}" method="POST">
                @csrf
                {{-- Campo oculto para la imagen base64 del odontograma --}}
                <input type="hidden" name="results[odontograma_captura]" id="odontograma_imagen">

                {{-- Área del Odontograma --}}
                <div class="bg-white p-2 md:p-10 rounded-[3.5rem] shadow-sm border border-slate-100 mb-8 overflow-x-auto">
                    <div id="capture-area" class="min-w-[800px] p-10 bg-slate-50 rounded-[2.5rem] border-2 border-dashed border-slate-200">
                        <div class="space-y-16">
                            {{-- Arcada Superior --}}
                            <div class="flex justify-center gap-3">
                                @foreach([18,17,16,15,14,13,12,11,21,22,23,24,25,26,27,28] as $n)
                                    <div class="tooth-container">
                                        @include('medical_exams.evaluations.partials.tooth', ['n' => $n])
                                        <div class="text-center mt-2 tooth-label">{{ $n }}</div>
                                    </div>
                                @endforeach
                            </div>

                            {{-- Arcada Inferior --}}
                            <div class="flex justify-center gap-3">
                                @foreach([48,47,46,45,44,43,42,41,31,32,33,34,35,36,37,38] as $n)
                                    <div class="tooth-container">
                                        <div class="text-center mb-2 tooth-label">{{ $n }}</div>
                                        @include('medical_exams.evaluations.partials.tooth', ['n' => $n])
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Campos de Diagnóstico --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                    <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-100 group transition-all hover:border-blue-200">
                        <label class="text-[10px] font-black text-slate-400 uppercase mb-4 block tracking-[0.2em] group-focus-within:text-blue-500">Higiene Oral</label>
                        <select name="results[higiene]" class="w-full border-none bg-slate-50 rounded-2xl focus:ring-4 focus:ring-blue-500/10 py-4 font-bold text-slate-700">
                            <option value="Buena">🟢 Buena Higiene</option>
                            <option value="Regular">🟡 Regular Higiene</option>
                            <option value="Mala">🔴 Mala Higiene</option>
                        </select>
                    </div>

                    <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-100 group transition-all hover:border-blue-200">
                        <label class="text-[10px] font-black text-slate-400 uppercase mb-4 block tracking-[0.2em] group-focus-within:text-blue-500">Tejidos Blandos</label>
                        <input type="text" name="results[tejidos_blandos]" 
                               class="w-full border-none bg-slate-50 rounded-2xl focus:ring-4 focus:ring-blue-500/10 py-4 font-bold text-slate-700 placeholder:text-slate-300"
                               placeholder="Describa hallazgos en encías, lengua o mucosa...">
                    </div>
                </div>

                {{-- Cierre y Guardado --}}
                <div class="bg-slate-900 p-10 rounded-[3rem] shadow-2xl shadow-blue-900/10">
                    <div class="flex items-center mb-6">
                        <div class="w-1.5 h-8 bg-blue-500 rounded-full mr-4"></div>
                        <label class="text-xs font-black text-blue-400 uppercase tracking-widest">Observaciones y Plan de Tratamiento</label>
                    </div>

                    <textarea name="notes" id="notes" rows="4" required 
                              class="w-full bg-slate-800/50 border-none rounded-3xl text-white text-base focus:ring-4 focus:ring-blue-500/20 p-6 placeholder:text-slate-500 transition-all" 
                              placeholder="Indique detalladamente el tratamiento requerido (ej: profilaxis, sellantes, resinas)..."></textarea>
                    
                    <div class="flex flex-col md:flex-row justify-between items-center pt-10 gap-6">
                        <p class="text-slate-400 text-[10px] font-bold uppercase tracking-widest max-w-xs text-center md:text-left leading-relaxed">
                            Al guardar, el sistema generará una captura visual del odontograma para el reporte PDF.
                        </p>
                        <button type="submit" id="btn-save" 
                                class="w-full md:w-auto bg-blue-600 text-white px-14 py-5 rounded-2xl font-black shadow-xl shadow-blue-900/30 hover:bg-blue-500 hover:-translate-y-1 active:scale-95 transition-all uppercase tracking-tighter text-sm">
                            Finalizar Registro Odontológico
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- SCRIPTS DE CAPTURA --}}
    <script src="https://html2canvas.hertzen.com/dist/html2canvas.min.js"></script>
    <script>
        function odontogramaLogic() {
            return {
                // Aquí podrías añadir interactividad extra con AlpineJS si la necesitas
            }
        }

        document.getElementById('form-odontologia').addEventListener('submit', function(e) {
            e.preventDefault();
            const btn = document.getElementById('btn-save');
            const originalText = btn.innerHTML;
            
            btn.disabled = true;
            btn.classList.add('opacity-75');
            btn.innerHTML = `<span class="flex items-center tracking-normal italic"><svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> PROCESANDO ODONTOGRAMA...</span>`;

            // Capturamos el área del odontograma
            html2canvas(document.querySelector("#capture-area"), {
                backgroundColor: '#f8fafc',
                scale: 2, // Mayor calidad para el PDF
                logging: false,
                useCORS: true
            }).then(canvas => {
                document.getElementById('odontograma_imagen').value = canvas.toDataURL('image/png');
                this.submit();
            }).catch(error => {
                console.error('Error al capturar:', error);
                btn.disabled = false;
                btn.innerHTML = originalText;
            });
        });
    </script>
</x-app-layout>