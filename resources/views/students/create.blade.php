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

                    {{-- ERRORES --}}
                    @if ($errors->any())
                        <div class="mb-8 p-6 bg-red-50 border-l-4 border-red-500 rounded-r-2xl text-red-700">
                            <ul class="list-disc list-inside text-sm">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('students.store') }}" class="space-y-10">
                        @csrf

                        {{-- ESTUDIANTE --}}
                        <div>
                            <h3 class="text-xs font-black text-blue-600 mb-6 uppercase">Información del Estudiante</h3>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

<<<<<<< HEAD
                                <x-text-input name="document_number" placeholder="Documento" :value="old('document_number')" required />
                                <x-text-input name="first_name" placeholder="Nombres" :value="old('first_name')" required />
                                <x-text-input name="last_name" placeholder="Apellidos" :value="old('last_name')" required />
=======
                                <div>
                                    <x-input-label for="document_number" :value="__('Número Documento')" class="text-[10px] uppercase tracking-widest font-bold text-slate-400" />
                                    <x-text-input id="document_number" class="block mt-1 w-full rounded-xl border-slate-200 focus:ring-0" type="text" name="document_number" :value="old('document_number')" required />
                                </div>

                                <div>
                                    <x-input-label for="first_name" :value="__('Nombres')" class="text-[10px] uppercase tracking-widest font-bold text-slate-400" />
                                    <x-text-input id="first_name" class="block mt-1 w-full rounded-xl border-slate-200" type="text" name="first_name" :value="old('first_name')" required />
                                </div>
>>>>>>> d595ce883907d6fa5c169be06e55300caa155884

                                <x-text-input name="age" type="number" placeholder="Edad" :value="old('age')" required />

                                <select name="gender" class="rounded-xl border-slate-200" required>
                                    <option value="">Sexo</option>
                                    <option value="Masculino">Masculino</option>
                                    <option value="Femenino">Femenino</option>
                                </select>

                                <x-text-input name="previous_school" placeholder="Colegio" :value="old('previous_school')" required />

                            </div>
                        </div>

                        {{-- ACUDIENTE --}}
                        <div>
                            <h3 class="text-xs font-black text-blue-600 mb-6 uppercase">Información del Acudiente</h3>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                                <x-text-input name="guardian_name" placeholder="Nombres" :value="old('guardian_name')" required />
                                <x-text-input name="guardian_lastname" placeholder="Apellidos" :value="old('guardian_lastname')" required />
                                <x-text-input name="guardian_document" placeholder="Documento" :value="old('guardian_document')" required />

                                <x-text-input name="guardian_phone" placeholder="Teléfono" :value="old('guardian_phone')" required />
                                <x-text-input name="guardian_relationship" placeholder="Parentesco" :value="old('guardian_relationship')" required />
                                <x-text-input name="guardian_email" type="email" placeholder="Correo" :value="old('guardian_email')" required />

                                {{-- NUEVOS CAMPOS --}}
                                <x-text-input name="guardian_age" type="number" placeholder="Edad acudiente" :value="old('guardian_age')" required />

                                <div class="md:col-span-2">
                                    <x-text-input name="guardian_address" placeholder="Dirección acudiente" :value="old('guardian_address')" required />
                                </div>

                            </div>
                        </div>

<<<<<<< HEAD
                        {{-- EXÁMENES --}}
                        <div class="bg-slate-900 p-6 rounded-2xl text-white">
                            <h3 class="text-xs font-black mb-4 uppercase">Exámenes Médicos</h3>

                            @foreach([
                                'valoracion_medica' => 'Valoración Médica',
                                'odontologia' => 'Odontología',
                                'optometria' => 'Optometría',
                                'psicologia' => 'Psicología'
                            ] as $value => $label)

                                <label class="block">
                                    <input type="checkbox" name="requested_areas[]" value="{{ $value }}" checked>
                                    {{ $label }}
                                </label>

                            @endforeach
=======
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
>>>>>>> d595ce883907d6fa5c169be06e55300caa155884
                        </div>

                        <button class="bg-blue-600 text-white px-6 py-3 rounded-xl">
                            Guardar
                        </button>

                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
