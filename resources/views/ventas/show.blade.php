@extends('layouts.app')

@section('title', 'Venta - Recibo')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-12 col-md-9">
            <div class="card">
                <div class="card-body">

                    <!-- Contenedor del ticket para POS 58mm -->
                    <div class="ticket" style="font-family: 'Courier New', monospace; width: 260mm; margin: auto; padding: 5px; border: 1px solid #ddd;">

                        <h3 style="text-align:center; font-size:75px; font-weight:bold;">Recibo de Venta</h3>
                        <div style="text-align:center; margin-bottom:10px;">
                            <img src="{{ asset('img/AleyH.png') }}" 
                                style="max-width:250px;">
                        </div>
                        <h3 style="text-align:center; font-size:30px; font-weight:bold;">Pa'i Perez casi Tte. Gutierrez - Itauguá - Paraguay</h3>
                        <h3 style="text-align:center; font-size:30px; font-weight:bold;">Cel.: (0983) 460 212</h3>

                        <p style="font-size:30px; font-weight:bold;"><strong>Cliente:</strong> {{ $venta->cliente->nombre ?? 'Ocasional' }}</p>
                        <p style="font-size:30px; font-weight:bold;"><strong>Fecha:</strong> {{ $venta->created_at->format('d/m/Y H:i') }}</p>
                        <p style="font-size:30px; font-weight:bold;"><strong>Nro. Comprobante:</strong> {{ $venta->nro_comprobante }}</p>

                        <hr style="border-top: 1px dashed #000; margin: 2px 0;">

                        <table style="width:100%; font-size:30px; border-collapse: collapse;">
                            <thead>
                                <tr>
                                    <th style="text-align:left;">Producto</th>
                                    <th style="text-align:center;">Cant</th>
                                    <th style="text-align:center;">Precio</th>
                                    <th style="text-align:center;">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($venta->productos as $producto)
                                <tr style="font-weight:bold;">
                                    <td>{{ $producto->nombre }}</td>
                                    <td style="text-align:center;">
                                        @if($producto->tipo == 'peso')

                                            @if($producto->pivot->cantidad < 1)
                                                {{ $producto->pivot->cantidad * 1000 }} g
                                            @else
                                                {{ number_format($producto->pivot->cantidad) }} Kg
                                            @endif

                                        @else
                                            {{ $producto->pivot->cantidad }}
                                        @endif
                                    </td>
                                    <td style="text-align:center;">
                                        @if($producto->tipo == 'peso')
                                            Gs.{{ number_format($producto->pivot->subtotal / $producto->pivot->cantidad, 0) }} / Kg
                                        @else
                                            Gs.{{ number_format($producto->pivot->subtotal / $producto->pivot->cantidad, 0) }}
                                        @endif
                                    </td> 
                                    <td style="text-align:center;">Gs.{{ number_format($producto->pivot->cantidad * $producto->precio, 0) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <hr style="border-top: 1px dashed #000; margin: 2px 0; font-size:55px;">
                        <p style="text-align:center; font-weight:bold; font-size:55px;">TOTAL: Gs.{{ number_format($venta->total, 0) }}</p>
                        @if($venta->cobros && $venta->cobros->count() > 0)

                            <hr style="border-top: 1px dashed #000; margin: 2px 0;">

                            <p style="text-align:center; font-weight:bold; font-size:45px;">
                                    PAGOS REALIZADOS
                            </p>

                                @forelse($venta->cobros as $cobro)
                                    <div style="text-align:center; font-size:30px; font-weight:bold;">
                                     
                                        <hr style="border-top: 1px dashed #000; margin: 2px 0;">

                                        Fecha: {{ $cobro->created_at->format('d/m/Y H:i') }} <br>
                                        Entregado: Gs {{ number_format($cobro->monto_pagado) }} <br>
                                        Vuelto: Gs {{ number_format($cobro->monto_pagado - $cobro->monto_aplicado) }}

                                        
                                    </div>
                                @empty
                                    <p class="text-muted">Sin pagos registrados</p>
                                @endforelse
                        @endif
                            <hr style="text-align:center; border-top: 1px dashed #000; margin: 2px 0;">
                        {{-- 🔥 ESTADO DE PAGO --}}
                        @if($venta->estado == 'pendiente')

                        <hr style="border-top: 1px dashed #000; margin: 2px 0;">

                            <p style="text-align:center; font-weight:bold; font-size:55px; color:red;">
                                ⚠️ PENDIENTE DE PAGO
                            </p>
                        <hr style="border-top: 1px dashed #000; margin: 2px 0;">

                            <p style="text-align:center; font-weight:bold; font-size:50px;">
                                Saldo: Gs.{{ number_format($venta->saldo, 0) }}
                            </p>

                        @else

                            <p style="text-align:center; font-weight:bold; font-size:55px; color:green;">
                                ✔️ PAGADO
                            </p>

                        @endif

                        <hr style="border-top: 1px dashed #000; margin: 2px 0;">
                        <p style="text-align:center; font-weight:bold; font-size:55px;">¡Gracias por su compra!</p>
                        <p style="text-align:center; font-size:55px;">--------------------------</p>
                      

                    </div>

                    <!-- Botones de navegación -->
                    <div class="text-center mt-4 no-print">
                        <a href="{{ route('ventas.index') }}" class="btn btn-secondary">Volver al listado de ventas</a>
                        <button class="btn btn-primary" onclick="window.print()">Imprimir Recibo</button>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    body {
        margin: 0;
        padding: 0;
    }

    .no-print {
        display: none; /* Oculta botones */
    }

    .ticket {
        width: 58mm; /* Tamaño del POS mini 58mm */
        margin: 0;
        padding: 5px;
        border: none;
        font-family: 'Courier New', monospace;
        font-size: 12px; /* Tamaño general */
    }

    .ticket hr {
        border-top: 1px dashed #000;
        margin: 2px 0;
    }

    .ticket table {
        width: 100%;
        border-collapse: collapse;
        font-size: 16px;
    }

    .ticket table th,
    .ticket table td {
        padding: 2px 0;
    }
}
</style>

@endsection
