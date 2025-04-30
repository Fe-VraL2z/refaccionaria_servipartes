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
    <title>Agregar Producto - SERVIPARTES</title>
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
    </style>
</head>
<body>
    <div class="form-container">
        <h1>Agregar Producto</h1>
        <form id="productoForm" action="guardar_producto.php" method="post">
            <div class="form-group">
                <label for="id_producto">ID del Producto:</label>
                <input type="text" id="id_producto" name="id_producto" required>
                <div id="id_error" class="error-message">⚠ Este ID de producto ya existe</div>
            </div>
            <div class="form-group">
                <label for="nombre">Nombre del producto:</label>
                <input type="text" id="nombre" name="nombre" maxlength="50" required>
            </div>
            <div class="form-group">
                <label for="descripcion">Descripción:</label>
                <input type="text" id="descripcion" name="descripcion" maxlength="50">
            </div>
            <div class="form-group">
                <label for="modelo">Modelo:</label>
                <input type="text" id="modelo" name="modelo" maxlength="30">
            </div>
            <div class="form-group">
                <label for="precio">Precio ($):</label>
                <input type="number" id="precio" name="precio" step="0.01" min="0" required>
            </div>
            <div class="form-group">
                <label for="cantidad_existencia">Cantidad de existencia:</label>
                <input type="number" id="cantidad_existencia" name="cantidad_existencia" min="0" required>
            </div>
            <div class="form-group">
                <label for="codigo_pieza">Código de pieza:</label>
                <input type="text" id="codigo_pieza" name="codigo_pieza" maxlength="50">
            </div>
            <div class="form-group">
                <label for="marca">Marca:</label>
                <input type="text" id="marca" name="marca" maxlength="30">
            </div>
            <div class="form-group">
                <label for="categoria">Categoría:</label>
                <input type="text" id="categoria" name="categoria" maxlength="25">
            </div>
            <div class="form-group">
                <label for="unidad_medida">Unidad de medida:</label>
                <input type="text" id="unidad_medida" name="unidad_medida" maxlength="30">
            </div>
            <div class="form-group">
                <label for="dimensiones">Dimensiones:</label>
                <input type="text" id="dimensiones" name="dimensiones" maxlength="50" placeholder="Ej: 10x20x15 cm">
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
        $('#id_producto').on('input', function() {
            clearTimeout(delayTimer);
            var idProducto = $(this).val().trim();
            
            if(idProducto.length > 0) {
                delayTimer = setTimeout(function() {
                    $.ajax({
                        url: 'validar_id_producto.php',
                        type: 'POST',
                        data: { id_producto: idProducto },
                        dataType: 'json',
                        success: function(response) {
                            if(response.existe) {
                                $('#id_error').show();
                                $('#id_producto').addClass('error-input');
                                idExiste = true;
                                $('#btnGuardar').prop('disabled', true);
                            } else {
                                $('#id_error').hide();
                                $('#id_producto').removeClass('error-input');
                                idExiste = false;
                                $('#btnGuardar').prop('disabled', false);
                            }
                        },
                        error: function() {
                            console.log('Error al verificar el ID');
                        }
                    });
                }, 500); // Espera 500ms después de que el usuario deja de escribir
            } else {
                $('#id_error').hide();
                $('#id_producto').removeClass('error-input');
                idExiste = false;
                $('#btnGuardar').prop('disabled', false);
            }
        });

        // Validación antes de enviar el formulario
        $('#productoForm').on('submit', function(e) {
            if(idExiste) {
                e.preventDefault();
                $('#id_error').show();
                $('#id_producto').addClass('error-input').focus();
            }
        });
    });
    </script>
</body>
</html>