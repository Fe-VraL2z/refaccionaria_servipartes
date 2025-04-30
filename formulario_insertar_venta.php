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
    <title>Agregar Venta - SERVIPARTES</title>
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
            background-color: rgba(255, 255, 255, 0.9);
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 600px;
            max-height: 90vh;
            overflow-y: auto;
        }
        .form-container h1 {
            margin-bottom: 20px;
            font-size: 24px;
            color: #333;
            text-align: center;
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
        .form-group input, .form-group select, .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
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
        }
        .button-group button {
            width: 48%;
            padding: 12px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
        }
        .btn-guardar {
            background-color: #28a745;
            color: white;
        }
        .btn-cancelar {
            background-color: #dc3545;
            color: white;
        }
        button:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }
        #tiempo_garantia_group {
            display: none;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h1>Agregar Venta</h1>
        <form id="ventaForm" action="guardar_venta.php" method="post">
            <!-- Datos del cliente -->
            <div class="form-group">
                <label for="id_cliente">ID del Cliente:</label>
                <input type="text" id="id_cliente" name="id_cliente" required>
                <div id="id_cliente_error" class="error-message">⚠ Este ID de cliente no existe</div>
            </div>
            
            <!-- Datos de la venta -->
            <div class="form-group">
                <label for="folio">Folio:</label>
                <input type="text" id="folio" name="folio" maxlength="20" required>
            </div>
            
            <div class="form-group">
                <label for="direccion_negocio">Dirección del negocio:</label>
                <input type="text" id="direccion_negocio" name="direccion_negocio" maxlength="100" required>
            </div>
            
            <div class="form-group">
                <label for="numero_atencion">Número de atención a cliente:</label>
                <input type="text" id="numero_atencion" name="numero_atencion" maxlength="10" required>
            </div>
            
            <!-- Datos del producto -->
            <div class="form-group">
                <label for="id_producto">ID del Producto:</label>
                <input type="text" id="id_producto" name="id_producto" required>
                <div id="id_producto_error" class="error-message">⚠ Este ID de producto no existe</div>
            </div>
            
            <div class="form-group">
                <label for="nombre_producto">Nombre del producto:</label>
                <input type="text" id="nombre_producto" name="nombre_producto" maxlength="50" required>
            </div>
            
            <div class="form-group">
                <label for="precio_venta">Precio de venta:</label>
                <input type="number" id="precio_venta" name="precio_venta" step="0.01" min="0" required>
            </div>
            
            <div class="form-group">
                <label for="marca">Marca:</label>
                <input type="text" id="marca" name="marca" maxlength="5" required>
            </div>
            
            <div class="form-group">
                <label for="modelo">Modelo:</label>
                <input type="text" id="modelo" name="modelo" maxlength="25" required>
            </div>
            
            <!-- Garantía -->
            <div class="form-group">
                <label for="garantia">Garantía:</label>
                <select id="garantia" name="garantia" required>
                    <option value="1">Sí aplica</option>
                    <option value="0">No aplica</option>
                </select>
            </div>
            
            <div class="form-group" id="tiempo_garantia_group">
                <label for="tiempo_garantia">Tiempo de garantía:</label>
                <input type="text" id="tiempo_garantia" name="tiempo_garantia" maxlength="25">
            </div>
            
            <div class="button-group">
                <button type="submit" id="btnGuardar" class="btn-guardar">Guardar</button>
                <button type="button" class="btn-cancelar" onclick="window.location.href='dashboard.php'">Cancelar</button>
            </div>
        </form>
    </div>

    <script>
    $(document).ready(function() {
        var idClienteValido = false;
        var idProductoValido = false;

        // Validación en tiempo real del ID Cliente
        $('#id_cliente').on('blur', function() {
            var idCliente = $(this).val().trim();
            if(idCliente.length > 0) {
                $.ajax({
                    url: 'validar_id_cliente.php',
                    type: 'POST',
                    data: { id_cliente: idCliente },
                    dataType: 'json',
                    success: function(response) {
                        if(response.existe) {
                            $('#id_cliente_error').hide();
                            $('#id_cliente').removeClass('error-input');
                            idClienteValido = true;
                        } else {
                            $('#id_cliente_error').show();
                            $('#id_cliente').addClass('error-input');
                            idClienteValido = false;
                        }
                        actualizarBotonGuardar();
                    }
                });
            }
        });

        // Validación en tiempo real del ID Producto
        $('#id_producto').on('blur', function() {
            var idProducto = $(this).val().trim();
            if(idProducto.length > 0) {
                $.ajax({
                    url: 'validar_id_producto.php',
                    type: 'POST',
                    data: { id_producto: idProducto },
                    dataType: 'json',
                    success: function(response) {
                        if(response.existe) {
                            $('#id_producto_error').hide();
                            $('#id_producto').removeClass('error-input');
                            idProductoValido = true;
                            
                            // Autocompletar datos del producto
                            if(response.datos) {
                                $('#nombre_producto').val(response.datos['Nombre del producto'] || '');
                                $('#precio_venta').val(response.datos['Precio'] || '');
                                $('#marca').val(response.datos['Marca'] || '');
                                $('#modelo').val(response.datos['Modelo'] || '');
                            }
                        } else {
                            $('#id_producto_error').show();
                            $('#id_producto').addClass('error-input');
                            idProductoValido = false;
                        }
                        actualizarBotonGuardar();
                    }
                });
            }
        });

        // Manejo de la garantía
        $('#garantia').change(function() {
            if($(this).val() == '1') {
                $('#tiempo_garantia_group').show();
                $('#tiempo_garantia').prop('required', true);
            } else {
                $('#tiempo_garantia_group').hide();
                $('#tiempo_garantia').prop('required', false);
                $('#tiempo_garantia').val('');
            }
        });

        // Validación antes de enviar
        $('#ventaForm').submit(function(e) {
            if(!idClienteValido || !idProductoValido) {
                e.preventDefault();
                if(!idClienteValido) {
                    $('#id_cliente_error').show();
                    $('#id_cliente').addClass('error-input').focus();
                }
                if(!idProductoValido) {
                    $('#id_producto_error').show();
                    $('#id_producto').addClass('error-input').focus();
                }
            }
        });

        function actualizarBotonGuardar() {
            if(idClienteValido && idProductoValido) {
                $('#btnGuardar').prop('disabled', false);
            } else {
                $('#btnGuardar').prop('disabled', true);
            }
        }
    });
    </script>
</body>
</html>