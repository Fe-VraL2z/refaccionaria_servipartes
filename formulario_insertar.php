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
    <title>Agregar Cliente - SERVIPARTES</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        /* Fondo desenfocado */
        body {
            margin: 0;
            padding: 0;
            height: 100vh;
            background: url('images/paginaprinsipalservipartes.png') no-repeat center center fixed;
            background-size: cover;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        /* Efecto de desenfoque */
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

        /* Estilo del cuadro del formulario */
        .form-container {
            background-color: rgba(255, 255, 255, 0.9);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }

        .form-container h1 {
            margin-bottom: 20px;
            font-size: 24px;
            color: #333;
        }

        .form-container label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #555;
        }

        .form-container input {
            width: 100%;
            padding: 10px;
            margin-bottom: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }

        .form-container button {
            width: 48%;
            padding: 10px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
        }

        .form-container button.btn-guardar {
            background-color: #28a745;
            color: white;
        }

        .form-container button.btn-cancelar {
            background-color: #dc3545;
            color: white;
        }

        .form-container button:hover {
            opacity: 0.9;
        }

        .form-container button:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .error-message {
            color: #dc3545;
            font-size: 14px;
            margin-bottom: 10px;
            display: none;
        }

        .error-input {
            border-color: #dc3545 !important;
        }
    </style>
</head>
<body>
    <!-- Cuadro del formulario -->
    <div class="form-container">
        <h1>Agregar Cliente</h1>
        <form id="clienteForm" action="guardar_cliente.php" method="post">
            <div class="mb-3">
                <label for="id_cliente">ID del cliente:</label>
                <input type="text" id="id_cliente" name="id_cliente" required>
                <div id="id_error" class="error-message">⚠ Este ID de cliente ya existe</div>
            </div>
            <div class="mb-3">
                <label for="nombre">Nombre:</label>
                <input type="text" id="nombre" name="nombre" required>
            </div>
            <div class="mb-3">
                <label for="telefono">Teléfono:</label>
                <input type="text" id="telefono" name="telefono" required>
            </div>
            <div class="mb-3">
                <label for="direccion">Dirección:</label>
                <input type="text" id="direccion" name="direccion" required>
            </div>
            <div class="mb-3">
                <label for="rfc">RFC:</label>
                <input type="text" id="rfc" name="rfc" required>
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
        $('#id_cliente').on('input', function() {
            clearTimeout(delayTimer);
            var idCliente = $(this).val().trim();
            
            if(idCliente.length > 0) {
                delayTimer = setTimeout(function() {
                    $.ajax({
                        url: 'validar_id.php',
                        type: 'POST',
                        data: { id_cliente: idCliente },
                        dataType: 'json',
                        success: function(response) {
                            if(response.existe) {
                                $('#id_error').show();
                                $('#id_cliente').addClass('error-input');
                                idExiste = true;
                                $('#btnGuardar').prop('disabled', true);
                            } else {
                                $('#id_error').hide();
                                $('#id_cliente').removeClass('error-input');
                                idExiste = false;
                                $('#btnGuardar').prop('disabled', false);
                            }
                        },
                        error: function() {
                            console.log('Error al verificar el ID');
                        }
                    });

// Limpiar campos cuando el ID existe
$('#btnLimpiar').click(function() {
            $('#nombre').val('');
            $('#telefono').val('');
            $('#direccion').val('');
            $('#rfc').val('');
            $('#id_error').hide();
            $('#id_cliente').removeClass('error-input');
            $(this).hide();
            idExiste = false;
            $('#btnGuardar').prop('disabled', false);
        });

                }, 500); // Espera 500ms después de que el usuario deja de escribir
            } else {
                $('#id_error').hide();
                $('#id_cliente').removeClass('error-input');
                idExiste = false;
                $('#btnGuardar').prop('disabled', false);
            }
        });
        
        // Validación antes de enviar el formulario
        $('#clienteForm').on('submit', function(e) {
            if(idExiste) {
                e.preventDefault();
                $('#id_error').show();
                $('#id_cliente').addClass('error-input').focus();
            }
        });
    });
    </script>
</body>
</html>