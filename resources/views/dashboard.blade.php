<x-app-layout>
    <x-slot name="header">
        <h2 class="text-sm font-semibold text-slate-700">Menu principal</h2>
    </x-slot>

    <div class="py-6 md:py-8 min-h-screen">
        <div class="max-w-screen-2xl mx-auto px-4 md:px-8">

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                <section class="lg:col-span-12 app-panel-strong rounded-[1.5rem] p-5 md:p-6 relative overflow-hidden">

                    <div class="flex justify-between items-start gap-4 mb-5">
                        <div>
                            <h3 class="app-display text-lg md:text-xl font-semibold text-slate-900">Circuito Médico</h3>
                            <p class="text-sm text-slate-500 mt-1">Flujo operativo de pacientes en evaluación integral.</p>
                        </div>
                        <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-teal-50 text-teal-800 text-[10px] font-semibold uppercase tracking-[0.12em] border border-teal-100">
                            Flujo activo
                        </span>
                    </div>

                    <div class="space-y-3">
                        <div class="group flex items-center gap-4 p-4 rounded-2xl bg-slate-50 hover:bg-slate-50 transition-colors border border-slate-100">
                            <div class="w-12 h-12 rounded-xl bg-white flex items-center justify-center text-amber-600 border border-slate-100 flex-none">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-[10px] font-semibold uppercase tracking-[0.12em] text-slate-400">Pendientes</p>
                                <h4 class="text-base md:text-lg font-medium text-slate-900 leading-tight">Pacientes esperando inicio</h4>
                            </div>
                            <div class="text-right">
                                <p class="text-2xl font-semibold text-slate-900 leading-none">{{ $circuitoMedico['pendiente'] }}</p>
                                <p class="text-[10px] text-slate-400 mt-1 uppercase tracking-[0.12em]">Hoy</p>
                            </div>
                        </div>

                        <div class="group flex items-center gap-4 p-4 rounded-2xl bg-slate-50 hover:bg-slate-50 transition-colors border border-slate-100">
                            <div class="w-12 h-12 rounded-xl bg-white flex items-center justify-center text-sky-600 border border-slate-100 flex-none">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m7 0a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-[10px] font-semibold uppercase tracking-[0.12em] text-slate-400">En proceso</p>
                                <h4 class="text-base md:text-lg font-medium text-slate-900 leading-tight">Evaluaciones activas en curso</h4>
                            </div>
                            <div class="text-right">
                                <p class="text-2xl font-semibold text-slate-900 leading-none">{{ $circuitoMedico['en_proceso'] }}</p>
                                <p class="text-[10px] text-slate-400 mt-1 uppercase tracking-[0.12em]">Ahora</p>
                            </div>
                        </div>

                        <div class="group flex items-center gap-4 p-4 rounded-2xl bg-slate-50 hover:bg-slate-50 transition-colors border border-slate-100">
                            <div class="w-12 h-12 rounded-xl bg-white flex items-center justify-center text-emerald-600 border border-slate-100 flex-none">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-[10px] font-semibold uppercase tracking-[0.12em] text-slate-400">Completados</p>
                                <h4 class="text-base md:text-lg font-medium text-slate-900 leading-tight">Casos listos para entrega</h4>
                            </div>
                            <div class="text-right">
                                <p class="text-2xl font-semibold text-slate-900 leading-none">{{ $circuitoMedico['completado'] }}</p>
                                <p class="text-[10px] text-slate-400 mt-1 uppercase tracking-[0.12em]">Total</p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 pt-5 border-t border-slate-200">
                        <div class="flex justify-between items-center mb-3">
                            <p class="text-xs font-semibold text-slate-500 uppercase tracking-[0.12em]">Progreso general del circuito</p>
                            <p class="text-xs font-semibold text-slate-700">{{ $circuitoMedico['completado'] }} / {{ $circuitoMedico['total'] }}</p>
                        </div>
                        <div class="w-full bg-slate-200 rounded-full h-2.5 overflow-hidden">
                            <div class="bg-teal-600 h-full rounded-full transition-all duration-300" style="width: {{ $circuitoMedico['total'] > 0 ? ($circuitoMedico['completado'] / $circuitoMedico['total'] * 100) : 0 }}%"></div>
                        </div>
                    </div>
                </section>

                <section class="lg:col-span-12 bg-slate-50/90 app-panel rounded-[1.5rem] p-5 md:p-6">
                    <div class="flex items-center justify-between mb-6 gap-4">
                        <div>
                            <h3 class="app-display text-lg md:text-xl font-semibold text-slate-900">Análisis de Crecimiento</h3>
                            <p class="text-sm text-slate-500 mt-1">Flujo mensual de registros de pacientes.</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full bg-teal-600"></span>
                            <span class="text-[10px] font-semibold text-slate-400 uppercase tracking-[0.12em]">Nuevos ingresos</span>
                        </div>
                    </div>

                    <div class="relative" style="height: 360px;">
                        <canvas id="pacientesChart"></canvas>
                    </div>
                </section>

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
