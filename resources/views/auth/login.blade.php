<x-guest-layout>
    <div class="mb-6 text-center">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white">Masuk ke Akun</h2>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Selamat datang kembali! Silakan masukkan akun Anda.</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1.5 w-full" type="email" name="email" :value="old('email')" placeholder="nama@email.com" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div>
            <div class="flex items-center justify-between">
                <x-input-label for="password" :value="__('Password')" />
                @if (Route::has('password.request'))
                    <a class="text-xs text-red-600 hover:text-red-700 dark:text-red-400 font-medium hover:underline transition-colors" href="{{ route('password.request') }}">
                        Lupa kata sandi?
                    </a>
                @endif
            </div>

            <x-text-input id="password" class="block mt-1.5 w-full"
                            type="password"
                            name="password"
                            placeholder="••••••••"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="flex items-center justify-between pt-1">
            <label for="remember_me" class="inline-flex items-center cursor-pointer">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 dark:border-gray-700 text-red-600 shadow-sm focus:ring-red-500 dark:focus:ring-red-500 dark:bg-gray-800" name="remember">
                <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">Ingat saya</span>
            </label>
        </div>

        <div class="pt-2">
            <x-primary-button class="w-full">
                Masuk Sekarang
            </x-primary-button>
        </div>
    </form>

    <div class="mt-6 text-center text-sm text-gray-600 dark:text-gray-400">
        Belum memiliki akun?
        <a href="{{ route('register') }}" class="font-semibold text-red-600 hover:text-red-700 dark:text-red-400 hover:underline">
            Daftar sekarang
        </a>
    </div>
</x-guest-layout>

