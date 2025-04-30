<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: index.php");
    exit;
}

$conn = new mysqli('localhost', 'root', '', 'refaccionaria_servipartes');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$busqueda = $_GET['busqueda'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Procesar el formulario de edición
    $id_usuario = $_POST['id_usuario'];
    $usuario = $_POST['usuario'];
    $cambiar_password = isset($_POST['cambiar_password']) ? true : false;

    if ($cambiar_password) {
        $contraseña = md5($_POST['nueva_contraseña']);
        $stmt = $conn->prepare("UPDATE usuarios SET usuario = ?, contraseña = ? WHERE id = ?");
        $stmt->bind_param("sss", $usuario, $contraseña, $id_usuario);
    } else {
        $stmt = $conn->prepare("UPDATE usuarios SET usuario = ? WHERE id = ?");
        $stmt->bind_param("ss", $usuario, $id_usuario);
    }

    if ($stmt->execute()) {
        $_SESSION['mensaje'] = "Usuario actualizado exitosamente";
        $_SESSION['tipo_mensaje'] = "success";
        header("Location: dashboard.php");
        exit;
    } else {
        $_SESSION['mensaje'] = "Error al actualizar el usuario: " . $conn->error;
        $_SESSION['tipo_mensaje'] = "error";
    }
}

$usuario = null;
if ($busqueda) {
    $query = "SELECT * FROM usuarios WHERE id = ? OR usuario LIKE ?";
    $stmt = $conn->prepare($query);
    $busquedaLike = "%$busqueda%";
    $stmt->bind_param("ss", $busqueda, $busquedaLike);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $usuario = $result->fetch_assoc();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Usuario - SERVIPARTES</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
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
        .form-group input, .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
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
        .status-message {
            margin-bottom: 15px;
            padding: 10px;
            border-radius: 5px;
            text-align: center;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
        }
        #password_fields {
            display: none;
            margin-top: 15px;
            padding: 15px;
            background-color: rgba(240, 240, 240, 0.8);
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <?php if ($usuario): ?>
            <h1>Editar Usuario</h1>
            <div class="status-message success">
                Usuario encontrado, puedes editar
            </div>
            <form method="post">
                <input type="hidden" name="id_usuario" value="<?php echo $usuario['id']; ?>">
                
                <div class="form-group">
                    <label for="id_usuario">ID Usuario:</label>
                    <input type="text" id="id_usuario" value="<?php echo $usuario['id']; ?>" disabled>
                </div>
                
                <div class="form-group">
                    <label for="usuario">Nombre de usuario:</label>
                    <input type="text" id="usuario" name="usuario" value="<?php echo htmlspecialchars($usuario['usuario']); ?>" maxlength="50" required>
                </div>
                
                <div class="form-group">
                    <label>
                        <input type="checkbox" id="cambiar_password" name="cambiar_password" onchange="togglePasswordFields()">
                        Cambiar contraseña
                    </label>
                </div>
                
                <div id="password_fields">
                    <div class="form-group">
                        <label for="nueva_contraseña">Nueva contraseña:</label>
                        <input type="password" id="nueva_contraseña" name="nueva_contraseña">
                    </div>
                    <div class="form-group">
                        <label for="confirmar_nueva_contraseña">Confirmar nueva contraseña:</label>
                        <input type="password" id="confirmar_nueva_contraseña" name="confirmar_nueva_contraseña">
                        <div id="password_error" class="error-message" style="color: #dc3545; display: none;">⚠ Las contraseñas no coinciden</div>
                    </div>
                </div>
                
                <div class="button-group">
                    <button type="submit" class="btn-guardar">Guardar cambios</button>
                    <button type="button" class="btn-cancelar" onclick="window.location.href='dashboard.php'">Cancelar</button>
                </div>
            </form>
        <?php else: ?>
            <?php if ($busqueda): ?>
                <div class="status-message error">
                    Usuario no encontrado
                </div>
            <?php else: ?>
                <div class="status-message error">
                    Por favor, inserta un ID o nombre
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <script>
    function togglePasswordFields() {
        var checkbox = document.getElementById('cambiar_password');
        var passwordFields = document.getElementById('password_fields');
        if (checkbox.checked) {
            passwordFields.style.display = 'block';
            document.getElementById('nueva_contraseña').required = true;
            document.getElementById('confirmar_nueva_contraseña').required = true;
        } else {
            passwordFields.style.display = 'none';
            document.getElementById('nueva_contraseña').required = false;
            document.getElementById('confirmar_nueva_contraseña').required = false;
        }
    }

    // Validación de contraseñas coincidentes
    document.getElementById('confirmar_nueva_contraseña').addEventListener('keyup', function() {
        var password = document.getElementById('nueva_contraseña').value;
        var confirmPassword = this.value;
        var errorDiv = document.getElementById('password_error');
        if (password !== confirmPassword) {
            errorDiv.style.display = 'block';
            this.classList.add('error-input');
        } else {
            errorDiv.style.display = 'none';
            this.classList.remove('error-input');
        }
    });
    </script>
</body>
</html>