<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-teal-700 border border-teal-700 text-xs font-semibold text-white hover:bg-teal-600 hover:border-teal-600 focus:outline-none focus:ring-2 focus:ring-teal-500/20 transition duration-150']) }}>
    {{ $slot }}
</button>
