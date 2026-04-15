@extends('layouts.app')

@section('title', 'Editar Cliente')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4">Editar cliente</h1>

    <form action="{{ route('clientes.update', $cliente) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre</label>
            <input type="text" name="nombre" id="nombre" value="{{ $cliente->nombre }}" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Correo electrónico</label>
            <input type="email" name="email" id="email" value="{{ $cliente->email }}" class="form-control">
        </div>

        <div class="mb-3">
            <label for="telefono" class="form-label">Teléfono</label>
            <input type="text" name="telefono" id="telefono" value="{{ $cliente->telefono }}" class="form-control">
        </div>

        <div class="mb-3">
            <label for="direccion" class="form-label">Dirección</label>
            <input type="text" name="direccion" id="direccion" value="{{ $cliente->direccion }}" class="form-control">
        </div>

        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> Actualizar cliente
        </button>
        <a href="{{ route('clientes.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection
