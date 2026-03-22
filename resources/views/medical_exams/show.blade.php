@extends('layouts.app')

@section('header')
    <div class="flex justify-between items-center w-full">
        <div>
            <h2 class="font-black text-xl text-slate-800 leading-tight uppercase tracking-tighter">
                Expediente Médico #{{ $medical_exam->id }}
            </h2>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Detalle de Valoraciones Realizadas</p>
        </div>
        
        <div class="flex space-x-3">
            <a href="{{ route('medical_exams.report', $medical_exam) }}" target="_blank" 
               class="inline-flex items-center px-4 py-2 bg-slate-900 hover:bg-red-600 text-white text-xs font-black rounded-xl transition-all duration-300 shadow-lg hover:shadow-red-200 uppercase tracking-widest">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Generar PDF Final
            </a>
        </div>
    </div>
@endsection

@section('content')
<div class="py-6 px-4 sm:px-6 lg:px-8">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        {{-- Columna Izquierda: Info Paciente --}}
        <div class="space-y-6">
            <div class="bg-white p-8 rounded-[2rem] shadow-xl shadow-slate-200/50 border border-slate-200/60">
                <div class="flex items-center mb-6">
                    <div class="h-12 w-12 rounded-2xl bg-blue-600 flex items-center justify-center text-white font-black text-xl shadow-lg shadow-blue-200">
                        {{ substr($medical_exam->student->first_name, 0, 1) }}
                    </div>
                    <div class="ml-4">
                        <h3 class="font-black text-slate-800 leading-none uppercase">{{ $medical_exam->student->first_name }}</h3>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">Paciente Registrado</p>
                    </div>
                </div>

                <div class="space-y-4 border-t border-slate-100 pt-6">
                    <div>
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Documento</span>
                        <p class="text-sm font-bold text-slate-700">{{ $medical_exam->student->document_type }}: {{ $medical_exam->student->document_number }}</p>
                    </div>
                    <div>
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Estado del Proceso</span>
                        <div class="mt-1">
                            <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-tighter {{ $medical_exam->status == 'completado' ? 'bg-emerald-50 text-emerald-600 border border-emerald-100' : 'bg-amber-50 text-amber-600 border border-amber-100' }}">
                                {{ str_replace('_', ' ', $medical_exam->status) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Resumen de Áreas --}}
            <div class="bg-slate-900 p-8 rounded-[2rem] text-white shadow-xl shadow-slate-900/20">
                <h4 class="text-xs font-black uppercase tracking-[0.2em] mb-4 text-slate-400 text-center">Checklist del Circuito</h4>
                <div class="space-y-3">
                    @foreach($medical_exam->requested_areas as $area)
                        @php $haEvaluado = $medical_exam->results->where('area', Str::slug($area, '_'))->first(); @endphp
                        <div class="flex items-center justify-between p-3 rounded-xl {{ $haEvaluado ? 'bg-white/10' : 'bg-red-500/10 border border-red-500/20' }}">
                            <span class="text-[11px] font-bold uppercase">{{ $area }}</span>
                            @if($haEvaluado)
                                <svg class="w-4 h-4 text-emerald-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                            @else
                                <span class="text-[9px] font-black text-red-400 uppercase">Pendiente</span>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Columna Derecha: Resultados --}}
        <div class="lg:col-span-2 space-y-6">
            @forelse($medical_exam->results as $result)
                <div class="bg-white rounded-[2.5rem] shadow-xl shadow-slate-200/50 border border-slate-200/60 overflow-hidden group">
                    <div class="bg-slate-50 px-8 py-4 border-b border-slate-100 flex justify-between items-center group-hover:bg-blue-50/30 transition-colors">
                        <h3 class="font-black text-slate-800 uppercase tracking-tighter text-sm">
                            {{ str_replace('_', ' ', $result->area) }}
                        </h3>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                            Dr(a). {{ $result->specialist->name }}
                        </span>
                    </div>

                    <div class="p-8">
                        @php 
                            $slug = Str::slug($result->getAttributes()['area'], '_');
                            $viewName = "medical_exams.evaluations.views." . $slug;
                        @endphp

                        @if(view()->exists($viewName))
                            @include($viewName, ['data' => $result->data])
                        @else
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                @foreach($result->data as $key => $value)
                                    @if(!is_array($value) && $key !== 'odontograma_path')
                                        <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100">
                                            <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-1">{{ str_replace('_', ' ', $key) }}</span>
                                            <p class="text-sm font-bold text-slate-700">{{ $value ?: 'N/A' }}</p>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        @endif

                        {{-- Mostrar Odontograma si existe --}}
                        @if(isset($result->data['odontograma_path']))
                            <div class="mt-6 p-4 bg-slate-50 rounded-2xl border-2 border-dashed border-slate-200 text-center">
                                <p class="text-[10px] font-black text-slate-400 uppercase mb-3">Registro de Odontograma</p>
                                <img src="{{ asset('storage/' . $result->data['odontograma_path']) }}" class="mx-auto max-h-64 rounded-lg shadow-md">
                            </div>
                        @endif

                        @if($result->notes)
                            <div class="mt-6 p-4 bg-blue-50/50 rounded-2xl border-l-4 border-blue-500">
                                <span class="text-[10px] font-black text-blue-600 uppercase tracking-widest">Observaciones Clínicas:</span>
                                <p class="text-sm text-slate-700 font-medium mt-1 italic">"{{ $result->notes }}"</p>
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="bg-white border-2 border-dashed border-slate-200 rounded-[2.5rem] p-20 text-center">
                    <div class="inline-flex p-6 bg-slate-50 rounded-full mb-4">
                        <svg class="h-12 w-12 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                    <h4 class="text-xl font-black text-slate-800 uppercase tracking-tighter">Sin Valoraciones</h4>
                    <p class="text-slate-400 font-medium text-sm mt-2">El paciente aún no ha pasado por ninguna de las áreas solicitadas.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection