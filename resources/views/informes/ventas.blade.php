@extends('layouts.app')

@section('content')

<h2>Informe de Ventas</h2>

<form method="GET">

    Desde:
    <input type="date" name="desde" value="{{ $desde }}">

    Hasta:
    <input type="date" name="hasta" value="{{ $hasta }}">

    <button class="btn btn-primary">Filtrar</button>

</form>

<br>

<div class="row text-center mb-4">

    <div class="col-md-4">
        <div class="card shadow-sm">
            <div class="card-body">
                <h6 class="text-success">Pagadas</h6>
                <h5>Gs {{ number_format($totalPagadas) }}</h5>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card shadow-sm">
            <div class="card-body">
                <h6 class="text-warning">Pendientes</h6>
                <h5>Gs {{ number_format($totalPendientes) }}</h5>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card shadow-sm">
            <div class="card-body">
                <h6 class="text-danger">Anuladas</h6>
                <h5>Gs {{ number_format($totalAnuladas) }}</h5>
            </div>
        </div>
    </div>

</div>
<div class="mb-3" style="max-width: 250px;">
    <label class="form-label fw-semibold">Filtrar por estado</label>
    <select id="filtroEstado" class="form-control form-control-sm">
        <option value="">Todos</option>
        <option value="pagado">Pagados</option>
        <option value="pendiente">Pendientes</option>
        <option value="anulada">Anuladas</option>
    </select>
</div>

<div class="d-flex justify-content-center mb-3">
    <div class="table-responsive shadow-sm rounded" style="max-width: 1500px; width:100%;">
        <table id="tabla-ventas" class="table table-hover align-middle mb-0">

        <thead class="table-dark text-center">
            <tr>
                <th>#</th>
                <th class="text-start">Cliente</th>
                <th>Fecha</th>
                <th>Estado</th>
                <th class="text-end">Total</th>
            </tr>
        </thead>

        <tbody>

            @forelse($ventas as $venta)

            <tr class="fila-venta" data-total="{{ $venta->total }}">

                <td class="text-center fw-semibold">
                    {{ $venta->id }}
                </td>

                <td class="text-center fw-semibold">
                    {{ $venta->cliente->nombre ?? 'Ocasional' }}
                </td>

                <td class="text-center text-muted">
                    {{ $venta->created_at->format('d/m/Y H:i') }}
                </td>

                <td class="text-center" data-estado="{{ $venta->estado }}">
                    @if($venta->estado == 'pendiente')
                        <span class="badge rounded-pill bg-warning text-dark px-3 py-2">
                            ⏳ Pendiente
                        </span>
                    @elseif($venta->estado == 'pagado')
                        <span class="badge rounded-pill bg-success px-3 py-2">
                            ✔ Pagado
                        </span>
                    @elseif($venta->estado == 'anulada')
                        <span class="badge rounded-pill bg-danger px-3 py-2">
                            ✖ Anulada
                        </span>
                    @endif
                </td>

                <td class="text-center fw-bold text-success">
                    Gs {{ number_format($venta->total) }}
                </td>

            </tr>

            @empty

            <tr>
                <td colspan="5" class="text-center text-muted py-4">
                    No hay ventas en este rango de fechas
                </td>
            </tr>

            @endforelse

        </tbody>

        </table>
    </div>
</div>

<div class="mb-3">
    <h5 class="mb-0 fw-bold">
        Total: <span id="total-dinamico" class="text-success" data-total="{{ $total }}">
            Gs {{ number_format($total) }}
        </span>
    </h5>
</div>
<button onclick="window.print()" class="btn btn-success no-print">
Imprimir
</button>
<script>
    document.addEventListener('DOMContentLoaded', function () {

        const filtro = document.getElementById('filtroEstado');
        const filas = document.querySelectorAll('#tabla-ventas tbody tr');
        const totalSpan = document.getElementById('total-dinamico');

        filtro.addEventListener('change', function () {

            const valor = this.value;
            let total = 0;

            filas.forEach(fila => {

                const estado = fila.querySelector('td[data-estado]').dataset.estado;
                const monto = parseFloat(fila.dataset.total) || 0;

                if (valor === '' || estado === valor) {
                    fila.style.display = '';
                    total += monto;
                } else {
                    fila.style.display = 'none';
                }

            });

            // 🔥 actualizar total dinámico
            totalSpan.innerText = 'Gs ' + total.toLocaleString();

            if (valor === 'pendiente') {
                totalSpan.className = 'text-warning fw-bold';
            } else if (valor === 'anulada') {
                totalSpan.className = 'text-danger fw-bold';
            } else {
                totalSpan.className = 'text-success fw-bold';
            }

        });

    });
</script>
@endsection