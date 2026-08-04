<x-guest-layout>
    <div class="mb-6 text-center">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white">Daftar Akun Baru</h2>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Mulai kelola pembukuan & stok UMKM Anda.</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Nama Lengkap')" />
            <x-text-input id="name" class="block mt-1.5 w-full" type="text" name="name" :value="old('name')" placeholder="Nama Pemilik / Toko" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1.5 w-full" type="email" name="email" :value="old('email')" placeholder="nama@email.com" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div>
            <x-input-label for="password" :value="__('Kata Sandi')" />
            <x-text-input id="password" class="block mt-1.5 w-full"
                            type="password"
                            name="password"
                            placeholder="Minimal 8 karakter"
                            required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div>
            <x-input-label for="password_confirmation" :value="__('Konfirmasi Kata Sandi')" />
            <x-text-input id="password_confirmation" class="block mt-1.5 w-full"
                            type="password"
                            name="password_confirmation"
                            placeholder="Ulangi kata sandi"
                            required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="pt-2">
            <x-primary-button class="w-full">
                Daftar Sekarang
            </x-primary-button>
        </div>
    </form>

    <div class="mt-6 text-center text-sm text-gray-600 dark:text-gray-400">
        Sudah memiliki akun?
        <a href="{{ route('login') }}" class="font-semibold text-red-600 hover:text-red-700 dark:text-red-400 hover:underline">
            Masuk di sini
        </a>
    </div>
</x-guest-layout>

