<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Matricular Nuevo Estudiante') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-slate-50/50 min-h-screen">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-[2rem] border border-slate-200">
                <div class="p-8 md:p-12 text-gray-900">

                    {{-- BLOQUE DE ERRORES --}}
                    @if ($errors->any())
                        <div class="mb-8 p-6 bg-red-50 border-l-4 border-red-500 rounded-r-2xl text-red-700">
                            <div class="flex items-center mb-2">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                                <p class="font-black uppercase text-xs tracking-widest">Atención</p>
                            </div>
                            <ul class="list-disc list-inside text-sm opacity-80">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('students.store') }}" class="space-y-10">
                        @csrf

                        {{-- SECCIÓN 1: DATOS DEL ESTUDIANTE --}}
                        <div class="pb-6">
                            <h3 class="text-xs font-black text-blue-600 mb-6 flex items-center uppercase tracking-[0.2em]">
                                <span class="w-8 h-8 bg-blue-50 rounded-lg flex items-center justify-center mr-3">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                </span>
                                Información del Estudiante
                            </h3>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div>
                                    <x-input-label for="document_type" :value="__('Tipo Documento')" class="text-[10px] uppercase tracking-widest font-bold text-slate-400" />
                                    <select id="document_type" name="document_type" class="border-slate-200 focus:border-blue-500 focus:ring-0 rounded-xl shadow-sm block mt-1 w-full text-sm font-medium" required>
                                        <option value="TI" {{ old('document_type') == 'TI' ? 'selected' : '' }}>Tarjeta de Identidad (T.I)</option>
                                        <option value="RC" {{ old('document_type') == 'RC' ? 'selected' : '' }}>Registro Civil (R.C)</option>
                                        <option value="CC" {{ old('document_type') == 'CC' ? 'selected' : '' }}>Cédula de Ciudadanía (C.C)</option>
                                    </select>
                                </div>

                                <div>
                                    <x-input-label for="document_number" :value="__('Número Documento')" class="text-[10px] uppercase tracking-widest font-bold text-slate-400" />
                                    <x-text-input id="document_number" class="block mt-1 w-full rounded-xl border-slate-200 focus:ring-0" type="text" name="document_number" :value="old('document_number')" required />
                                </div>

                                <div>
                                    <x-input-label for="first_name" :value="__('Nombres')" class="text-[10px] uppercase tracking-widest font-bold text-slate-400" />
                                    <x-text-input id="first_name" class="block mt-1 w-full rounded-xl border-slate-200" type="text" name="first_name" :value="old('first_name')" required />
                                </div>

                                <div>
                                    <x-input-label for="last_name" :value="__('Apellidos')" class="text-[10px] uppercase tracking-widest font-bold text-slate-400" />
                                    <x-text-input id="last_name" class="block mt-1 w-full rounded-xl border-slate-200" type="text" name="last_name" :value="old('last_name')" required />
                                </div>

                                <div>
                                    <x-input-label for="age" :value="__('Edad')" class="text-[10px] uppercase tracking-widest font-bold text-slate-400" />
                                    <x-text-input id="age" class="block mt-1 w-full rounded-xl border-slate-200" type="number" name="age" :value="old('age')" required />
                                </div>

                                <div>
                                    <x-input-label for="gender" :value="__('Sexo')" class="text-[10px] uppercase tracking-widest font-bold text-slate-400" />
                                    <select id="gender" name="gender" class="border-slate-200 focus:ring-0 rounded-xl shadow-sm block mt-1 w-full text-sm font-medium" required>
                                        <option value="Masculino" {{ old('gender') == 'Masculino' ? 'selected' : '' }}>Masculino</option>
                                        <option value="Femenino" {{ old('gender') == 'Femenino' ? 'selected' : '' }}>Femenino</option>
                                        <option value="Otro" {{ old('gender') == 'Otro' ? 'selected' : '' }}>Otro</option>
                                    </select>
                                </div>

                                <div class="md:col-span-2">
                                    <x-input-label for="previous_school" :value="__('Colegio de Procedencia')" class="text-[10px] uppercase tracking-widest font-bold text-slate-400" />
                                    <x-text-input id="previous_school" class="block mt-1 w-full rounded-xl border-slate-200" type="text" name="previous_school" :value="old('previous_school')" required />
                                </div>

                                <div>
                                    <x-input-label for="grade" :value="__('Grado al que aplica')" class="text-[10px] uppercase tracking-widest font-bold text-slate-400" />
                                    <select id="grade" name="grade" class="border-slate-200 focus:ring-0 rounded-xl shadow-sm block mt-1 w-full text-sm font-medium" required>
                                        @foreach(['Transición', '1° Primaria', '2° Primaria', '3° Primaria', '4° Primaria', '5° Primaria', '6° Bachillerato', '7° Bachillerato', '8° Bachillerato', '9° Bachillerato', '10° Bachillerato', '11° Bachillerato'] as $g)
                                            <option value="{{ $g }}" {{ old('grade') == $g ? 'selected' : '' }}>{{ $g }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        {{-- SECCIÓN 2: INFORMACIÓN DEL ACUDIENTE (CORREGIDA) --}}
                        <div class="pb-6">
                            <h3 class="text-xs font-black text-blue-600 mb-6 flex items-center uppercase tracking-[0.2em]">
                                <span class="w-8 h-8 bg-blue-50 rounded-lg flex items-center justify-center mr-3">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                </span>
                                Información del Acudiente
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div>
                                    <x-input-label for="guardian_name" :value="__('Nombres')" class="text-[10px] uppercase tracking-widest font-bold text-slate-400" />
                                    <x-text-input id="guardian_name" class="block mt-1 w-full rounded-xl border-slate-200" type="text" name="guardian_name" :value="old('guardian_name')" required />
                                </div>
                                <div>
                                    <x-input-label for="guardian_lastname" :value="__('Apellidos')" class="text-[10px] uppercase tracking-widest font-bold text-slate-400" />
                                    <x-text-input id="guardian_lastname" class="block mt-1 w-full rounded-xl border-slate-200" type="text" name="guardian_lastname" :value="old('guardian_lastname')" required />
                                </div>
                                <div>
                                    <x-input-label for="guardian_document" :value="__('Identificación')" class="text-[10px] uppercase tracking-widest font-bold text-slate-400" />
                                    <x-text-input id="guardian_document" class="block mt-1 w-full rounded-xl border-slate-200" type="text" name="guardian_document" :value="old('guardian_document')" required />
                                </div>

                                {{-- Campo de Edad del Acudiente --}}
                                <div>
                                    <x-input-label for="guardian_age" :value="__('Edad del Acudiente')" class="text-[10px] uppercase tracking-widest font-bold text-slate-400" />
                                    <x-text-input id="guardian_age" class="block mt-1 w-full rounded-xl border-slate-200" type="number" name="guardian_age" :value="old('guardian_age')" required />
                                </div>

                                <div>
                                    <x-input-label for="guardian_phone" :value="__('Teléfono de contacto')" class="text-[10px] uppercase tracking-widest font-bold text-slate-400" />
                                    <x-text-input id="guardian_phone" class="block mt-1 w-full rounded-xl border-slate-200" type="text" name="guardian_phone" :value="old('guardian_phone')" required />
                                </div>
                                <div>
                                    <x-input-label for="guardian_relationship" :value="__('Parentesco')" class="text-[10px] uppercase tracking-widest font-bold text-slate-400" />
                                    <x-text-input id="guardian_relationship" class="block mt-1 w-full rounded-xl border-slate-200" type="text" name="guardian_relationship" :value="old('guardian_relationship')" required />
                                </div>

                                {{-- Campo de Dirección --}}
                                <div class="md:col-span-2">
                                    <x-input-label for="guardian_address" :value="__('Dirección de Residencia')" class="text-[10px] uppercase tracking-widest font-bold text-slate-400" />
                                    <x-text-input id="guardian_address" class="block mt-1 w-full rounded-xl border-slate-200" type="text" name="guardian_address" :value="old('guardian_address')" required />
                                </div>

                                <div>
                                    <x-input-label for="guardian_email" :value="__('Correo Electrónico')" class="text-[10px] uppercase tracking-widest font-bold text-slate-400" />
                                    <x-text-input id="guardian_email" class="block mt-1 w-full rounded-xl border-slate-200" type="email" name="guardian_email" :value="old('guardian_email')" required />
                                </div>
                            </div>
                        </div>

                        {{-- SECCIÓN 3: CIRCUITO MÉDICO --}}
                        <div class="bg-slate-900 p-8 rounded-[2rem] shadow-xl shadow-slate-200">
                            <h3 class="text-xs font-black text-white mb-6 flex items-center uppercase tracking-[0.2em]">
                                <span class="w-8 h-8 bg-white/10 rounded-lg flex items-center justify-center mr-3 text-blue-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </span>
                                Exámenes Médicos Requeridos
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                @php
                                    $especialidades = [
                                        'valoracion_medica' => 'Valoración Médica',
                                        'odontologia' => 'Odontología',
                                        'optometria' => 'Optometría',
                                        'fonoaudiologia' => 'Fonoaudiología',
                                        'audiometria' => 'Audiometría',
                                        'psicologia' => 'Psicología'
                                    ];
                                @endphp

                                @foreach($especialidades as $value => $label)
                                    <label class="group relative flex items-center p-4 rounded-2xl border border-white/10 hover:bg-white/5 cursor-pointer transition-all">
                                        <input type="checkbox" name="requested_areas[]" value="{{ $value }}"
                                            class="rounded-lg border-white/20 text-blue-500 bg-transparent focus:ring-0 w-6 h-6 transition-all"
                                            {{ (is_array(old('requested_areas')) && in_array($value, old('requested_areas'))) || !old('requested_areas') ? 'checked' : '' }}>
                                        <span class="ml-4 text-xs font-black uppercase tracking-widest text-white/70 group-hover:text-white transition-colors">{{ $label }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        {{-- BOTONES DE ACCIÓN --}}
                        <div class="flex items-center justify-end space-x-6 pt-6">
                            <a href="{{ route('students.index') }}" class="text-xs font-black uppercase tracking-widest text-slate-400 hover:text-slate-900 transition-colors">
                                Cancelar
                            </a>
                            <button type="submit" class="bg-blue-600 text-white px-10 py-4 rounded-2xl font-black uppercase text-xs tracking-[0.2em] hover:bg-blue-700 hover:scale-105 active:scale-95 transition-all shadow-lg shadow-blue-100">
                                {{ __('Finalizar Matrícula') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
