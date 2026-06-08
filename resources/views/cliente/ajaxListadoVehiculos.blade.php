<table class="table table-bordered">
    <thead>
        <tr>
            <th>Vehículo</th>
            <th>Modelo</th>
            <th>Placa</th>
            <th width="120">Acciones</th>
        </tr>
    </thead>

    <tbody>
        @forelse ($vehiculos as $vehiculo)
            <tr>
                <td>{{ $vehiculo->nombre_vehiculo }}</td>
                <td>{{ $vehiculo->modelo }}</td>
                <td>{{ $vehiculo->placa }}</td>
                <td>
                    <button class="btn btn-warning btn-sm" onclick='editarVehiculo(@json($vehiculo))'>
                        <i class="fa fa-edit"></i>
                    </button>
                    <button class="btn btn-danger btn-sm" onclick="eliminarVehiculo({{ $vehiculo->id }})">
                        <i class="fa fa-trash"></i>
                    </button>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="4" class="text-center">
                    No hay vehículos registrados
                </td>
            </tr>
        @endforelse
    </tbody>
</table>