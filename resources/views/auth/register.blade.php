<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Họ tên -->
        <div>
            <x-input-label for="ho_ten" :value="__('Họ tên')" />
            <x-text-input id="ho_ten" class="block mt-1 w-full" type="text" name="ho_ten" :value="old('ho_ten')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('ho_ten')" class="mt-2" />
        </div>

        <!-- Email -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Số điện thoại -->
        <div class="mt-4">
            <x-input-label for="so_dien_thoai" :value="__('Số điện thoại')" />
            <x-text-input id="so_dien_thoai" class="block mt-1 w-full" type="text" name="so_dien_thoai" :value="old('so_dien_thoai')" required autocomplete="tel" />
            <x-input-error :messages="$errors->get('so_dien_thoai')" class="mt-2" />
        </div>

        <!-- Mật khẩu -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Mật khẩu')" />
            <x-text-input id="password" class="block mt-1 w-full"
                          type="password"
                          name="password"
                          required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Xác nhận mật khẩu -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Xác nhận mật khẩu')" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                          type="password"
                          name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
               href="{{ route('login') }}">
                {{ __('Đã có tài khoản?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Đăng ký') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
