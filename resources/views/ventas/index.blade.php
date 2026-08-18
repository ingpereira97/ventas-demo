@extends('layouts.app')

@section('title', 'Ventas Registradas')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4">Ventas Registradas</h1>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="h4 mb-0">Listado de ventas</h2>
        <a href="{{ route('ventas.create') }}" class="btn btn-success">
            <i class="fas fa-plus"></i> Nueva venta
        </a>
    </div>

    <!-- 📅 BARRA DE FILTRO POR FECHA Y TOTAL DEL DÍA -->
    <div class="card mb-3">
        <div class="card-body py-2">
            <div class="row align-items-center">
                <!-- Filtro de Fecha -->
                <div class="col-md-7 d-flex align-items-center gap-2">
                    <!-- Opción limpia: Sin botón de filtrar -->
                    <form action="{{ route('ventas.index') }}" method="GET" class="d-flex align-items-center gap-2 mb-0">
                        <label for="fecha" class="fw-bold mb-0 text-nowrap">Ver ventas de: </label>
                        <input type="date" 
                            name="fecha" 
                            id="fecha" 
                            class="form-control form-control-sm" 
                            value="{{ $fecha }}" 
                            onchange="this.form.submit()">

                        @if($fecha != date('Y-m-d'))
                            <a href="{{ route('ventas.index') }}" class="btn btn-outline-secondary btn-sm text-nowrap">
                                <i class="fas fa-calendar-day"></i> Ver Hoy
                            </a>
                        @endif
                    </form>
                </div>

                <!-- Total del Día Grande (Derecha) -->
                <div class="col-md-5 text-md-end mt-3 mt-md-0">
                    <div class="d-inline-flex align-items-center bg-success text-white px-3 py-2 rounded-3 shadow-sm">
                        <i class="fas fa-cash-register fa-2x me-3 opacity-75"></i>
                        <div class="text-end">
                            <small class="d-block text-uppercase fw-semibold" style="font-size: 0.75rem; letter-spacing: 1px;">TOTAL DEL DÍA</small>
                            <span class="fs-2 fw-bold leading-none">
                                 Gs {{ number_format($totalDia ?? 0, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                </div>
                </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body table-responsive p-0">
            <table id="tabla-ventas" class="table table-hover text-nowrap">
                <thead>
                    <tr>
                        <th>Cliente</th>
                        <th>Fecha</th>
                        <th>Nro Comprobante</th>
                        <th>Total</th>
                        <th>Saldo</th>
                        <th>Estado</th>
                        <th class="no-print">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($ventas as $venta)
                        <tr>
                            <td>{{ optional($venta->cliente)->nombre ?? 'Ocasional' }}</td>
                            <td>{{ $venta->created_at->format('d/m/Y H:i') }}</td>
                            <td>{{ $venta->nro_comprobante }}</td>
                            <td>Gs {{ number_format($venta->total, 0, ',', '.') }}</td>
                            <td>
                                @if($venta->estado == 'pendiente')
                                    <span class="text-danger fw-bold">
                                        Gs {{ number_format($venta->saldo, 0, ',', '.') }}
                                    </span>
                                @else
                                    <span class="text-success">0</span>
                                @endif
                            </td>
                            <td>
                                @if($venta->estado == 'pendiente')
                                    <span class="badge bg-warning text-dark">
                                        🟡 Pendiente
                                    </span>
                                @elseif($venta->estado == 'pagado')
                                    <span class="badge bg-success">
                                        🟢 Pagado
                                    </span>
                                @elseif($venta->estado == 'anulada')
                                    <span class="badge bg-danger">
                                        🔴 Anulada
                                    </span>
                                @else
                                    <span class="badge bg-secondary">
                                        {{ ucfirst($venta->estado) }}
                                    </span>
                                @endif
                            </td>
                            <td>
                                {{-- ❌ SI ESTÁ ANULADA --}}
                                @if($venta->estado === 'anulada')
                                    <a href="{{ route('ventas.show', $venta->id) }}" 
                                       class="btn btn-danger btn-sm" title="Ver Recibo">
                                        <i class="fas fa-file-invoice"></i>
                                    </a>

                                {{-- 🔴 SI ESTÁ PENDIENTE --}}
                                @elseif($venta->estado === 'pendiente')
                                    <a href="{{ route('cobros.create', $venta->id) }}" 
                                       class="btn btn-success btn-sm" title="Cobrar">
                                        <i class="fas fa-dollar-sign"></i>
                                    </a>

                                    <button class="btn btn-danger btn-sm btn-anular" 
                                            onclick="anularVenta({{ $venta->id }})"
                                            title="Anular">
                                        <i class="fas fa-times"></i>
                                    </button>

                                    <a href="{{ route('ventas.show', $venta->id) }}" 
                                       class="btn btn-info btn-sm" title="Ver Recibo">
                                        <i class="fas fa-file-invoice"></i>
                                    </a>

                                {{-- 🟢 SI YA PAGÓ --}}
                                @else
                                    <a href="{{ route('ventas.show', $venta->id) }}" 
                                       class="btn btn-info btn-sm" title="Ver Recibo">
                                        <i class="fas fa-file-invoice"></i>
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<form id="anularForm" method="POST" style="display: none;">
    @csrf
    @method('PUT')
</form>

@endsection

@push('scripts')
    @if(session('vuelto') !== null)
    <script>
        Swal.fire({
            title: 'Cobro exitoso',
            text: 'Vuelto: Gs {{ number_format(session('vuelto'), 0, ',', '.') }}',
            icon: 'success',
            confirmButtonText: 'Aceptar'
        });
    </script>
    @endif

    <script>
        $(document).ready(function () {
            // Formatear la fecha seleccionada para los reportes de exportación
            var fechaSeleccionada = "{{ \Carbon\Carbon::parse($fecha)->format('d/m/Y') }}";

            $('#tabla-ventas').DataTable({
                order: [[1, 'desc']], // Ordenar por fecha desc

                dom: "<'row mb-3 align-items-center'<'col-md-6'B><'col-md-6 text-end'<'p-2'f>>>" +
                     "<'row'<'col-12'tr>>" +
                     "<'row mt-3'<'col-md-5'i><'col-md-7'p>>",

                buttons: [
                    {
                        extend: 'excel',
                        text: '📊 Excel',
                        className: 'btn btn-success btn-sm me-2',
                        exportOptions: {
                            columns: ':not(.no-print)'
                        }
                    },
                    {
                        extend: 'pdf',
                        text: '📄 PDF',
                        className: 'btn btn-danger btn-sm me-2',
                        exportOptions: {
                            columns: ':not(.no-print)'
                        },
                        title: 'Listado de Ventas',
                        messageTop: 'Fecha del informe: ' + fechaSeleccionada,

                        customize: function (doc) {
                            doc.pageMargins = [30, 50, 30, 50];

                            doc.styles.title = {
                                alignment: 'center',
                                fontSize: 16,
                                bold: true
                            };

                            doc.styles.tableHeader = {
                                bold: true,
                                fontSize: 11,
                                alignment: 'center',
                                fillColor: '#eeeeee'
                            };

                            doc.content[1].alignment = 'center';
                            var table = doc.content[2].table;

                            table.widths = Array(table.body[0].length).fill('*');

                            table.body.forEach(function(row) {
                                row.forEach(function(cell) {
                                    cell.alignment = 'center';
                                    cell.margin = [2, 4, 2, 4];
                                });
                            });
                        }
                    },
                    {
                        extend: 'print',
                        text: '🖨️ Imprimir',
                        className: 'btn btn-primary btn-sm',
                        exportOptions: {
                            columns: ':not(.no-print)'
                        },
                        customize: function (win) {
                            $(win.document.body)
                                .css({
                                    'font-size': '10pt',
                                    'text-align': 'center'
                                })
                                .prepend(
                                    '<div style="text-align:center; margin-bottom:15px;">' +
                                    '<small>Fecha del informe: ' + fechaSeleccionada + '</small>' +
                                    '</div>'
                                );

                            $(win.document.body).find('table')
                                .addClass('table table-bordered')
                                .css({
                                    'font-size': '10pt',
                                    'margin': 'auto',
                                    'width': '100%'
                                });

                            $(win.document.body).find('th, td').css({
                                'text-align': 'center',
                                'padding': '6px',
                                'border': '1px solid #ccc'
                            });
                        }
                    }
                ],

                language: {
                    decimal: ",",
                    thousands: ".",
                    lengthMenu: "Mostrar _MENU_ registros",
                    zeroRecords: "No se encontraron ventas para la fecha seleccionada",
                    info: "Página _PAGE_ de _PAGES_",
                    infoEmpty: "Sin registros",
                    infoFiltered: "(de _MAX_ total)",
                    search: "Buscar:",
                    paginate: {
                        first: "Primero",
                        last: "Último",
                        next: "→",
                        previous: "←"
                    }
                }
            });
        });

        // Evento para Anular Venta con SweetAlert
        function anularVenta(id) {
            Swal.fire({
                title: 'Anular venta',
                input: 'textarea',
                inputLabel: 'Motivo de anulación',
                inputPlaceholder: 'Escriba el motivo...',
                showCancelButton: true,
                confirmButtonText: 'Anular',
                cancelButtonText: 'Cancelar',
                inputValidator: (value) => {
                    if (!value) {
                        return 'Debes escribir un motivo';
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    let form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `{{ url('/ventas') }}/${id}/anular`;
                    form.innerHTML = `
                        @csrf
                        <input type="hidden" name="_method" value="PUT">
                        <input type="hidden" name="motivo" value="${result.value}">
                    `;

                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }
    </script>
@endpush