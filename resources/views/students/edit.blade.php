<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Editar Matrícula: ') }} <span class="text-blue-600">{{ $student->first_name }} {{ $student->last_name }}</span>
        </h2>
    </x-slot>

    <div class="py-12 bg-slate-50/50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-[2rem] border border-slate-200">

                <div class="p-8 md:p-12">
                    {{-- Formulario de Edición --}}
                    <form action="{{ route('students.update', $student) }}" method="POST" class="space-y-10">
                        @csrf
                        @method('PATCH')

                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">

                            {{-- SECCIÓN 1: DATOS DEL ESTUDIANTE --}}
                            <div class="space-y-6">
                                <h3 class="text-xs font-black text-blue-600 flex items-center uppercase tracking-[0.2em]">
                                    <span class="w-8 h-8 bg-blue-50 rounded-lg flex items-center justify-center mr-3">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                    </span>
                                    Información Académica
                                </h3>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <x-input-label for="document_type" :value="__('Tipo Doc.')" class="text-[10px] uppercase tracking-widest font-bold text-slate-400" />
                                        <select name="document_type" id="document_type" class="block mt-1 w-full border-slate-200 rounded-xl shadow-sm focus:ring-0 text-sm font-medium">
                                            <option value="RC" {{ old('document_type', $student->document_type) == 'RC' ? 'selected' : '' }}>Registro Civil</option>
                                            <option value="TI" {{ old('document_type', $student->document_type) == 'TI' ? 'selected' : '' }}>Tarjeta Identidad</option>
                                            <option value="CC" {{ old('document_type', $student->document_type) == 'CC' ? 'selected' : '' }}>Cédula</option>
                                            <option value="CE" {{ old('document_type', $student->document_type) == 'CE' ? 'selected' : '' }}>Cédula de Extranjería</option>
                                            <option value="RE" {{ old('document_type', $student->document_type) == 'RE' ? 'selected' : '' }}>Registro de Extranjería</option>
                                        </select>
                                    </div>
                                    <div>
                                        <x-input-label for="document_number" :value="__('Número')" class="text-[10px] uppercase tracking-widest font-bold text-slate-400" />
                                        <x-text-input id="document_number" class="block mt-1 w-full rounded-xl border-slate-200" type="text" name="document_number" :value="old('document_number', $student->document_number)" required />
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <x-input-label for="first_name" :value="__('Nombres')" class="text-[10px] uppercase tracking-widest font-bold text-slate-400" />
                                        <x-text-input id="first_name" class="block mt-1 w-full rounded-xl border-slate-200" type="text" name="first_name" :value="old('first_name', $student->first_name)" required />
                                    </div>
                                    <div>
                                        <x-input-label for="last_name" :value="__('Apellidos')" class="text-[10px] uppercase tracking-widest font-bold text-slate-400" />
                                        <x-text-input id="last_name" class="block mt-1 w-full rounded-xl border-slate-200" type="text" name="last_name" :value="old('last_name', $student->last_name)" required />
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <x-input-label for="birth_date" :value="__('Fecha de Nacimiento')" class="text-[10px] uppercase tracking-widest font-bold text-slate-400" />
                                        <x-text-input id="birth_date" class="block mt-1 w-full rounded-xl border-slate-200" type="date" name="birth_date" :value="old('birth_date', optional($student->birth_date)->format('Y-m-d'))" required />
                                    </div>
                                    <div>
                                        <x-input-label for="age" :value="__('Edad')" class="text-[10px] uppercase tracking-widest font-bold text-slate-400" />
                                        <x-text-input id="age" class="block mt-1 w-full rounded-xl border-slate-200 bg-slate-50" type="number" name="age" :value="old('age', $student->age)" required readonly />
                                    </div>
                                    <div>
                                        <x-input-label for="gender" :value="__('Género')" class="text-[10px] uppercase tracking-widest font-bold text-slate-400" />
                                        <select name="gender" id="gender" class="block mt-1 w-full border-slate-200 rounded-xl shadow-sm focus:ring-0 text-sm font-medium">
                                            <option value="Masculino" {{ old('gender', $student->gender) == 'Masculino' ? 'selected' : '' }}>Masculino</option>
                                            <option value="Femenino" {{ old('gender', $student->gender) == 'Femenino' ? 'selected' : '' }}>Femenino</option>
                                            <option value="Otro" {{ old('gender', $student->gender) == 'Otro' ? 'selected' : '' }}>Otro</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <x-input-label for="grade" :value="__('Grado Actual')" class="text-[10px] uppercase tracking-widest font-bold text-slate-400" />
                                        <x-text-input id="grade" class="block mt-1 w-full rounded-xl border-none bg-blue-50/50 text-blue-700 font-bold" type="text" name="grade" :value="old('grade', $student->grade)" required />
                                    </div>
                                    <div>
                                        <x-input-label for="previous_school" :value="__('Colegio Anterior')" class="text-[10px] uppercase tracking-widest font-bold text-slate-400" />
                                        <x-text-input id="previous_school" class="block mt-1 w-full rounded-xl border-slate-200" type="text" name="previous_school" :value="old('previous_school', $student->previous_school)" required />
                                    </div>
                                </div>
                            </div>

                            {{-- SECCIÓN 2: DATOS DEL ACUDIENTE --}}
                            <div class="space-y-6 bg-slate-50 p-6 rounded-[2rem] border border-slate-100">
                                <h3 class="text-xs font-black text-green-600 flex items-center uppercase tracking-[0.2em]">
                                    <span class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                    </span>
                                    Datos del Responsable
                                </h3>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <x-input-label for="guardian_name" :value="__('Nombres')" class="text-[10px] uppercase tracking-widest font-bold text-slate-400" />
                                        <x-text-input id="guardian_name" class="block mt-1 w-full rounded-xl border-slate-200" type="text" name="guardian_name" :value="old('guardian_name', $student->guardian_name)" required />
                                    </div>
                                    <div>
                                        <x-input-label for="guardian_lastname" :value="__('Apellidos')" class="text-[10px] uppercase tracking-widest font-bold text-slate-400" />
                                        <x-text-input id="guardian_lastname" class="block mt-1 w-full rounded-xl border-slate-200" type="text" name="guardian_lastname" :value="old('guardian_lastname', $student->guardian_lastname)" required />
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <x-input-label for="guardian_document" :value="__('Documento ID')" class="text-[10px] uppercase tracking-widest font-bold text-slate-400" />
                                        <x-text-input id="guardian_document" class="block mt-1 w-full rounded-xl border-slate-200" type="text" name="guardian_document" :value="old('guardian_document', $student->guardian_document)" required />
                                    </div>
                                    <div>
                                        <x-input-label for="guardian_age" :value="__('Edad')" class="text-[10px] uppercase tracking-widest font-bold text-slate-400" />
                                        <x-text-input id="guardian_age" class="block mt-1 w-full rounded-xl border-slate-200" type="number" name="guardian_age" :value="old('guardian_age', $student->guardian_age)" required />
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <x-input-label for="guardian_phone" :value="__('Teléfono')" class="text-[10px] uppercase tracking-widest font-bold text-slate-400" />
                                        <x-text-input id="guardian_phone" class="block mt-1 w-full rounded-xl border-slate-200" type="text" name="guardian_phone" :value="old('guardian_phone', $student->guardian_phone)" required />
                                    </div>
                                    <div>
                                        <x-input-label for="guardian_relationship" :value="__('Parentesco')" class="text-[10px] uppercase tracking-widest font-bold text-slate-400" />
                                        <x-text-input id="guardian_relationship" class="block mt-1 w-full rounded-xl border-slate-200" type="text" name="guardian_relationship" :value="old('guardian_relationship', $student->guardian_relationship)" required />
                                    </div>
                                </div>

                                <div>
                                    <x-input-label for="guardian_address" :value="__('Dirección de Residencia (Opcional)')" class="text-[10px] uppercase tracking-widest font-bold text-slate-400" />
                                    <x-text-input id="guardian_address" class="block mt-1 w-full rounded-xl border-slate-200" type="text" name="guardian_address" :value="old('guardian_address', $student->guardian_address)" />
                                </div>

                                <div>
                                    <x-input-label for="guardian_email" :value="__('Correo Electrónico (Opcional)')" class="text-[10px] uppercase tracking-widest font-bold text-slate-400" />
                                    <x-text-input id="guardian_email" class="block mt-1 w-full rounded-xl border-slate-200" type="email" name="guardian_email" :value="old('guardian_email', $student->guardian_email)" />
                                </div>
                            </div>

                        </div>

                        <div class="mt-12 flex items-center justify-end space-x-6 pt-8 border-t border-slate-100">
                            <a href="{{ route('students.index') }}" class="text-xs font-black uppercase tracking-widest text-slate-400 hover:text-slate-900 transition-colors">
                                Descartar Cambios
                            </a>
                            <button type="submit" class="bg-slate-900 text-white px-10 py-4 rounded-2xl font-black uppercase text-xs tracking-[0.2em] hover:bg-blue-600 hover:scale-105 active:scale-95 transition-all shadow-xl shadow-slate-200">
                                Guardar Actualización
                            </button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>

<script>
    (function () {
        const birthInput = document.getElementById('birth_date');
        const ageInput = document.getElementById('age');

        if (!birthInput || !ageInput) {
            return;
        }

        const calculateAge = (birthDateValue) => {
            if (!birthDateValue) return '';

            const today = new Date();
            const birthDate = new Date(birthDateValue + 'T00:00:00');

            if (Number.isNaN(birthDate.getTime())) return '';

            let age = today.getFullYear() - birthDate.getFullYear();
            const monthDiff = today.getMonth() - birthDate.getMonth();

            if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
                age--;
            }

            return age >= 0 ? age : '';
        };

        const syncAge = () => {
            ageInput.value = calculateAge(birthInput.value);
        };

        birthInput.addEventListener('change', syncAge);
        birthInput.addEventListener('input', syncAge);
        syncAge();
    })();
</script>
