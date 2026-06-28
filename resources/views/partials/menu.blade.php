<div class="app-sidebar-menu overflow-hidden flex-column-fluid">
    <div id="kt_app_sidebar_menu_wrapper" class="app-sidebar-wrapper">
        <div id="kt_app_sidebar_menu_scroll" class="scroll-y my-5 mx-3" data-kt-scroll="true"
            data-kt-scroll-activate="true" data-kt-scroll-height="auto"
            data-kt-scroll-dependencies="#kt_app_sidebar_logo, #kt_app_sidebar_footer"
            data-kt-scroll-wrappers="#kt_app_sidebar_menu" data-kt-scroll-offset="5px" data-kt-scroll-save-state="true">
            <div class="menu menu-column menu-rounded menu-sub-indention fw-semibold fs-6" id="kt_app_sidebar_menu"
                data-kt-menu="true" data-kt-menu-expand="false">
                <div class="menu-item pt-5">
                    <div class="menu-content">
                        <span class="fs-7 text-white fw-bold">
                            MENUS
                        </span>
                    </div>
                </div>

                @if(
                        auth()->user()->esAlmacen()
                    )
                    <div data-kt-menu-trigger="click" class="menu-item menu-accordion">
                        <span class="menu-link">
                            <span class="menu-icon">
                                <i class="fa fa-university"></i>
                            </span>
                            <span class="menu-title text-white">
                                ADMINISTRACIÓN
                            </span>
                            <span class="menu-arrow"></span>
                        </span>

                        <div class="menu-sub menu-sub-accordion">

                            <div class="menu-item">
                                <a class="menu-link {{ Route::currentRouteName() == 'categoria.listado' ? 'active' : '' }}"
                                    href="{{ route('categoria.listado') }}">

                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>

                                    <span class="menu-title text-white">
                                        Categorias
                                    </span>
                                </a>
                            </div>
                            <div class="menu-item">
                                <a class="menu-link {{ Route::currentRouteName() == 'producto.listado' ? 'active' : '' }}"
                                    href="{{ route('producto.listado') }}">

                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>

                                    <span class="menu-title text-white">
                                        Producto
                                    </span>
                                </a>
                            </div>

                            <div class="menu-item">
                                <a class="menu-link {{ Route::currentRouteName() == 'proveedor.listado' ? 'active' : '' }}"
                                    href="{{ route('proveedor.listado') }}">

                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>

                                    <span class="menu-title text-white">
                                        Proveedores
                                    </span>
                                </a>
                            </div>
                            <div class="menu-item">
                                <a class="menu-link {{ Route::currentRouteName() == 'marca.listado' ? 'active' : '' }}"
                                    href="{{ route('marca.listado') }}">

                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>

                                    <span class="menu-title text-white">
                                        Marcas
                                    </span>
                                </a>
                            </div>
                            <div class="menu-item">
                                <a class="menu-link {{ Route::currentRouteName() == 'unidad.listado' ? 'active' : '' }}"
                                    href="{{ route('unidad.listado') }}">

                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>

                                    <span class="menu-title text-white">
                                        Unidades
                                    </span>
                                </a>
                            </div>


                        </div>
                    </div>

                @endif

                @if(
                        auth()->user()->esAlmacen()
                    )
                    <div data-kt-menu-trigger="click" class="menu-item menu-accordion">
                        <span class="menu-link">
                            <span class="menu-icon">
                                <i class="fa fa-university"></i>
                            </span>
                            <span class="menu-title text-white">
                                ADMINISTRACIÓN
                            </span>
                            <span class="menu-arrow"></span>
                        </span>

                        <div class="menu-sub menu-sub-accordion">
                            <div class="menu-item">
                                <a class="menu-link {{ Route::currentRouteName() == 'caja.listado' ? 'active' : '' }}"
                                    href="{{ route('caja.listado') }}">

                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>

                                    <span class="menu-title text-white">
                                        Caja
                                    </span>
                                </a>
                            </div>

                        </div>
                    </div>

                @endif


                @if(auth()->user()->esAdministrador())
                    <div data-kt-menu-trigger="click" class="menu-item menu-accordion">
                        <span class="menu-link">
                            <span class="menu-icon">
                                <i class="fa fa-university"></i>
                            </span>
                            <span class="menu-title text-white">
                                ADMINISTRACIÓN
                            </span>
                            <span class="menu-arrow"></span>
                        </span>

                        <div class="menu-sub menu-sub-accordion">

                            <div class="menu-item">
                                <a class="menu-link {{ Route::currentRouteName() == 'caja.listado' ? 'active' : '' }}"
                                    href="{{ route('caja.listado') }}">

                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>

                                    <span class="menu-title text-white">
                                        Caja
                                    </span>
                                </a>
                            </div>
                            <div class="menu-item">
                                <a class="menu-link {{ Route::currentRouteName() == 'categoria.listado' ? 'active' : '' }}"
                                    href="{{ route('categoria.listado') }}">

                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>

                                    <span class="menu-title text-white">
                                        Categorias
                                    </span>
                                </a>
                            </div>
                            <div class="menu-item">
                                <a class="menu-link {{ Route::currentRouteName() == 'cliente.listado' ? 'active' : '' }}"
                                    href="{{ route('cliente.listado') }}">

                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>

                                    <span class="menu-title text-white">
                                        Clientes
                                    </span>
                                </a>
                            </div>
                            <div class="menu-item">
                                <a class="menu-link" href="{{ url('pago/listadoDeuda') }}">

                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>

                                    <span class="menu-title text-white">
                                        Cuentas por Cobrar
                                    </span>
                                </a>
                            </div>

                            <div class="menu-item">
                                <a class="menu-link {{ Route::currentRouteName() == 'devolucion.listado' ? 'active' : '' }}"
                                    href="{{ route('devolucion.listado') }}">

                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>

                                    <span class="menu-title text-white">
                                        Devolucion
                                    </span>
                                </a>
                            </div>

                            <div class="menu-item">
                                <a class="menu-link {{ Route::currentRouteName() == 'incorporacion.listado' ? 'active' : '' }}"
                                    href="{{ route('incorporacion.listado') }}">

                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>

                                    <span class="menu-title text-white">
                                        Incorporacion
                                    </span>
                                </a>
                            </div>

                            <div class="menu-item">
                                <a class="menu-link {{ Route::currentRouteName() == 'marca.listado' ? 'active' : '' }}"
                                    href="{{ route('marca.listado') }}">

                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>

                                    <span class="menu-title text-white">
                                        Marcas
                                    </span>
                                </a>
                            </div>

                            <div class="menu-item">
                                <a class="menu-link {{ Route::currentRouteName() == 'proveedor.listado' ? 'active' : '' }}"
                                    href="{{ route('proveedor.listado') }}">

                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>

                                    <span class="menu-title text-white">
                                        Proveedores
                                    </span>
                                </a>
                            </div>

                            <div class="menu-item">
                                <a class="menu-link {{ Route::currentRouteName() == 'producto.listado' ? 'active' : '' }}"
                                    href="{{ route('producto.listado') }}">

                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>

                                    <span class="menu-title text-white">
                                        Producto
                                    </span>
                                </a>
                            </div>

                            <div class="menu-item">
                                <a class="menu-link {{ Route::currentRouteName() == 'rol.listado' ? 'active' : '' }}"
                                    href="{{ route('rol.listado') }}">

                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>

                                    <span class="menu-title text-white">
                                        Roles
                                    </span>
                                </a>
                            </div>

                            <div class="menu-item">
                                <a class="menu-link {{ Route::currentRouteName() == 'sucursal.listado' ? 'active' : '' }}"
                                    href="{{ route('sucursal.listado') }}">

                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>

                                    <span class="menu-title text-white">
                                        Sucursales
                                    </span>
                                </a>
                            </div>

                            <div class="menu-item">
                                <a class="menu-link {{ Route::currentRouteName() == 'unidad.listado' ? 'active' : '' }}"
                                    href="{{ route('unidad.listado') }}">

                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>

                                    <span class="menu-title text-white">
                                        Unidades
                                    </span>
                                </a>
                            </div>

                            <div class="menu-item">
                                <a class="menu-link {{ Route::currentRouteName() == 'user.listado' ? 'active' : '' }}"
                                    href="{{ route('user.listado') }}">

                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>

                                    <span class="menu-title text-white">
                                        Usuarios
                                    </span>
                                </a>
                            </div>

                        </div>
                    </div>

                @endif

                @if(
                        auth()->user()->esAdministrador()
                        || auth()->user()->esOperador()
                        || auth()->user()->esVentas()
                    )

                    <div data-kt-menu-trigger="click" class="menu-item menu-accordion">

                        <span class="menu-link">

                            <span class="menu-icon">
                                <i class="fa fa-university"></i>
                            </span>

                            <span class="menu-title text-white">
                                VENTAS
                            </span>

                            <span class="menu-arrow"></span>
                        </span>

                        <div class="menu-sub menu-sub-accordion">

                            {{-- VENTA --}}
                            <div class="menu-item">
                                <a class="menu-link" href="{{ route('venta.formulario') }}">

                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>

                                    <span class="menu-title text-white">
                                        Venta
                                    </span>
                                </a>
                            </div>

                            {{-- LISTADO --}}
                            <div class="menu-item">
                                <a class="menu-link" href="{{ route('venta.listado') }}">

                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>

                                    <span class="menu-title text-white">
                                        Listado Venta
                                    </span>
                                </a>
                            </div>

                            {{-- VENTAS DIA --}}
                            <div class="menu-item">
                                <a class="menu-link {{ Route::currentRouteName() == 'pago.listado' ? 'active' : '' }}"
                                    href="{{ route('pago.listado') }}">

                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>

                                    <span class="menu-title text-white">
                                        Ventas del Dia
                                    </span>
                                </a>
                            </div>

                        </div>
                    </div>

                @endif


                @if(
                        auth()->user()->esAdministrador()
                        || auth()->user()->esAlmacen()
                    )

                    <div data-kt-menu-trigger="click" class="menu-item menu-accordion">

                        <span class="menu-link">

                            <span class="menu-icon">
                                <i class="fa fa-university"></i>
                            </span>

                            <span class="menu-title text-white">
                                REPORTES
                            </span>

                            <span class="menu-arrow"></span>
                        </span>

                        <div class="menu-sub menu-sub-accordion">

                            <div class="menu-item">
                                <a class="menu-link {{ Route::currentRouteName() == 'reporte.inventarios' ? 'active' : '' }}"
                                    href="{{ route('reporte.inventarios') }}">

                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>

                                    <span class="menu-title text-white">
                                        Reporte de inventarios
                                    </span>
                                </a>
                            </div>


                            <div class="menu-item">
                                <a class="menu-link {{ Route::currentRouteName() == 'reporte.ventas' ? 'active' : '' }}"
                                    href="{{ route('reporte.ventas') }}">

                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>

                                    <span class="menu-title text-white">
                                        Reporte de ventas
                                    </span>
                                </a>
                            </div>


                            <div class="menu-item">
                                <a class="menu-link {{ Route::currentRouteName() == 'reporte.cajas' ? 'active' : '' }}"
                                    href="{{ route('reporte.cajas') }}">

                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>

                                    <span class="menu-title text-white">
                                        Reporte de cajas
                                    </span>
                                </a>
                            </div>


                            <div class="menu-item">
                                <a class="menu-link {{ Route::currentRouteName() == 'reporte.utilidades' ? 'active' : '' }}"
                                    href="{{ route('reporte.utilidades') }}">

                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>

                                    <span class="menu-title text-white">
                                        Reporte de utilidades
                                    </span>
                                </a>
                            </div>


                            <div class="menu-item">
                                <a class="menu-link {{ Route::currentRouteName() == 'reporte.ingreso_salida' ? 'active' : '' }}"
                                    href="{{ route('reporte.ingreso_salida') }}">

                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>

                                    <span class="menu-title text-white">
                                        Reporte de ingresos y salidas
                                    </span>
                                </a>
                            </div>


                            <div class="menu-item">
                                <a class="menu-link {{ Route::currentRouteName() == 'reporte.historial_precios' ? 'active' : '' }}"
                                    href="{{ route('reporte.historial_precios') }}">

                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>

                                    <span class="menu-title text-white">
                                        Historial de precios
                                    </span>
                                </a>
                            </div>


                            <div class="menu-item">
                                <a class="menu-link {{ Route::currentRouteName() == 'reporte.pagos' ? 'active' : '' }}"
                                    href="{{ route('reporte.pagos') }}">

                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>

                                    <span class="menu-title text-white">
                                        Reporte de pagos
                                    </span>
                                </a>
                            </div>

                        </div>
                    </div>

                @endif

            </div>
        </div>
    </div>
</div>
```