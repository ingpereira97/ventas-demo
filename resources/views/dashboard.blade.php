@extends('layouts.app')

@section('title', 'Sistema de Ventas - ALE & H')

@section('content')
<div class="container">
    <h1 class="mb-4">Dashboard de Ventas</h1>

    {{-- Filtro por rango de fechas --}}
    <form method="GET" class="row g-3 mb-4">
        <div class="col-md-4">
            <label for="start_date" class="form-label">Desde:</label>
            <input type="date" name="start_date" class="form-control" value="{{ $start }}">
        </div>
        <div class="col-md-4">
            <label for="end_date" class="form-label">Hasta:</label>
            <input type="date" name="end_date" class="form-control" value="{{ $end }}">
        </div>
        <div class="col-md-4 d-flex align-items-end">
            <button type="submit" class="btn btn-primary w-100">Filtrar</button>
        </div>
    </form>

    {{-- Gráfico --}}
    <canvas id="ventasChart" height="100"></canvas>

    {{-- Resumen total --}}
    <div class="row mt-5">
        @foreach($totales as $estado => $monto)
            <div class="col-md-4">
                <div class="alert alert-info text-center">
                    <h5>Total {{ ucfirst($estado) }}</h5>
                    <strong>${{ number_format($monto, 2) }}</strong>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Botones de exportación --}}
    <div class="mt-4">
        <button class="btn btn-outline-success"><i class="fas fa-file-excel"></i> Exportar a Excel</button>
        <button class="btn btn-outline-danger"><i class="fas fa-file-pdf"></i> Exportar a PDF</button>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('ventasChart').getContext('2d');
    const ventasChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($labels) !!},
            datasets: [
                {
                    label: 'Pagado',
                    data: {!! json_encode($data['pagado']) !!},
                    backgroundColor: 'rgba(75, 192, 192, 0.7)',
                },
                {
                    label: 'Pendiente',
                    data: {!! json_encode($data['pendiente']) !!},
                    backgroundColor: 'rgba(255, 206, 86, 0.7)',
                },
                {
                    label: 'Anulada',
                    data: {!! json_encode($data['anulada']) !!},
                    backgroundColor: 'rgba(255, 99, 132, 0.7)',
                }
            ]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Monto en $'
                    }
                }
            }
        }
    });
</script>
@endpush
