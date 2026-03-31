@extends('layouts.app')

@section('header')
    <div class="flex justify-between items-center w-full">
        <div class="flex items-center space-x-4">
            <div class="p-3 bg-slate-900 rounded-2xl shadow-lg shadow-slate-200">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                </svg>
            </div>
            <div>
                <h2 class="font-black text-xl text-slate-800 leading-tight tracking-tighter uppercase">
                    {{ __('Panel de Evaluación') }}
                </h2>
                <p class="text-[10px] font-black text-blue-600 uppercase tracking-[0.2em] flex items-center">
                    <span class="w-2 h-2 bg-blue-600 rounded-full mr-2 animate-pulse"></span>
                    Área: {{ $userArea }}
                </p>
            </div>
        </div>

        <div class="flex items-center space-x-3">
            {{-- Rol del Profesional --}}
            <span class="hidden md:inline-flex px-4 py-1.5 bg-white text-slate-500 text-[10px] font-black rounded-xl uppercase tracking-widest border border-slate-100 shadow-sm">
                {{ Auth::user()->role->name }}
            </span>

            {{-- Contador dinámico optimizado --}}
            <div class="flex items-center bg-emerald-500 text-white px-5 py-2 rounded-2xl shadow-xl shadow-emerald-100 border border-emerald-400/20">
                <span class="text-[11px] font-black tracking-widest uppercase">
                    {{ $pendingExams->count() }} {{ Str::plural('Pendiente', $pendingExams->count()) }}
                </span>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div class="py-8 bg-slate-50/50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Alertas con diseño SnakeDEV --}}
            @if (session('success'))
                <div class="mb-8 flex items-center bg-white border-l-4 border-emerald-500 text-slate-800 px-6 py-4 rounded-2xl shadow-xl shadow-slate-200/50 animate-fade-in-down">
                    <div class="p-2 bg-emerald-100 rounded-lg mr-4">
                        <svg class="w-5 h-5 text-emerald-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                    </div>
                    <span class="text-xs font-black uppercase tracking-tight">{{ session('success') }}</span>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-2xl shadow-slate-200/60 rounded-[3rem] border border-slate-100">
                <div class="p-10">
                    <div class="flex flex-col md:flex-row md:items-center justify-between mb-10 gap-4">
                        <div>
                            <h3 class="text-2xl font-black text-slate-900 tracking-tighter uppercase">Lista de Espera</h3>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.3em] mt-2 flex items-center">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                Central Operativa • Cali, Valle
                            </p>
                        </div>
                        <div class="flex items-center space-x-2 bg-slate-50 px-5 py-2.5 rounded-2xl border border-slate-100">
                            <span class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">Bandeja Prioritaria</span>
                        </div>
                    </div>

                    @if ($pendingExams->isEmpty())
                        <div class="py-24 text-center">
                            <div class="inline-flex p-8 bg-slate-50 rounded-[2.5rem] mb-6 border border-slate-100 shadow-inner">
                                <svg class="h-14 w-14 text-slate-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <h4 class="text-2xl font-black text-slate-800 tracking-tighter uppercase">Sin pacientes en cola</h4>
                            <p class="text-slate-400 font-bold text-xs uppercase tracking-widest mt-3">Buen trabajo, el área de {{ $userArea }} está despejada.</p>
                        </div>
                    @else
                        <div class="overflow-hidden rounded-[2rem] border border-slate-100 shadow-sm">
                            <table class="min-w-full divide-y divide-slate-100">
                                <thead class="bg-slate-50/80">
                                    <tr>
                                        <th class="px-8 py-5 text-left text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Paciente</th>
                                        <th class="px-8 py-5 text-left text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Identificación</th>
                                        <th class="px-8 py-5 text-center text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Grado</th>
                                        <th class="px-8 py-5 text-center text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Estado</th>
                                        <th class="px-8 py-5 text-left text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Espera</th>
                                        <th class="px-8 py-5 text-right text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Acción</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-slate-50">
                                    @foreach ($pendingExams as $exam)
                                        @if($exam->student) {{-- ✅ Validar que el estudiante existe --}}
                                        <tr class="hover:bg-slate-50/50 transition-all duration-300 group">
                                            <td class="px-8 py-6">
                                                <div class="flex items-center">
                                                    <div class="h-12 w-12 rounded-2xl bg-slate-900 flex items-center justify-center text-white font-black text-sm shadow-xl shadow-slate-200 group-hover:bg-blue-600 transition-colors duration-500">
                                                        {{ substr($exam->student->first_name, 0, 1) }}{{ substr($exam->student->last_name, 0, 1) }}
                                                    </div>
                                                    <div class="ml-5">
                                                        <div class="text-sm font-black text-slate-800 tracking-tighter uppercase group-hover:text-blue-600 transition-colors">
                                                            {{ $exam->student->first_name }} {{ $exam->student->last_name }}
                                                        </div>
                                                        <span class="text-[9px] font-black text-slate-400 uppercase tracking-[0.15em] mt-1 block">ID: #EXAM-{{ $exam->id }}</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-8 py-6">
                                                <span class="text-[10px] font-bold text-slate-600 bg-slate-100 px-3 py-1.5 rounded-xl border border-slate-200/60 tracking-tight">
                                                    {{ $exam->student->document_type }}: {{ $exam->student->document_number }}
                                                </span>
                                            </td>
                                            <td class="px-8 py-6 text-center">
                                                <span class="px-3 py-1.5 bg-blue-50 text-blue-700 text-[10px] font-black rounded-xl border border-blue-100 uppercase tracking-widest">
                                                    {{ $exam->student->grade ?? 'S/G' }}
                                                </span>
                                            </td>
                                            <td class="px-8 py-6 text-center">
                                                <div class="inline-flex items-center px-4 py-1.5 text-[9px] font-black rounded-full border {{ $exam->status === 'en_proceso' ? 'bg-amber-50 text-amber-600 border-amber-100' : 'bg-slate-50 text-slate-500 border-slate-200' }} uppercase tracking-[0.2em]">
                                                    @if($exam->status === 'en_proceso')
                                                        <span class="relative flex h-2 w-2 mr-2">
                                                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                                                            <span class="relative inline-flex rounded-full h-2 w-2 bg-amber-500"></span>
                                                        </span>
                                                    @endif
                                                    {{ str_replace('_', ' ', $exam->status) }}
                                                </div>
                                            </td>
                                            <td class="px-8 py-6">
                                                <div class="text-[10px] font-black text-slate-700 uppercase tracking-tighter">
                                                    {{ $exam->created_at->timezone('America/Bogota')->diffForHumans() }}
                                                </div>
                                                <div class="text-[9px] font-bold text-slate-400 mt-1 italic uppercase">{{ $exam->created_at->timezone('America/Bogota')->format('h:i A') }}</div>
                                            </td>
                                            <td class="px-8 py-6 text-right">
                                                @php
                                                    $roleSlug = \Illuminate\Support\Str::slug((string) $userArea, '_');
                                                    $requestedAreas = collect($exam->requested_areas ?? [])->map(fn ($a) => \Illuminate\Support\Str::slug((string) $a, '_'));
                                                    $completedAreas = $exam->results->pluck('area')->map(fn ($a) => \Illuminate\Support\Str::slug((string) $a, '_'));
                                                    $canOpenAudio = $roleSlug === 'audiometria';
                                                    $isAdmin = $roleSlug === 'administrador';
                                                    $pendingByArea = $requestedAreas->diff($completedAreas)->values();
                                                    $nextPendingArea = $pendingByArea->first();
                                                    $areaLabels = [
                                                        'valoracion_medica' => 'Valoración Médica',
                                                        'odontologia' => 'Odontología',
                                                        'optometria' => 'Optometría',
                                                        'audiometria' => 'Audiometría',
                                                        'fonoaudiologia' => 'Fonoaudiología',
                                                        'psicologia' => 'Psicología',
                                                    ];
                                                @endphp

                                                @if($canOpenAudio)
                                                    <div class="flex justify-end gap-2">
                                                        @if($requestedAreas->contains('audiometria') && !$completedAreas->contains('audiometria'))
                                                            <a href="{{ route('medical_exams.evaluate', ['medical_exam' => $exam->id, 'area' => 'audiometria']) }}"
                                                               class="inline-flex items-center px-4 py-3 bg-slate-900 hover:bg-blue-600 text-white text-[10px] font-black rounded-2xl transition-all duration-500 shadow-xl hover:shadow-blue-200/50 uppercase tracking-[0.15em] group/btn">
                                                                <span>Audiometría</span>
                                                            </a>
                                                        @endif
                                                        @if($requestedAreas->contains('fonoaudiologia') && !$completedAreas->contains('fonoaudiologia'))
                                                            <a href="{{ route('medical_exams.evaluate', ['medical_exam' => $exam->id, 'area' => 'fonoaudiologia']) }}"
                                                               class="inline-flex items-center px-4 py-3 bg-orange-600 hover:bg-orange-500 text-white text-[10px] font-black rounded-2xl transition-all duration-500 shadow-xl uppercase tracking-[0.15em] group/btn">
                                                                <span>Fonoaudiología</span>
                                                            </a>
                                                        @endif
                                                    </div>
                                                @else
                                                    <a href="{{ route('medical_exams.evaluate', $exam) }}"
                                                       class="inline-flex items-center px-6 py-3 bg-slate-900 hover:bg-blue-600 text-white text-[10px] font-black rounded-2xl transition-all duration-500 shadow-xl hover:shadow-blue-200/50 uppercase tracking-[0.2em] group/btn">
                                                        <span>Iniciar Evaluación</span>
                                                        <svg class="w-4 h-4 ml-2 group-hover/btn:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                                                        </svg>
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

            <div class="mt-12 text-center">
                <p class="text-[9px] font-black text-slate-300 uppercase tracking-[0.5em] flex items-center justify-center">
                    <span class="w-8 h-[1px] bg-slate-200 mr-4"></span>
                    SnakeDEV Health Systems • Cali, CO
                    <span class="w-8 h-[1px] bg-slate-200 ml-4"></span>
                </p>
            </div>
        </div>
    </div>
@endsection
