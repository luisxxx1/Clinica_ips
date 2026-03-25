<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'I.P.S Crear Integral') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            [x-cloak] { display: none !important; }
        </style>
    </head>
    <body
        class="font-sans antialiased bg-slate-50 theme-transition"
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
                class="bg-white border-r border-slate-200 transition-all duration-300 ease-in-out flex flex-col flex-shrink-0 shadow-sm z-20">
                @include('layouts.navigation')
            </aside>

            <div class="flex-1 flex flex-col min-w-0 overflow-hidden">

                {{-- Navbar --}}
                <header class="bg-white border-b border-slate-200 h-16 flex items-center justify-between px-4 shadow-sm z-10">
                    <div class="flex items-center space-x-4">
                        <button @click="sidebarOpen = !sidebarOpen" class="text-slate-500 hover:text-blue-600 focus:outline-none p-2 rounded-lg hover:bg-slate-100 transition">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7" />
                            </svg>
                        </button>

                        <button @click="toggleTheme()" class="text-slate-500 hover:text-blue-600 focus:outline-none p-2 rounded-lg hover:bg-slate-100 transition" :title="darkMode ? 'Cambiar a modo claro' : 'Cambiar a modo oscuro'">
                            <svg x-show="!darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v2m0 14v2m9-9h-2M5 12H3m15.364 6.364l-1.414-1.414M7.05 7.05 5.636 5.636m12.728 0L16.95 7.05M7.05 16.95l-1.414 1.414M12 8a4 4 0 100 8 4 4 0 000-8z" />
                            </svg>
                            <svg x-show="darkMode" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                            </svg>
                        </button>

                        <div class="flex items-center justify-center" style="width: 150px; height: 50px;">
                            <img src="{{ asset('LOGIN.png') }}" alt="Logo" class="w-full h-full object-contain pointer-events-none">
                        </div>
                    </div>

                    @isset($header)
                        <div class="hidden md:block font-semibold text-slate-700 uppercase tracking-wider text-sm">
                            {{ $header }}
                        </div>
                    @endisset
                </header>

                {{-- Contenido Principal Híbrido --}}
                <main class="flex-1 overflow-y-auto p-6 bg-slate-50">
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
