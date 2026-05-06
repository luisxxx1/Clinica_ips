@props(['number'])

<div x-data="{
    {{-- Estado independiente para cada una de las 5 caras --}}
    faces: {
        top: 'white',
        bottom: 'white',
        left: 'white',
        right: 'white',
        center: 'white'
    },

    {{-- Función para rotar colores según la nueva lista de convenciones --}}
    toggleFace(face) {
        {{-- Orden: Sano, Caries/Partido, Sellante, Restauración, Ausente, Observación --}}
        const states = ['white', 'red', 'green', 'blue', 'black', 'yellow'];
        let currentIndex = states.indexOf(this.faces[face]);
        this.faces[face] = states[(currentIndex + 1) % states.length];
    },

    {{-- Colores dinámicos para el SVG basados en tu lista --}}
    getColor(face) {
        const colors = {
            'white': '#ffffff',  {{-- Sano (Blanco) --}}
            'red': '#ef4444',    {{-- Caries / Partido (Rojo) --}}
            'green': '#22c55e',  {{-- Sellante (Verde) --}}
            'blue': '#3b82f6',   {{-- Restauración (Azul) --}}
            'black': '#000000',  {{-- Ausente (Negro) --}}
            'yellow': '#eab308'  {{-- Observación (Amarillo) --}}
        };
        return colors[this.faces[face]] || '#ffffff';
    }
}" class="flex flex-col items-center gap-1 group">

    {{-- SVG interactivo --}}
    <svg width="45" height="45" viewBox="0 0 100 100" class="drop-shadow-sm transition-transform group-hover:scale-110">
        {{-- Cara Superior --}}
        <path data-face="top" @click="toggleFace('top')" :fill="getColor('top')" d="M10,10 L90,10 L70,30 L30,30 Z" stroke="#cbd5e1" stroke-width="2" class="cursor-pointer hover:opacity-80" />

        {{-- Cara Derecha --}}
        <path data-face="right" @click="toggleFace('right')" :fill="getColor('right')" d="M90,10 L90,90 L70,70 L70,30 Z" stroke="#cbd5e1" stroke-width="2" class="cursor-pointer hover:opacity-80" />

        {{-- Cara Inferior --}}
        <path data-face="bottom" @click="toggleFace('bottom')" :fill="getColor('bottom')" d="M10,90 L90,90 L70,70 L30,70 Z" stroke="#cbd5e1" stroke-width="2" class="cursor-pointer hover:opacity-80" />

        {{-- Cara Izquierda --}}
        <path data-face="left" @click="toggleFace('left')" :fill="getColor('left')" d="M10,10 L10,90 L30,70 L30,30 Z" stroke="#cbd5e1" stroke-width="2" class="cursor-pointer hover:opacity-80" />

        {{-- Centro --}}
        <rect data-face="center" @click="toggleFace('center')" :fill="getColor('center')" x="30" y="30" width="40" height="40" stroke="#cbd5e1" stroke-width="2" class="cursor-pointer hover:opacity-80" />

        {{-- Número del diente --}}
        <text x="50" y="55" font-family="Arial" font-size="12" font-weight="bold"
              :fill="faces.center === 'black' ? '#ffffff' : '#1e293b'"
              text-anchor="middle" pointer-events="none">
            {{ $number }}
        </text>
    </svg>

    {{-- Inputs ocultos para enviar los datos al servidor --}}
    <template x-for="(color, face) in faces">
        <input type="hidden" :name="'results[odontograma][{{ $number }}][' + face + ']'" :value="color">
    </template>
</div>
