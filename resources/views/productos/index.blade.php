@extends('layouts.app')

@section('title', 'Productos')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between mb-3">
            <h1 class="h3">Listado de productos</h1>
            <a href="{{ route('productos.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nuevo producto
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif


        <div class="card">
            <div class="card-body table-responsive p-0">
                <table id="tabla-productos" class="table table-hover text-nowrap">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nombre</th>
                            <th>Descripción</th>
                            <th>Precio</th>
                            <th>Stock</th>
                            <th class="no-print">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($productos as $producto)
                            <tr>
                                <td>{{ $producto->id }}</td>
                                <td>{{ $producto->nombre }}</td>
                                <td>{{ $producto->descripcion }}</td>
                                <td>{{ number_format($producto->precio, 0) }}</td>
                               <td>
                                    @php
                                        $claseStock = '';

                                        if ($producto->stock == 0) {
                                            $claseStock = 'text-danger fw-bold'; // 🔴 rojo
                                        } elseif ($producto->stock <= 3) {
                                            $claseStock = 'text-warning fw-bold'; // 🟡 amarillo
                                        }
                                    @endphp

                                    <span class="{{ $claseStock }}">

                                        @if($producto->tipo == 'peso')

                                            @if($producto->stock < 1)
                                                {{ $producto->stock * 1000 }} g
                                            @else
                                                {{ number_format($producto->stock, 2) }} Kg
                                            @endif

                                        @else
                                            {{ number_format($producto->stock, 0) }} 
                                        @endif

                                    </span>
                                </td>
                                <td class="no-print">
                                    <a href="{{ route('productos.edit', $producto) }}" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('productos.destroy', $producto) }}" method="POST" style="display:inline-block;">
                                        @csrf
                                        @method('DELETE')
                                       <!-- Botón para abrir el modal -->
                                        <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#modalEliminar{{ $producto->id }}">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                       

                                    </form>
                                         
                                        <!-- Modal -->
                                        <div class="modal fade" id="modalEliminar{{ $producto->id }}" tabindex="-1" aria-labelledby="modalEliminarLabel{{ $producto->id }}" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-danger text-white">
                                                        <h5 class="modal-title" id="modalEliminarLabel{{ $producto->id }}">Confirmar Eliminación</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        ¿Estás seguro de que deseas eliminar el producto <strong>{{ $producto->nombre }}</strong>?
                                                    </div>
                                                    <div class="modal-footer">
                                                        <form action="{{ route('productos.destroy', $producto->id) }}" method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger">Sí, eliminar</button>
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                </td>
                            </tr>
                        @empty
                        
                        @endforelse
                    </tbody>
                 

                </table>
            </div>
        </div>
    </div>


@push('scripts')
<script>
    $(document).ready(function () {

        var fechaActual = new Date().toLocaleDateString();

        $('#tabla-productos').DataTable({
            dom: 'Bfrtip',

            buttons: [

                {
                    extend: 'excel',
                    text: 'Exportar a Excel',
                    exportOptions: {
                        columns: ':not(.no-print)'
                    }
                },

                {
                    extend: 'pdf',
                    text: 'Exportar a PDF',
                    exportOptions: {
                        columns: ':not(.no-print)'
                    },
                    title: 'Listado de Productos',
                    messageTop: 'Fecha: ' + fechaActual,
                    customize: function (doc) {

                        doc.pageMargins = [40, 60, 40, 60];

                        doc.styles.title = {
                            alignment: 'center',
                            fontSize: 16,
                            bold: true
                        };

                        doc.styles.tableHeader = {
                            bold: true,
                            fontSize: 12,
                            alignment: 'center'
                        };

                        doc.content[1].alignment = 'center';

                        var tableBody = doc.content[2].table.body;

                        tableBody.forEach(function(row) {
                            row.forEach(function(cell) {
                                cell.alignment = 'center';
                            });
                        });
                    }
                },

                {
                    extend: 'print',
                    text: 'Imprimir',
                    exportOptions: {
                        columns: ':not(.no-print)'
                    },
                    customize: function (win) {
                        $(win.document.body)
                            .css('font-size', '10pt')
                            .prepend(
                                '<div style="text-align:center; margin-bottom:15px;">' +
                                '<h3>Listado de Productos</h3>' +
                                '<p>Fecha: ' + fechaActual + '</p>' +
                                '</div>'
                            );

                        $(win.document.body).find('table')
                            .addClass('compact')
                            .css('font-size', 'inherit');
                    }
                }
            ],

            language: {
                decimal: ",",
                thousands: ".",
                lengthMenu: "Mostrar _MENU_ registros por página",
                zeroRecords: "No se encontraron resultados",
                info: "Mostrando página _PAGE_ de _PAGES_",
                infoEmpty: "No hay registros disponibles",
                infoFiltered: "(filtrado de _MAX_ registros totales)",
                search: "Buscar:",
                paginate: {
                    first: "Primero",
                    last: "Último",
                    next: "Siguiente",
                    previous: "Anterior"
                }
            }
        });

    });
</script>
@endpush
@endsection
