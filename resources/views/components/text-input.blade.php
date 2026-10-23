@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'w-full rounded-md border-surface-300 bg-white text-surface-900 shadow-sm focus:border-primary-500 focus:ring-primary-500 disabled:bg-surface-100 disabled:text-surface-500 placeholder:text-surface-400 text-sm']) }}>