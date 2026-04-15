@extends('layouts.app')

@section('title', 'Compras')

@section('content')

<div class="container">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4>Listado de Compras</h4>
            <a href="{{ route('compras.create') }}" class="btn btn-primary">
                Nueva Compra
            </a>
        </div>
         {{-- Mensajes de éxito o error --}}
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Fecha</th>
                        <th>Proveedor</th>
                        <th>Método Pago</th>
                        <th>Total</th>
                        <th>Usuario</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($compras as $compra)
                        <tr>
                            <td>{{ $compra->id }}</td>
                            <td>{{ $compra->created_at->format('d/m/Y H:i') }}</td>
                            <td>{{ $compra->proveedor->nombre ?? 'N/A' }}</td>
                            <td>
                                <span class="badge bg-secondary">
                                    {{ ucfirst($compra->metodo_pago) }}
                                </span>
                            </td>
                            <td><strong>$ {{ number_format($compra->total, 0) }}</strong></td>
                            <td>{{ $compra->user->name ?? 'N/A' }}</td>
                            <td>
                                <a href="{{ route('compras.show', $compra->id) }}"
                                   class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">
                                No hay compras registradas
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

        </div>
    </div>
</div>

@endsection
