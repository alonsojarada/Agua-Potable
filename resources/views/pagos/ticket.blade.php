<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ticket de Pago - {{ $pago->usuario->folio }}</title>
    <style>
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 12px;
            color: #000;
            background: #eee;
            margin: 0;
            padding: 20px;
        }
        .ticket {
            width: 280px; /* Ancho estándar para impresoras térmicas de 80mm */
            background: #fff;
            padding: 15px;
            margin: 0 auto;
            box-shadow: 0 0 5px rgba(0,0,0,0.1);
        }
        .center { text-align: center; }
        .right { text-align: right; }
        .bold { font-weight: bold; }
        .line { border-bottom: 1px dashed #000; margin: 8px 0; }
        .flex { display: flex; justify-content: space-between; }
        
        /* Ocultar elementos de navegación al imprimir */
        @media print {
            body { background: none; padding: 0; }
            .no-print { display: none; }
            .ticket { width: 100%; box-shadow: none; padding: 0; }
        }
    </style>
</head>
<body>

    <!-- Botones de Acción (No salen en la impresión) -->
    <div class="center no-print" style="margin-bottom: 15px;">
        <button onclick="window.print()" style="background: #2563eb; color: white; border: none; padding: 8px 15px; font-weight: bold; border-radius: 4px; cursor: pointer;">
            🖨️ Imprimir Ticket
        </button>
        <button onclick="window.close()" style="background: #4b5563; color: white; border: none; padding: 8px 15px; font-weight: bold; border-radius: 4px; cursor: pointer; margin-left: 5px;">
            ✖ Cerrar
        </button>
    </div>

    <!-- Contenido del Ticket -->
    <div class="ticket">
        <div class="center bold" style="font-size: 14px;">SISTEMA DE AGUA / COBRANZA</div>
        <div class="center">Comprobante de Pago</div>
        <div class="center" style="font-size: 10px; color: #555;">Boquilla de las Perlas</div>
        
        <div class="line"></div>

        <div><span class="bold">Folio:</span> {{ $pago->usuario->folio }}</div>
        <div><span class="bold">Cliente:</span> {{ $pago->usuario->nombre_completo }}</div>
        <div><span class="bold">Dirección:</span> {{ $pago->usuario->direccion ?? 'N/D' }}</div>
        <div><span class="bold">Red:</span> {{ $pago->usuario->red->nombre ?? 'N/D' }}</div>

        <div class="line"></div>

        <div><span class="bold">Periodo:</span> {{ $pago->periodo_mes }} {{ $pago->periodo_anio }}</div>
        <div><span class="bold">F. Pago:</span> {{ $pago->fecha_pago ? date('d/m/Y H:i', strtotime($pago->fecha_pago)) : date('d/m/Y H:i') }}</div>
        <div><span class="bold">Estatus:</span> <span class="bold" style="text-transform: uppercase;">{{ $pago->estatus }}</span></div>

        <div class="line"></div>

        <div class="flex bold" style="font-size: 14px;">
            <span>TOTAL PAGADO:</span>
            <span>${{ number_format($pago->monto, 2) }}</span>
        </div>

        <div class="line"></div>

       <!-- CÓDIGO QR DE SEGURIDAD -->
        @php
            // Buscamos el recibo vinculado a este pago
            $periodoCompleto = $pago->periodo_mes . ' ' . $pago->periodo_anio;
            $recibo = \App\Models\Recibo::where('usuario_id', $pago->usuario_id)
                ->where('mes_anio', $periodoCompleto)
                ->first();
        @endphp

        @if($recibo)
            <div class="center" style="margin: 10px 0;">
                @php
                    // Usamos el token_seguridad del recibo para el QR
                    $urlVerificacion = route('recibos.verificar', ['token' => $recibo->token_seguridad]);
                @endphp
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=120x120&data={{ urlencode($urlVerificacion) }}" 
                     alt="QR de Validación" style="width: 100px; height: 100px; background: #fff; padding: 2px; border: 1px solid #ccc;">
                <div style="font-size: 8px; color: #555; margin-top: 2px; font-family: monospace;">{{ $recibo->folio_recibo }}</div>
            </div>
            <div class="line"></div>
        @endif

        <div class="center" style="font-size: 10px; margin-top: 10px;">
            ¡Gracias por su pago! <br>
            Conserve este ticket para cualquier aclaración.
        </div>
    </div>

    <script>
        // Opcional: Abre el diálogo de impresión automáticamente al abrir la vista
        // window.onload = function() { window.print(); }
    </script>
</body>
</html>