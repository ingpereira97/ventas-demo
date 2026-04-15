@extends('layouts.app')

@section('content')

<div id="area-imprimir">
<h2 class="text-2xl font-bold mb-4 text-center text-gray-700">
    Informe de Caja
</h2>

<div class=" d-flex justify-content-center card p-3 mb-3 " style="max-width: 520px; ">
    <form method="GET" class="d-flex align-items-end gap-2 mb-3" style="max-width: 500px;">
        
            <div>
                <label class="form-label small mb-1">Desde</label>
                <input type="date" name="desde" value="{{ $desde }}" class="form-control form-control-sm">
            </div>

            <div>
                <label class="form-label small mb-1">Hasta</label>
                <input type="date" name="hasta" value="{{ $hasta }}" class="form-control form-control-sm">
            </div>
            <div>
                <h2></h2>
            </div>
            <div>
                <div style="margin-left: 10px;">
                    <button class="btn btn-primary btn-sm">
                        Filtrar
                    </button>
                </div>
            </div>
    
    </form>
 </div>
<br>

<div class="row text-center mb-4">

    <div class="col-md-3">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h6 class="text-muted">Total Ventas</h6>
                <h5 class="text-success">Gs {{ number_format($totalVentas) }}</h5>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h6 class="text-muted">Total Egresos</h6>
                <h5 class="text-danger">Gs {{ number_format($totalEgresos) }}</h5>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h6 class="text-muted">Total Compras</h6>
                <h5 class="text-warning">Gs {{ number_format($totalCompras) }}</h5>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h6 class="text-muted">Balance</h6>
                <h5 class="{{ $balance >= 0 ? 'text-success' : 'text-danger' }}">
                    {{ $balance >= 0 ? 'Ganancia' : 'Pérdida' }} 
                    Gs {{ number_format($balance) }}
                </h5>
            </div>
        </div>
    </div>

</div>

<hr>

{{-- VENTAS --}}
<h3 class="mb-3 text-gray-700">Cobros</h3>
<div class="table-responsive mb-4">
<table class="table table-hover table-striped align-middle shadow-sm">

    <thead class="table-dark">
        <tr>
            <th>Cliente</th>
            <th>Total</th>
            <th>Fecha</th>
        </tr>
    </thead>

    <tbody>
        @forelse($cobros as $cobro)
        <tr>
            <td class="fw-semibold">
                {{ $cobro->venta->cliente->nombre ?? 'Ocasional' }}
            </td>

            <td class="text-success fw-bold">
                Gs {{ number_format($cobro->monto_pagado) }}
            </td>

            <td class="text-muted">
                {{ $cobro->created_at->format('d/m/Y H:i') }}
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="3" class="text-center text-muted py-3">
                Sin Cobros
            </td>
        </tr>
        @endforelse
    </tbody>

</table>
</div>

{{-- EGRESOS --}}
<h3 class="mb-3 text-gray-700">Egresos</h3>
<div class="table-responsive mb-4">
<table class="table table-hover table-striped align-middle shadow-sm">

    <thead class="table-dark">
        <tr>
            <th>Descripción</th>
            <th>Monto</th>
        </tr>
    </thead>

    <tbody>
        @forelse($egresos as $egreso)
        <tr>
            <td>{{ $egreso->descripcion }}</td>

            <td class="text-danger fw-bold">
                Gs {{ number_format($egreso->monto) }}
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="2" class="text-center text-muted py-3">
                Sin egresos
            </td>
        </tr>
        @endforelse
    </tbody>

</table>
</div>

{{-- COMPRAS --}}
<h3 class="mb-3 text-gray-700">Compras</h3>
<div class="table-responsive mb-4">
<table class="table table-hover table-striped align-middle shadow-sm">

    <thead class="table-dark">
        <tr>
            <th>Proveedor</th>
            <th>Total</th>
        </tr>
    </thead>

    <tbody>
        @forelse($compras as $compra)
        <tr>
            <td class="fw-semibold">
                {{ $compra->proveedor->nombre ?? '' }}
            </td>

            <td class="text-warning fw-bold">
                Gs {{ number_format($compra->total) }}
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="2" class="text-center text-muted py-3">
                Sin compras
            </td>
        </tr>
        @endforelse
    </tbody>

</table>
</div>
</div> {{-- 👈 CIERRE DEL AREA IMPRIMIBLE --}}
<div class="text-end">
    <button onclick="window.print()" class="btn btn-success">
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

    table {
        font-size: 12px;
    }

    button, form {
        display: none !important;
    }
}
</style>

@endsection