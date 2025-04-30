<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Proveedor - SERVIPARTES</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body {
            margin: 0;
            padding: 0;
            height: 100vh;
            background: url('images/paginaprinsipalservipartes.png') no-repeat center center fixed;
            background-size: cover;
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: Arial, sans-serif;
        }
        
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: inherit;
            filter: blur(8px);
            z-index: -1;
        }
        
        .form-container {
            background-color: rgba(255, 255, 255, 0.95);
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 500px;
            max-height: 80vh;
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: #888 #f1f1f1;
        }
        
        .form-container::-webkit-scrollbar {
            width: 8px;
        }
        
        .form-container::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }
        
        .form-container::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 10px;
        }
        
        .form-container::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
        
        .form-container h1 {
            margin-bottom: 20px;
            font-size: 24px;
            color: #333;
            text-align: center;
            position: sticky;
            top: 0;
            background-color: rgba(255, 255, 255, 0.95);
            padding: 10px 0;
            z-index: 1;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #444;
        }
        
        .form-group input, .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            background-color: rgba(255, 255, 255, 0.8);
        }
        
        .form-group input:focus {
            border-color: #4CAF50;
            outline: none;
            box-shadow: 0 0 5px rgba(76, 175, 80, 0.3);
        }
        
        .error-message {
            color: #dc3545;
            font-size: 14px;
            margin-top: 5px;
            display: none;
        }
        
        .error-input {
            border-color: #dc3545 !important;
        }
        
        .button-group {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
            position: sticky;
            bottom: 0;
            background-color: rgba(255, 255, 255, 0.95);
            padding: 15px 0;
            z-index: 1;
        }
        
        .button-group button {
            width: 48%;
            padding: 12px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .btn-guardar {
            background-color: #28a745;
            color: white;
        }
        
        .btn-guardar:hover {
            background-color: #218838;
        }
        
        .btn-cancelar {
            background-color: #dc3545;
            color: white;
        }
        
        .btn-cancelar:hover {
            background-color: #c82333;
        }
        
        button:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }
        
        @media (max-height: 700px) {
            .form-container {
                max-height: 65vh;
            }
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h1>Agregar Proveedor</h1>
        <form id="proveedorForm" action="guardar_proveedor.php" method="post">
            <div class="form-group">
                <label for="id_proveedor">ID Proveedor:</label>
                <input type="number" id="id_proveedor" name="id_proveedor" min="1" max="9999" required>
                <div id="id_error" class="error-message">⚠ Este ID de proveedor ya existe</div>
            </div>
            
            <div class="form-group">
                <label for="nombre">Nombre:</label>
                <input type="text" id="nombre" name="nombre" maxlength="50" required>
            </div>
            
            <div class="form-group">
                <label for="telefono">Teléfono:</label>
                <input type="tel" id="telefono" name="telefono" maxlength="10" pattern="[0-9]{10}" required>
            </div>
            
            <div class="form-group">
                <label for="direccion">Dirección:</label>
                <input type="text" id="direccion" name="direccion" maxlength="50" required>
            </div>
            
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" maxlength="100">
            </div>
            
            <div class="form-group">
                <label for="rfc">RFC:</label>
                <input type="text" id="rfc" name="rfc" maxlength="14" pattern="^[A-ZÑ&]{3,4}[0-9]{6}[A-Z0-9]{3}$" title="Formato de RFC válido">
            </div>
            
            <div class="form-group">
                <label for="contacto_principal">Contacto principal:</label>
                <input type="tel" id="contacto_principal" name="contacto_principal" maxlength="10" pattern="[0-9]{10}">
            </div>
            
            <div class="form-group">
                <label for="metodo_pago">Método de Pago:</label>
                <select id="metodo_pago" name="metodo_pago">
                    <option value="">Seleccione...</option>
                    <option value="Transferencia">Transferencia</option>
                    <option value="Cheque">Cheque</option>
                    <option value="Efectivo">Efectivo</option>
                    <option value="Tarjeta">Tarjeta</option>
                    <option value="Otro">Otro</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="plazo_entrega">Plazo de Entrega:</label>
                <select id="plazo_entrega" name="plazo_entrega">
                    <option value="">Seleccione...</option>
                    <option value="Inmediato">Inmediato</option>
                    <option value="24 horas">24 horas</option>
                    <option value="48 horas">48 horas</option>
                    <option value="3-5 días">3-5 días</option>
                    <option value="1 semana">1 semana</option>
                    <option value="2 semanas">2 semanas</option>
                    <option value="Personalizado">Personalizado</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="forma_envio">Forma de Envío:</label>
                <select id="forma_envio" name="forma_envio">
                    <option value="">Seleccione...</option>
                    <option value="Recoger en sucursal">Recoger en sucursal</option>
                    <option value="Mensajería">Mensajería</option>
                    <option value="Transporte propio">Transporte propio</option>
                    <option value="Flete">Flete</option>
                    <option value="Otro">Otro</option>
                </select>
            </div>
            
            <div class="button-group">
                <button type="submit" id="btnGuardar" class="btn-guardar">Guardar</button>
                <button type="button" class="btn-cancelar" onclick="window.location.href='dashboard.php'">Cancelar</button>
            </div>
        </form>
    </div>

    <script>
    $(document).ready(function() {
        var delayTimer;
        var idExiste = false;
        
        // Validación en tiempo real del ID
        $('#id_proveedor').on('input', function() {
            clearTimeout(delayTimer);
            var idProveedor = $(this).val().trim();
            
            if(idProveedor.length > 0) {
                delayTimer = setTimeout(function() {
                    $.ajax({
                        url: 'validar_id_proveedor.php',
                        type: 'POST',
                        data: { id_proveedor: idProveedor },
                        dataType: 'json',
                        success: function(response) {
                            if(response.existe) {
                                $('#id_error').show();
                                $('#id_proveedor').addClass('error-input');
                                idExiste = true;
                                $('#btnGuardar').prop('disabled', true);
                            } else {
                                $('#id_error').hide();
                                $('#id_proveedor').removeClass('error-input');
                                idExiste = false;
                                $('#btnGuardar').prop('disabled', false);
                            }
                        },
                        error: function() {
                            console.log('Error al verificar el ID');
                        }
                    });
                }, 500);
            } else {
                $('#id_error').hide();
                $('#id_proveedor').removeClass('error-input');
                idExiste = false;
                $('#btnGuardar').prop('disabled', false);
            }
        });
        
        $('#proveedorForm').on('submit', function(e) {
            if(idExiste) {
                e.preventDefault();
                $('#id_error').show();
                $('#id_proveedor').addClass('error-input').focus();
            }
        });
    });
    </script>
</body>
</html>