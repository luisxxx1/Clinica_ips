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

                                <x-text-input name="document_number" placeholder="Documento" :value="old('document_number')" required />
                                <x-text-input name="first_name" placeholder="Nombres" :value="old('first_name')" required />
                                <x-text-input name="last_name" placeholder="Apellidos" :value="old('last_name')" required />

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
