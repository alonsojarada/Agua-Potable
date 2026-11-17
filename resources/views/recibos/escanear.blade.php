<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xs sm:text-sm md:text-base lg:text-xl text-gray-800 leading-tight">
            {{ __('Caja Express - Escanear Recibo QR') }}
        </h2>
    </x-slot>

    <!-- Librería Open Source para lectura de QR con cámara web -->
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

    <div class="py-2 px-2 sm:px-3 w-full">
        @if(session('success'))
            <div class="mb-3 bg-emerald-100 border-l-4 border-emerald-500 text-emerald-700 px-4 py-2 rounded text-xs" role="alert">
                <p>{{ session('success') }}</p>
            </div>
        @endif

        @if($errors->any())
            <div class="mb-3 bg-red-100 border-l-4 border-red-500 text-red-700 px-4 py-2 rounded text-xs" role="alert">
                <p>{{ $errors->first() }}</p>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-3 max-w-5xl mx-auto">
            
            <!-- Columna Izquierda: Visor de la Cámara -->
            <div class="lg:col-span-6 bg-white border border-gray-200 rounded-lg p-4 shadow-2xs flex flex-col items-center">
                <h3 class="font-bold text-gray-800 text-xs sm:text-sm mb-3 uppercase">Cámara / Lector QR</h3>
                
                <div id="reader" class="w-full rounded-md overflow-hidden border border-gray-300"></div>
                <p class="text-[11px] text-gray-500 mt-2 text-center">Apunta la cámara de la PC o celular hacia el código QR del recibo o ticket.</p>
            </div>

            <!-- Columna Derecha: Resultado y Confirmación de Cobro -->
            <div class="lg:col-span-6 bg-white border border-gray-200 rounded-lg p-4 shadow-2xs flex flex-col justify-between">
                <div>
                    <h3 class="font-bold text-gray-800 text-xs sm:text-sm mb-3 uppercase">Información del Recibo Detectado</h3>
                    
                    <div id="resultado-recibo" class="bg-gray-50 border border-gray-200 rounded-md p-4 text-xs sm:text-sm text-gray-600">
                        <p class="italic text-center text-gray-400 py-6">Esperando lectura de código QR...</p>
                    </div>
                </div>

                <div id="contenedor-boton-cobro" class="hidden pt-4 border-t border-gray-200">
                    <form id="form-cobrar-qr" method="POST">
                        @csrf
                        <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-2.5 px-4 rounded-md text-xs sm:text-sm shadow-2xs transition cursor-pointer text-center">
                            ✔ Confirmar y Aplicar Cobro en Caja
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>

    <!-- Script de control de la cámara y validación AJAX -->
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            function onScanSuccess(decodedText, decodedResult) {
                // decodedText lee la URL generada en el QR (ej: http://127.0.0.1:8000/recibos/verificar/token_abc123)
                // Vamos a extraer o redirigir la petición al endpoint JSON de verificación
                
                // Reemplazamos la ruta de verificación visual por la ruta JSON de verificación
                let urlVerificacion = decodedText.replace('/recibos/verificar/', '/recibos/verificar-json/');

                fetch(urlVerificacion, {
                    headers: { 'Accept': 'application/json' }
                })
                .then(response => response.json())
                .then(data => {
                    const contenedor = document.getElementById('resultado-recibo');
                    const btnCobro = document.getElementById('contenedor-boton-cobro');
                    const formCobro = document.getElementById('form-cobrar-qr');

                    if (data.success) {
                        contenedor.innerHTML = `
                            <div class="space-y-1.5">
                                <p><strong>Folio Recibo:</strong> <span class="text-blue-600 font-bold">${data.recibo.folio_recibo}</span></p>
                                <p><strong>Titular:</strong> ${data.recibo.usuario.nombre_completo} (${data.recibo.usuario.folio})</p>
                                <p><strong>Período:</strong> ${data.recibo.mes_anio}</p>
                                <p class="text-base font-bold text-emerald-600 pt-1">Monto a Pagar: $${parseFloat(data.recibo.monto).toFixed(2)}</p>
                                <p class="text-[10px] text-green-600 font-bold uppercase mt-2">✔ Recibo Válido y Pendiente de Cobro</p>
                            </div>
                        `;
                        // Asignar dinámicamente la ruta de cobro con el ID del recibo
                        formCobro.action = `/recibos/cobrar-qr/${data.recibo.id}`;
                        btnCobro.classList.remove('hidden');
                    } else {
                        contenedor.innerHTML = `<p class="text-red-600 font-bold text-center py-4">${data.message}</p>`;
                        btnCobro.classList.add('hidden');
                    }
                })
                .catch(error => {
                    console.error('Error al procesar el QR:', error);
                });
            }

            // Inicializar el escáner HTML5 QR
            let html5QrcodeScanner = new Html5QrcodeScanner(
                "reader", { fps: 10, qrbox: 220 }
            );
            html5QrcodeScanner.render(onScanSuccess);
        });
    </script>
</x-app-layout>