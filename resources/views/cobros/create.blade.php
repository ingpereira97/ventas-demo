@extends('layouts.app')

@section('title', 'Cobrar Venta')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4">Cobrar Venta</h1>
    
    @if(session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
    @endif

    <div class="card shadow-sm border-0">
        <div class="card-body p-4">
            <form action="{{ route('cobros.store', $venta) }}" method="POST">
                @csrf
                <input type="hidden" name="venta_id" value="{{ $venta->id }}">
                
                <div class="row">
                    <!-- CLIENTE Y SALDO -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold text-muted">Cliente</label>
                        <input type="text" class="form-control form-control-lg bg-light" value="{{ $venta->cliente->nombre ?? 'Ocasional' }}" readonly>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold text-muted">Saldo a Cobrar</label>
                        <input type="text" id="saldo" class="form-control form-control-lg text-danger fw-bold bg-light" 
                               value="Gs {{ number_format($venta->saldo, 0, ',', '.') }}" readonly>
                    </div>
                </div>

                <hr class="my-3 opacity-25">

                <!-- 💳 MÉTODO DE PAGO MODERNO (TARJETAS SELECCIONABLES) -->
                <div class="mb-4">
                    <label class="form-label fw-bold text-dark d-block mb-3">
                        <i class="fas fa-wallet text-primary me-1"></i> Selecciona el Método de Pago
                    </label>

                    <div class="row g-3">
                        <!-- 💵 Efectivo -->
                        <div class="col-4">
                            <input type="radio" class="btn-check" name="metodo_pago" id="pago_efectivo" value="Efectivo" checked autocomplete="off">
                            <label class="btn btn-outline-success w-100 py-3 rounded-3 d-flex flex-column align-items-center shadow-sm h-100 justify-content-center" for="pago_efectivo">
                                <i class="fas fa-money-bill-wave fa-2x mb-2"></i>
                                <span class="fw-bold fs-6">Efectivo</span>
                            </label>
                        </div>

                        <!-- 💳 Tarjeta -->
                        <div class="col-4">
                            <input type="radio" class="btn-check" name="metodo_pago" id="pago_tarjeta" value="Tarjeta" autocomplete="off">
                            <label class="btn btn-outline-primary w-100 py-3 rounded-3 d-flex flex-column align-items-center shadow-sm h-100 justify-content-center" for="pago_tarjeta">
                                <i class="fas fa-credit-card fa-2x mb-2"></i>
                                <span class="fw-bold fs-6">Tarjeta</span>
                            </label>
                        </div>

                        <!-- 🏦 Transferencia -->
                        <div class="col-4">
                            <input type="radio" class="btn-check" name="metodo_pago" id="pago_transf" value="Transferencia" autocomplete="off">
                            <label class="btn btn-outline-info w-100 py-3 rounded-3 d-flex flex-column align-items-center shadow-sm h-100 justify-content-center" for="pago_transf">
                                <i class="fas fa-university fa-2x mb-2"></i>
                                <span class="fw-bold fs-6">Transferencia</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- 💵 MONTO PAGADO Y VUELTO -->
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="monto_pagado" class="form-label fw-semibold">Monto Pagado</label>
                        <input type="number" step="any" name="monto_pagado" id="monto_pagado" class="form-control form-control-lg" placeholder="Escriba el monto..." required>
                        
                        <small class="text-muted fw-bold mt-1 d-block">
                            Vista previa: <span id="lbl_monto_pagado" class="text-primary fs-6">Gs 0</span>
                        </small>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="vuelto" class="form-label fw-semibold">Vuelto / Estado</label>
                        <input type="text" id="vuelto" class="form-control form-control-lg fw-bold" readonly value="Ingresa el monto pagado">
                    </div>
                </div>

                <!-- BOTONES DE ACCIÓN -->
                <div class="d-flex justify-content-between pt-3 border-top mt-3">
                    <a href="{{ route('ventas.index') }}" class="btn btn-secondary px-4 fw-semibold">
                        <i class="fas fa-arrow-left me-1"></i> Cancelar
                    </a>
                    <button type="submit" class="btn btn-primary px-5 py-2 fw-bold fs-5 shadow-sm">
                        <i class="fas fa-check-circle me-1"></i> Confirmar Cobro
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {

        const saldo = parseFloat({{ $venta->saldo }});
        const inputPagado = document.getElementById('monto_pagado');
        const inputVuelto = document.getElementById('vuelto');
        const lblMontoPagado = document.getElementById('lbl_monto_pagado');
        const radiosMetodo = document.querySelectorAll('input[name="metodo_pago"]');

        // Formateador numérico
        const formatoGs = new Intl.NumberFormat('es-PY', {
            minimumFractionDigits: 0,
            maximumFractionDigits: 2
        });

        // 🟢 Autocompletar cuando selecciona Tarjeta o Transferencia (suelen ser montos exactos)
        radiosMetodo.forEach(radio => {
            radio.addEventListener('change', function() {
                if (this.value === 'Tarjeta' || this.value === 'Transferencia') {
                    inputPagado.value = saldo;
                    inputPagado.dispatchEvent(new Event('input')); // Dispara el evento de cálculo
                }
            });
        });

        // 🟢 Cálculo en vivo de Vuelto y Vista Previa
        inputPagado.addEventListener('input', function () {

            const pagado = parseFloat(this.value) || 0;

            // 1. Mostrar vista previa del monto pagado formateado
            lblMontoPagado.textContent = 'Gs ' + formatoGs.format(pagado);

            // 2. Calcular Vuelto / Estado
            if (pagado > saldo) {
                const vuelto = pagado - saldo;
                inputVuelto.value = `Gs ${formatoGs.format(vuelto)}`;
                inputVuelto.className = 'form-control form-control-lg fw-bold bg-success-subtle text-success';
            } else if (pagado === saldo) {
                inputVuelto.value = 'Pago exacto';
                inputVuelto.className = 'form-control form-control-lg fw-bold bg-success-subtle text-success';
            } else if (pagado > 0) {
                const falta = saldo - pagado;
                inputVuelto.value = `Falta: Gs ${formatoGs.format(falta)}`;
                inputVuelto.className = 'form-control form-control-lg fw-bold bg-danger-subtle text-danger';
            } else {
                inputVuelto.value = 'Ingresa el monto pagado';
                inputVuelto.className = 'form-control form-control-lg fw-bold';
            }

        });

    });
</script>
@endpush