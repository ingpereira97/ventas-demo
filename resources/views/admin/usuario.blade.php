@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="fw-bold">👥 Gestión de Usuarios</h3>
    </div>

    {{-- CARD --}}
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">

            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>Usuario</th>
                        <th>Email</th>
                        <th class="text-center">Rol</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($users as $user)
                    <tr>

                        {{-- USUARIO --}}
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="bg-primary text-white rounded-circle d-flex justify-content-center align-items-center me-2"
                                     style="width:35px;height:35px;">
                                    {{ strtoupper(substr($user->name,0,1)) }}
                                </div>

                                <strong>{{ $user->name }}</strong>
                            </div>
                        </td>

                        {{-- EMAIL --}}
                        <td>{{ $user->email }}</td>

                        {{-- CAMBIAR ROL --}}
                        <td class="text-center">

                            @foreach($roles as $role)
                                <form action="{{ route('usuarios.updateRole', $user->id) }}" method="POST" style="display:inline;">
                                    @csrf

                                    <input type="hidden" name="role" value="{{ $role->name }}">

                                    <button type="button"
                                        class="btn btn-sm 
                                            {{ $user->hasRole($role->name) ? 'btn-dark' : 'btn-outline-secondary' }}"
                                        onclick="confirmarCambioBoton(this)">

                                        {{ ucfirst($role->name) }}
                                    </button>
                                </form>
                            @endforeach

                        </td>
                        <td class="text-center">

                            <a href="{{ route('usuarios.edit', $user->id) }}" 
                            class="btn btn-sm btn-primary">
                                ✏️
                            </a>

                            <form action="{{ route('usuarios.destroy', $user->id) }}" 
                                method="POST" 
                                style="display:inline;">
                                @csrf
                                @method('DELETE')

                                <button type="button" 
                                        class="btn btn-sm btn-danger"
                                        onclick="confirmarEliminar(this)">
                                    🗑
                                </button>
                            </form>

                        </td>

                    </tr>
                    @endforeach
                </tbody>
            </table>

        </div>
    </div>

</div>
@endsection
@push('scripts')
    @if(session('success'))
            <script>
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: "{{ session('success') }}",
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });
            </script>
        @endif

        @if(session('error'))
            <script>
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'error',
                title: "{{ session('error') }}",
                showConfirmButton: false,
                timer: 3000
            });
            </script>
        @endif

    <script>
    function confirmarCambioBoton(btn) {

        let form = btn.closest('form');

        Swal.fire({
            title: '¿Cambiar rol?',
            text: "Se actualizará el permiso del usuario",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, cambiar'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    }
    </script>
    
    <script>
    function confirmarEliminar(btn) {

        let form = btn.closest('form');

        Swal.fire({
            title: '¿Eliminar usuario?',
            text: "Esta acción no se puede revertir",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, eliminar'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    }
    </script>

@endpush
