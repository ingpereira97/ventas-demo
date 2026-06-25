@extends('layouts.app')

@section('content')
<div class="container">

    <h3 class="mb-4">✏️ Editar Usuario</h3>
    @error('password')
    <div class="text-danger">{{ $message }}</div>
    @enderror
    <div class="card shadow-sm">
        <div class="card-body">

            <form action="{{ route('usuarios.update', $user->id) }}" method="POST">
                @csrf
                @method('PUT')

                {{-- NOMBRE --}}
                <div class="mb-3">
                    <label class="form-label">Nombre</label>
                    <input type="text" 
                           name="name" 
                           class="form-control"
                           value="{{ $user->name }}" required>
                </div>

                {{-- EMAIL --}}
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" 
                           name="email" 
                           class="form-control"
                           value="{{ $user->email }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Nueva Contraseña</label>
                    <input type="password" name="password" class="form-control">
                    <small class="text-muted">Dejar vacío si no desea cambiarla</small>
                </div>

                <div class="mb-3">
                    <label class="form-label">Confirmar Contraseña</label>
                    <input type="password" name="password_confirmation" class="form-control">
                </div>

                {{-- ROL --}}
                <div class="mb-3">
                    <label class="form-label">Rol</label>

                    <select name="role" class="form-select">
                        @foreach($roles as $role)
                            <option value="{{ $role->name }}"
                                {{ $user->hasRole($role->name) ? 'selected' : '' }}>
                                {{ ucfirst($role->name) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('usuarios.index') }}" class="btn btn-secondary">
                        Volver
                    </a>

                    <button class="btn btn-primary">
                        💾 Guardar Cambios
                    </button>
                </div>

            </form>

        </div>
    </div>

</div>
@endsection