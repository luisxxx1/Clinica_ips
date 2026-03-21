<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Evaluación Médica:') }} <span class="text-blue-600">{{ $medical_exam->student->full_name }}</span>
            </h2>
            <span class="px-4 py-1 bg-slate-100 text-slate-600 rounded-full text-xs font-bold uppercase">
                {{ Auth::user()->role->name }}
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- 
                DINAMISMO: 
                Aquí cargamos el formulario que corresponda al rol del usuario.
                Si el usuario es 'Medico', carga 'valoracion_medica.blade.php'.
                Si es 'Psicologo', carga 'psicologia.blade.php'.
            --}}

            @php
                $roleName = Auth::user()->role->name;
                
                $view = match($roleName) {
                    'Medico', 'Medicina General' => 'medical_exams.evaluations.valoracion_medica',
                    'Psicologo', 'Psicología'     => 'medical_exams.evaluations.psicologia',
                    'Odontologo', 'Odontología'   => 'medical_exams.evaluations.odontologia',
                    default => null
                };
            @endphp

            @if($view && view()->exists($view))
                @include($view)
            @else
                <div class="bg-red-50 border-l-4 border-red-400 p-4 rounded-md">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-red-700">
                                No se ha encontrado un formulario de evaluación para tu rol: <strong>{{ $roleName }}</strong>. 
                                Contacta al administrador.
                            </p>
                        </div>
                    </div>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>