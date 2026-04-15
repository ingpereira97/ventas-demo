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

    <div class="d-flex justify-content-between mb-3">
        <h2 class="h4">Listado de ventas</h2>
        <a href="{{ route('ventas.create') }}" class="btn btn-success">
            <i class="fas fa-plus"></i> Nueva venta
        </a>
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
                            <td>${{ number_format($venta->total, 0) }}</td>
                             <td>
                                @if($venta->estado == 'pendiente')
                                    <span class="text-danger fw-bold">
                                        Gs {{ number_format($venta->saldo) }}
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
                                    <span class="badge bg-danger">Anulada</span>

                                {{-- 🔴 SI ESTÁ PENDIENTE --}}
                                @elseif($venta->estado === 'pendiente')

                                    {{-- 💰 COBRAR --}}
                                    <a href="{{ route('cobros.create', $venta->id) }}" 
                                        class="btn btn-success btn-sm">
                                        <i class="fas fa-dollar-sign"></i>
                                    </a>

                                    {{-- ❌ ANULAR --}}
                                    <button class="btn btn-danger btn-sm btn-anular" data-id="{{ $venta->id }}">
                                        <i class="fas fa-times"></i>
                                    </button>

                                    {{-- 🧾 RECIBO --}}
                                    <a href="{{ route('ventas.show', $venta->id) }}" 
                                        class="btn btn-info btn-sm">
                                        <i class="fas fa-file-invoice"></i>
                                    </a>

                                {{-- 🟢 SI YA PAGÓ --}}
                                @else

                                    {{-- 🧾 SOLO RECIBO --}}
                                    <a href="{{ route('ventas.show', $venta->id) }}" 
                                        class="btn btn-info btn-sm">
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
            text: 'Vuelto: ${{ number_format(session('vuelto'), 0) }}',
            icon: 'success',
            confirmButtonText: 'Aceptar'
        });
    </script>
     @endif
    <script>
        document.querySelectorAll('.btn-anular').forEach(button => {
            button.addEventListener('click', function () {
                const ventaId = this.getAttribute('data-id');
                Swal.fire({
                    title: '¿Estás seguro?',
                    text: 'Esta acción anulará la venta. No podrás revertirlo.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Sí, anular',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const form = document.getElementById('anularForm');
                            form.setAttribute('action', "{{ url('ventas') }}/" + ventaId + "/anular");                              
                            form.submit();
                    }
                });
            });
        });
    </script>
    <script>
    $(document).ready(function () {

        var fechaActual = new Date().toLocaleDateString();

        $('#tabla-ventas').DataTable({
            order: [[2, 'desc']],

            dom:"<'row mb-3 align-items-center'<'col-md-6'B><'col-md-6 text-end'<'p-2'f>>>" +
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
                    messageTop: 'Fecha: ' + fechaActual,

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

                        // Centrar tabla
                        doc.content[1].alignment = 'center';

                        var table = doc.content[2].table;

                        // Bordes más visibles
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
                                '<small>Fecha: ' + fechaActual + '</small>' +
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
                zeroRecords: "No se encontraron resultados",
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
</script>
@endpush
