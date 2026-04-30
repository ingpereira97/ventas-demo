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
                <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Buscar Producto</label>
                            <input type="text" id="buscar_producto" class="form-control" placeholder="Escribí nombre o código...">

                            <div id="resultados" class="list-group mt-2"></div>
                
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-bold">Cantidad</label>
                            <input type="number" id="cantidad_scan" class="form-control" value="1" min="0.001" step="0.001">
                        </div>

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
                <div class="mb-3">
                    <label class="form-label fw-bold">Tipo de Venta</label>
                    <select name="tipo_pago" class="form-control" required>
                        <option value="contado">Contado</option>
                        <option value="credito">Crédito</option>
                    </select>
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
let productosDB = @json($productos);
</script>
<script>
let ignorarBusqueda = false;

let productos = [];
let indexSeleccionado = -1;
let resultadosActuales = [];
let ultimoQuery = '';
let timeout = null;

document.getElementById('buscar_producto').addEventListener('input', function() {

    if (ignorarBusqueda) {
        ignorarBusqueda = false;
        return;
    }
    let query = this.value.trim();
    ultimoQuery = query;

    // ❌ IGNORAR CÓDIGO DE BARRAS
    if (/^\d{8,}$/.test(query)) {
        return;
    }

    if (query.length < 2) {
        document.getElementById('resultados').innerHTML = '';
        resultadosActuales = [];
        return;
    }

    fetch(`/buscar-productos?q=${query}`)
        .then(res => res.json())
        .then(data => {

            // 🔥 SI YA CAMBIÓ EL INPUT → IGNORAR RESPUESTA
            if (query !== ultimoQuery || ignorarBusqueda) return;
            resultadosActuales = data;
            indexSeleccionado = data.length > 0 ? 0 : -1;

            let html = '';

            data.forEach((p, index) => {
                html += `
                    <a href="#" 
                       class="list-group-item list-group-item-action resultado-item"
                       data-index="${index}">
                        ${p.nombre} - Gs ${parseInt(p.precio).toLocaleString()}
                    </a>
                `;
            });

            document.getElementById('resultados').innerHTML = html;

            setTimeout(() => {
                let items = document.querySelectorAll('.resultado-item');
                actualizarSeleccion(items);
            }, 50);
        });
});
document.getElementById('buscar_producto').addEventListener('keydown', function(e) {
     let items = document.querySelectorAll('.resultado-item');

     // 🔽 BAJAR
    if (e.key === 'ArrowDown') {
        e.preventDefault();

        indexSeleccionado++;

        if (indexSeleccionado >= items.length) indexSeleccionado = 0;

        actualizarSeleccion(items);
        return;
    }

    // 🔼 SUBIR
    if (e.key === 'ArrowUp') {
        e.preventDefault();

        indexSeleccionado--;

        if (indexSeleccionado < 0) indexSeleccionado = items.length - 1;

        actualizarSeleccion(items);
        return;
    }

    if (e.key === 'Enter') {

        e.preventDefault();

        let codigo = this.value.trim();

        // 📦 SCANNER (INSTANTÁNEO)
        if (/^[a-zA-Z0-9]{6,}$/.test(codigo)) {


            ignorarBusqueda = true;
            ultimoQuery = ''; // 🔥 MATA cualquier búsqueda pendiente

            let producto = productosDB.find(p => 
                String(p.codigo_barras) === String(codigo)
            );

            if (producto) {
                agregarProductoDesdeBusqueda(producto);
            } else {
                alert("Producto no encontrado");
            }

            limpiarBuscador();
            return;
        }

        // 🔍 BUSCADOR NORMAL
        if (indexSeleccionado >= 0 && resultadosActuales[indexSeleccionado]) {
            agregarProductoDesdeBusqueda(resultadosActuales[indexSeleccionado]);
            limpiarBuscador();
        }
    }
});
function limpiarBuscador() {
    document.getElementById('buscar_producto').value = '';
    document.getElementById('resultados').innerHTML = '';
    indexSeleccionado = -1;
}

// 🔥 CLICK TAMBIÉN FUNCIONA
document.addEventListener('click', function(e) {

    if (e.target.classList.contains('resultado-item')) {

        let item = e.target.closest('.resultado-item');
        if (!item) return;

        let index = item.getAttribute('data-index');
        agregarProductoDesdeBusqueda(resultadosActuales[index]);
    }

    // cerrar lista si haces click afuera
    if (!e.target.closest('#buscar_producto') && !e.target.closest('#resultados')) {
        document.getElementById('resultados').innerHTML = '';
    }
});


// 🔥 RESALTAR SELECCIÓN
function actualizarSeleccion(items) {

    items.forEach(item => item.classList.remove('active'));

    if (items[indexSeleccionado]) {
    items[indexSeleccionado].classList.add('active');
    items[indexSeleccionado].scrollIntoView({
        block: 'nearest'
    });
}
}

function renderTabla() {

    let tbody = document.querySelector('#tabla-productos tbody');
    let inputs = document.getElementById('inputs-hidden');

    tbody.innerHTML = '';
    inputs.innerHTML = '';

    let total = 0;
    let hayError = false;

    productos.forEach((p, index) => {

        let cantidad = parseFloat(p.cantidad) || 0;

        if (cantidad <= 0) {
            hayError = true;
        }

        let subtotal = p.precio * cantidad;
        total += subtotal;

        tbody.innerHTML += `
            <tr>
                <td>${p.nombre}</td>

                <td>
                    <input type="number"
                        step="${p.tipo === 'peso' ? '0,001' : '1'}"
                        min="${p.tipo === 'peso' ? '0,001' : '1'}"
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
                        ? 'Gs ' + p.precio.toLocaleString() + ' / Kg'
                        : 'Gs ' + p.precio.toLocaleString()}
                </td>

                <td>Gs ${subtotal.toLocaleString()}</td>

                <td>
                    <button type="button" class="btn btn-danger btn-sm"
                        onclick="eliminarProducto(${index})">
                        🗑
                    </button>
                </td>
            </tr>
        `;

        inputs.innerHTML += `
            <input type="hidden" name="productos[]" value="${p.id}">
            <input type="hidden" name="cantidades[]" value="${cantidad}">
            <input type="hidden" name="precios[]" value="${p.precio}">
        `;
    });

    // 🔥 TOTAL
    let totalFinal = parseFloat(total.toFixed(3));
    document.getElementById('total').value = totalFinal;

    // 🔥 BLOQUEAR BOTÓN
    let btnGuardar = document.getElementById('btn-guardar');

    if (productos.length === 0 || totalFinal <= 0 || hayError) {
        btnGuardar.disabled = true;
    } else {
        btnGuardar.disabled = false;
    }
}
// 🔥 AGREGAR PRODUCTO
function agregarProductoDesdeBusqueda(producto) {

    let cantidadInput = document.getElementById('cantidad_scan').value;

    let cantidad = parseFloat(cantidadInput) || 1;

    let existente = productos.find(p => p.id == producto.id);

    if (existente) {

        if (existente.cantidad + cantidad > producto.stock) {
            alert("Stock insuficiente");
            return;
        }

        existente.cantidad += cantidad;

    } else {

        if (producto.stock <= 0) {
            alert("Sin stock");
            return;
        }

        productos.push({
            id: producto.id,
            nombre: producto.nombre,
            precio: parseFloat(producto.precio),
            cantidad: cantidad,
            tipo: producto.tipo,
            stock: parseFloat(producto.stock)
        });
    }

    renderTabla();
    limpiarBuscador();

    document.getElementById('cantidad_scan').value = 1;
    document.getElementById('buscar_producto').value = '';
    document.getElementById('resultados').innerHTML = '';
    indexSeleccionado = -1;
}
function cambiarCantidad(index, valor) {

    let producto = productos[index];
    let cantidad = parseFloat(valor) || 0;


    if (producto.stock <= 0) {
        alert("Este producto no tiene stock");
        return;
    }

    if (cantidad <= 0) {
        alert("Cantidad inválida");
        productos[index].cantidad = producto.tipo === 'peso' ? 0.001 : 1;
        renderTabla();
        return;
    }

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
@endpush
