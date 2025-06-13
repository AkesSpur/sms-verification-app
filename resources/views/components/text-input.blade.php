@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'auth-input w-full text-gray-900 placeholder-gray-500']) }}>
