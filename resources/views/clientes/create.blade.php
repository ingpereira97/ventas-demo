@extends('layouts.app')

@section('title', 'Nuevo Cliente')

@section('content')
<div class="container px-4 mx-auto" style="max-width: 1000px;">
    <div class="card card-primary">
        <div class="card-header">
            <h3 class="card-title">Registrar Nuevo Cliente</h3>
        </div>

        <form action="{{ route('clientes.store') }}" method="POST">
            @csrf
            <div class="card-body">
                <div class="form-group">
                    <label for="nombre">Nombre</label>
                    <input type="text" class="form-control" name="nombre" required>
                </div>

                <div class="form-group">
                    <label for="email" class="form-label">Correo electrónico</label>
                    <input type="email" name="email" id="email" class="form-control">
                </div>

                <div class="form-group">
                    <label for="telefono" class="form-label">Teléfono</label>
                    <input type="text" name="telefono" id="telefono" class="form-control">
                </div>
                <div class="form-group">
                    <label for="direccion" class="form-label">Dirección</label>
                    <input type="text" name="direccion" id="direccion" class="form-control">
                </div>
            </div>
            <div class="card-footer text-right">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Guardar
                </button>
                <a href="{{ route('productos.index') }}" class="btn btn-secondary">
                    <i class="fa-solid fa-x"></i>Cancelar
                </a>
                
            </div>
        </form>
    </div>

</div>
@endsection
