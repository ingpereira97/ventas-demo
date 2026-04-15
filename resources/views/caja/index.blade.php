@extends('layouts.app')

@section('title', 'Caja')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-12 col-md-6">

            {{-- Mensajes de éxito o error --}}
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <div class="card">
                <div class="card-header">
                    <h4>Caja</h4>
                </div>
                <div class="card-body">

                    @if(!$caja)
                        {{-- Formulario para abrir caja --}}
                        <form action="{{ route('caja.abrir') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="monto_inicial" class="form-label">Monto Inicial</label>
                                <input type="number" step="0.01" name="monto_inicial" id="monto_inicial" class="form-control" required>
                            </div>
                            <button type="submit" class="btn btn-success w-100">Abrir Caja</button>
                        </form>
                        @if($ultimaCaja)
                        
                            <a href="{{ route('caja.show', $ultimaCaja->id) }}" class="btn btn-primary w-100 mt-2">
                                Ver Último Informe
                            </a>
                        @endif
                    @else
                        {{-- Mostrar caja abierta --}}
                        <p><strong>Usuario:</strong> {{ auth()->user()->name }}</p>
                        <p><strong>Monto Inicial:</strong> ${{ number_format($caja->monto_inicial, 0) }}</p>
                        <p><strong>Total Ventas:</strong> ${{ number_format($totalVentas) }}</p>
                        <p><strong>Total Compras:</strong> ${{ number_format($totalCompras) }}</p>
                        <p><strong>Total Egresos:</strong> ${{ number_format($totalEgresos) }}</p>
                        <p><strong>Saldo Actual:</strong> ${{ number_format($saldoActual, 0) }}</p>
                        <p><strong>Estado:</strong> {{ ucfirst($caja->estado) }}</p>

                        {{-- Botones de acciones --}}
                        <div class="d-grid gap-2">
                            {{-- Formulario para cerrar caja --}}
                            <form action="{{ route('caja.cerrar') }}" method="POST">
                                @csrf
                                <div class="mb-2">
                                    <label for="monto_cierre" class="form-label">Monto de Cierre</label>
                                    <input type="number" step="0.01" name="monto_cierre" id="monto_cierre" class="form-control" required>
                                </div>
                                <button type="submit" class="btn btn-danger w-100">Cerrar Caja</button>
                            </form>

                            {{-- Agregar Egreso --}}
                            <button class="btn btn-warning w-100" data-bs-toggle="collapse" data-bs-target="#egresoForm">Agregar Egreso</button>
                            <div class="collapse mt-2" id="egresoForm">
                                <form action="{{ route('caja.egreso') }}" method="POST">
                                    @csrf
                                    <div class="mb-2">
                                        <input type="text" name="descripcion" placeholder="Descripción" class="form-control" required>
                                    </div>
                                    <div class="mb-2">
                                        <input type="number" step="0.01" name="monto" placeholder="Monto" class="form-control" required>
                                    </div>
                                    <button type="submit" class="btn btn-warning w-100">Registrar Egreso</button>
                                </form>
                            </div>
                        </div>

                    @endif

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
