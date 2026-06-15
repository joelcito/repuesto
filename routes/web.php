<?php

use App\Http\Controllers\CajasController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ClienteVehiculoController;
use App\Http\Controllers\DevolucionController;
use App\Http\Controllers\FacturaController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\IncorporacionController;
use App\Http\Controllers\MarcaController;
use App\Http\Controllers\MovimientoCajaController;
use App\Http\Controllers\MovimientoController;
use App\Http\Controllers\PagoController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\RolController;
use App\Http\Controllers\SucursalController;
use App\Http\Controllers\UnidadController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VentasController;
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


    Route::prefix('/unidad')->group(function () {
        Route::get('/listado', [UnidadController::class, 'listado'])->name('unidad.listado');
        Route::post('/ajaxListado', [UnidadController::class, 'ajaxListado'])->name('unidad.ajaxListado');
        Route::post('/guardarUnidad', [UnidadController::class, 'guardarUnidad'])->name('unidad.guardarUnidad');
        Route::post('/eliminarUnidad', [UnidadController::class, 'eliminarUnidad'])->name('unidad.eliminarUnidad');
    });

    Route::prefix('/marca')->group(function () {
        Route::get('/listado', [MarcaController::class, 'listado'])->name('marca.listado');
        Route::post('/ajaxListado', [MarcaController::class, 'ajaxListado'])->name('marca.ajaxListado');
        Route::post('/guardarMarca', [MarcaController::class, 'guardarMarca'])->name('marca.guardarMarca');
        Route::post('/eliminarMarca', [MarcaController::class, 'eliminarMarca'])->name('marca.eliminarMarca');
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
        Route::post('/guardar-transferencia', [MovimientoController::class, 'guardarTransferencia'])->name('movimiento.guardarTransferencia');

        //Route::post('/guardarMovimiento', [MovimientoController::class, 'guardarMovimiento'])->name('movimiento.guardarMovimiento');
    });

    //venta
    // Route::prefix('/venta')->group(function () {
    //     Route::get('/listado', [VentasController::class, 'listado'])->name('venta.listado');
    //     Route::post('/ajaxListado', [VentasController::class, 'ajaxListado'])->name('venta.ajaxListado');
    //     Route::post('/guardarVenta', [VentasController::class, 'guardarVenta'])->name('venta.guardarVenta');
    //     Route::post('/eliminar', [VentasController::class, 'eliminar'])->name('venta.eliminar');
    // });

    //caja
    Route::prefix('/caja')->group(function () {
        Route::get('/listado', [CajasController::class, 'listado'])->name('caja.listado');
        Route::post('/ajaxListado', [CajasController::class, 'ajaxListado'])->name('caja.ajaxListado');
        Route::post('/guardar', [CajasController::class, 'guardar'])->name('caja.guardar');
        Route::post('/eliminar', [CajasController::class, 'eliminar'])->name('caja.eliminar');

        Route::post('/abrirCaja', [CajasController::class, 'abrirCaja'])->name('caja.abrirCaja');
        Route::post('/cerrarCaja', [CajasController::class, 'cerrarCaja'])->name('caja.cerrarCaja');
    });


    Route::post('/movimiento-caja/guardar', [MovimientoCajaController::class, 'guardarMovimiento'])->name('movimientoCaja.guardar');


    Route::prefix('/devolucion')->group(function () {
        Route::get('/listado', [DevolucionController::class, 'listado'])->name('devolucion.listado');
        Route::post('/ajaxListado', [DevolucionController::class, 'ajaxListado'])->name('devolucion.ajaxListado');
        Route::post('/guardarDevolucion', [DevolucionController::class, 'guardarDevolucion'])->name('devolucion.guardarDevolucion');
        Route::post('/eliminarDevolucion', [DevolucionController::class, 'eliminarDevolucion'])->name('devolucion.eliminarDevolucion');
        Route::post('/obtenerDetalleVenta', [DevolucionController::class, 'obtenerDetalleVenta'])->name('devolucion.obtenerDetalleVenta');
        Route::get('/detalledevolucion/{devolucion_id}', [DevolucionController::class, 'detalledevolucion'])->name('devolucion.detalledevolucion');
    });

    Route::prefix('/incorporacion')->group(function () {
        Route::get('/listado', [IncorporacionController::class, 'listado'])->name('incorporacion.listado');
        Route::post('/ajaxListado', [IncorporacionController::class, 'ajaxListado'])->name('incorporacion.ajaxListado');
        Route::post('/guardarIncorporacion', [IncorporacionController::class, 'guardarIncorporacion'])->name('incorporacion.guardarIncorporacion');
        Route::post('/eliminarIncorporacion', [IncorporacionController::class, 'eliminarIncorporacion'])->name('incorporacion.eliminarIncorporacion');

    });

    Route::post('/guardarVehiculo', [ClienteVehiculoController::class, 'guardarVehiculo'])->name('cliente.guardarVehiculo');
    Route::post('/ajaxListadoVehiculos', [ClienteVehiculoController::class, 'ajaxListadoVehiculos'])->name('cliente.ajaxListadoVehiculos');
    Route::post('/eliminarVehiculo', [ClienteVehiculoController::class, 'eliminarVehiculo'])->name('cliente.eliminarVehiculo');


    // FACTURA
    Route::prefix('/venta')->group(function () {
        Route::get('/formulario', [VentasController::class, 'formulario'])->name('venta.formulario');
        Route::post('/recepcionar', [VentasController::class, 'recepcionar'])->name('venta.recepcionar');
        Route::get('/listado', [VentasController::class, 'listado'])->name('venta.listado');
        Route::get('/recibo/{venta_id}', [VentasController::class, 'recibo'])->name('venta.recibo');
        Route::get('/detalle/{venta_id}', [VentasController::class, 'detalle'])->name('venta.detalle');
        Route::post('/anularRecibo', [VentasController::class, 'anularRecibo']);
        Route::post('/agregarNuevoOrdenTrabajo', [VentasController::class, 'agregarNuevoOrdenTrabajo']);
        Route::get('/ventas-estado-null', [VentasController::class, 'getVentaNull'])->name('venta.estadoNull');
        Route::get('/ots', [VentasController::class, 'getOTs'])->name('venta.obtenerOTs');
        Route::post('/obtenerProductosAprobados', [VentasController::class, 'obtenerProductosAprobados'])->name('venta.obtenerProductosAprobados');
        Route::post('/enviarArchivar', [VentasController::class, 'enviarArchivar'])->name('venta.enviarArchivar');
        Route::post('/guardarVenta', [VentasController::class, 'guardarVenta'])->name('venta.guardarVenta');
        Route::post('/ajaxListado', [VentasController::class, 'ajaxListado'])->name('venta.ajaxListado');
        Route::post('/ajaxListadoDetalleVenta', [VentasController::class, 'ajaxListadoDetalleVenta'])->name('venta.ajaxListadoDetalleVenta');
        Route::post('/buscar-productos', [VentasController::class, 'buscarProductos'])->name('venta.buscarProductos');

    });

    //PAGO
    Route::prefix('/pago')->group(function () {
        Route::post('/guardarTipoIngresoSalida', [PagoController::class, 'guardarTipoIngresoSalida']);
        Route::get('/listado', [PagoController::class, 'listado'])->name('pago.listado');
        Route::post('/ajaxListado', [PagoController::class, 'ajaxListado'])->name('pago.ajaxListado');
        Route::get('/listadoDeuda', [PagoController::class, 'listadoDeuda'])->name('pago.listadoDeuda');
        Route::post('/ajaxListadoDeuda', [PagoController::class, 'ajaxListadoDeuda'])->name('pago.ajaxListadoDeuda');
        Route::post('/ajaxFormPagoDeuda', [PagoController::class, 'ajaxFormPagoDeuda'])->name('pago.ajaxFormPagoDeuda');
        Route::post('/guardarPagoDeuda', [PagoController::class, 'guardarPagoDeuda'])->name('pago.guardarPagoDeuda');
        Route::post('/ajaxDescargarReportePago', [PagoController::class, 'ajaxDescargarReportePago'])->name('pago.ajaxDescargarReportePago');
        Route::post('/formularioDecuentoAdicional', [PagoController::class, 'formularioDecuentoAdicional'])->name('pago.formularioDecuentoAdicional');
        Route::post('/guardarDescuentoAdicional', [PagoController::class, 'guardarDescuentoAdicional'])->name('pago.guardarDescuentoAdicional');
        Route::get('/comprobantePago/{pago_id}', [PagoController::class, 'comprobantePago'])->name('pago.comprobantePago');
        Route::post('/generaExcelPago', [PagoController::class, 'generaExcelPago'])->name('pago.generaExcelPago');
    });

    // REPORTE
    Route::prefix('/reporte')->group(function () {
        Route::get('/cajas', [ReporteController::class, 'cajas'])->name('reporte.cajas');
        Route::get('/historial', [ReporteController::class, 'historial'])->name('reporte.historial_precios');
        Route::get('/ingresosalida', [ReporteController::class, 'ingresosalida'])->name('reporte.ingreso_salida');
        Route::get('/inventarios', [ReporteController::class, 'inventarios'])->name('reporte.inventarios');
        Route::get('/pagos', [ReporteController::class, 'pagos'])->name('reporte.pagos');
        Route::get('/utilidades', [ReporteController::class, 'utilidades'])->name('reporte.utilidades');
        Route::get('/ventas', [ReporteController::class, 'ventas'])->name('reporte.ventas');


        Route::post('/inventarios/pdf', [ReporteController::class, 'inventariosPdf'])->name('reporte.inventarios.pdf');
        Route::post('/ventas/pdf', [ReporteController::class, 'ventasPdf'])->name('reporte.ventas.pdf');
        Route::post('/cajas/pdf', [ReporteController::class, 'cajasPdf'])->name('reporte.cajas.pdf');
        Route::post('/utilidades/pdf', [ReporteController::class, 'utilidadesPdf'])->name('reporte.utilidades.pdf');
        Route::post('/ingreso-salida/pdf', [ReporteController::class, 'ingresoSalidaPdf'])->name('reporte.ingreso_salida.pdf');
        Route::post('/historial/pdf', [ReporteController::class, 'historialPdf'])->name('reporte.historial.pdf');
    });

});

require __DIR__ . '/auth.php';
