<x-guest-layout>
    <div class="w-full max-w-md mx-auto px-1 sm:px-0">
        <!-- ENCABEZADO CON GOTA DE AGUA -->
        <div class="flex flex-col items-center text-center mb-5 sm:mb-6">
            <h1 class="mt-3 text-lg sm:text-xl font-bold text-slate-800">
                {{ __('Iniciar Sesión') }}
            </h1>
        </div>

        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf

            <!-- Email Address -->
            <div class="min-w-0">
                <x-input-label for="email" :value="__('Email')" />
                <x-text-input id="email"
                    class="block mt-1 w-full min-w-0 text-sm focus:border-teal-600 focus:ring-teal-600"
                    type="email"
                    name="email"
                    :value="old('email')"
                    required
                    autofocus
                    autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
            </div>

            <!-- Password con opción de mostrar/ocultar -->
            <div class="min-w-0">
                <x-input-label for="password" :value="__('Password')" />

                <div class="relative mt-1 flex items-center">
                    <x-text-input id="password"
                        class="block w-full min-w-0 pr-11 text-sm focus:border-teal-600 focus:ring-teal-600"
                        type="password"
                        name="password"
                        required
                        autocomplete="current-password" />

                    <button type="button"
                        id="togglePasswordBtn"
                        aria-label="Mostrar u ocultar contraseña"
                        aria-pressed="false"
                        class="absolute inset-y-0 right-0 flex items-center justify-center px-3 text-slate-500 hover:text-teal-700 focus:outline-none focus:text-teal-700 transition">
                        <!-- Ícono ojo cerrado (contraseña oculta) -->
                        <svg id="eyeClosedIcon" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                        </svg>

                        <!-- Ícono ojo abierto (contraseña visible) -->
                        <svg id="eyeOpenIcon" class="w-5 h-5 hidden" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                        </svg>
                    </button>
                </div>

                <x-input-error :messages="$errors->get('password')" class="mt-1.5" />
            </div>

            <!-- Remember Me -->
            <div class="block">
                <label for="remember_me" class="inline-flex items-center cursor-pointer select-none">
                    <input id="remember_me"
                        type="checkbox"
                        class="rounded border-slate-300 text-teal-600 shadow-xs focus:ring-teal-600"
                        name="remember">
                    <span class="ms-2 text-xs sm:text-sm text-slate-600">{{ __('Remember me') }}</span>
                </label>
            </div>

            <!-- Acciones adaptables a pantallas chicas -->
            <div class="flex flex-col-reverse sm:flex-row sm:items-center sm:justify-between gap-3 pt-2">
                @if (Route::has('password.request'))
                    <a class="text-center sm:text-left underline text-xs sm:text-sm text-slate-600 hover:text-teal-700 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-600 transition"
                        href="{{ route('password.request') }}">
                        {{ __('Forgot your password?') }}
                    </a>
                @else
                    <span></span>
                @endif

                <x-primary-button class="w-full sm:w-auto justify-center bg-teal-600 hover:bg-teal-700 focus:bg-teal-700 active:bg-teal-800 focus:ring-teal-600">
                    {{ __('Log in') }}
                </x-primary-button>
            </div>
        </form>
    </div>

    <script>
        (function () {
            const toggleBtn = document.getElementById('togglePasswordBtn');
            const passwordInput = document.getElementById('password');
            const eyeOpenIcon = document.getElementById('eyeOpenIcon');
            const eyeClosedIcon = document.getElementById('eyeClosedIcon');

            if (!toggleBtn || !passwordInput || !eyeOpenIcon || !eyeClosedIcon) return;

            toggleBtn.addEventListener('click', function () {
                const isPassword = passwordInput.getAttribute('type') === 'password';
                passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
                toggleBtn.setAttribute('aria-pressed', isPassword ? 'true' : 'false');

                eyeOpenIcon.classList.toggle('hidden', !isPassword);
                eyeClosedIcon.classList.toggle('hidden', isPassword);
            });
        })();
    </script>
</x-guest-layout>