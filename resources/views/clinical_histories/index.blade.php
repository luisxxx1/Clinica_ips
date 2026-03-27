<x-app-layout>
    @php
        $roleName = mb_strtolower(auth()->user()->role->name ?? '');
        $canDownloadClinicalPdf = in_array($roleName, ['administrador', 'admisión', 'admision']);
    @endphp

    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-black text-xl text-slate-800 leading-tight uppercase tracking-tighter">
                    Historial Clínico por Paciente
                </h2>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Módulo independiente del circuito de exámenes</p>
            </div>
        </div>
    </x-slot>

    <div class="py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-6xl mx-auto space-y-6">
            <div class="bg-white rounded-3xl shadow-xl shadow-slate-200/50 border border-slate-200/60 p-6">
                <form method="GET" action="{{ route('clinical_histories.index') }}" class="flex flex-col md:flex-row gap-3">
                    <input
                        type="text"
                        name="search"
                        value="{{ $search }}"
                        placeholder="Buscar por nombre o documento"
                        class="w-full md:flex-1 rounded-xl border-slate-200 text-sm font-semibold text-slate-700 focus:border-blue-500 focus:ring-blue-500"
                    >
                    <button type="submit" class="px-6 py-3 rounded-xl bg-blue-600 text-white text-xs font-black uppercase tracking-widest hover:bg-blue-700 transition w-full md:w-auto">
                        Buscar
                    </button>
                    <a href="{{ route('clinical_histories.index') }}" class="px-6 py-3 rounded-xl bg-slate-100 text-slate-600 text-xs font-black uppercase tracking-widest hover:bg-slate-200 transition text-center w-full md:w-auto">
                        Limpiar
                    </a>
                </form>
            </div>

            <div class="bg-white rounded-3xl shadow-xl shadow-slate-200/50 border border-slate-200/60 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-100">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-6 py-4 text-left text-[11px] font-black uppercase tracking-widest text-slate-500">Paciente</th>
                                <th class="px-6 py-4 text-left text-[11px] font-black uppercase tracking-widest text-slate-500">Documento</th>
                                <th class="px-6 py-4 text-left text-[11px] font-black uppercase tracking-widest text-slate-500">Grado</th>
                                <th class="px-6 py-4 text-right text-[11px] font-black uppercase tracking-widest text-slate-500">Acción</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @forelse($students as $student)
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="px-6 py-4">
                                        <p class="text-sm font-black text-slate-800 uppercase tracking-tight">{{ $student->first_name }} {{ $student->last_name }}</p>
                                    </td>
                                    <td class="px-6 py-4 text-sm font-bold text-slate-600">{{ $student->document_type }}: {{ $student->document_number }}</td>
                                    <td class="px-6 py-4 text-sm font-bold text-slate-600">{{ $student->grade }}</td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex flex-col sm:flex-row justify-end gap-2">
                                            <a href="{{ route('clinical_histories.show', $student) }}" class="inline-flex items-center justify-center px-4 py-2 rounded-xl bg-slate-900 text-white text-[11px] font-black uppercase tracking-widest hover:bg-blue-600 transition w-full sm:w-auto">
                                                Abrir Historial
                                            </a>

                                            @if($canDownloadClinicalPdf)
                                                <a href="{{ route('clinical_histories.pdf', $student) }}" class="inline-flex items-center justify-center px-4 py-2 rounded-xl bg-emerald-600 text-white text-[11px] font-black uppercase tracking-widest hover:bg-emerald-700 transition w-full sm:w-auto">
                                                    PDF
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-16 text-center">
                                        <p class="text-sm font-bold text-slate-500 uppercase tracking-widest">No hay pacientes para mostrar</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="px-6 py-4 border-t border-slate-100">
                    {{ $students->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
