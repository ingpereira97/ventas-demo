@extends('layouts.app')

@section('title', 'Detalle de Compra')

@section('content')
<div class="container" id="area-imprimir">
    <h2 class="mb-4 text-center">Detalle de Compra #{{ $compra->id }}</h2>

    <div class="card mb-4">
        <div class="card-body">
            <p><strong>Proveedor:</strong> {{ $compra->proveedor->nombre ?? 'Sin proveedor' }}</p>
            <p><strong>Registrado por:</strong> {{ $compra->user->name ?? 'No registrado' }}</p>
            <p><strong>Método de Pago:</strong> {{ ucfirst($compra->metodo_pago) }}</p>
            <p><strong>Fecha:</strong> {{ $compra->created_at ? $compra->created_at->format('d/m/Y H:i') : 'No disponible' }}</p>
        </div>
    </div>

    <div class="card" >
        <div class="card-header"><h4>Productos Comprados</h4></div>
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Cantidad</th>
                        <th>Precio Compra</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($compra->detalles as $detalle)
                        <tr>
                            <td>{{ $detalle->producto->nombre ?? 'Sin producto' }}</td>
                            <td>{{ $detalle->cantidad, 0}} 
                                {{ $detalle->producto->tipo == 'peso' ? 'Kg' : 'Und' }}</td>
                            <td>$ {{ number_format($detalle->precio_compra, 0) }}</td>
                            <td>$ {{ number_format($detalle->subtotal, 0) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center">No hay productos registrados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

        </div>
            <p style="text-align:end; padding:5px; margin-right: 60px; font-size:20px; font-weight: 700;"><strong>Total:</strong> ${{ number_format($compra->total, 0) }}</p>

    </div>

    <a href="{{ route('compras.index') }}" class="btn btn-secondary mt-3">Volver</a>
    <button onclick="window.print()" class="btn btn-primary mt-3">
        🖨️ Imprimir
    </button>
</div>
<style>
@media print {

    body * {
        visibility: hidden;
    }

    #area-imprimir, #area-imprimir * {
        visibility: visible;
    }

    #area-imprimir {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
    }

    button, .btn {
        display: none !important;
    }
}
</style>
@endsection