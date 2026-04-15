@extends('layouts.app')

@section('content')

<h2>Informe de Compras</h2>

    <form method="GET">

        Desde:
        <input type="date" name="desde" value="{{ $desde }}">

        Hasta:
        <input type="date" name="hasta" value="{{ $hasta }}">

        <button class="btn btn-primary">Filtrar</button>

    </form>

        <br>

            <table class="table table-bordered">

                <thead>
                    <tr>
                        <th>#</th>
                        <th>Fecha</th>
                        <th>Proveedor</th>
                        <th>Total</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse($compras as $compra)

                    <tr>
                        <td>{{ $compra->id }}</td>
                        <td>{{ $compra->created_at->format('d/m/Y') }}</td>
                        <td>{{ $compra->proveedor->nombre }}</td>
                        <td>Gs {{ number_format($compra->total) }}</td>
                    </tr>

                    @empty

                    <tr>
                        <td colspan="4" class="text-center">
                            No hay compras en este rango de fechas
                        </td>
                    </tr>

                    @endforelse

                </tbody>

            </table>

            <h4>Total Comprado: Gs {{ number_format($total) }}</h4>

                <button onclick="window.print()" class="btn btn-success">
                Imprimir
                </button>

@endsection