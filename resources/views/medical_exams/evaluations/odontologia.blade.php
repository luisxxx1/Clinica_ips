{{--
    NOTA: Se eliminó <x-app-layout> para evitar el error de "ventana doble".
    Este archivo se carga dentro de evaluar.blade.php que ya contiene el Layout.
--}}

<style>
    .odontology-shell {
        position: relative;
        isolation: isolate;
        background:
            radial-gradient(circle at top right, rgba(29, 78, 216, 0.08), transparent 28%),
            radial-gradient(circle at bottom left, rgba(15, 23, 42, 0.06), transparent 30%);
    }

    .tooth-container {
        transition: all 0.25s ease-in-out;
        cursor: pointer;
        border-radius: 1rem;
        padding: 0.25rem;
    }

    .tooth-container:hover {
        transform: translateY(-6px) scale(1.04);
        z-index: 10;
        background: rgba(255, 255, 255, 0.65);
        box-shadow: 0 14px 35px rgba(15, 23, 42, 0.08);
    }

    .tooth-label {
        font-size: 10px;
        font-weight: 900;
        color: #334155;
        letter-spacing: 0.22em;
        text-transform: uppercase;
    }

    #capture-area {
        background:
            linear-gradient(180deg, rgba(248, 250, 252, 0.98), rgba(241, 245, 249, 0.96)),
            radial-gradient(circle at top left, rgba(148, 163, 184, 0.12) 1px, transparent 1px);
        background-size: auto, 20px 20px;
    }

    .legend-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 0.85rem;
        border-radius: 999px;
        border-width: 1px;
        box-shadow: 0 10px 20px rgba(15, 23, 42, 0.05);
    }
</style>

<div class="odontology-shell py-4 md:py-6 bg-transparent" x-data="odontogramaLogic()">
    <div class="max-w-7xl mx-auto px-3 md:px-6">

        {{-- Encabezado Profesional --}}
        <div class="mb-8 app-panel-strong rounded-[2.5rem] p-6 md:p-8 flex flex-col md:flex-row justify-between items-start md:items-end gap-6">
            <div>
                <span class="text-[10px] font-black text-blue-600 uppercase tracking-[0.35em] mb-2 block">Módulo de Salud Oral</span>
                <h2 class="app-display text-4xl md:text-5xl font-black text-slate-900 leading-none">
                    Valoración: <span class="text-blue-600">Odontología</span>
                </h2>
                <p class="mt-3 text-sm text-slate-500 max-w-2xl leading-6">
                    Captura el odontograma como una pieza visual limpia, legible y lista para reporte. Los cambios se guardan en base64 para mantener el flujo consistente con audiometría.
                </p>
                <div class="flex items-center mt-5">
                    <div class="h-11 w-11 rounded-2xl bg-slate-900 text-white flex items-center justify-center font-black text-xs shadow-lg mr-3 uppercase">
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
            <div class="flex flex-wrap gap-3 app-panel bg-white/80 p-4 rounded-[2rem] items-center justify-center md:justify-start">
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
            <div class="app-panel rounded-[3rem] p-3 md:p-6 mb-8 overflow-x-auto">
                <div id="capture-area" class="min-w-[800px] p-8 rounded-[2.25rem] border border-slate-200/80">
                    <div class="space-y-12">
                        <div class="flex justify-center gap-2 md:gap-3">
                            @foreach([55,54,53,52,51,61,62,63,64,65] as $n)
                                <div class="tooth-container">
                                    <x-tooth :number="$n" />
                                    <div class="text-center mt-2 tooth-label">{{ $n }}</div>
                                </div>
                            @endforeach
                        </div>
                        <div class="flex justify-center gap-2 md:gap-3">
                            @foreach([85,84,83,82,81,71,72,73,74,75] as $n)
                                <div class="tooth-container">
                                    <div class="text-center mb-2 tooth-label">{{ $n }}</div>
                                    <x-tooth :number="$n" />
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Botón Explícito de Captura --}}
                <div class="flex justify-center mt-6">
                    <button type="button" id="btn-capture" class="bg-slate-900 hover:bg-blue-600 text-white px-8 py-4 rounded-2xl font-black uppercase text-xs tracking-[0.24em] transition-all shadow-[0_16px_30px_rgba(15,23,42,0.18)]">
                        📸 Capturar Odontograma
                    </button>
                </div>

                {{-- Preview de la Captura --}}
                <div id="capture-preview" style="display:none; margin-top: 20px; text-align: center;" class="app-panel rounded-[2rem] p-4 mt-6">
                    <p class="text-sm font-black text-slate-700 mb-3 uppercase tracking-[0.2em]">Vista previa de lo que se guardará</p>
                    <img id="preview-img" style="max-width: 100%; max-height: 300px; border: 1px solid rgba(148,163,184,0.35); border-radius: 1.25rem; box-shadow: 0 14px 30px rgba(15,23,42,0.08);">
                </div>
            </div>

            {{-- Diagnóstico --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div class="app-panel p-6 rounded-[2rem]">
                    <label class="text-[10px] font-black text-slate-400 uppercase mb-3 block tracking-[0.24em]">Higiene Oral</label>
                    <select name="results[higiene]" class="w-full border-none bg-slate-50 rounded-2xl py-3 px-4 font-bold text-slate-700 shadow-inner focus:ring-4 focus:ring-blue-500/10">
                        <option value="Buena">🟢 Buena Higiene</option>
                        <option value="Regular">🟡 Regular Higiene</option>
                        <option value="Mala">🔴 Mala Higiene</option>
                    </select>
                </div>
                <div class="app-panel p-6 rounded-[2rem]">
                    <label class="text-[10px] font-black text-slate-400 uppercase mb-3 block tracking-[0.24em]">Tejidos Blandos</label>
                    <input type="text" name="results[tejidos_blandos]" class="w-full border-none bg-slate-50 rounded-2xl py-3 px-4 font-bold text-slate-700 shadow-inner focus:ring-4 focus:ring-blue-500/10" placeholder="Describa hallazgos...">
                </div>
            </div>

            <div class="app-panel-strong p-8 rounded-[2.5rem]">
                   <textarea name="notes" id="notes" rows="3" class="w-full bg-white border border-slate-200 rounded-[1.75rem] text-slate-800 p-5 shadow-inner placeholder:text-slate-400" placeholder="Observaciones...">Odontograma capturado y evaluado.</textarea>
                <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4 pt-8">
                    <p class="text-slate-500 text-[9px] font-black uppercase tracking-[0.24em] max-w-xs">Se capturará el estado actual del odontograma.</p>
                    <button type="submit" id="btn-save" class="bg-blue-600 text-white px-10 py-4 rounded-2xl font-black uppercase text-xs hover:bg-slate-900 transition-all shadow-[0_16px_32px_rgba(37,99,235,0.24)]">
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

    let capturedImageBase64 = null;
    let toothStates = {};

// Capturar estado de los dientes desde el SVG renderizado
    function captureToothStates() {
        console.log('🔍 Leyendo colores del SVG renderizado...');

        const toothContainers = document.querySelectorAll('.tooth-container');
        console.log(`Encontrados ${toothContainers.length} contenedores de dientes`);

        const colorHexToName = {
            '#ffffff': 'white',
            'rgb(255, 255, 255)': 'white',
            '#ef4444': 'red',
            'rgb(239, 68, 68)': 'red',
            '#22c55e': 'green',
            'rgb(34, 197, 94)': 'green',
            '#3b82f6': 'blue',
            'rgb(59, 130, 246)': 'blue',
            '#000000': 'black',
            'rgb(0, 0, 0)': 'black',
            '#eab308': 'yellow',
            'rgb(234, 179, 8)': 'yellow'
        };

        toothContainers.forEach(container => {
            const label = container.querySelector('.tooth-label');
            const toothNum = label?.textContent?.trim();

            if (!toothNum) {
                console.log('No se encontró tooth-label');
                return;
            }

            // Buscar todos los elementos SVG (path/rect) dentro del contenedor
            const svgElements = container.querySelectorAll('svg path, svg rect');
            const faces = {
                top: 'white',
                bottom: 'white',
                left: 'white',
                right: 'white',
                center: 'white'
            };

            // Mapear los elementos del SVG a las caras
            svgElements.forEach((el, idx) => {
                const fill = window.getComputedStyle(el).fill;
                const hexFill = fill.toLowerCase();
                const colorName = colorHexToName[hexFill] || 'white';

                // Asumir orden: top, bottom, left, right, center
                const faceNames = ['top', 'bottom', 'left', 'right', 'center'];
                if (faceNames[idx]) {
                    faces[faceNames[idx]] = colorName;
                }

                console.log(`  ${faceNames[idx]}: ${fill} → ${colorName}`);
            });

            toothStates[toothNum] = faces;
            console.log(`✅ Diente ${toothNum}:`, toothStates[toothNum]);
        });

        console.log('📊 Todos los estados capturados:', toothStates);
        console.log('Total de dientes capturados:', Object.keys(toothStates).length);
    }

    // Dibujar odontograma en canvas
    function drawOdontogramOnCanvas() {
        const canvas = document.createElement('canvas');
        canvas.width = 1600;
        canvas.height = 400;
        const ctx = canvas.getContext('2d');

        // Fondo
        ctx.fillStyle = '#f8fafc';
        ctx.fillRect(0, 0, canvas.width, canvas.height);

        const colorMap = {
            'white': '#ffffff',
            'red': '#ef4444',
            'green': '#22c55e',
            'blue': '#3b82f6',
            'black': '#000000',
            'yellow': '#eab308'
        };

        // Dientes superiores e inferiores
        const teethTop = [55, 54, 53, 52, 51, 61, 62, 63, 64, 65];
        const teethBottom = [85, 84, 83, 82, 81, 71, 72, 73, 74, 75];

        function drawTooth(toothNum, x, y) {
            const faces = toothStates[toothNum] || {
                top: 'white', bottom: 'white', left: 'white', right: 'white', center: 'white'
            };

            const size = 60;
            const startX = x - size / 2;
            const startY = y - size / 2;

            // Borde exterior
            ctx.strokeStyle = '#cbd5e1';
            ctx.lineWidth = 2;

            // Cara superior
            ctx.fillStyle = colorMap[faces.top] || '#ffffff';
            ctx.fillRect(startX + 10, startY, 40, 20);
            ctx.strokeRect(startX + 10, startY, 40, 20);

            // Cara inferior
            ctx.fillStyle = colorMap[faces.bottom] || '#ffffff';
            ctx.fillRect(startX + 10, startY + 40, 40, 20);
            ctx.strokeRect(startX + 10, startY + 40, 40, 20);

            // Cara izquierda
            ctx.fillStyle = colorMap[faces.left] || '#ffffff';
            ctx.fillRect(startX, startY + 10, 10, 40);
            ctx.strokeRect(startX, startY + 10, 10, 40);

            // Cara derecha
            ctx.fillStyle = colorMap[faces.right] || '#ffffff';
            ctx.fillRect(startX + 50, startY + 10, 10, 40);
            ctx.strokeRect(startX + 50, startY + 10, 10, 40);

            // Centro
            ctx.fillStyle = colorMap[faces.center] || '#ffffff';
            ctx.fillRect(startX + 15, startY + 15, 30, 30);
            ctx.strokeRect(startX + 15, startY + 15, 30, 30);

            // Número del diente
            ctx.fillStyle = faces.center === 'black' ? '#ffffff' : '#1e293b';
            ctx.font = 'bold 16px Arial';
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';
            ctx.fillText(toothNum, x, y);
        }

        // Dibujar dientes superiores
        let xPos = 80;
        teethTop.forEach(num => {
            drawTooth(num, xPos, 80);
            xPos += 140;
        });

        // Dibujar dientes inferiores
        xPos = 80;
        teethBottom.forEach(num => {
            drawTooth(num, xPos, 280);
            xPos += 140;
        });

        return canvas.toDataURL('image/png');
    }

    // Botón para capturar el odontograma
    document.getElementById('btn-capture').addEventListener('click', function(e) {
        e.preventDefault();
        const btn = document.getElementById('btn-capture');
        const originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = `⏳ Capturando...`;

        setTimeout(() => {
            try {
                // Limpiar estados anteriores
                toothStates = {};

                // Capturar estado actual
                captureToothStates();

                // Generar imagen
                capturedImageBase64 = drawOdontogramOnCanvas();

                // Mostrar vista previa
                document.getElementById('preview-img').src = capturedImageBase64;
                document.getElementById('capture-preview').style.display = 'block';

                // Guardar en input oculto
                document.getElementById('odontograma_imagen').value = capturedImageBase64;

                btn.disabled = false;
                btn.innerHTML = `✅ ${originalText}`;
                btn.classList.remove('bg-purple-600', 'hover:bg-purple-500');
                btn.classList.add('bg-green-600', 'hover:bg-green-500');

                setTimeout(() => {
                    btn.innerHTML = originalText;
                    btn.classList.remove('bg-green-600', 'hover:bg-green-500');
                    btn.classList.add('bg-purple-600', 'hover:bg-purple-500');
                }, 2000);
            } catch (err) {
                console.error('Error capturando odontograma:', err);
                btn.disabled = false;
                btn.innerHTML = `❌ Error en captura`;
                setTimeout(() => {
                    btn.innerHTML = originalText;
                }, 2000);
            }
        }, 150);
    });

    // Formulario: guardar todo
    document.getElementById('form-odontologia').addEventListener('submit', function(e) {
        e.preventDefault();

        if (!capturedImageBase64) {
            alert('⚠️ Debes capturar el odontograma primero antes de guardar.');
            return;
        }

        const btn = document.getElementById('btn-save');
        const form = this;
        btn.disabled = true;
        btn.innerHTML = `PROCESANDO...`;

        console.log('📤 Enviando formulario...');
        console.log('Base64 image size:', capturedImageBase64.length, 'bytes');
        console.log('Input value:', document.getElementById('odontograma_imagen').value.substring(0, 100) + '...');

        // La imagen ya está en el input oculto, simplemente enviamos el form
        form.submit();
    });
</script>
