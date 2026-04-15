@extends('layouts.app')

@section('title', 'Nueva Venta')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4">Registrar Nueva Venta</h1>
        {{-- Mensajes de alerta de stock --}}
        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

    <form id="ventaForm" action="{{ route('ventas.store') }}" method="POST">
        @csrf

        <div class="card">
            <div class="card-body">
                <!-- Cliente -->
                <div class="mb-3">
                    <label for="cliente_id" class="form-label">Cliente</label>
                    <select name="cliente_id" id="cliente_id" class="form-control" required>
                        <option value="0">Cliente Ocasional</option> <!-- Agregamos esta opción -->
                        @foreach ($clientes as $cliente)
                            <option value="{{ $cliente->id }}">{{ $cliente->nombre }}</option>
                        @endforeach
                    </select>
                </div>


                <!-- Productos -->
                {{-- 🔍 BUSCADOR DE PRODUCTO --}}
                <div class="mb-3">
                    <label class="form-label fw-bold">Agregar Producto</label>

                    <select id="producto_select" class="form-select">
                        <option value="">Buscar producto...</option>

                        @foreach($productos as $producto)
                            <option value="{{ $producto->id }}"
                                data-precio="{{ $producto->precio }}"
                                data-tipo="{{ $producto->tipo }}"
                                data-stock="{{ $producto->stock }}"
                                {{ $producto->stock <= 0 ? 'disabled' : '' }}>

                                {{ $producto->nombre }} 
                                ({{ $producto->tipo == 'peso' ? 'Kg' : 'Und' }})
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- 📦 TABLA --}}
                <table class="table table-bordered" id="tabla-productos">
                    <thead class="table-dark">
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

                {{-- inputs ocultos --}}
                <div class="mb-2" id="inputs-hidden"></div>

                <!-- Total -->
                <div class="mb-3">
                    <label class="form-label fw-bold">Total</label>
                    <input type="text" name="total" id="total" class="form-control" readonly value="0">
                </div>

                <button type="submit" id="btn-guardar" class="btn btn-primary">Guardar Venta</button>
            </div>
        </div>
    </form>
    <div class="modal fade" id="stockAlertModal" tabindex="-1" aria-labelledby="stockAlertModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="stockAlertModalLabel">Alerta de Stock</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p id="stockMessage">Este producto no tiene suficiente stock.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let productos = [];

$('#producto_select').change(function () {

    let option = $(this).find('option:selected');

    let id = option.val();
    if (!id) return;

    let nombre = option.text();
    let precio = parseFloat(option.data('precio'));
    let tipo = option.data('tipo');
    let stock = parseFloat(option.data('stock'));

    // evitar duplicados
    if (productos.find(p => p.id == id)) {
        alert("Este producto ya fue agregado.");
        return;
    }

    let cantidad = tipo === 'peso' ? 0.01 : 1;

    productos.push({
        id,
        nombre,
        precio,
        cantidad,
        tipo,
        stock
    });

    renderTabla();
});

function renderTabla() {

    let tbody = $('#tabla-productos tbody');
    let inputs = $('#inputs-hidden');

    tbody.empty();
    inputs.empty();

    let total = 0;
    let hayError = false;

    productos.forEach((p, index) => {

        let cantidad = parseFloat(p.cantidad) || 0;

        // 🚨 VALIDAR CANTIDAD
        if (cantidad <= 0) {
            hayError = true;
        }

        let subtotal = parseFloat(p.precio) * cantidad;
        total += subtotal;

        tbody.append(`
        <tr>
            <td>${p.nombre}</td>

          <td>
                <input type="number"
                    step="${p.tipo === 'peso' ? '0.01' : '1'}"
                    min="${p.tipo === 'peso' ? '0.01' : '1'}"
                    value="${p.cantidad}"
                    class="form-control ${cantidad <= 0 || cantidad > p.stock ? 'is-invalid' : ''}"
                    onchange="cambiarCantidad(${index}, this.value)">

                <small class="d-block ${p.stock <= 0 ? 'text-danger' : 'text-muted'}">
                    ${
                        p.stock <= 0
                        ? '❌ SIN STOCK'
                        : 'Stock: ' + p.stock + (p.tipo === 'peso' ? ' Kg' : ' unidades')
                    }
                </small>
            </td>

            <td>
                ${p.tipo === 'peso' 
                    ? '$' + p.precio.toLocaleString() + ' / Kg'
                    : '$' + p.precio.toLocaleString()}
            </td>

            <td>$${subtotal.toLocaleString()}</td>

            <td>
                <button class="btn btn-danger btn-sm"
                    onclick="eliminarProducto(${index})">
                    🗑
                </button>
            </td>
        </tr>
        `);

        inputs.append(`
            <input type="hidden" name="productos[]" value="${p.id}">
            <input type="hidden" name="cantidades[]" value="${cantidad}">
            <input type="hidden" name="precios[]" value="${p.precio}">
        `);
    });

    $('#total').val(total.toFixed(0));

    // 🔥 REDONDEAR TOTAL (CLAVE)
    let totalFinal = parseFloat(total) || 0;
    totalFinal = parseFloat(totalFinal.toFixed(2));


    $('#total').val(totalFinal);

    // 🔥 BLOQUEAR BOTÓN
    let btnGuardar = document.getElementById('btn-guardar');
    // 🚨 VALIDACIÓN COMPLETA
    if (productos.length === 0 || totalFinal <= 0 || hayError) {
        btnGuardar.disabled = true;
    } else {
        btnGuardar.disabled = false;
    }
}

function cambiarCantidad(index, valor) {

    let producto = productos[index];
    let cantidad = parseFloat(valor) || 0;

    if (producto.tipo === 'unidad') {
        cantidad = Math.floor(cantidad);
    }

    // 🚨 SIN STOCK
    if (producto.stock <= 0) {
        alert("Este producto no tiene stock");
        return;
    }

    // 🚨 CANTIDAD INVÁLIDA
    if (cantidad <= 0) {
        alert("Cantidad inválida");
        productos[index].cantidad = producto.tipo === 'peso' ? 0.01 : 1;
        renderTabla();
        return;
    }

    // 🚨 STOCK INSUFICIENTE
    if (cantidad > producto.stock) {
        alert("Stock insuficiente");
        productos[index].cantidad = producto.stock;
    } else {
        productos[index].cantidad = cantidad;
    }

    renderTabla();
}

function eliminarProducto(index) {
    productos.splice(index, 1);
    renderTabla();
}
</script>
<script>
    document.addEventListener('change', function(e) {

        if (e.target.classList.contains('producto-select')) {

            let row = e.target.closest('.producto-item');
            let option = e.target.selectedOptions[0];

            let stock = parseFloat(option.dataset.stock);
            let tipo = option.dataset.tipo;

            let mensaje = row.querySelector('.mensaje-stock');
            let unidad = row.querySelector('.unidad-label');
            let inputCantidad = row.querySelector('.cantidad');

            // Mostrar stock disponible
            if (tipo === 'peso') {
                mensaje.innerText = 'Stock disponible: ' + stock + ' Kg';
                inputCantidad.step = "0.01";
                inputCantidad.min = "0.01";
            } else {
                mensaje.innerText = 'Stock disponible: ' + stock + ' unidades';
                inputCantidad.step = "1";
                inputCantidad.min = "1";
            }

            // 🔴 SIN STOCK
            if (stock <= 0) {
                mensaje.innerText = '❌ SIN STOCK';
                mensaje.classList.remove('text-muted');
                mensaje.classList.add('text-danger');

                inputCantidad.value = '';
                inputCantidad.disabled = true;

            } else {
                inputCantidad.disabled = false;
                mensaje.classList.remove('text-danger');
                mensaje.classList.add('text-muted');
            }
        }
    });
</script>
<script>
    document.addEventListener('input', function() {

        let valido = true;

        document.querySelectorAll('.producto-item').forEach(function(row) {

            let select = row.querySelector('.producto-select');
            let cantidadInput = row.querySelector('.cantidad');

            if (!select || !cantidadInput) return;

            let option = select.selectedOptions[0];
            if (!option) return;

            let stock = parseFloat(option.dataset.stock);
            let cantidad = parseFloat(cantidadInput.value) || 0;

            if (cantidad <= 0 || cantidad > stock) {
                valido = false;
            }
        });

        document.querySelector('button[type="submit"]').disabled = !valido;
    });
</script>
@endpush
