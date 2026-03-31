<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Panel de Estudiantes - Clínica IPS Escolar') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-slate-50/50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Mensajes de Estado Estilizados --}}
            @if(session('success'))
                <div class="mb-6 bg-white border-l-4 border-green-500 shadow-sm rounded-r-xl p-4 flex items-center" role="alert">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-bold text-green-800">Operación Exitosa</p>
                        <p class="text-xs text-green-700">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 bg-white border-l-4 border-red-500 shadow-sm rounded-r-xl p-4 flex items-center" role="alert">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-bold text-red-800">Error</p>
                        <p class="text-xs text-red-700">{{ session('error') }}</p>
                    </div>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-[2rem] border border-slate-200">
                <div class="p-8 text-gray-900">

                    {{-- CABECERA CON BUSCADOR Y BOTÓN --}}
                    <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
                        <div>
                            <h3 class="text-xs font-black text-slate-400 uppercase tracking-[0.2em]">Gestión Académica</h3>
                            <p class="text-xl font-bold text-slate-800">Listado de Estudiantes</p>
                        </div>

                        <div class="flex space-x-3">
                            <a href="{{ route('students.create') }}" class="inline-flex items-center px-6 py-3 bg-blue-600 border border-transparent rounded-xl font-black text-xs text-white uppercase tracking-widest hover:bg-slate-900 shadow-lg shadow-blue-100 transition-all duration-200 hover:scale-105 active:scale-95">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4" />
                                </svg>
                                Nueva Matrícula
                            </a>
                        </div>
                    </div>

                    <div class="overflow-hidden rounded-2xl border border-slate-100">
                        <table class="min-w-full divide-y divide-slate-100">
                            <thead class="bg-slate-50/50">
                                <tr>
                                    <th class="px-6 py-4 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Estudiante</th>
                                    <th class="px-6 py-4 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Identificación</th>
                                    <th class="px-6 py-4 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Grado</th>
                                    <th class="px-6 py-4 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Estado Médico</th>
                                    <th class="px-6 py-4 text-right text-[10px] font-black text-slate-400 uppercase tracking-widest">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-slate-50">
                                @forelse ($students as $student)
                                    <tr class="hover:bg-blue-50/30 transition-colors group">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="h-10 w-10 flex-shrink-0 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 font-bold text-xs group-hover:bg-blue-100 group-hover:text-blue-600 transition-colors">
                                                    {{ substr($student->first_name, 0, 1) }}{{ substr($student->last_name, 0, 1) }}
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-bold text-slate-800">{{ $student->full_name }}</div>
                                                    <div class="text-[10px] font-medium text-slate-400 tracking-wider">REF: #{{ str_pad($student->id, 4, '0', STR_PAD_LEFT) }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">
                                            <span class="font-black text-slate-900 text-xs">{{ $student->document_type }}</span> {{ $student->document_number }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-3 py-1 text-[10px] font-black rounded-full bg-slate-100 text-slate-600 uppercase tracking-tighter">
                                                {{ $student->grade }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @php
                                                $latestExam = $student->medicalExams()->latest()->first();
                                            @endphp

                                            @if(!$latestExam)
                                                <span class="flex items-center text-[10px] font-bold text-slate-300 uppercase italic">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-slate-200 mr-2"></span>
                                                    Sin registro
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-[10px] font-black uppercase tracking-wider
                                                    {{ $latestExam->status == 'completado' ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700' }}">
                                                    <span class="w-1 h-1 rounded-full mr-1.5 {{ $latestExam->status == 'completado' ? 'bg-green-500' : 'bg-amber-500' }}"></span>
                                                    {{ str_replace('_', ' ', $latestExam->status) }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <div class="flex justify-end items-center space-x-2">
                                                {{-- Botón Médico --}}
                                                <form action="{{ route('medical_exams.store') }}" method="POST" class="inline">
                                                    @csrf
                                                    <input type="hidden" name="student_id" value="{{ $student->id }}">
                                                    <button type="submit" title="Iniciar Evaluación" class="p-2 text-green-600 hover:bg-green-50 rounded-lg transition-colors">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                    </button>
                                                </form>

                                                <a href="{{ route('students.show', $student) }}" title="Ver Ficha" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6M7 4h8a2 2 0 012 2v12a2 2 0 01-2 2H7a2 2 0 01-2-2V6a2 2 0 012-2z"></path></svg>
                                                </a>

                                                <a href="{{ route('students.edit', $student) }}" title="Editar" class="p-2 text-slate-400 hover:text-slate-900 hover:bg-slate-100 rounded-lg transition-colors">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                                </a>

                                                {{-- Botón Eliminar --}}
                                                <button onclick="openDeleteModal('{{ $student->id }}', '{{ $student->full_name }}')" title="Eliminar" class="p-2 text-red-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-20 text-center">
                                            <div class="flex flex-col items-center">
                                                <svg class="w-12 h-12 text-slate-200 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                                                <p class="text-slate-400 text-sm italic font-medium">Base de datos vacía. Comience registrando un nuevo estudiante.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-8">
                        {{ $students->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>

    {{-- Modal de Confirmación de Eliminación --}}
    <div id="deleteModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-2xl shadow-2xl p-8 max-w-md w-full mx-4 animate-in fade-in scale-95 transition-all">
            <div class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 rounded-full mb-4">
                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4v.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>

            <h3 class="text-lg font-black text-slate-900 text-center mb-2">¿Eliminar estudiante?</h3>
            <p class="text-sm text-slate-600 text-center mb-6">
                Estás a punto de eliminar el registro de <strong id="studentName"></strong>. Esta acción no se puede deshacer.
            </p>

            <div class="flex gap-3">
                <button onclick="closeDeleteModal()" class="flex-1 px-4 py-2 bg-slate-100 text-slate-700 font-bold rounded-lg hover:bg-slate-200 transition-colors">
                    Cancelar
                </button>
                <button onclick="confirmDelete()" class="flex-1 px-4 py-2 bg-red-600 text-white font-bold rounded-lg hover:bg-red-700 transition-colors">
                    Eliminar
                </button>
            </div>
        </div>
    </div>

    {{-- Formulario Oculto para DELETE --}}
    <form id="deleteForm" method="POST" class="hidden">
        @csrf
        @method('DELETE')
    </form>

    <script>
        let studentIdToDelete = null;

        function openDeleteModal(studentId, studentName) {
            studentIdToDelete = studentId;
            document.getElementById('studentName').textContent = studentName;
            document.getElementById('deleteModal').classList.remove('hidden');
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.add('hidden');
            studentIdToDelete = null;
        }

        function confirmDelete() {
            if (studentIdToDelete) {
                const form = document.getElementById('deleteForm');
                form.action = `{{ url('students') }}/${studentIdToDelete}`;
                form.submit();
            }
        }

        // Cerrar modal al presionar ESC
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeDeleteModal();
            }
        });
    </script>
</x-app-layout>
