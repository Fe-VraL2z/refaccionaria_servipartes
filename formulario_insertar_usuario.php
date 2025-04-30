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
    <title>Agregar Usuario - SERVIPARTES</title>
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
        .form-group input {
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
        <h1>Agregar Usuario</h1>
        <form id="usuarioForm" action="guardar_usuario.php" method="post">
            <div class="form-group">
                <label for="id_usuario">ID Usuario:</label>
                <input type="text" id="id_usuario" name="id_usuario" required>
                <div id="id_error" class="error-message">⚠ Este ID de usuario ya existe</div>
            </div>
            <div class="form-group">
                <label for="usuario">Nombre de usuario:</label>
                <input type="text" id="usuario" name="usuario" maxlength="50" required>
                <div id="usuario_error" class="error-message">⚠ Este nombre de usuario ya existe</div>
            </div>
            <div class="form-group">
                <label for="contraseña">Contraseña:</label>
                <input type="password" id="contraseña" name="contraseña" required>
            </div>
            <div class="form-group">
                <label for="confirmar_contraseña">Confirmar contraseña:</label>
                <input type="password" id="confirmar_contraseña" name="confirmar_contraseña" required>
                <div id="password_error" class="error-message">⚠ Las contraseñas no coinciden</div>
            </div>
            <div class="button-group">
                <button type="submit" id="btnGuardar" class="btn-guardar">Guardar</button>
                <button type="button" class="btn-cancelar" onclick="window.location.href='dashboard.php'">Cancelar</button>
            </div>
        </form>
    </div>

    <script>
    $(document).ready(function() {
        var idExiste = false;
        var usuarioExiste = false;
        var passwordMatch = false;

        // Validación en tiempo real del ID
        $('#id_usuario').on('blur', function() {
            var idUsuario = $(this).val().trim();
            if(idUsuario.length > 0) {
                $.ajax({
                    url: 'validar_id_usuario.php',
                    type: 'POST',
                    data: { id_usuario: idUsuario },
                    dataType: 'json',
                    success: function(response) {
                        if(response.existe) {
                            $('#id_error').text('⚠ Este ID de usuario ya existe').show();
                            $('#id_usuario').addClass('error-input');
                            idExiste = true;
                        } else {
                            $('#id_error').hide();
                            $('#id_usuario').removeClass('error-input');
                            idExiste = false;
                        }
                        actualizarBotonGuardar();
                    }
                });
            }
        });

        // Validación en tiempo real del nombre de usuario
        $('#usuario').on('blur', function() {
            var usuario = $(this).val().trim();
            if(usuario.length > 0) {
                $.ajax({
                    url: 'validar_usuario.php',
                    type: 'POST',
                    data: { usuario: usuario },
                    dataType: 'json',
                    success: function(response) {
                        if(response.existe) {
                            $('#usuario_error').text('⚠ Este nombre de usuario ya existe').show();
                            $('#usuario').addClass('error-input');
                            usuarioExiste = true;
                        } else {
                            $('#usuario_error').hide();
                            $('#usuario').removeClass('error-input');
                            usuarioExiste = false;
                        }
                        actualizarBotonGuardar();
                    }
                });
            }
        });

        // Validación de contraseñas coincidentes
        $('#confirmar_contraseña').on('keyup', function() {
            var password = $('#contraseña').val();
            var confirmPassword = $(this).val();
            if(password !== confirmPassword) {
                $('#password_error').show();
                $('#confirmar_contraseña').addClass('error-input');
                passwordMatch = false;
            } else {
                $('#password_error').hide();
                $('#confirmar_contraseña').removeClass('error-input');
                passwordMatch = true;
            }
            actualizarBotonGuardar();
        });

        // Envío del formulario
        $('#usuarioForm').submit(function(e) {
            if(idExiste || usuarioExiste || !passwordMatch) {
                e.preventDefault();
                if(idExiste) {
                    $('#id_error').show();
                    $('#id_usuario').addClass('error-input').focus();
                }
                if(usuarioExiste) {
                    $('#usuario_error').show();
                    $('#usuario').addClass('error-input').focus();
                }
                if(!passwordMatch) {
                    $('#password_error').show();
                    $('#confirmar_contraseña').addClass('error-input').focus();
                }
            }
        });

        function actualizarBotonGuardar() {
            if(!idExiste && !usuarioExiste && passwordMatch) {
                $('#btnGuardar').prop('disabled', false);
            } else {
                $('#btnGuardar').prop('disabled', true);
            }
        }
    });
    </script>
</body>
</html>