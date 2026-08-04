@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800/80 text-gray-900 dark:text-gray-100 focus:border-red-500 dark:focus:border-red-500 focus:ring-red-500 dark:focus:ring-red-500 rounded-xl shadow-sm transition-all']) }}>

