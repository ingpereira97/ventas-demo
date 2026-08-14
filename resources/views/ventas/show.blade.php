@extends('layouts.app')

@section('title', 'Venta - Recibo')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-12 col-md-4">
            <div class="card">
                <div class="card-body">

                    <!-- Contenedor del ticket para POS 58mm -->
                    <div class="ticket">
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
                        <h3 style="text-align:center; font-size:20px; font-weight:bold;">Pa'i Perez casi Tte. Gutierrez - Itauguá - Paraguay</h3>
                        <h3 style="text-align:center; font-size:20px; font-weight:bold;">Cel.: (0987) 485 836</h3>

                        <p style="font-size:20px; font-weight:bold;"><strong>Cliente:</strong> {{ $venta->cliente->nombre ?? 'Ocasional' }}</p>
                        <p style="font-size:20px; font-weight:bold;"><strong>Fecha:</strong> {{ $venta->created_at->format('d/m/Y H:i') }}</p>
                        <p style="font-size:20px; font-weight:bold;"><strong>Nro. Comprobante:</strong> {{ $venta->nro_comprobante }}</p>
                        @if($venta->estado === 'anulada')
                            <hr style="border-top: 2px solid red; margin: 5px 0;">

                            <div style="font-size:20px; font-weight:bold; color:red; text-align:center;">
                                DETALLE DE ANULACIÓN
                            </div>

                            <hr style="border-top: 1px dashed red; margin: 5px 0;">

                            <div style="font-size:20px; font-weight:bold;">
                                Motivo:
                            </div>

                            <div style="font-size:13px;">
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

                        <table style="width:100%; font-size:18px; border-collapse: collapse;">
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
                                            Gs.{{ number_format($producto->pivot->subtotal / $producto->pivot->cantidad, 0, ',', '.') }} /Kg
                                        @else
                                            Gs.{{ number_format($producto->pivot->subtotal / $producto->pivot->cantidad, 0, ',', '.') }}
                                        @endif
                                    </td> 
                                    <td style="text-align:center;">Gs.{{ number_format($producto->pivot->cantidad * $producto->precio, 0, ',', '.') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <hr style="border-top: 1px dashed #000; margin: 2px 0;">
                        
                        {{-- 🔥 DESGLOSE DE TOTALES Y DELIVERY --}}
                        @if($venta->costo_delivery > 0)
                            <div style="display: flex; justify-content: space-between; font-size:18px; font-weight:bold;">
                                <span>Subtotal Productos:</span>
                                <span>Gs.{{ number_format($venta->total - $venta->costo_delivery, 0, ',', '.') }}</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; font-size:18px; font-weight:bold;">
                                <span>Delivery:</span>
                                <span>Gs.{{ number_format($venta->costo_delivery, 0, ',', '.') }}</span>
                            </div>
                            <hr style="border-top: 1px dashed #000; margin: 2px 0;">
                        @endif

                        <p style="text-align:center; font-weight:bold; font-size:22px; margin: 4px 0;">
                            TOTAL: Gs.{{ number_format($venta->total, 0, ',', '.') }}
                        </p>

                        @if($venta->cobros && $venta->cobros->count() > 0)

                            <hr style="border-top: 1px dashed #000; margin: 2px 0;">

                            <p style="text-align:center; font-weight:bold; font-size:20px;">
                                    PAGOS REALIZADOS
                            </p>

                                @forelse($venta->cobros as $cobro)
                                    <div style="text-align:center; font-size:20px; font-weight:bold;">
                                     
                                        <hr style="border-top: 1px dashed #000; margin: 2px 0;">

                                        Fecha: {{ $cobro->created_at->format('d/m/Y H:i') }} <br>
                                        Entregado: Gs {{ number_format($cobro->monto_pagado, 0, ',', '.') }} <br>
                                        Vuelto: Gs {{ number_format($cobro->monto_pagado - $cobro->monto_aplicado, 0, ',', '.') }}

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
                                Saldo: Gs.{{ number_format($venta->saldo, 0, ',', '.') }}
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
                        <a href="{{ url()->previous() }}" class="btn btn-secondary">Volver</a>
                        <button class="btn btn-primary" onclick="window.print()">Imprimir Recibo</button>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* ==========================================
   1. ESTILOS PARA LA PANTALLA (Navegador)
   ========================================== */
.ticket {
    font-family: 'Courier New', Courier, monospace;
    width: 100%;
    max-width: 520px;
    margin: 10px auto;
    padding: 10px;
    border: 1px solid #ccc;
    background-color: #fff;
    color: #000;
    font-size: 13px;
}
.card {
    width: 600px;    
}

/* ==========================================
   2. ESTILOS EXCLUSIVOS DE IMPRESIÓN (Epson TM-U220)
   ========================================== */
@page {
    size: 76mm auto; /* Fuerza el tamaño de bobina de 76mm */
    margin: 0;       /* Elimina los márgenes predeterminados de la hoja */
}

@media print {
    /* Oculta todo el contenido de la web excepto el ticket */
    body * {
        visibility: hidden;
    }

    .no-print {
        display: none !important;
    }

    /* Muestra únicamente el ticket centrado en el área de impresión */
    .ticket, .ticket * {
        visibility: visible;
    }

    .ticket {
        position: absolute;
        left: 0;
        top: 0;
        width: 72mm !important;       /* Ancho imprimible exacto */
        max-width: 72mm !important;
        margin: 0 !important;
        padding: 0 !important;
        border: none !important;
        font-family: 'Courier New', Courier, monospace !important;
        font-size: 12px !important;    /* Tamaño legible para agujas de matriz */
        line-height: 1.2 !important;
        color: #000 !important;
    }

    /* Configuración de tabla para no desbordar */
    .ticket table {
        width: 100% !important;
        border-collapse: collapse !important;
        table-layout: fixed !important;
        font-size: 10px !important;
    }

    .ticket table th,
    .ticket table td {
        padding: 2px 0 !important;
        word-wrap: break-word;
    }

    .ticket table th:nth-child(1), 
    .ticket table td:nth-child(1) { 
        width: 28% !important; 
        text-align: left !important; 
    }

    .ticket table th:nth-child(2), 
    .ticket table td:nth-child(2) { 
        width: 18% !important; 
        text-align: center !important; 
    }

    .ticket table th:nth-child(3), 
    .ticket table td:nth-child(3) { 
        width: 27% !important; 
        text-align: right !important; 
        white-space: nowrap !important;
    }

    .ticket table th:nth-child(4), 
    .ticket table td:nth-child(4) { 
        width: 27% !important; 
        text-align: right !important; 
        white-space: nowrap !important;
    }

    /* Separadores punteados limpios para cinta de tinta */
    .ticket hr {
        border: none !important;
        border-top: 1px dashed #000 !important;
        margin: 3px 0 !important;
    }

    /* Textos y títulos */
    .ticket h3 {
        font-size: 13px !important;
        font-weight: bold !important;
        margin: 2px 0 !important;
        text-align: center;
    }

    .ticket p, 
    .ticket div, 
    .ticket span {
        font-size: 12px !important;
        margin: 1px 0 !important;
    }

    /* Optimización de logo para impresora matricial */
    .ticket img {
        max-width: 100% !important;
        height: auto !important;
        filter: grayscale(100%);
    }
}
</style>

@endsection