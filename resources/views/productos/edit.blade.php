@extends('layouts.app')

@section('content')
<div class="container-fluid mt-4">
    <div class="card shadow-sm">
        <div class="card-header">
            <h3 class="card-title">Editar Producto</h3>
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route('productos.update', $producto->id) }}">
                @csrf
                @method('PUT')

                <div class="form-group mb-3">
                    <label>Código de Barras</label>

                    <input type="text" name="codigo_barras"
                        value="{{ old('codigo_barras', $producto->codigo_barras) }}"
                        class="form-control">
                </div>

                {{-- NOMBRE --}}
                <div class="form-group mb-3">
                    <label>Nombre</label>
                    <input type="text" name="nombre"
                        value="{{ old('nombre', $producto->nombre) }}"
                        class="form-control" required>
                </div>

                {{-- DESCRIPCIÓN --}}
                <div class="form-group mb-3">
                    <label>Descripción</label>
                    <textarea name="descripcion"
                        class="form-control" rows="3">{{ old('descripcion', $producto->descripcion) }}</textarea>
                </div>

                {{-- TIPO 🔥 --}}
                <div class="form-group mb-3">
                    <label>Tipo de producto</label>

                    <select name="tipo" class="form-control" required>
                        <option value="unidad" {{ $producto->tipo == 'unidad' ? 'selected' : '' }}>
                            Unidad
                        </option>

                        <option value="peso" {{ $producto->tipo == 'peso' ? 'selected' : '' }}>
                            Por Kg
                        </option>
                    </select>
                </div>

                {{-- PRECIO --}}
                <div class="form-group mb-3">
                    <label>
                        Precio 
                        <small class="text-muted">(por unidad o Kg)</small>
                    </label>

                    <input type="number" step="0.001" name="precio"
                        value="{{ old('precio', rtrim(rtrim($producto->precio, '0'), '.')) }}"
                        class="form-control" required>
                </div>

                {{-- STOCK --}}
                <div class="form-group mb-3">
                    <label>Stock</label>

                    <input type="number" step="0.001" name="stock"
                        value="{{ old('stock', rtrim(rtrim($producto->stock, '0'), '.')) }}"
                        class="form-control" required>

                    <small class="text-muted">
                        Si es por peso: en Kg (ej: 5.500)
                    </small>
                </div>

                {{-- BOTONES --}}
                <div class="mt-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Actualizar
                    </button>

                    <a href="{{ route('productos.index') }}" class="btn btn-secondary">
                        Cancelar
                    </a>
                </div>

            </form>
        </div>
    </div>
</div>
@endsection