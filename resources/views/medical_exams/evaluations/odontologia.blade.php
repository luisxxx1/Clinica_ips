<x-app-layout>
    {{-- Estilos para el odontograma --}}
    <style>
        .tooth-container { transition: all 0.3s ease; }
        .tooth-container:hover { transform: scale(1.1); z-index: 50; }
        .tooth-label { font-size: 9px; font-weight: 800; color: #64748b; }
    </style>

    <div class="py-12" x-data="odontogramaLogic()">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="mb-8 flex justify-between items-end">
                <div>
                    <h2 class="text-3xl font-black text-slate-800 tracking-tighter uppercase">
                        Valoración: <span class="text-blue-600">Odontología</span>
                    </h2>
                    <p class="text-slate-500 font-medium italic">{{ $medical_exam->student->full_name }}</p>
                </div>
                {{-- Leyenda de Colores --}}
                <div class="flex gap-3 bg-white p-3 rounded-2xl shadow-sm border border-slate-100">
                    <div class="flex items-center gap-1"><span class="w-3 h-3 bg-red-500 rounded-full"></span> <span class="text-[9px] font-bold uppercase">Caries</span></div>
                    <div class="flex items-center gap-1"><span class="w-3 h-3 bg-blue-500 rounded-full"></span> <span class="text-[9px] font-bold uppercase">Obturado</span></div>
                    <div class="flex items-center gap-1"><span class="w-3 h-3 bg-green-500 rounded-full"></span> <span class="text-[9px] font-bold uppercase">Sellante</span></div>
                    <div class="flex items-center gap-1"><span class="w-3 h-3 bg-slate-800 rounded-full"></span> <span class="text-[9px] font-bold uppercase">Ausente</span></div>
                </div>
            </div>

            <form id="form-odontologia" action="{{ route('medical_exams.store_result', $medical_exam) }}" method="POST">
                @csrf
                {{-- Campo oculto para la imagen del odontograma --}}
                <input type="hidden" name="results[odontograma_captura]" id="odontograma_imagen">

                <div class="bg-white p-8 rounded-[3rem] shadow-sm border border-slate-100 mb-6">
                    {{-- ÁREA DE CAPTURA (Lo que se convertirá en imagen) --}}
                    <div id="capture-area" class="p-4 bg-slate-50 rounded-3xl border border-dashed border-slate-200">
                        <div class="grid grid-cols-1 gap-8">
                            {{-- Arcada Superior --}}
                            <div class="flex justify-center gap-2 flex-wrap">
                                @foreach([18,17,16,15,14,13,12,11,21,22,23,24,25,26,27,28] as $n)
                                    @include('medical_exams.evaluations.partials.tooth', ['n' => $n])
                                @endforeach
                            </div>
                            {{-- Arcada Inferior --}}
                            <div class="flex justify-center gap-2 flex-wrap">
                                @foreach([48,47,46,45,44,43,42,41,31,32,33,34,35,36,37,38] as $n)
                                    @include('medical_exams.evaluations.partials.tooth', ['n' => $n])
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100">
                        <label class="text-[10px] font-black text-slate-400 uppercase mb-3 block tracking-widest">Higiene Oral</label>
                        <select name="results[higiene]" class="w-full border-none bg-slate-50 rounded-2xl focus:ring-2 focus:ring-blue-500 text-sm">
                            <option value="Buena">Buena</option>
                            <option value="Regular">Regular</option>
                            <option value="Mala">Mala</option>
                        </select>
                    </div>
                    <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100">
                        <label class="text-[10px] font-black text-slate-400 uppercase mb-3 block tracking-widest">Tejidos Blandos</label>
                        <input type="text" name="results[tejidos_blandos]" placeholder="Normal / Sano" class="w-full border-none bg-slate-50 rounded-2xl focus:ring-2 focus:ring-blue-500 text-sm">
                    </div>
                </div>

                <div class="bg-slate-900 p-8 rounded-[2.5rem] shadow-xl">
                    <label class="text-[10px] font-black text-blue-400 uppercase mb-4 block tracking-widest">Observaciones y Plan de Tratamiento</label>
                    <textarea name="notes" id="notes" rows="3" required class="w-full bg-slate-800 border-none rounded-2xl text-white text-sm focus:ring-2 focus:ring-blue-500" placeholder="Ej: Requiere profilaxis y resinas en 16 y 24..."></textarea>
                    
                    <div class="flex justify-end pt-8">
                        <button type="submit" id="btn-save" class="bg-blue-600 text-white px-10 py-4 rounded-2xl font-black shadow-lg hover:bg-blue-500 transition-all">
                            GUARDAR EVALUACIÓN ODONTOLÓGICA
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- SCRIPTS --}}
    <script src="https://html2canvas.hertzen.com/dist/html2canvas.min.js"></script>
    <script>
        function odontogramaLogic() {
            return {
                updateTooth(n, face, color) {
                    // Lógica para manejar el estado de cada diente si fuera necesario
                }
            }
        }

        // Lógica de captura de pantalla antes de enviar
        document.getElementById('form-odontologia').addEventListener('submit', function(e) {
            e.preventDefault();
            const btn = document.getElementById('btn-save');
            btn.disabled = true;
            btn.innerHTML = "Procesando Imagen...";

            html2canvas(document.querySelector("#capture-area"), {
                backgroundColor: '#f8fafc',
                scale: 2
            }).then(canvas => {
                document.getElementById('odontograma_imagen').value = canvas.toDataURL('image/png');
                this.submit();
            });
        });
    </script>
</x-app-layout>