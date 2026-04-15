@extends('layouts.app')

@section('title', 'Informes')

@section('content')

<div class="container py-5">

    <h1 class="text-center mb-5 fw-bold text-dark">
         Panel de Informes
    </h1>

    <div class="row g-4 align-items-start">

        <!-- COMPRAS -->
        <div class="col-md-4">
            <a href="{{ route('informes.compras') }}" class="text-decoration-none">
                <div class="card shadow-sm border-0 h-100 informe-card bg-danger text-white">
                    <div class="card-body text-center">
                        <i class="fas fa-shopping-cart fa-3x mb-3"></i>
                        <h5>Informe de Compras</h5>
                    </div>
                </div>
            </a>
        </div>

        <!-- VENTAS -->
        <div class="col-md-4">
            <a href="{{ route('informes.ventas') }}" class="text-decoration-none">
                <div class="card shadow-sm border-0 h-100 informe-card bg-success text-white">
                    <div class="card-body text-center">
                        <i class="fas fa-cash-register fa-3x mb-3"></i>
                        <h5>Informe de Ventas</h5>
                    </div>
                </div>
            </a>
        </div>

        <!-- PRODUCTOS -->
        <div class="col-md-4">
            <a href="{{ route('informes.productos') }}" class="text-decoration-none">
                <div class="card shadow-sm border-0 h-100 informe-card bg-info text-white">
                    <div class="card-body text-center">
                        <i class="fas fa-box fa-3x mb-3"></i>
                        <h5>Informe de Productos</h5>
                    </div>
                </div>
            </a>
        </div>

        <!-- CAJA -->
        <div class="col-md-4">
            <a href="{{ route('informes.caja') }}" class="text-decoration-none">
                <div class="card shadow-sm border-0 h-100 informe-card bg-dark text-white">
                    <div class="card-body text-center">
                        <i class="fas fa-wallet fa-3x mb-3"></i>
                        <h5>Informe de Caja</h5>
                    </div>
                </div>
            </a>
        </div>

        <!-- CLIENTES -->
        <div class="col-md-4">
            <a href="{{ route('informes.clientes') }}" class="text-decoration-none">
                <div class="card shadow-sm border-0 h-100 informe-card bg-warning text-dark">
                    <div class="card-body text-center">
                        <i class="fas fa-users fa-3x mb-3"></i>
                        <h5>Informe de Clientes</h5>
                    </div>
                </div>
            </a>
        </div>

        <!-- EXTRA (ESPACIO PARA FUTURO) -->
        <div class="col-md-4">
            <div class="card shadow-sm border-0 h-100 informe-card bg-secondary text-white">
                <div class="card-body text-center">
                    <i class="fas fa-chart-line fa-3x mb-3"></i>
                    <h5>Próximamente</h5>
                </div>
            </div>
        </div>

    </div>
</div>

<style>
.informe-card {
    border-radius: 12px;
    transition: all 0.3s ease;
    cursor: pointer;
}

.informe-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.2);
}
</style>

@endsection