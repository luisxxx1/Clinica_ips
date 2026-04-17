@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'block w-full rounded-xl border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-800 shadow-sm focus:border-teal-500 focus:ring-2 focus:ring-teal-500/15 dark:border-slate-300 dark:bg-white dark:text-slate-800 dark:placeholder:text-slate-400']) }}>
