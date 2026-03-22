{{-- resources/views/medical_exams/evaluations/valoracion_medica.blade.php --}}
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medicina General - Clinica_ips</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700;900&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-slate-50 text-slate-900">

<div x-data="{ tab: 'bandeja' }" class="flex min-h-screen">
    
    {{-- SIDEBAR PERSONALIZADO (SIN GRÁFICAS) --}}
    <aside class="w-64 bg-white border-r border-slate-200 flex flex-col p-6 sticky top-0 h-screen">
        <div class="mb-10 flex items-center gap-2">
            <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center text-white shadow-lg">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            </div>
            <span class="font-black text-xl tracking-tighter">SnakeDEV</span>
        </div>

        <nav class="space-y-3 flex-1">
            <button @click="tab = 'bandeja'" 
                    :class="tab === 'bandeja' ? 'bg-blue-600 text-white shadow-xl shadow-blue-200' : 'text-slate-400 hover:bg-slate-50'"
                    class="w-full flex items-center gap-3 p-4 rounded-2xl font-bold transition-all text-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                Bandeja de Entrada
            </button>

            <button @click="tab = 'historial'" 
                    :class="tab === 'historial' ? 'bg-blue-600 text-white shadow-xl shadow-blue-200' : 'text-slate-400 hover:bg-slate-50'"
                    class="w-full flex items-center gap-3 p-4 rounded-2xl font-bold transition-all text-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                Buscar Historial
            </button>
        </nav>

        <div class="mt-auto p-4 bg-slate-900 rounded-3xl text-white">
            <p class="text-[10px] font-bold text-blue-400 uppercase">Luis Jimenez</p>
            <p class="text-[9px] text-slate-500 font-bold uppercase tracking-widest leading-none">CEO Medicina General</p>
        </div>
    </aside>

    {{-- CONTENIDO PRINCIPAL --}}
    <main class="flex-1 p-12">
        
        {{-- SECCIÓN: BANDEJA --}}
        <div x-show="tab === 'bandeja'">
            <header class="mb-10">
                <h1 class="text-4xl font-black uppercase tracking-tighter">Bandeja de Pacientes</h1>
                <p class="text-slate-400 font-medium">Lista de espera para valoración médica.</p>
            </header>

            <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 overflow-hidden">
                {{-- Aquí pones tu tabla de pacientes pendientes --}}
                <div class="p-10 text-center border-2 border-dashed border-slate-100 m-6 rounded-[2rem]">
                    <p class="text-slate-300 font-bold italic">Cargando pacientes de Clinica_ips...</p>
                </div>
            </div>
        </div>

        {{-- SECCIÓN: HISTORIAL --}}
        <div x-show="tab === 'historial'" x-cloak>
            <header class="mb-10 flex items-end justify-between">
                <div>
                    <h1 class="text-4xl font-black uppercase tracking-tighter">Historial</h1>
                    <p class="text-slate-400 font-medium">Consulta de registros anteriores.</p>
                </div>
                <div class="relative">
                    <input type="text" placeholder="Buscar por DNI..." class="bg-white border border-slate-200 rounded-2xl py-3 px-5 pl-12 font-bold focus:ring-2 focus:ring-blue-600 outline-none">
                    <svg class="w-5 h-5 absolute left-4 top-3.5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
            </header>

            <div class="bg-white rounded-[2.5rem] p-12 shadow-sm border border-slate-100 text-center">
                <p class="text-slate-300 font-bold italic">No se han realizado búsquedas aún.</p>
            </div>
        </div>

    </main>
</div>

</body>
</html>