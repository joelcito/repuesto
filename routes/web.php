<?php

use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MovimientoController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\RolController;
use App\Http\Controllers\SucursalController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    //  return view('welcome');
    return redirect('home');
});

Auth::routes();

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    // Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    // Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    // Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/home', [HomeController::class, 'index'])->middleware(['auth', 'verified'])->name('home');


    Route::prefix('/rol')->group(function () {
        Route::get('/listado', [RolController::class, 'listado'])->name('rol.listado');
        Route::post('/ajaxListado', [RolController::class, 'ajaxListado'])->name('rol.ajaxListado');
        Route::post('/guardarRol', [RolController::class, 'guardarRol'])->name('rol.guardarRol');
        Route::post('/eliminarRol', [RolController::class, 'eliminarRol'])->name('rol.eliminarRol');
    });


    Route::prefix('/user')->group(function () {
        Route::get('/listado', [UserController::class, 'listado'])->name('user.listado');
        Route::post('/ajaxListado', [UserController::class, 'ajaxListado'])->name('user.ajaxListado');
        Route::post('/guardarUser', [UserController::class, 'guardarUser'])->name('user.guardarUser');
        Route::post('/eliminarUser', [UserController::class, 'eliminarUser'])->name('user.eliminarUser');

        Route::get('/control-personal/user/{id}', [UserController::class, 'getUser']);
    });


    Route::prefix('/sucursal')->group(function () {
        Route::get('/listado', [SucursalController::class, 'listado'])->name('sucursal.listado');
        Route::post('/ajaxListado', [SucursalController::class, 'ajaxListado'])->name('sucursal.ajaxListado');
        Route::post('/guardarSucursal', [SucursalController::class, 'guardarSucursal'])->name('sucursal.guardarSucursal');
        Route::post('/eliminarSucursal', [SucursalController::class, 'eliminarSucursal'])->name('sucursal.eliminarSucursal');
    });


    Route::prefix('/proveedor')->group(function () {
        Route::get('/listado', [ProveedorController::class, 'listado'])->name('proveedor.listado');
        Route::post('/ajaxListado', [ProveedorController::class, 'ajaxListado'])->name('proveedor.ajaxListado');
        Route::post('/guardarProveedor', [ProveedorController::class, 'guardarProveedor'])->name('proveedor.guardarProveedor');
        Route::post('/eliminarProveedor', [ProveedorController::class, 'eliminarProveedor'])->name('proveedor.eliminarProveedor');
    });

    Route::prefix('/cliente')->group(function () {
        Route::get('/listado', [ClienteController::class, 'listado'])->name('cliente.listado');
        Route::post('/ajaxListado', [ClienteController::class, 'ajaxListado'])->name('cliente.ajaxListado');
        Route::post('/guardarCliente', [ClienteController::class, 'guardarCliente'])->name('cliente.guardarCliente');
        Route::post('/eliminarCliente', [ClienteController::class, 'eliminarCliente'])->name('cliente.eliminarCliente');
    });

    Route::prefix('/producto')->group(function () {
        Route::get('/listado', [ProductoController::class, 'listado'])->name('producto.listado');
        Route::post('/ajaxListado', [ProductoController::class, 'ajaxListado'])->name('producto.ajaxListado');
        Route::post('/guardarProducto', [ProductoController::class, 'guardarProducto'])->name('producto.guardarProducto');
        Route::post('/eliminarProducto', [ProductoController::class, 'eliminarProducto'])->name('producto.eliminarProducto');

        Route::post('/generar-codigo', [ProductoController::class, 'generarCodigo']);

    });

    Route::prefix('/categoria')->group(function () {
        Route::get('/listado', [CategoriaController::class, 'listado'])->name('categoria.listado');
        Route::post('/ajaxListado', [CategoriaController::class, 'ajaxListado'])->name('categoria.ajaxListado');
        Route::post('/guardar', [CategoriaController::class, 'guardar'])->name('categoria.guardar');
        Route::post('/eliminar', [CategoriaController::class, 'eliminar'])->name('categoria.eliminar');
    });


    // MOVIMIENTO
    Route::prefix('/movimiento')->group(function () {
        Route::get('/listado', [MovimientoController::class, 'listado'])->name('movimiento.listado');
        Route::get('/ajaxListado', [MovimientoController::class, 'ajaxListado'])->name('movimiento.ajaxListado');
        Route::post('/guardarIngreso', [MovimientoController::class, 'guardarIngreso'])->name('movimiento.guardarIngreso');
        Route::post('/guardarSalida', [MovimientoController::class, 'guardarSalida'])->name('movimiento.guardarSalida');
        Route::post('/sacarTipoIngreso', [MovimientoController::class, 'sacarTipoIngreso'])->name('movimiento.sacarTipoIngreso');

        //Route::post('/guardarMovimiento', [MovimientoController::class, 'guardarMovimiento'])->name('movimiento.guardarMovimiento');
    });


});

require __DIR__ . '/auth.php';
