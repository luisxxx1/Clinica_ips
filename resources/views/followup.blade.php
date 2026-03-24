<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-black text-xl text-slate-800 leading-tight uppercase tracking-tighter">
                Seguimiento de Circuito <span class="text-blue-600 ml-2">|</span> <span class="text-slate-400 text-sm ml-2 font-bold italic">Valoraciones Pendientes</span>
            </h2>
            <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest bg-slate-100 px-3 py-1 rounded-full">
                Operativo
            </div>
        </div>
    </x-slot>

    <div class="py-12 bg-slate-50/50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-gradient-to-br from-slate-50 to-slate-100 overflow-hidden shadow-sm rounded-[2rem] border border-slate-300 p-8 mb-8">
                <div class="mb-8">
                    <h3 class="text-lg font-black text-slate-800 tracking-tight mb-2">Circuito Médico</h3>
                    <p class="text-xs font-bold text-slate-500">Resumen de estados y control de valoraciones faltantes por estudiante.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-white rounded-xl p-6 border-2 border-gray-200 shadow-sm">
                        <p class="text-[11px] font-black text-gray-500 uppercase tracking-wider mb-2">Pendiente</p>
                        <p class="text-3xl font-black text-gray-800">{{ $circuitoMedico['pendiente'] }}</p>
                    </div>
                    <div class="bg-white rounded-xl p-6 border-2 border-blue-200 shadow-sm">
                        <p class="text-[11px] font-black text-blue-600 uppercase tracking-wider mb-2">En Proceso</p>
                        <p class="text-3xl font-black text-slate-800">{{ $circuitoMedico['en_proceso'] }}</p>
                    </div>
                    <div class="bg-white rounded-xl p-6 border-2 border-green-200 shadow-sm">
                        <p class="text-[11px] font-black text-green-600 uppercase tracking-wider mb-2">Completado</p>
                        <p class="text-3xl font-black text-slate-800">{{ $circuitoMedico['completado'] }}</p>
                    </div>
                </div>

                <div class="mt-6">
                    <div class="flex justify-between items-center mb-3">
                        <p class="text-xs font-bold text-slate-600">Progreso General</p>
                        <p class="text-xs font-black text-slate-700">{{ $circuitoMedico['completado'] }} / {{ $circuitoMedico['total'] }} completados</p>
                    </div>
                    <div class="w-full bg-slate-200 rounded-full h-3 overflow-hidden">
                        <div class="bg-gradient-to-r from-green-400 to-green-600 h-full rounded-full" style="width: {{ $circuitoMedico['total'] > 0 ? ($circuitoMedico['completado'] / $circuitoMedico['total'] * 100) : 0 }}%"></div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <div class="lg:col-span-1 bg-white rounded-[1.5rem] border border-slate-200 p-6 shadow-sm">
                    <p class="text-xs font-black text-slate-700 uppercase tracking-wider mb-4">Faltantes por Especialidad</p>
                    <div class="space-y-2">
                        @forelse($faltantesPorArea as $area => $cantidad)
                            <div class="flex justify-between items-center px-3 py-2 rounded-lg border {{ $cantidad > 0 ? 'bg-amber-50 border-amber-200 text-amber-700' : 'bg-slate-50 border-slate-200 text-slate-400' }}">
                                <span class="text-[11px] font-black uppercase tracking-tight">{{ $area }}</span>
                                <span class="text-sm font-black">{{ $cantidad }}</span>
                            </div>
                        @empty
                            <p class="text-xs text-slate-400 font-bold">Sin datos de faltantes.</p>
                        @endforelse
                    </div>
                </div>

                <div class="lg:col-span-2 bg-white rounded-[1.5rem] border border-slate-200 p-6 shadow-sm">
                    <div class="flex justify-between items-center mb-4">
                        <p class="text-xs font-black text-slate-700 uppercase tracking-wider">Estudiantes con Áreas Pendientes</p>
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-wider">Top {{ $faltantesPorEstudiante->count() }}</span>
                    </div>

                    <div class="space-y-3 max-h-[28rem] overflow-y-auto pr-1">
                        @forelse($faltantesPorEstudiante as $item)
                            <div class="bg-slate-50 rounded-xl border border-slate-200 px-4 py-3">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0">
                                        <a href="{{ route('students.show', $item['student_id']) }}" class="text-sm font-black text-blue-700 hover:text-blue-900 hover:underline block truncate">
                                            {{ $item['estudiante'] }}
                                        </a>
                                        <p class="text-[11px] font-bold text-slate-500 mt-1">{{ $item['faltantes']->implode(', ') }}</p>
                                    </div>
                                    <div class="flex flex-col items-end gap-2">
                                        <span class="px-2 py-1 rounded-lg text-[10px] font-black uppercase tracking-wider {{ $item['faltantes_count'] >= 3 ? 'bg-red-50 text-red-700 border border-red-200' : 'bg-amber-50 text-amber-700 border border-amber-200' }}">
                                            Faltan {{ $item['faltantes_count'] }}
                                        </span>
                                        <a href="{{ route('students.show', $item['student_id']) }}" class="text-[10px] font-black uppercase tracking-wider px-2 py-1 rounded-lg border border-blue-200 text-blue-700 hover:bg-blue-50">
                                            Ver caso
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-xs text-slate-400 font-bold">No hay estudiantes con valoraciones faltantes.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
