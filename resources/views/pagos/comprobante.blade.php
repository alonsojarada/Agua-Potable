<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Comprobante de Pago - {{ $pago->usuario->folio }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            color: #333;
            background: #f5f5f5;
            margin: 0;
            padding: 30px;
        }
        .sheet {
            max-width: 700px;
            background: #fff;
            padding: 40px;
            margin: 0 auto;
            border: 1px solid #ddd;
            box-shadow: 0 0 10px rgba(0,0,0,0.05);
            border-radius: 8px;
        }
        .header {
            display: flex;
            justify-content: space-between;
            border-bottom: 2px solid #2563eb;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .company-title {
            font-size: 18px;
            font-weight: bold;
            color: #1e3a8a;
        }
        .receipt-info {
            text-align: right;
        }
        .receipt-info h2 {
            margin: 0;
            font-size: 18px;
            color: #2563eb;
        }
        .section-title {
            font-size: 12px;
            font-weight: bold;
            color: #6b7280;
            text-transform: uppercase;
            margin-bottom: 5px;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 3px;
        }
        .grid {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .col {
            flex: 1;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            margin-bottom: 25px;
        }
        th, td {
            border: 1px solid #e5e7eb;
            padding: 10px;
            text-align: left;
            font-size: 13px;
        }
        th {
            background: #f9fafb;
            color: #374151;
        }
        .total-box {
            text-align: right;
            font-size: 16px;
            font-weight: bold;
            margin-top: 10px;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 12px;
            color: #6b7280;
            border-top: 1px solid #e5e7eb;
            padding-top: 15px;
        }
        @media print {
            body { background: none; padding: 0; }
            .sheet { border: none; box-shadow: none; padding: 10px; max-width: 100%; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>

    <!-- Botones de Acción (No salen al imprimir/guardar PDF) -->
    <div style="max-width: 700px; margin: 0 auto 15px auto; text-align: right;" class="no-print">
        <button onclick="window.print()" style="background: #2563eb; color: white; border: none; padding: 8px 16px; font-weight: bold; border-radius: 4px; cursor: pointer;">
            🖨️ Imprimir / Guardar como PDF
        </button>
        <button onclick="window.close()" style="background: #4b5563; color: white; border: none; padding: 8px 16px; font-weight: bold; border-radius: 4px; cursor: pointer; margin-left: 5px;">
            ✖ Cerrar
        </button>
    </div>

    <!-- Hoja del Comprobante -->
    <div class="sheet">
        
        <!-- Cabecera -->
        <div class="header">
            <div>
                <div class="company-title">SISTEMA DE AGUA POTABLE</div>
                <div style="font-size: 12px; color: #555;">Boquilla de las Perlas</div>
            </div>
            <div class="receipt-info">
                <h2>COMPROBANTE DE PAGO</h2>
                <div style="font-size: 12px; color: #555; margin-top: 3px;">Folio Interno: #{{ $pago->id }}</div>
            </div>
        </div>

        <!-- Datos del Usuario y Fecha -->
        <div class="grid">
            <div class="col">
                <div class="section-title">Datos del Usuario</div>
                <div style="margin-top: 5px;"><strong>Folio / Toma:</strong> {{ $pago->usuario->folio }}</div>
                <div><strong>Nombre:</strong> {{ $pago->usuario->nombre_completo }}</div>
                <div><strong>Dirección:</strong> {{ $pago->usuario->direccion ?? 'N/D' }}</div>
                <div><strong>Red de Agua:</strong> {{ $pago->usuario->red->nombre ?? 'N/D' }}</div>
            </div>
            <div class="col" style="text-align: right;">
                <div class="section-title">Detalles de Emisión</div>
                <div style="margin-top: 5px;"><strong>Fecha de Pago:</strong> {{ $pago->fecha_pago ? date('d/m/Y H:i', strtotime($pago->fecha_pago)) : date('d/m/Y H:i') }}</div>
                <div><strong>Estatus:</strong> <span style="color: #16a34a; font-weight: bold; text-transform: uppercase;">{{ $pago->estatus }}</span></div>
            </div>
        </div>

        <!-- Tabla de Concepto -->
        <div class="section-title">Concepto del Cobro</div>
        <table>
            <thead>
                <tr>
                    <th>Periodo / Mes</th>
                    <th>Año</th>
                    <th>Descripción</th>
                    <th style="text-align: right;">Importe</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $pago->periodo_mes }}</td>
                    <td>{{ $pago->periodo_anio }}</td>
                    <td>Servicio de suministro de agua potable correspondiente al periodo.</td>
                    <td style="text-align: right;">${{ number_format($pago->monto, 2) }}</td>
                </tr>
            </tbody>
        </table>

        <!-- Totales -->
        <div class="total-box">
            Total Pagado: ${{ number_format($pago->monto, 2) }} MXN
        </div>

        <!-- Pie de página -->
        <div class="footer">
            Este comprobante es una representación digital de un pago registrado en el sistema.<br>
            ¡Gracias por su puntualidad en sus pagos!
        </div>

    </div>

</body>
</html>