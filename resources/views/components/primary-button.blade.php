<button {{ $attributes->merge(['type' => 'submit', 'class' => 'w-full inline-flex items-center justify-center px-4 py-2.5 rounded-lg text-sm font-bold text-white transition-all shadow-lg hover:shadow-xl hover:-translate-y-0.5 bg-gradient-to-br from-primary-800 to-primary-900 hover:from-primary-700 hover:to-primary-800 disabled:opacity-50 disabled:cursor-not-allowed']) }}>
    {{ $slot }}
</button>
