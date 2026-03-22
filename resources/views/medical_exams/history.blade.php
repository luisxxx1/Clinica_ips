<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Encabezado de la Sección --}}
            <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-bold text-slate-800">Historial de Pacientes</h2>
                    <p class="text-slate-500 text-sm">Consulta de estados médicos y generación de Historias Clínicas unificadas.</p>
                </div>

                <div class="flex items-center gap-4">
                    <form action="{{ route('medical_exams.history') }}" method="GET" class="relative group">
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Buscar por nombre o documento..."
                            class="pl-10 pr-4 py-2 bg-white border border-slate-200 rounded-xl text-sm focus:ring-blue-500 focus:border-blue-500 w-64 transition-all shadow-sm group-hover:border-slate-300">
                        <div class="absolute left-3 top-2.5 text-slate-400 group-hover:text-blue-500 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                    </form>

                    <div class="bg-white px-4 py-2 rounded-xl shadow-sm border border-slate-100 flex items-center gap-3">
                        <div class="p-2 bg-green-100 rounded-lg">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-[10px] uppercase font-bold text-slate-400 leading-none">Registros</p>
                            <p class="text-lg font-bold text-slate-700 leading-tight">{{ $completedExams->total() }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tabla de Registros --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-slate-100">
                @if($completedExams->isEmpty())
                    <div class="p-20 flex flex-col items-center justify-center text-center">
                        <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mb-4 border border-slate-100">
                            <svg class="w-10 h-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="text-slate-600 font-bold text-lg">No se encontraron registros</h3>
                        <p class="text-slate-400 text-sm max-w-xs mx-auto">Aún no hay pacientes en el sistema o no coinciden con la búsqueda.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-50/50 border-b border-slate-100">
                                    <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-center w-16">#</th>
                                    <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Paciente</th>
                                    <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">Estado Clínica</th>
                                    <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">Fecha de Atención</th>
                                    <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50">
                                @foreach($completedExams as $exam)
                                    <tr class="hover:bg-slate-50/50 transition-colors group">
                                        <td class="px-6 py-4 text-center text-xs font-bold text-slate-400">
                                            {{ $loop->iteration + ($completedExams->currentPage() - 1) * $completedExams->perPage() }}
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center">
                                                <div class="h-10 w-10 rounded-full bg-slate-900 text-white flex items-center justify-center font-bold text-sm shadow-sm mr-4 uppercase">
                                                    {{ substr($exam->student->first_name, 0, 1) }}{{ substr($exam->student->last_name, 0, 1) }}
                                                </div>
                                                <div>
                                                    <span class="text-sm font-bold text-slate-800 block">{{ $exam->student->full_name }}</span>
                                                    <span class="text-[10px] text-slate-500 font-bold px-2 py-0.5 bg-slate-100 rounded border border-slate-200 uppercase">
                                                        {{ $exam->student->document_type }}: {{ $exam->student->document_number }}
                                                    </span>
                                                </div>
                                            </div>
                                        </td>

                                        {{-- NUEVA COLUMNA DE ESTADO --}}
                                        <td class="px-6 py-4 text-center">
                                            @if($exam->is_ready) {{-- Usando el atributo que definimos en el Modelo --}}
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-[9px] font-black bg-green-100 text-green-700 border border-green-200 uppercase tracking-widest">
                                                    ● Completado
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-[9px] font-black bg-amber-50 text-amber-600 border border-amber-100 uppercase tracking-widest">
                                                    ○ En Proceso
                                                </span>
                                            @endif
                                        </td>

                                        <td class="px-6 py-4 text-center">
                                            <span class="text-sm text-slate-700 font-bold block">{{ $exam->updated_at->format('d/m/Y') }}</span>
                                            <span class="text-[10px] text-slate-400 uppercase font-medium">{{ $exam->updated_at->format('h:i A') }}</span>
                                        </td>

                                        <td class="px-6 py-4">
                                            <div class="flex justify-center gap-3">
                                                <a href="{{ route('medical_exams.show', $exam) }}"
                                                   class="flex items-center gap-2 px-3 py-1.5 bg-white border border-slate-200 rounded-lg text-slate-600 text-xs font-bold hover:text-blue-600 hover:border-blue-200 hover:shadow-sm transition"
                                                   title="Ver Resultados">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                    Detalles
                                                </a>

                                                {{-- LÓGICA DE BOTÓN PDF --}}
                                                @if($exam->is_ready)
                                                    <a href="{{ route('medical_exams.report', $exam) }}"
                                                       target="_blank"
                                                       class="flex items-center gap-2 px-3 py-1.5 bg-slate-900 border border-slate-900 rounded-lg text-white text-xs font-bold hover:bg-purple-600 hover:border-purple-600 transition shadow-md shadow-slate-200"
                                                       title="Descargar Historia Clínica">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                                        </svg>
                                                        HISTORIA PDF
                                                    </a>
                                                @else
                                                    <button disabled class="flex items-center gap-2 px-3 py-1.5 bg-slate-50 border border-slate-100 rounded-lg text-slate-300 text-xs font-bold cursor-not-allowed">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                                        </svg>
                                                        PENDIENTE
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="px-6 py-4 bg-slate-50/30 border-t border-slate-100">
                        {{ $completedExams->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>