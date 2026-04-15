<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CobroController;
use App\Http\Controllers\VentaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CajaController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\CompraController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\InformeController;





/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return redirect()->route('login');  // Redirige a la ruta de login
});

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    });
    Route::get('/cobros/{venta}/create', [CobroController::class, 'create'])->name('cobros.create');
    Route::post('cobros', [CobroController::class, 'store'])->name('cobros.store');
    Route::get('cobros', [CobroController::class, 'index'])->name('cobros.index');
    Route::put('/ventas/{venta}/anular', [VentaController::class, 'anular'])->name('ventas.anular');
    Route::get('/caja', [CajaController::class, 'index'])->name('caja.index');
    Route::get('/caja/{caja}', [CajaController::class, 'show'])->name('caja.show');
    Route::post('/caja/abrir', [CajaController::class, 'abrir'])->name('caja.abrir');
    Route::post('/caja/egreso', [CajaController::class, 'agregarEgreso'])->name('caja.egreso');
    Route::post('/caja/cerrar', [CajaController::class, 'cerrar'])->name('caja.cerrar');

    Route::get('/compras/create', [CompraController::class, 'create'])->name('compras.create');
    Route::get('/compras/{compra}', [CompraController::class, 'show'])->name('compras.show');
    Route::post('/compras', [CompraController::class, 'store'])->name('compras.store');
    Route::get('/compras', [CompraController::class, 'index'])->name('compras.index');
    Route::get('/informes', [InformeController::class, 'index'])->name('informes.index');
    Route::get('/informes/compras', [InformeController::class, 'compras'])->name('informes.compras');
    Route::get('/informes/ventas', [InformeController::class, 'ventas'])->name('informes.ventas');
    Route::get('/informes/productos', [InformeController::class, 'productos'])->name('informes.productos');
    Route::get('/informes/caja', [InformeController::class, 'caja'])->name('informes.caja');
    Route::get('/informes/clientes', [InformeController::class, 'clientes'])->name('informes.clientes');
});

Route::middleware(['auth'])->group(function () {
    Route::resource('productos', \App\Http\Controllers\ProductoController::class);
    Route::resource('clientes', \App\Http\Controllers\ClienteController::class);
    Route::resource('ventas', \App\Http\Controllers\VentaController::class);
    Route::resource('proveedores', \App\Http\Controllers\ProveedorController::class);

    
});



Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');
    
Route::get('/dashboard/export/excel', [DashboardController::class, 'exportExcel'])->name('dashboard.export.excel');
Route::get('/dashboard/export/pdf', [DashboardController::class, 'exportPDF'])->name('dashboard.export.pdf');
    


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
