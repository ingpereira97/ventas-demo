@extends('layouts.app')

@section('title', 'Proveedores')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between mb-3">
        <h1 class="h3">Listado de Proveedores</h1>
        <a href="{{ route('proveedores.create') }}" class="btn btn-primary">
            <i class="fas fa-user-plus"></i> Nuevo Proveedor
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
            <table id="tabla-proveedor" class="table table-hover text-nowrap">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nombre</th>
                        <th>Teléfono</th>
                        <th>Email</th>
                        <th>Dirección</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($proveedores as $p)
                        <tr>
                            <td>{{ $p->id }}</td>
                            <td>{{ $p->nombre }}</td>
                            <td>{{ $p->telefono }}</td>
                            <td>{{ $p->email }}</td>
                            <td>{{ $p->direccion }}</td>
                            <td>
                                <a href="{{ route('proveedores.edit', $p) }}" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i>
                                </a>

                                <!-- Modal trigger -->
                                <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#modalEliminar{{ $p->id }}">
                                    <i class="fas fa-trash-alt"></i>
                                </button>

                                <!-- Modal -->
                                <div class="modal fade" id="modalEliminar{{ $p->id }}" tabindex="-1" aria-labelledby="modalLabel{{ $p->id }}" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header bg-danger text-white">
                                                <h5 class="modal-title" id="modalLabel{{ $p->id }}">Confirmar Eliminación</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                                            </div>
                                            <div class="modal-body">
                                                ¿Estás seguro que deseas eliminar al Proveedor <strong>{{ $p->nombre }}</strong>?
                                            </div>
                                            <div class="modal-footer">
                                                <form action="{{ route('proveedores.destroy', $p->id) }}" method="POST">
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
        $('#tabla-proveedor').DataTable({
            dom: 'Bfrtip',
            buttons: ['copy', 'excel', 'pdf', 'print'
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
                },
            }
        });
    });
</script>
@endpush
@endsection
