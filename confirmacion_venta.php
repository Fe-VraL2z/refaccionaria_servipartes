<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmación de Venta - SERVIPARTES</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .confirmation-card {
            max-width: 600px;
            margin: 50px auto;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            background-color: white;
        }
        .ticket-container {
            background-color: white;
            width: 300px;
            margin: 20px auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .btn-action {
            width: 200px;
            margin: 10px;
            padding: 15px;
            font-size: 18px;
        }
        .option-checkbox {
            display: flex;
            align-items: center;
            margin: 15px 0;
            padding: 10px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .option-checkbox:hover {
            background-color: #f0f0f0;
        }
        .option-checkbox input {
            margin-right: 15px;
            transform: scale(1.5);
        }
        .option-checkbox label {
            margin-bottom: 0;
            font-size: 18px;
            cursor: pointer;
        }
        @media print {
            body * {
                visibility: hidden;
            }
            .ticket-container, .ticket-container * {
                visibility: visible;
            }
            .ticket-container {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                margin: 0;
                padding: 15px;
                box-shadow: none;
                border: none;
            }
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body>
    <!-- Confirmación de Venta -->
    <div class="confirmation-card">
        <div class="text-center mb-4">
            <i class="fas fa-check-circle text-success" style="font-size: 72px;"></i>
            <h2 class="mt-3">¡Venta Registrada Exitosamente!</h2>
        </div>
        
        <div class="p-3 bg-light rounded mb-4">
            <h4 class="text-center">Detalles de la Venta</h4>
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Folio:</strong> MINE202505129746</p>
                    <p><strong>Cliente:</strong> Michelle Orduño Jiménez</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Fecha:</strong> 12/05/2025 02:34 a. m.</p>
                    <p><strong>Total:</strong> $53,372.38</p>
                </div>
            </div>
        </div>
        
        <h4 class="text-center mb-4">¿Qué deseas hacer ahora?</h4>
        
        <!-- Opciones con checkboxes -->
        <div class="options-container mb-4">
            <div class="option-checkbox" onclick="toggleCheckbox('print')">
                <input type="checkbox" id="printOption">
                <label for="printOption"><i class="fas fa-print me-2"></i> Imprimir Ticket</label>
            </div>
            
            <div class="option-checkbox" onclick="toggleCheckbox('email')">
                <input type="checkbox" id="emailOption" checked>
                <label for="emailOption"><i class="fas fa-envelope me-2"></i> Enviar por Correo</label>
            </div>
            
            <div class="option-checkbox" onclick="toggleCheckbox('exit')">
                <input type="checkbox" id="exitOption">
                <label for="exitOption"><i class="fas fa-home me-2"></i> Volver al Inicio</label>
            </div>
        </div>
        
        <!-- Botón de confirmar -->
        <div class="d-flex justify-content-center">
            <button class="btn btn-primary btn-lg" onclick="procesarOpciones()">
                <i class="fas fa-check-circle me-2"></i> Confirmar
            </button>
        </div>
    </div>

    <!-- Ticket de Venta (oculto inicialmente) -->
    <div id="ticket-venta" class="ticket-container" style="display: none;">
        <div class="ticket-header text-center mb-3">
            <h2>SERVIPARTES</h2>
            <p>Ticket de Venta</p>
            <p>Fecha: 12/05/2025 02:34</p>
        </div>
        
        <div class="ticket-info mb-3">
            <p><strong>Folio:</strong> MINE202505129746</p>
            <p><strong>Cliente:</strong> Michelle Orduño Jiménez</p>
            <p><strong>Dirección:</strong> michelleorduñojim03@gmail.com</p>
            <p><strong>Teléfono:</strong> 7331259358</p>
        </div>
        
        <div class="ticket-items mb-3">
            <div class="d-flex justify-content-between border-bottom py-2">
                <span>Faros con lupa x5</span>
                <span>$2,699.95</span>
            </div>
            <div class="d-flex justify-content-between border-bottom py-2">
                <span>Radiador Automotriz x7</span>
                <span>$49,360.43</span>
            </div>
            <div class="d-flex justify-content-between border-bottom py-2">
                <span>Aceite de carro x2</span>
                <span>$1,512.00</span>
            </div>
        </div>
        
        <div class="ticket-total text-end mb-3">
            <h4><strong>Total: $53,572.38</strong></h4>
        </div>
        
        <div class="ticket-info mb-3">
            <p><strong>Método de Pago:</strong> Efectivo</p>
            <p><strong>Atendido por:</strong> Minerva Martínez Neri</p>
        </div>
        
        <div class="ticket-footer text-center mt-4">
            <p>Gracias por su compra</p>
            <p>Válido como comprobante fiscal</p>
        </div>
        
        <div class="button-group no-print mt-4 text-center">
            <button class="btn btn-primary me-2" onclick="window.print()">
                <i class="fas fa-print me-2"></i> Imprimir
            </button>
            <button class="btn btn-secondary" onclick="volverAlInicio()">
                <i class="fas fa-home me-2"></i> Salir
            </button>
        </div>
    </div>

    <!-- Modal para enviar por correo -->
    <div class="modal fade" id="emailModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Enviar Ticket por Correo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="mb-3">
                            <label class="form-label">Correo electrónico del cliente</label>
                            <input type="email" class="form-control" value="michelleorduñojim03@gmail.com">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Mensaje adicional (opcional)</label>
                            <textarea class="form-control" rows="3" placeholder="Ej: Adjunto encontrará el comprobante de su compra..."></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-success" onclick="enviarCorreo()">
                        <i class="fas fa-paper-plane me-2"></i> Enviar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Función para alternar checkboxes
        function toggleCheckbox(option) {
            const checkbox = document.getElementById(option + 'Option');
            checkbox.checked = !checkbox.checked;
        }

        // Función para procesar las opciones seleccionadas
        function procesarOpciones() {
            const printOption = document.getElementById('printOption').checked;
            const emailOption = document.getElementById('emailOption').checked;
            const exitOption = document.getElementById('exitOption').checked;

            if (printOption) {
                mostrarTicket();
            }

            if (emailOption) {
                mostrarEnviarCorreo();
            }

            if (exitOption) {
                volverAlInicio();
            }

            // Si no se seleccionó ninguna opción
            if (!printOption && !emailOption && !exitOption) {
                alert('Por favor selecciona al menos una opción');
            }
        }

        function mostrarTicket() {
            document.querySelector('.confirmation-card').style.display = 'none';
            document.getElementById('ticket-venta').style.display = 'block';
        }

        function mostrarEnviarCorreo() {
            var modal = new bootstrap.Modal(document.getElementById('emailModal'));
            modal.show();
        }

        function enviarCorreo() {
            alert('Ticket enviado con éxito a: michelleorduñojim03@gmail.com');
            var modal = bootstrap.Modal.getInstance(document.getElementById('emailModal'));
            modal.hide();
            
            // Si también se seleccionó imprimir, mostrar el ticket después de enviar el correo
            if (document.getElementById('printOption').checked) {
                mostrarTicket();
            } else if (document.getElementById('exitOption').checked) {
                volverAlInicio();
            }
        }

        function volverAlInicio() {
            // Simulación de redirección
            alert('Redirigiendo al dashboard...');
            // En un caso real: window.location.href = 'dashboard.php';
        }
    </script>
</body>
</html>