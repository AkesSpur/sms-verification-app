<button {{ $attributes->merge(['type' => 'submit', 'class' => 'auth-button w-full inline-flex items-center justify-center border-0 text-sm uppercase tracking-widest']) }}>
    {{ $slot }}
</button>
