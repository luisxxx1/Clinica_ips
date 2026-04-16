<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'I.P.S Crear Integral') }}</title>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Public+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            [x-cloak] { display: none !important; }

            :root {
                --app-bg: #f8fafb;
                --app-surface: rgba(255, 255, 255, 0.82);
                --app-surface-strong: rgba(255, 255, 255, 0.94);
                --app-border: rgba(189, 201, 202, 0.45);
                --app-accent: #016064;
                --app-accent-soft: rgba(1, 96, 100, 0.08);
                --app-text: #191c1d;
            }

            html.dark {
                --app-bg: #0f1419;
                --app-surface: rgba(22, 31, 36, 0.72);
                --app-surface-strong: rgba(28, 40, 47, 0.88);
                --app-border: rgba(55, 74, 84, 0.55);
                --app-accent: #00d4d9;
                --app-accent-soft: rgba(0, 212, 217, 0.12);
                --app-text: #e0e7eb;
            }

            body {
                font-family: 'Public Sans', sans-serif;
                color: var(--app-text);
                background: #f7f9fa;
                transition: background-color 0.3s ease, color 0.3s ease;
            }

            html.dark body {
                background: #0f1419;
            }

            .app-display {
                font-family: 'Manrope', sans-serif;
                letter-spacing: -0.03em;
            }

            .app-panel {
                background: var(--app-surface);
                backdrop-filter: blur(24px);
                -webkit-backdrop-filter: blur(24px);
                border: 1px solid var(--app-border);
                box-shadow: 0 8px 20px rgba(15, 23, 42, 0.04);
            }

            html.dark .app-panel {
                box-shadow: 0 8px 20px rgba(0, 0, 0, 0.22);
            }

            .app-panel-strong {
                background: var(--app-surface-strong);
                backdrop-filter: blur(24px);
                -webkit-backdrop-filter: blur(24px);
                border: 1px solid rgba(189, 201, 202, 0.55);
                box-shadow: 0 10px 24px rgba(15, 23, 42, 0.05);
            }

            html.dark .app-panel-strong {
                border: 1px solid rgba(55, 74, 84, 0.65);
                box-shadow: 0 10px 24px rgba(0, 0, 0, 0.24);
            }

            .app-accent-chip {
                background: rgba(1, 96, 100, 0.08);
                border: 1px solid rgba(1, 96, 100, 0.12);
            }

            html.dark .app-accent-chip {
                background: rgba(0, 212, 217, 0.12);
                border: 1px solid rgba(0, 212, 217, 0.18);
            }

            .glass-nav {
                backdrop-filter: blur(24px);
                -webkit-backdrop-filter: blur(24px);
            }

            html.dark .glass-nav {
                background: rgba(28, 40, 47, 0.6) !important;
            }

            .scale-98-on-click:active {
                transform: scale(0.98);
            }

            html.dark {
                color-scheme: dark;
            }

            html {
                color-scheme: light;
            }
        </style>
    </head>
    <body
        class="antialiased theme-transition"
        x-data="{
            sidebarOpen: true,
            darkMode: localStorage.getItem('theme') === 'dark',
            toggleTheme() {
                this.darkMode = !this.darkMode;
                localStorage.setItem('theme', this.darkMode ? 'dark' : 'light');
            }
        }"
        x-init="if (darkMode) document.documentElement.classList.add('dark'); $watch('darkMode', value => document.documentElement.classList.toggle('dark', value))"
        :class="darkMode ? 'dark' : ''"
    >
        <div class="min-h-screen flex overflow-hidden">

            {{-- Sidebar --}}
            <aside
                :class="sidebarOpen ? 'w-64' : 'w-20'"
                class="app-panel transition-all duration-300 ease-in-out flex flex-col flex-shrink-0 z-20">
                @include('layouts.navigation')
            </aside>

            <div class="flex-1 flex flex-col min-w-0 overflow-hidden">

                {{-- Navbar --}}
                <header class="app-panel-strong h-16 flex items-center justify-between px-4 z-10 sticky top-0 glass-nav bg-white/80 gap-4 dark:bg-slate-900/60">
                    <div class="flex items-center space-x-4 min-w-0">
                        <button @click="sidebarOpen = !sidebarOpen" class="text-slate-500 hover:text-teal-700 focus:outline-none p-2 rounded-full hover:bg-slate-100 transition scale-98-on-click dark:text-slate-400 dark:hover:text-cyan-400 dark:hover:bg-slate-800">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7" />
                            </svg>
                        </button>

                        <button @click="toggleTheme()" class="text-slate-500 hover:text-teal-700 focus:outline-none p-2 rounded-full hover:bg-slate-100 transition scale-98-on-click dark:text-slate-400 dark:hover:text-cyan-400 dark:hover:bg-slate-800" :title="darkMode ? 'Cambiar a modo claro' : 'Cambiar a modo oscuro'">
                            <svg x-show="!darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v2m0 14v2m9-9h-2M5 12H3m15.364 6.364l-1.414-1.414M7.05 7.05 5.636 5.636m12.728 0L16.95 7.05M7.05 16.95l-1.414 1.414M12 8a4 4 0 100 8 4 4 0 000-8z" />
                            </svg>
                            <svg x-show="darkMode" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                            </svg>
                        </button>

                    </div>

                    @isset($header)
                        <div class="hidden md:flex min-w-0 max-w-[52rem] items-center justify-end text-right">
                            <div class="text-slate-700 leading-tight truncate dark:text-slate-300">
                            {{ $header }}
                            </div>
                        </div>
                    @endisset
                </header>

                {{-- Contenido Principal Híbrido --}}
                <main class="flex-1 overflow-y-auto p-6 md:p-8">
                    {{-- 1. Soporte para @section('content') (Nuevas vistas como Medical Exams) --}}
                    @yield('content')

                    {{-- 2. Soporte para <x-app-layout> (Vistas de Dashboard y Registro) --}}
                    @isset($slot)
                        {{ $slot }}
                    @endisset
                </main>
            </div>
        </div>
        @stack('scripts')
    </body>
</html>
