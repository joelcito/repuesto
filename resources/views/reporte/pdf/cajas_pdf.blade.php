<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Cajas</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
        }

        .titulo {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        th,
        td {
            border: 1px solid #444;
            padding: 3px;
            text-align: center;
        }

        th {
            background-color: #f0f0f0;
        }

        .producto {
            font-weight: bold;
            font-size: 12px;
            margin-top: 10px;
        }
    </style>
</head>

<body>
    <div class="titulo">
        REPORTE DE CAJAS
    </div>
    <table>
        <thead>
            <tr>
                <th>Usuario</th>
                <th>Apertura</th>
                <th>Cierre</th>
                <th>Ingresos</th>
                <th>Egresos</th>
                <th>Saldo</th>
            </tr>

        </thead>
        <tbody>
            @foreach($cajas as $caja)
                    <tr>
                        <td>{{ $caja->usuario->name ?? '' }}</td>
                        <td>{{ $caja->monto_apertura }}</td>
                        <td>{{ $caja->monto_cierre }}</td>
                        <td>{{ $caja->total_ingresos }}</td>
                        <td>{{ $caja->total_egresos }}</td>
                        <td>
                            {{
                $caja->monto_apertura +
                $caja->total_ingresos -
                $caja->total_egresos
                                }}
                        </td>
                    </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>