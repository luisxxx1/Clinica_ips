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