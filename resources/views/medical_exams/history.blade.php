<x-app-layout>
    <div class="py-12 bg-slate-50/50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Encabezado de la Sección (SnakeDEV Style) --}}
            <div class="mb-8 flex flex-col xl:flex-row xl:items-center xl:justify-between gap-4">
                <div>
                    <span class="text-[10px] font-black text-blue-500 uppercase tracking-[0.3em] mb-1 block">Panel de Auditoría</span>
                    <h2 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tighter uppercase">Historial de <span class="text-blue-600">Pacientes</span></h2>
                    <p class="text-slate-500 text-sm font-medium">Consulta de estados médicos y generación de Historias Clínicas unificadas.</p>
                </div>

                <div class="flex flex-col sm:flex-row sm:flex-wrap sm:items-center gap-4 w-full xl:w-auto">
                    {{-- Buscador Dinámico --}}
                    <form action="{{ route('medical_exams.history') }}" method="GET" class="flex flex-col sm:flex-row sm:flex-wrap sm:items-center gap-3 w-full xl:w-auto">
                        <div class="relative group w-full sm:w-auto">
                            <input type="text" name="search" value="{{ $search }}"
                                placeholder="Buscar nombre o documento..."
                                class="pl-10 pr-4 py-2.5 bg-white border border-slate-200 rounded-2xl text-sm focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 w-full sm:w-72 transition-all shadow-sm group-hover:border-slate-300 font-bold text-slate-700">
                            <div class="absolute left-3 top-3 text-slate-400 group-hover:text-blue-500 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                        </div>

                        <select name="school" class="px-4 py-2.5 bg-white border border-slate-200 rounded-2xl text-sm focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all shadow-sm font-bold text-slate-700 w-full sm:min-w-[220px] sm:w-auto">
                            <option value="">Todos los colegios</option>
                            @foreach($schools as $schoolOption)
                                <option value="{{ $schoolOption }}" {{ $school === $schoolOption ? 'selected' : '' }}>
                                    {{ $schoolOption }}
                                </option>
                            @endforeach
                        </select>

                        <button type="submit" class="px-4 py-2.5 rounded-2xl bg-blue-600 text-white text-xs font-black uppercase tracking-widest hover:bg-blue-700 transition shadow-sm w-full sm:w-auto">
                            Filtrar
                        </button>
                    </form>

                    {{-- Contador de Registros --}}
                    <div class="bg-white px-5 py-2.5 rounded-2xl shadow-sm border border-slate-100 flex items-center gap-3">
                        <div class="p-2 bg-blue-50 rounded-xl">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-[9px] uppercase font-black text-slate-400 leading-none tracking-widest">Total Registros</p>
                            <p class="text-xl font-black text-slate-900 leading-tight">{{ $completedExams->total() }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Contenedor de Tabla --}}
            <div class="bg-white overflow-hidden shadow-sm rounded-[2rem] border border-slate-100">
                @if($completedExams->isEmpty())
                    <div class="p-10 sm:p-16 lg:p-24 flex flex-col items-center justify-center text-center">
                        <div class="w-24 h-24 bg-slate-50 rounded-full flex items-center justify-center mb-6 border border-slate-100 shadow-inner">
                            <svg class="w-12 h-12 text-slate-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="text-slate-800 font-black text-xl uppercase tracking-tighter">Sin resultados</h3>
                        <p class="text-slate-400 text-sm max-w-xs mx-auto font-medium mt-2">No encontramos pacientes con los filtros aplicados.</p>
                        <a href="{{ route('medical_exams.history') }}" class="mt-6 text-blue-600 font-bold text-xs uppercase tracking-widest hover:underline">Limpiar búsqueda</a>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-50/50 border-b border-slate-100">
                                    <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center w-20">ID</th>
                                    <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Información del Paciente</th>
                                    <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Estado de Circuito</th>
                                    <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Última Atención</th>
                                    <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50">
                                @foreach($completedExams as $exam)
                                    <tr class="hover:bg-blue-50/30 transition-colors group">
                                        <td class="px-6 py-4 text-center">
                                            <span class="text-xs font-black text-slate-300 group-hover:text-blue-400 transition-colors">
                                                #{{ $exam->id }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center">
                                                <div class="h-11 w-11 rounded-2xl bg-slate-900 text-white flex items-center justify-center font-black text-xs shadow-lg mr-4 uppercase tracking-tighter">
                                                    {{ strtoupper(substr($exam->student->full_name ?? 'NA', 0, 2)) }}
                                                </div>
                                                <div>
                                                    <span class="text-sm font-black text-slate-800 block leading-tight uppercase tracking-tight">{{ $exam->student->full_name ?? 'Estudiante no disponible' }}</span>
                                                    <span class="inline-flex items-center mt-1 text-[10px] text-slate-500 font-black px-2 py-0.5 bg-slate-100 rounded-lg border border-slate-200 uppercase tracking-tighter">
                                                        CC: {{ $exam->student->document_number ?? 'N/A' }}
                                                    </span>
                                                </div>
                                            </div>
                                        </td>

                                        <td class="px-6 py-4 text-center">
                                            @if($exam->status === 'completado')
                                                <span class="inline-flex items-center px-4 py-1.5 rounded-xl text-[9px] font-black bg-green-100 text-green-700 border border-green-200 uppercase tracking-widest">
                                                    <span class="w-1.5 h-1.5 bg-green-500 rounded-full mr-2 animate-pulse"></span>
                                                    Completado
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-4 py-1.5 rounded-xl text-[9px] font-black bg-amber-50 text-amber-600 border border-amber-100 uppercase tracking-widest">
                                                    <span class="w-1.5 h-1.5 bg-amber-400 rounded-full mr-2"></span>
                                                    En Proceso
                                                </span>
                                            @endif
                                        </td>

                                        <td class="px-6 py-4 text-center">
                                            <span class="text-sm text-slate-800 font-black block tracking-tighter">{{ $exam->updated_at->format('d/m/Y') }}</span>
                                            <span class="text-[10px] text-slate-400 uppercase font-bold">{{ $exam->updated_at->format('h:i A') }}</span>
                                        </td>

                                        <td class="px-6 py-4">
                                            <div class="flex justify-center gap-2">
                                                {{-- Botón Detalles --}}
                                                <a href="{{ route('medical_exams.evaluate', $exam) }}"
                                                   class="p-2.5 bg-white border border-slate-200 rounded-xl text-slate-600 hover:text-blue-600 hover:border-blue-200 hover:shadow-md transition-all"
                                                   title="Ver Detalles">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                </a>

                                                {{-- Botón PDF --}}
                                                @if($exam->status === 'completado' && in_array(auth()->user()->role->name ?? '', ['Administrador', 'Admisión']))
                                                    <a href="{{ route('medical_exams.unified_report', $exam) }}"
                                                       target="_blank"
                                                       class="flex items-center gap-2 px-4 py-2 bg-slate-900 border border-slate-900 rounded-xl text-white text-[10px] font-black hover:bg-blue-600 hover:border-blue-600 transition shadow-lg shadow-slate-200 uppercase tracking-widest"
                                                       title="Descargar Reporte Unificado">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                                        </svg>
                                                        Reporte Unificado
                                                    </a>
                                                @elseif($exam->status === 'completado')
                                                    <button disabled class="flex items-center gap-2 px-4 py-2 bg-slate-50 border border-slate-100 rounded-xl text-slate-300 text-[10px] font-black cursor-not-allowed uppercase tracking-widest" title="Solo Administrador y Admisión">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                                        </svg>
                                                        Restringido
                                                    </button>
                                                @else
                                                    <button disabled class="flex items-center gap-2 px-4 py-2 bg-slate-50 border border-slate-100 rounded-xl text-slate-300 text-[10px] font-black cursor-not-allowed uppercase tracking-widest">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                                        </svg>
                                                        Pendiente
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Paginación Personalizada --}}
                    <div class="px-8 py-6 bg-slate-50/50 border-t border-slate-100">
                        {{ $completedExams->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
