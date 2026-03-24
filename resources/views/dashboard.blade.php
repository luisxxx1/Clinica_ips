<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-black text-xl text-slate-800 leading-tight uppercase tracking-tighter">
                {{ __('Dashboard Operativo') }} <span class="text-blue-600 ml-2">|</span> <span class="text-slate-400 text-sm ml-2 font-bold italic">Crear Integral</span>
            </h2>
            <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest bg-slate-100 px-3 py-1 rounded-full">
                Sistema v2.0
            </div>
        </div>
    </x-slot>

    <div class="py-12 bg-slate-50/50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Grid de Estadísticas Rápidas --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-10">

                {{-- Card: Pacientes --}}
                <div class="bg-white overflow-hidden shadow-sm rounded-[2rem] border border-slate-200 p-8 hover:shadow-xl hover:shadow-blue-900/5 transition-all duration-300 group">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-1">Total Pacientes</p>
                            <h3 class="text-3xl font-black text-slate-800 tracking-tighter">{{ $totalPacientes }}</h3>
                        </div>
                        <div class="p-4 bg-blue-50 text-blue-600 rounded-2xl group-hover:bg-blue-600 group-hover:text-white transition-colors duration-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        </div>
                    </div>
                </div>

                {{-- Card: Certificados --}}
                <div class="bg-white overflow-hidden shadow-sm rounded-[2rem] border border-slate-200 p-8 hover:shadow-xl hover:shadow-green-900/5 transition-all duration-300 group">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-1">Certificados Listos</p>
                            <h3 class="text-3xl font-black text-slate-800 tracking-tighter">{{ $totalCertificados }}</h3>
                        </div>
                        <div class="p-4 bg-green-50 text-green-600 rounded-2xl group-hover:bg-green-600 group-hover:text-white transition-colors duration-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                    </div>
                </div>

                {{-- Card: Pendientes --}}
                <div class="bg-white overflow-hidden shadow-sm rounded-[2rem] border border-slate-200 p-8 hover:shadow-xl hover:shadow-orange-900/5 transition-all duration-300 group">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-1">En Espera</p>
                            <h3 class="text-3xl font-black text-orange-600 tracking-tighter">{{ $pendientes }}</h3>
                        </div>
                        <div class="p-4 bg-orange-50 text-orange-600 rounded-2xl group-hover:bg-orange-600 group-hover:text-white transition-colors duration-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tarjeta de Circuito Médico (Solo Administrador y Admisión) --}}
            @if($isAdminOrAdmision)
            <div class="mb-10">
                <div class="bg-gradient-to-br from-slate-50 to-slate-100 overflow-hidden shadow-sm rounded-[2rem] border border-slate-300 p-8">
                    <div class="mb-8">
                        <h3 class="text-lg font-black text-slate-800 tracking-tight mb-2">Circuito Médico</h3>
                        <p class="text-xs font-bold text-slate-500">Flujo de pacientes en el proceso de evaluación integral</p>
                    </div>

                    {{-- Flujo Visual del Circuito --}}
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        {{-- Pendiente --}}
                        <div class="bg-white rounded-xl p-6 border-2 border-gray-200 hover:border-yellow-400 transition-all duration-300 shadow-sm group">
                            <div class="flex flex-col items-center text-center">
                                <div class="w-12 h-12 bg-yellow-50 text-yellow-600 rounded-full flex items-center justify-center mb-4 group-hover:bg-yellow-100 transition-colors">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </div>
                                <p class="text-[11px] font-black text-gray-500 uppercase tracking-wider mb-2">Pendiente</p>
                                <p class="text-3xl font-black text-gray-800">{{ $circuitoMedico['pendiente'] }}</p>
                                <p class="text-[10px] font-bold text-gray-400 mt-2">Esperando inicio</p>
                            </div>
                        </div>

                        {{-- Flecha --}}
                        <div class="hidden md:flex items-center justify-center">
                            <div class="text-slate-300">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 5l7 7m0 0l-7 7m7-7H6"></path></svg>
                            </div>
                        </div>

                        {{-- En Proceso --}}
                        <div class="bg-white rounded-xl p-6 border-2 border-blue-200 hover:border-blue-400 transition-all duration-300 shadow-sm group md:order-3">
                            <div class="flex flex-col items-center text-center">
                                <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center mb-4 group-hover:bg-blue-100 transition-colors">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m7 0a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </div>
                                <p class="text-[11px] font-black text-blue-600 uppercase tracking-wider mb-2">En Proceso</p>
                                <p class="text-3xl font-black text-slate-800">{{ $circuitoMedico['en_proceso'] }}</p>
                                <p class="text-[10px] font-bold text-gray-400 mt-2">Evaluación activa</p>
                            </div>
                        </div>

                        {{-- Flecha --}}
                        <div class="hidden md:flex items-center justify-center md:order-4">
                            <div class="text-slate-300">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 5l7 7m0 0l-7 7m7-7H6"></path></svg>
                            </div>
                        </div>

                        {{-- Completado --}}
                        <div class="bg-white rounded-xl p-6 border-2 border-green-200 hover:border-green-400 transition-all duration-300 shadow-sm group md:order-5">
                            <div class="flex flex-col items-center text-center">
                                <div class="w-12 h-12 bg-green-50 text-green-600 rounded-full flex items-center justify-center mb-4 group-hover:bg-green-100 transition-colors">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                </div>
                                <p class="text-[11px] font-black text-green-600 uppercase tracking-wider mb-2">Completado</p>
                                <p class="text-3xl font-black text-slate-800">{{ $circuitoMedico['completado'] }}</p>
                                <p class="text-[10px] font-bold text-gray-400 mt-2">Listo para entregar</p>
                            </div>
                        </div>
                    </div>

                    {{-- Barra de Progreso Total --}}
                    <div class="mt-8 pt-6 border-t border-slate-300">
                        <div class="flex justify-between items-center mb-3">
                            <p class="text-xs font-bold text-slate-600">Progreso General del Circuito</p>
                            <p class="text-xs font-black text-slate-700">{{ $circuitoMedico['completado'] }} / {{ $circuitoMedico['total'] }} completados</p>
                        </div>
                        <div class="w-full bg-slate-200 rounded-full h-3 overflow-hidden">
                            <div class="bg-gradient-to-r from-green-400 to-green-600 h-full rounded-full transition-all duration-300" style="width: {{ $circuitoMedico['total'] > 0 ? ($circuitoMedico['completado'] / $circuitoMedico['total'] * 100) : 0 }}%"></div>
                        </div>
                    </div>

                </div>
            </div>
            @endif

            {{-- Sección de Gráfico --}}
            <div class="bg-white overflow-hidden shadow-sm rounded-[2.5rem] border border-slate-200 p-10 relative">
                <div class="flex justify-between items-center mb-10">
                    <div>
                        <h4 class="text-lg font-black text-slate-800 tracking-tight">Análisis de Crecimiento</h4>
                        <p class="text-xs font-bold text-slate-400">Flujo mensual de registros de pacientes</p>
                    </div>
                    <div class="flex items-center space-x-2">
                        <span class="w-3 h-3 rounded-full bg-blue-600"></span>
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Nuevos Ingresos</span>
                    </div>
                </div>

                <div class="relative" style="height: 400px;">
                    <canvas id="pacientesChart"></canvas>
                </div>
            </div>

            {{-- Footer Técnico --}}
            <div class="mt-8 text-center">
                <p class="text-[10px] font-black text-slate-300 uppercase tracking-[0.4em]">Powered by SnakeDev Engine</p>
            </div>

        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('pacientesChart').getContext('2d');

            // Gradiente para el relleno del gráfico
            const gradient = ctx.createLinearGradient(0, 0, 0, 400);
            gradient.addColorStop(0, 'rgba(37, 99, 235, 0.2)');
            gradient.addColorStop(1, 'rgba(37, 99, 235, 0)');

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: ['Enero', 'Febrero', 'Marzo (Actual)', 'Abril'],
                    datasets: [{
                        label: 'Pacientes',
                        data: [5, 12, {{ $totalPacientes }}, 15],
                        borderColor: '#2563eb',
                        borderWidth: 4,
                        pointBackgroundColor: '#ffffff',
                        pointBorderColor: '#2563eb',
                        pointBorderWidth: 3,
                        pointRadius: 6,
                        pointHoverRadius: 8,
                        backgroundColor: gradient,
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#0f172a',
                            titleFont: { size: 13, weight: 'bold' },
                            bodyFont: { size: 12 },
                            padding: 12,
                            cornerRadius: 10,
                            displayColors: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: '#f1f5f9' },
                            ticks: { font: { weight: 'bold', size: 11 }, color: '#94a3b8' }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { font: { weight: 'bold', size: 11 }, color: '#94a3b8' }
                        }
                    }
                }
            });
        });
    </script>
    @endpush
</x-app-layout>
