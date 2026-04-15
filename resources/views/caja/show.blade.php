@extends('layouts.app')

@section('title', 'Informe de Caja - Cierre Diario')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-12 col-md-8">
            <div class="card">
                <div class="card-body">

                    <!-- Contenedor del informe -->
                    <div class="ticket" style="font-family: 'Courier New', monospace; width: 80%; margin: auto; padding: 10px; border: 1px solid #ddd;">

                        <h3 style="text-align:center; font-size:16px;">Informe de Caja Cerrada</h3>

                        <p style="font-size:12px;"><strong>Usuario:</strong> {{ $caja->user->name }}</p>
                        <p style="font-size:12px;"><strong>Fecha Apertura:</strong> {{ $caja->created_at->format('d/m/Y H:i') }}</p>
                        <p style="font-size:12px;"><strong>Fecha Cierre:</strong> {{ $caja->fecha_cierre ? $caja->fecha_cierre->format('d/m/Y H:i') : 'No cerrada' }}</p>

                        <hr style="border-top: 1px dashed #000; margin: 5px 0;">

                        <!-- Ventas -->
                        <h5 style="text-align:center; font-size:14px;">Ventas</h5>
                        <table style="width:100%; font-size:12px; border-collapse: collapse;">
                            <thead>
                                <tr>
                                    <th style="text-align:left;">Comprobante</th>
                                    <th style="text-align:left;">Cliente</th>
                                    <th style="text-align:right;">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($ventas as $venta)
                                <tr>
                                    <td>{{ $venta->nro_comprobante }}</td>
                                    <td>{{ $venta->cliente->nombre ?? 'Ocasional' }}</td>
                                    <td style="text-align:right;">${{ number_format($venta->total, 0) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <p style="text-align:right; font-weight:bold; font-size:12px;">Total Ventas: ${{ number_format($caja->total_ventas, 0) }}</p>

                        <hr style="border-top: 1px dashed #000; margin: 5px 0;">

                        <!-- Egresos -->
                        <h5 style="text-align:center; font-size:14px;">Egresos</h5>
                        <table style="width:100%; font-size:12px; border-collapse: collapse;">
                            <thead>
                                <tr>
                                    <th style="text-align:left;">Descripción</th>
                                    <th style="text-align:right;">Monto</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($egresos as $egreso)
                                <tr>
                                    <td>{{ $egreso->descripcion }}</td>
                                    <td style="text-align:right;">${{ number_format($egreso->monto, 0) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <p style="text-align:right; font-weight:bold; font-size:12px;">Total Egresos: ${{ number_format($caja->total_egresos, 0) }}</p>

                        <hr style="border-top: 1px dashed #000; margin: 5px 0;">

                        <!-- Total final -->
                        <p style="text-align:right; font-weight:bold; font-size:14px;">TOTAL FINAL: ${{ number_format($caja->total_final, 0) }}</p>

                        <hr style="border-top: 1px dashed #000; margin: 5px 0;">
                        <p style="text-align:center; font-size:10px;">¡Gracias! Informe generado automáticamente</p>

                    </div>

                    <!-- Botones -->
                    <div class="text-center mt-4 no-print">
                        <a href="{{ route('cajas.index') }}" class="btn btn-secondary">Volver al listado de cajas</a>
                        <button class="btn btn-primary" onclick="window.print()">Imprimir Informe</button>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<!-- Estilos para impresión -->
<style>
@media print {
    body { margin: 0; padding: 0; }
    .no-print { display: none !important; }
    .ticket { width: 80%; margin: 0 auto; padding: 5px; border: none; }
}
</style>
@endsection
