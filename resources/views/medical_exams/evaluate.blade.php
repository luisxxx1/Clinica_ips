<x-app-layout>
    <div class="min-h-screen bg-slate-50/50">
        <header class="bg-white border-b border-slate-100 p-6 flex items-center justify-between sticky top-0 z-40">
            <h1 class="text-sm font-black text-slate-400 uppercase tracking-widest">Módulo de Evaluación</h1>
            <div class="flex items-center gap-4">
                <span class="text-[10px] font-bold bg-slate-100 text-slate-500 px-3 py-1 rounded-full uppercase">Estética SnakeDEV</span>
                <img src="https://i.ibb.co/XfRzV8R/logo-ips.png" class="h-10 w-auto" alt="Logo IPS">
            </div>
        </header>

        <div class="p-6 md:p-12">
            <div class="max-w-5xl mx-auto">
                @php
                    $role = Auth::user()->role->name;
                    
                    /** * PRIORIDAD: Usamos $userArea si viene del controlador (más seguro), 
                     * de lo contrario, aplicamos el match de respaldo.
                     */
                    $area = $userArea ?? Str::slug($role, '_');
                    
                    $view = match(true) {
                        str_contains($area, 'medica') || str_contains($area, 'medico') 
                            => 'medical_exams.evaluations.valoracion_medica',
                        
                        str_contains($area, 'psico') 
                            => 'medical_exams.evaluations.psicologia',
                        
                        str_contains($area, 'fono') 
                            => 'medical_exams.evaluations.fonoaudiologia',
                        
                        str_contains($area, 'opto') 
                            => 'medical_exams.evaluations.optometria',
                        
                        str_contains($area, 'audio') 
                            => 'medical_exams.evaluations.audiometria',
                        
                        str_contains($area, 'odonto') 
                            => 'medical_exams.evaluations.odontologia',
                        
                        default => "medical_exams.evaluations.{$area}"
                    };
                @endphp

                @if(view()->exists($view))
                    {{-- Contenedor principal con bordes suavizados SnakeDEV --}}
                    <div class="bg-white p-8 md:p-12 rounded-[3rem] shadow-sm border border-slate-100">
                        {{-- 
                            IMPORTANTE: El @include debe pasar explícitamente el objeto 
                            para que el formulario sepa a qué ID enviar el POST.
                        --}}
                        @include($view, [
                            'medical_exam' => $medical_exam,
                            'area' => $area
                        ])
                    </div>
                @else
                    <div class="bg-white p-12 rounded-[3.5rem] shadow-sm border border-slate-100 text-center">
                        <div class="w-20 h-20 bg-red-50 text-red-500 rounded-3xl flex items-center justify-center mx-auto mb-6">
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <h3 class="text-2xl font-black text-slate-800 uppercase tracking-tighter">Vista no encontrada</h3>
                        <p class="text-slate-500 font-medium">
                            No existe un formulario para el área: <span class="text-red-600 font-bold">"{{ $area }}"</span>.
                            <br>
                            <span class="text-xs text-slate-400">Ruta intentada: resources/views/{{ str_replace('.', '/', $view) }}.blade.php</span>
                        </p>
                        <div class="mt-8">
                            <a href="{{ route('medical_exams.index') }}" class="text-xs font-black text-slate-400 uppercase tracking-widest hover:text-slate-900 transition-colors">← Volver a la bandeja</a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>