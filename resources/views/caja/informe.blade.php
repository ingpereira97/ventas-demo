@extends('layouts.app')

@section('title', 'Informe de Caja')

@section('content')
<div class="container mx-auto px-4 py-6" id="area-imprimir">
    <h2 class="text-xl font-bold mb-4 text-center">Informe de Caja</h2>

    <div class="bg-white p-4 rounded shadow mb-4">
        <p><strong>Monto Inicial:</strong> ${{ number_format($caja->monto_inicial) }}</p>
        <p><strong>Total Ventas:</strong> ${{ number_format($totalVentas) }}</p>
        <p><strong>Total Compras:</strong> ${{ number_format($totalCompras) }}</p>
        <p><strong>Total Egresos:</strong> ${{ number_format($totalEgresos) }}</p>
        <p><strong>Total Esperado:</strong> ${{ number_format($totalEsperado) }}</p>
        <p><strong>Monto de Cierre:</strong> ${{ number_format($caja->monto_cierre) }}</p>
        <p><strong>Diferencia:</strong> ${{ number_format($diferencia) }}</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="bg-white p-4 rounded shadow">
            <h3 class="font-semibold mb-2">Ventas</h3>
            <table class="w-full text-sm border">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="border px-2 py-1">Cliente</th>
                        <th class="border px-2 py-1">Total</th>
                        <th class="border px-2 py-1">Fecha</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($cobros as $cobro)                    
                        <tr>
                        <td class="border px-2 py-1">{{ $cobro->venta->cliente->nombre ?? 'Ocasional' }}</td>
                        <td class="border px-2 py-1">${{ number_format($cobro->monto_pagado) }}</td>
                        <td class="border px-2 py-1">{{ $cobro->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="bg-white p-4 rounded shadow">
            <h3 class="font-semibold mb-2">Egresos</h3>
            <table class="w-full text-sm border mb-3">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="border px-2 py-1">Descripción</th>
                        <th class="border px-2 py-1">Monto</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($egresos as $egreso)
                    <tr >
                        <td class="border px-2 py-1">{{ $egreso->descripcion }}</td>
                        <td class="border px-2 py-1">${{ number_format($egreso->monto) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="mb-3 no-print">
                <a href="{{ route('caja.index') }}" class="btn btn-secondary">
                    Volver
                </a>

                <button onclick="window.print()" class="btn btn-primary">
                    🖨️ Imprimir Informe
                </button>
            </div>
        </div>
    </div>
</div>
<style>
@media print {

    body * {
        visibility: hidden;
    }

    h2{
        position: ; 
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

    .no-print {
        display: none !important;
    }
}
</style>
@endsection
