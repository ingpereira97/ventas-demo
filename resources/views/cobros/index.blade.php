@extends('layouts.app')

@section('title', 'Cobros')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-center mb-3">

        <h1 class="h3 mb-4">Listado de Cobros</h1>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
    </div>
    <div class="card">
        <div class="card-body table-responsive p-0">
            <table id="tabla-cobros" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Nro. Comprobante</th>
                        <th>Cliente</th>
                        <th>Monto Total</th>
                        <th>Monto Pagado</th>
                        <th>Saldo</th>                        
                        <th>Método de Pago</th>
                        <th>Fecha</th>
                        <th>Estado</th>
                        
                    </tr>
                </thead>
                <tbody>
                    @foreach($cobros as $cobro)
                        <tr>
                            <td>{{ $cobro->venta->nro_comprobante}}</td>
                            <td>{{ $cobro->venta->cliente->nombre ?? 'Ocasional' }}</td>
                            <td>Gs {{ number_format($cobro->venta->total) }}</td>
                            <td>Gs {{ number_format($cobro->monto_pagado, 0) }}</td>
                            <td>Gs {{ number_format($cobro->venta->saldo) }}</td>
                            <td>{{ $cobro->metodo_pago }}</td>
                            <td>{{ $cobro->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                @if($cobro->venta->estado == 'pendiente')
                                    <span class="badge bg-warning">Pendiente</span>
                                @else
                                    <span class="badge bg-success">Pagado</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="d-flex justify-content-between">
                <a href="{{ route('ventas.index') }}" class="btn btn-secondary">Volver a Ventas</a>
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
    $(document).ready(function () {

        var fechaActual = new Date().toLocaleDateString();

        $('#tabla-cobros').DataTable({
            order: [[6, 'desc']],

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
                    title: 'Listado de Cobros',
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
