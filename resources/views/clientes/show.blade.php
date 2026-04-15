@extends('layouts.app')

@section('content')

<div class="container">

        <h3>Cliente: {{ $cliente->nombre }}</h3>

        <hr>
        <div class="mb-3" style="max-width: 250px;">
            <label class="form-label" style="text-align:right;">Filtrar ventas</label>
            <select id="filtroVentas" class="form-control form-control-sm">
                <option value="todas">Todas</option>
                <option value="pendientes">Pendientes</option>
                <option value="parciales">Pagos Parciales</option>
                <option value="pagadas">Pagadas</option>
            </select>
        </div>
        <div class="mb-3" style="max-width: 250px; text-align:right;">
            <input type="text" id="buscarVenta" 
                class="form-control form-control-sm"
                placeholder="Buscar...">
        </div>
        <div id="sinResultados" class="text-center text-muted mt-6" style="display:none; font-size:20px;">
            <i class="fas fa-search"> No hay coincidencias </i><br>
            
        </div>
        
    <div id="tabla-pendientes">
        <h5 class="text-danger">Ventas Pendientes</h5>

        <table class="table table-bordered text-center align-middle">
            <thead class="table-dark">
                <tr>
                    <th>Fecha</th>
                    <th>Comprobante</th>
                    <th>Total</th>
                    <th>Saldo</th>
                    <th>Progreso</th>
                    <th>Acción</th>
                </tr>
            </thead>

            <tbody>
            @forelse($ventasPendientes as $venta)
                <tr class="fila-venta">
                    <td class="fecha">{{ $venta->created_at->format('d/m/Y H:i') }}</td>
                    <td class="comprobante">{{ $venta->nro_comprobante }}</td>
                    <td class="monto">Gs {{ number_format($venta->total) }}</td>
                    <td class="text-danger fw-bold">Gs {{ number_format($venta->saldo) }}</td>

                    <td>
                        <div class="progress" style="height: 10px;">
                            <div class="progress-bar bg-danger" style="width: 0%"></div>
                        </div>
                    </td>

                    <td>
                        <a href="{{ route('cobros.create', $venta->id) }}" class="btn btn-success btn-sm">
                            💲 Cobrar
                        </a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6">Sin pendientes</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    

    <div id="tabla-parciales">
        <h5 class="text-warning">Pagos Parciales</h5>

        <table class="table table-bordered text-center align-middle">
            <thead class="table-dark">
                <tr>
                    <th>Fecha</th>
                    <th>Comprobante</th>
                    <th>Total</th>
                    <th>Saldo</th>
                    <th>Progreso</th>
                    <th>Acción</th>
                    <th>Ver Detalle</th>
                    
                </tr>
            </thead>

            <tbody>
            @forelse($ventasParciales as $venta)

                @php
                    $pagado = $venta->total - $venta->saldo;
                    $porcentaje = ($pagado / $venta->total) * 100;
                @endphp

                <tr class="fila-venta">
                    <td class="fecha">{{ $venta->created_at->format('d/m/Y H:i') }}</td>
                    <td class="comprobante">{{ $venta->nro_comprobante }}</td>

                    <td class="monto">Gs {{ number_format($venta->total) }}</td>

                    <td class="text-warning fw-bold">
                        Gs {{ number_format($venta->saldo) }}
                    </td>

                    <td>
                        <div class="progress" style="height: 10px;">
                            <div class="progress-bar bg-warning"
                                style="width: {{ $porcentaje }}%">
                            </div>
                        </div>

                        <small>
                            {{ number_format($porcentaje, 0) }}% pagado
                        </small>
                    </td>

                    <td>
                        <a href="{{ route('cobros.create', $venta->id) }}"
                            class="btn btn-success btn-sm">
                            💲 Cobrar
                        </a>
                    </td>
                    <td>
                        <a href="{{ route('ventas.show', $venta->id) }}"
                        class="btn btn-info btn-sm">
                        👁️ Detalle
                        </a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="9">Sin pagos parciales</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div id="tabla-pagadas">
        <hr>

        <h5 class="text-success">Ventas Pagadas</h5>

        <table class="table table-bordered text-center align-middle">
            <thead class="table-dark">
                <tr>
                    <th>Fecha Venta</th>
                    <th>Fecha Ultimo Pago</th>
                    <th>Comprobante</th>
                    <th>Total</th>
                    <th></th>
                    
                </tr>
            </thead>
            <tbody>
                @foreach($ventasPagadas as $venta)
            <tr class="fila-venta">
                    <td class="fecha">{{ $venta->created_at->format('d/m/Y H:i') }}</td>

                    <td>
                        {{ optional($venta->cobros->last())->created_at?->format('d/m/Y H:i') }}
                    </td>

                    <td class="comprobante">{{ $venta->nro_comprobante }}</td>

                    <td class="monto text-success fw-bold">
                        Gs {{ number_format($venta->total) }}
                    </td>

                    <td>
                        <a href="{{ route('ventas.show', $venta->id) }}"
                        class="btn btn-info btn-sm">
                        👁️ Ver detalle
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@endsection
@push('scripts')
<script>
document.getElementById('filtroVentas').addEventListener('change', function() {

    let valor = this.value;

    let pendientes = document.getElementById('tabla-pendientes');
    let parciales = document.getElementById('tabla-parciales');
    let pagadas = document.getElementById('tabla-pagadas');

    // Ocultar todo
    pendientes.style.display = 'none';
    parciales.style.display = 'none';
    pagadas.style.display = 'none';

    if (valor === 'todas') {
        pendientes.style.display = '';
        parciales.style.display = '';
        pagadas.style.display = '';
    }

    if (valor === 'pendientes') {
        pendientes.style.display = '';
    }

    if (valor === 'parciales') {
        parciales.style.display = '';
    }

    if (valor === 'pagadas') {
        pagadas.style.display = '';
    }

});
</script>
<script>
    document.getElementById('buscarVenta').addEventListener('keyup', function() {

        let filtro = this.value.toLowerCase();
        let filas = document.querySelectorAll('.fila-venta');
        let visibles = 0;

        filas.forEach(function(fila) {

            let comprobante = fila.querySelector('.comprobante')?.innerText.toLowerCase() || '';
            let fecha = fila.querySelector('.fecha')?.innerText.toLowerCase() || '';
            let monto = fila.querySelector('.monto')?.innerText.toLowerCase() || '';

            let textoCompleto = comprobante + " " + fecha + " " + monto;

            if (textoCompleto.includes(filtro)) {
                fila.style.display = '';
                visibles++;
            } else {
                fila.style.display = 'none';
            }

        });

        // 🔥 Mostrar mensaje si no hay resultados
        let mensaje = document.getElementById('sinResultados');

        if (visibles === 0) {
            mensaje.style.display = '';
        } else {
            mensaje.style.display = 'none';
        }

        actualizarTablas();
    
        function actualizarTablas() {

        ['tabla-pendientes','tabla-parciales','tabla-pagadas'].forEach(id => {

            let tabla = document.getElementById(id);
            let visibles = tabla.querySelectorAll('.fila-venta:not([style*="display: none"])');

            tabla.style.display = visibles.length ? '' : 'none';
        });

    }

    actualizarTablas();
}); 
</script>
@endpush