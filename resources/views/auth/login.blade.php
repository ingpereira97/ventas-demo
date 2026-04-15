<x-guest-layout>
    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email -->
        <div>
            <label for="email">Correo electrónico</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus class="mt-1 block w-full rounded border-gray-300" />
        </div>

        <!-- Contraseña -->
        <div class="mt-4">
            <label for="password">Contraseña</label>
            <input id="password" type="password" name="password" required class="mt-1 block w-full rounded border-gray-300" />
        </div>

        <div class="mt-6">
            <button class="w-full bg-green-600 text-blue hover:text-blue-800 px-4 py-2 rounded">Iniciar sesión</button>
        </div>

        <!-- Enlace para registrarse -->
        <div class="mt-4 text-center">
            <a href="{{ route('register') }}" class="text-sm text-blue-600 hover:text-blue-800">¿No tienes una cuenta? Regístrate aquí</a>
        </div>
    </form>
</x-guest-layout>
