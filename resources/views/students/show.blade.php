<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col lg:flex-row lg:justify-between lg:items-center gap-4 no-print">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Ficha Médica Escolar') }}
            </h2>
            @php
                $roleName = mb_strtolower(auth()->user()->role->name ?? '');
                $canDownloadClinicalPdf = in_array($roleName, ['administrador', 'admisión', 'admision']);
            @endphp
            <div class="flex flex-wrap gap-2 sm:gap-3">
                <a href="{{ route('students.index') }}" class="text-xs font-black uppercase tracking-widest text-slate-400 hover:text-slate-900 transition-colors self-center">
                    &larr; Volver
                </a>
                <a href="{{ route('clinical_histories.show', $student) }}" class="bg-blue-600 text-white px-4 sm:px-6 py-2.5 rounded-xl text-xs font-black uppercase tracking-[0.2em] hover:bg-blue-700 transition shadow-lg shadow-blue-200 flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    Historial Clínico
                </a>
                @if($canDownloadClinicalPdf)
                    <a href="{{ route('clinical_histories.pdf', $student) }}" class="bg-emerald-600 text-white px-4 sm:px-6 py-2.5 rounded-xl text-xs font-black uppercase tracking-[0.2em] hover:bg-emerald-700 transition shadow-lg shadow-emerald-200 flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        Descargar Historial PDF
                    </a>
                @endif
                <button onclick="window.print()" class="bg-slate-900 text-white px-4 sm:px-6 py-2.5 rounded-xl text-xs font-black uppercase tracking-[0.2em] hover:bg-blue-600 transition shadow-lg shadow-slate-200 flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    Imprimir Reporte
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-12 bg-slate-50/50 min-h-screen">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-2xl sm:rounded-[2.5rem] border border-slate-200 printable-card">

                {{-- Encabezado Estilo Reporte Corporativo --}}
                <div class="p-6 sm:p-10 border-b border-slate-100 bg-gradient-to-r from-slate-50 to-white flex flex-col md:flex-row md:justify-between md:items-center gap-4">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-blue-600 rounded-2xl flex items-center justify-center mr-4 shadow-lg shadow-blue-200">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2-2z"></path></svg>
                        </div>
                        <div>
                            <h1 class="text-xl font-black text-slate-800 tracking-tight uppercase">Clínica IPS <span class="text-blue-600">Escolar</span></h1>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.3em]">Historia Clínica Estudiantil</p>
                        </div>
                    </div>
                    <div class="text-left md:text-right">
                        <p class="text-[10px] font-black text-slate-300 uppercase tracking-widest mb-1">Generado por</p>
                        <span class="px-3 py-1 bg-slate-900 text-white text-[10px] font-black rounded-lg tracking-widest uppercase">SnakeDev</span>
                    </div>
                </div>

                <div class="p-6 sm:p-10">
                    {{-- SECCIÓN 1: DATOS DEL ESTUDIANTE --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-12 mb-12">
                        <div class="space-y-4">
                            <h4 class="text-[10px] font-black text-blue-600 uppercase tracking-[0.2em] mb-6 flex items-center">
                                <span class="w-2 h-2 rounded-full bg-blue-600 mr-2"></span> Identificación Básica
                            </h4>
                            <div class="space-y-3">
                                <div class="flex flex-col">
                                    <span class="text-[10px] font-bold text-slate-400 uppercase">Nombre del Paciente</span>
                                    <span class="text-base font-bold text-slate-800">{{ $student->first_name }} {{ $student->last_name }}</span>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-[10px] font-bold text-slate-400 uppercase">Documento de Identidad</span>
                                    <span class="text-sm font-semibold text-slate-700">{{ $student->document_type }} — {{ $student->document_number }}</span>
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="flex flex-col">
                                        <span class="text-[10px] font-bold text-slate-400 uppercase">Edad</span>
                                        <span class="text-sm font-semibold text-slate-700">{{ $student->age }} Años</span>
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-[10px] font-bold text-slate-400 uppercase">Género</span>
                                        <span class="text-sm font-semibold text-slate-700">{{ $student->gender }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <h4 class="text-[10px] font-black text-blue-600 uppercase tracking-[0.2em] mb-6 flex items-center">
                                <span class="w-2 h-2 rounded-full bg-blue-600 mr-2"></span> Registro Institucional
                            </h4>
                            <div class="space-y-3">
                                <div class="flex flex-col">
                                    <span class="text-[10px] font-bold text-slate-400 uppercase">Grado Académico</span>
                                    <div>
                                        <span class="inline-block bg-blue-600 text-white px-3 py-0.5 rounded-lg text-xs font-black uppercase tracking-wider">{{ $student->grade }}</span>
                                    </div>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-[10px] font-bold text-slate-400 uppercase">Institución de Procedencia</span>
                                    <span class="text-sm font-semibold text-slate-700">{{ $student->previous_school }}</span>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-[10px] font-bold text-slate-400 uppercase">Fecha de Alta en Sistema</span>
                                    <span class="text-sm font-semibold text-slate-700">{{ $student->created_at->timezone('America/Bogota')->translatedFormat('d F, Y') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- SECCIÓN 2: DATOS DEL ACUDIENTE --}}
                    <div class="bg-slate-900 rounded-[2rem] p-6 sm:p-8 mb-12 shadow-xl shadow-slate-200 relative overflow-hidden">
                        {{-- Decoración sutil --}}
                        <div class="absolute top-0 right-0 p-4 opacity-10 text-white">
                            <svg class="w-24 h-24" fill="currentColor" viewBox="0 0 20 20"><path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"></path></svg>
                        </div>

                        <h4 class="text-[10px] font-black text-blue-400 uppercase mb-6 tracking-[0.3em]">Responsable Legal</h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 relative z-10">
                            <div>
                                <p class="text-[10px] text-slate-500 font-bold uppercase tracking-widest mb-1">Nombre Completo</p>
                                <p class="text-sm text-white font-bold">{{ $student->guardian_name }} {{ $student->guardian_lastname }}</p>
                            </div>
                            <div>
                                <p class="text-[10px] text-slate-500 font-bold uppercase tracking-widest mb-1">Contacto Directo</p>
                                <p class="text-sm text-white font-bold">{{ $student->guardian_phone }}</p>
                            </div>
                            <div>
                                <p class="text-[10px] text-slate-500 font-bold uppercase tracking-widest mb-1">Parentesco</p>
                                <p class="text-sm text-white font-bold uppercase tracking-tighter">{{ $student->guardian_relationship }}</p>
                            </div>
                        </div>
                        <div class="mt-6 pt-6 border-t border-slate-800 grid grid-cols-1 md:grid-cols-2 gap-8 relative z-10">
                            <div>
                                <p class="text-[10px] text-slate-500 font-bold uppercase tracking-widest mb-1">Documento de Identidad</p>
                                <p class="text-sm text-slate-300 font-medium">{{ $student->guardian_document }}</p>
                            </div>
                            <div>
                                <p class="text-[10px] text-slate-500 font-bold uppercase tracking-widest mb-1">Canal de Notificación</p>
                                <p class="text-sm text-slate-300 font-medium">{{ $student->guardian_email }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- Sección de Exámenes --}}
                    <div>
                        <h4 class="text-[10px] font-black text-slate-400 uppercase mb-6 tracking-[0.2em] flex items-center">
                            <span class="w-8 h-[1px] bg-slate-200 mr-3"></span> Historial Clínico Reciente
                        </h4>

                        @if($student->medicalExams && $student->medicalExams->count() > 0)
                            <div class="overflow-hidden rounded-2xl border border-slate-100">
                                <table class="min-w-full divide-y divide-slate-100">
                                    <thead class="bg-slate-50/50">
                                        <tr>
                                            <th class="px-6 py-4 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Fecha Emisión</th>
                                            <th class="px-6 py-4 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Especialidad</th>
                                            <th class="px-6 py-4 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Diagnóstico/Resultado</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-50 bg-white">
                                        @foreach($student->medicalExams as $exam)
                                            <tr class="hover:bg-slate-50 transition-colors">
                                                <td class="px-6 py-4 whitespace-nowrap text-xs font-bold text-slate-800">
                                                    {{ $exam->created_at->timezone('America/Bogota')->format('d/m/Y') }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <span class="text-xs font-black text-blue-600 uppercase">{{ $exam->type }}</span>
                                                </td>
                                                <td class="px-6 py-4 text-xs text-slate-500 italic leading-relaxed">
                                                    {{ $exam->result }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="bg-slate-50 rounded-2xl p-8 text-center border-2 border-dashed border-slate-200">
                                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">No se registran valoraciones médicas a la fecha</p>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Pie de Página Reporte --}}
                <div class="p-6 sm:p-10 bg-slate-50 border-t border-slate-100 flex flex-col md:flex-row md:justify-between md:items-center gap-3 no-print">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                        Documento oficial emitido por el sistema Snake_DEV IPS
                    </p>
                    <a href="{{ route('students.edit', $student) }}" class="text-xs font-black text-amber-500 uppercase tracking-widest hover:text-amber-600">
                        Editar Información &rarr;
                    </a>
                </div>
            </div>
        </div>
    </div>

    <style>
        @media print {
            .no-print { display: none !important; }
            .py-12 { padding: 0 !important; }
            .shadow-2xl, .shadow-xl { box-shadow: none !important; }
            .sm\:rounded-\[2\.5rem\], .rounded-\[2rem\] { border-radius: 0 !important; }
            .printable-card { border: none !important; }
            body { background: white !important; }
            .bg-slate-900 { background-color: #0f172a !important; -webkit-print-color-adjust: exact; }
            .text-white { color: white !important; -webkit-print-color-adjust: exact; }
            .bg-blue-600 { background-color: #2563eb !important; -webkit-print-color-adjust: exact; }
        }
    </style>
</x-app-layout>
