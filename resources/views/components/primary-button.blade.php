<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center px-4 py-2.5 bg-red-600 hover:bg-red-700 active:bg-red-800 text-white font-semibold text-sm rounded-xl shadow-md shadow-red-500/20 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900 transition-all duration-200 disabled:opacity-50']) }}>
    {{ $slot }}
</button>

