@extends('layouts.app')

@section('title', 'Venta - Recibo')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-12 col-md-4">
            <div class="card">
                <div class="card-body">

                    <!-- Contenedor del ticket para POS 58mm -->
                    <div class="ticket" style="
                        position: relative;
                        font-family: 'Courier New', monospace;
                        width: 100%;
                        max-width: 380px;
                        margin: auto;
                        padding: 5px;
                        border: 1px solid #ddd;
                    ">
                        @if($venta->estado === 'anulada')
                            <div style="
                                position:absolute;
                                top:40%;
                                left:10%;
                                font-size:60px;
                                color:red;
                                opacity:0.1;
                                transform:rotate(-30deg);
                                pointer-events:none;
                                z-index:0;
                                width:100%;
                                text-align:center;
                            ">
                                ANULADA
                            </div>
                        @endif
                        <div style="text-align:center; margin-bottom:10px;">
                            <img src="{{ asset('img/AleyH.png') }}" 
                                style="max-width:200px; padding:7px;">
                        </div>
                        <h3 style="text-align:center; font-size:12px; font-weight:bold;">Pa'i Perez casi Tte. Gutierrez - Itauguá - Paraguay</h3>
                        <h3 style="text-align:center; font-size:12px; font-weight:bold;">Cel.: (0983) 460 212</h3>

                        <p style="font-size:12px; font-weight:bold;"><strong>Cliente:</strong> {{ $venta->cliente->nombre ?? 'Ocasional' }}</p>
                        <p style="font-size:12px; font-weight:bold;"><strong>Fecha:</strong> {{ $venta->created_at->format('d/m/Y H:i') }}</p>
                        <p style="font-size:12px; font-weight:bold;"><strong>Nro. Comprobante:</strong> {{ $venta->nro_comprobante }}</p>
                        @if($venta->estado === 'anulada')
                            <hr style="border-top: 2px solid red; margin: 5px 0;">

                            <div style="font-size:20px; font-weight:bold; color:red; text-align:center;">
                                DETALLE DE ANULACIÓN
                            </div>

                            <hr style="border-top: 1px dashed red; margin: 5px 0;">

                            <div style="font-size:20px; font-weight:bold;">
                                Motivo:
                            </div>

                            <div style="font-size:12px;">
                                {{ $venta->motivo_anulacion }}
                            </div>

                            <br>

                            <div style="font-size:20px;">
                                <strong>Anulado por:</strong> 
                                {{ $venta->usuarioAnulo->name ?? 'N/A' }}
                            </div>

                            <div style="font-size:20px;">
                                <strong>Fecha de anulación:</strong> 
                                {{ $venta->updated_at->format('d/m/Y H:i') }}
                            </div>

                            <hr style="border-top: 2px solid red; margin: 5px 0;">
                        @endif

                        <hr style="border-top: 1px dashed #000; margin: 2px 0;">

                        <table style="width:100%; font-size:12px; border-collapse: collapse;">
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
                                        @php
                                            $cantidad = $producto->pivot->cantidad;
                                        @endphp

                                        @if($producto->tipo == 'peso')

                                            @if($cantidad < 1)
                                                {{ number_format($cantidad * 1000, 0, ',', '.') }} g
                                            @else
                                                {{ round($cantidad, 3) == round($cantidad) 
                                                    ? number_format($cantidad, 0, '.', ',') 
                                                    : number_format($cantidad, 3, '.', ',') }} Kg
                                            @endif

                                        @else
                                            {{ number_format($cantidad, 0, ',', '.') }}
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

                        <hr style="border-top: 1px dashed #000; margin: 2px 0; font-size:20px;">
                        <p style="text-align:center; font-weight:bold; font-size:22px;">TOTAL: Gs.{{ number_format($venta->total, 0) }}</p>
                        @if($venta->cobros && $venta->cobros->count() > 0)

                            <hr style="border-top: 1px dashed #000; margin: 2px 0;">

                            <p style="text-align:center; font-weight:bold; font-size:20px;">
                                    PAGOS REALIZADOS
                            </p>

                                @forelse($venta->cobros as $cobro)
                                    <div style="text-align:center; font-size:20px; font-weight:bold;">
                                     
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
                        @if($venta->estado == 'anulada')

                            <p style="text-align:center; font-weight:bold; font-size:20px; color:red;">
                                ❌ ANULADA
                            </p>

                        @elseif($venta->estado == 'pendiente')

                        <hr style="border-top: 1px dashed #000; margin: 2px 0;">

                            <p style="text-align:center; font-weight:bold; font-size:20px; color:red;">
                                ⚠️ PENDIENTE DE PAGO
                            </p>
                        <hr style="border-top: 1px dashed #000; margin: 2px 0;">

                            <p style="text-align:center; font-weight:bold; font-size:22px;">
                                Saldo: Gs.{{ number_format($venta->saldo, 0) }}
                            </p>

                        @else

                            <p style="text-align:center; font-weight:bold; font-size:22px; color:green;">
                                ✔️ PAGADO
                            </p>

                        @endif

                        <hr style="border-top: 1px dashed #000; margin: 2px 0;">
                        @if($venta->estado === 'anulada')
                            <p style="text-align:center; font-weight:bold; font-size:20px; color:red;">
                                ⚠️ COMPROBANTE ANULADO
                            </p>
                        @else
                            <p style="text-align:center; font-weight:bold; font-size:22px;">
                                ¡Gracias por su compra!
                            </p>
                        @endif
                        <p style="text-align:center; font-size:22px;">--------------------------</p>
                      

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

@page {
    size: 76mm auto;
    margin: 0;
}

@media print {

    body {
        margin: 0;
        padding: 0;
    }

    .no-print {
        display: none; /* Oculta botones */
    }

    .card {
        border: none;
        box-shadow: none;
    }

    .ticket {
        width: 76mm !important;
        max-width: 76mm !important;
        margin: 0 auto;
        padding: 2mm;
        border: none;
        font-family: 'Courier New', monospace;
        font-size: 10px;
    }

    .ticket hr {
        border-top: 1px dashed #000;
        margin: 1px 0;
    }

    .ticket table {
        width: 100%;
        border-collapse: collapse;
        font-size: 10px;
    }

    .ticket table th,
    .ticket table td {
        padding: 2px 0;
    }

    img {
        max-width: 120px !important;
        height: auto;
    }

    h3 {
        font-size: 11px !important;
        margin: 2px 0;
    }

    p,
    div,
    span {
        font-size: 10px !important;
        margin: 1px 0;
    }
}
</style>

@endsection