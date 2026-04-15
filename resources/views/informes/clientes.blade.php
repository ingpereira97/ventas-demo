@extends('layouts.app')

@section('content')

<div class="container ">

    <h2 class="mb-4">Clientes</h2>

    {{-- BUSCADOR --}}
    <form method="GET" class="mb-3 no-print" style="max-width: 400px;">
        <input type="text" id="buscarCliente" name="buscar" value="{{ $buscar }}"
            class="form-control"
            placeholder="Buscar cliente...">
    </form>

    <table class="table table-bordered table-hover text-center">

        <thead class="table-dark">
            <tr>
                <th>Cliente</th>
                <th>Deuda</th>
                <th>Estado</th>
                <th>Última compra</th>
                <th>Antigüedad</th>
                <th class="no-print">Acción</th>
            </tr>
        </thead>

        <tbody id="tablaClientes">

        @forelse($clientes as $cliente)

            @php
                $ultimaVenta = $cliente->ventas->sortByDesc('created_at')->first();
            @endphp

            <tr>

                <td>{{ $cliente->nombre }}</td>

                <td class="fw-bold">
                    Gs {{ number_format($cliente->deuda) }}
                </td>

                <td>
                    @if($cliente->deuda > 0)
                        <span class="badge bg-danger">Debe</span>
                    @else
                        <span class="badge bg-success">Al día</span>
                    @endif
                </td>

                <td>
                    {{ $ultimaVenta ? $ultimaVenta->created_at->format('d/m/Y - H:i') : '-' }}
                </td>

                <td>
                    @if($ultimaVenta)
                        @php
                            $dias = $ultimaVenta->created_at->diffInDays();
                        @endphp

                        @if($dias <= 3)
                            <span class="text-success">Reciente</span>
                        @elseif($dias <= 7)
                            <span class="text-warning">Hace {{ $dias }} días</span>
                        @else
                            <span class="text-danger fw-bold">Hace {{ $dias }} días</span>
                        @endif
                    @endif
                </td>

                <td class="no-print">
                    <a href="{{ route('clientes.show', $cliente->id) }}"
                        class="btn btn-primary btn-sm">
                        <i class="fas fa-eye"></i>
                    </a>
                </td>

            </tr>

        @empty

            <tr>
                <td colspan="6" class="text-center">
                    Sin clientes
                </td>
            </tr>

        @endforelse

        <tr id="sinResultados" style="display:none;">
            <td colspan="6" class="text-center text-muted">
                No se encontraron clientes
            </td>
        </tr>

        </tbody>

    </table>
    @php
        $totalDeudaGeneral = $clientes->sum('deuda');
    @endphp

    <div class="mb-3">
        <h5>Total de deuda: 
            <span class="text-danger">
                Gs {{ number_format($totalDeudaGeneral) }}
            </span>
        </h5>
    </div>
    <button onclick="window.print()" class="btn btn-success no-print">
        Imprimir
    </button>

</div>
<script>
    document.getElementById('buscarCliente').addEventListener('keyup', function() {

        let filtro = this.value.toLowerCase();
        let filas = document.querySelectorAll('#tablaClientes tr');
        let hayVisible = false;

        filas.forEach(function(fila) {

            let texto = fila.innerText.toLowerCase();

            if (texto.includes(filtro)) {
                fila.style.display = '';
                hayVisible = true;
            } else {
                fila.style.display = 'none';
            }

        });

        // 🔥 mostrar mensaje si no hay resultados
        let sinResultados = document.getElementById('sinResultados');

        if (sinResultados) {
            sinResultados.style.display = hayVisible ? 'none' : '';
        }

    });
</script>
@endsection