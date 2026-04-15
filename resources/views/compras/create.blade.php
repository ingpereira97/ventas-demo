@extends('layouts.app')

@section('content')

<div class="container">
    <div class="card">
        <div class="card-header">
            <h4>Nueva Compra</h4>
        </div>
        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card-body">

            <form action="{{ route('compras.store') }}" method="POST">
                @csrf

                {{-- Proveedor --}}
                <div class="mb-3">
                    <label>Proveedor</label>
                    <select name="proveedor_id" class="form-control" required>
                        <option value="">Seleccione</option>
                        @foreach($proveedores as $proveedor)
                            <option value="{{ $proveedor->id }}">
                                {{ $proveedor->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Método de pago --}}
                <div class="mb-3">
                    <label>Método de Pago</label>
                    <select name="metodo_pago" class="form-control" required>
                        <option value="efectivo">Efectivo</option>
                        <option value="transferencia">Transferencia</option>
                        <option value="credito">Crédito</option>
                    </select>
                </div>

                <hr>

                {{-- Agregar productos --}}
                <div class="row mb-3">
                    <div class="col-md-4">
                        <select id="producto" class="form-control">
                            <option value="">Seleccione Producto</option>
                            @foreach($productos as $producto)
                                <option value="{{ $producto->id }}"
                                    data-tipo="{{ $producto->tipo }}">
                                    {{ $producto->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <input type="number"
                        step="0.01"
                        id="cantidad"
                        class="form-control cantidad"
                        required
                        placeholder="Cantidad">
                    </div>

                    <div class="col-md-3">
                        <input type="number" step="0.01" id="precio" class="form-control" placeholder="Precio Compra">
                    </div>

                    <div class="col-md-2">
                        <button type="button" class="btn btn-primary w-100" onclick="agregarProducto()">
                            Agregar
                        </button>
                    </div>
                </div>

                {{-- Tabla --}}
                <table class="table table-bordered" id="tablaProductos">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Cantidad</th>
                            <th>Precio</th>
                            <th>Subtotal</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>

                <h4 class="text-end">Total: $<span id="total">0</span></h4>

                <button type="submit" class="btn btn-success w-100 mt-3">
                    Guardar Compra
                </button>

            </form>
        </div>
    </div>
</div>

@endsection
@section('scripts')
<script>
let contador = 0;
let total = 0;

    function agregarProducto() {

        let productoSelect = document.getElementById('producto');
        let option = productoSelect.options[productoSelect.selectedIndex];

        let productoId = option.value;
        
        let existe = document.querySelector(`input[value="${productoId}"]`);
            if (existe) {
                alert("Este producto ya fue agregado");
                return;
            }

        let productoTexto = option.text;
        let tipo = option.getAttribute('data-tipo'); // 🔥 CLAVE

        let cantidad = parseFloat(document.getElementById('cantidad').value) || 0;
        let precio = parseFloat(document.getElementById('precio').value) || 0;

        if (!productoId || cantidad <= 0 || precio <= 0) {
            alert("Complete correctamente los campos");
            return;
        }

        // 🚨 VALIDAR SEGÚN TIPO
        if (tipo === 'unidad') {
            cantidad = Math.floor(cantidad); // 🔥 entero obligatorio
        }

        let subtotal = cantidad * precio;
        total += subtotal;

        let unidadTexto = tipo === 'peso' ? 'Kg' : 'Und';

        let fila = `
            <tr id="fila${contador}">
                <td>${productoTexto}
                    <input type="hidden" name="productos[${contador}][producto_id]" value="${productoId}">
                </td>

                <td>
                    ${cantidad} ${unidadTexto}
                    <input type="hidden" name="productos[${contador}][cantidad]" value="${cantidad}">
                </td>

                <td>
                    ${precio}
                    <input type="hidden" name="productos[${contador}][precio]" value="${precio}">
                </td>

                <td>${subtotal.toFixed(2)}</td>

                <td>
                    <button type="button" class="btn btn-danger btn-sm"
                        onclick="eliminar(${contador}, ${subtotal})">
                        X
                    </button>
                </td>
            </tr>
        `;

        document.querySelector('#tablaProductos tbody').innerHTML += fila;

        document.getElementById('total').innerText = total.toFixed(2);

        contador++;

    }

    function eliminar(index, subtotal) {
        document.getElementById('fila' + index).remove();
        if (total < 0) total = 0;
        document.getElementById('total').innerText = total.toFixed(2);
}

</script>

@endsection
