<?php
session_start();
$max_intentos = 3;
$tiempo_bloqueo = 30; // en segundos

// Reiniciar intentos si el bloqueo ha expirado
if (isset($_SESSION['bloqueado_hasta']) && time() >= $_SESSION['bloqueado_hasta']) {
    unset($_SESSION['intentos']);
    unset($_SESSION['bloqueado_hasta']);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $usuario = $_POST['usuario'];
    $contraseña = md5($_POST['contraseña']);

    $conn = new mysqli('localhost', 'root', '', 'refaccionaria_servipartes');
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $stmt = $conn->prepare("SELECT * FROM usuarios WHERE usuario = ? AND contraseña = ?");
    $stmt->bind_param("ss", $usuario, $contraseña);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $_SESSION['loggedin'] = true;
        $_SESSION['usuario'] = $usuario;
        header("Location: dashboard.php");
        exit;
    } else {
        if (!isset($_SESSION['intentos'])) {
            $_SESSION['intentos'] = 1;
        } else {
            $_SESSION['intentos']++;
        }

        if ($_SESSION['intentos'] >= $max_intentos) {
            $_SESSION['bloqueado_hasta'] = time() + $tiempo_bloqueo;
        }
    }
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Refaccionaria Servipartes</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .login-container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 300px;
            text-align: center;
        }
        .login-container img {
            width: 100px;
            height: 100px;
            margin-bottom: 20px;
            border-radius: 50%;
        }
        .login-container h2 {
            margin-bottom: 20px;
            color: #333;
        }
        .login-container input[type="text"],
        .login-container input[type="password"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .login-container input[type="submit"] {
            width: 100%;
            padding: 10px;
            background-color: #28a745;
            border: none;
            border-radius: 4px;
            color: #fff;
            font-size: 16px;
            cursor: pointer;
        }
        .login-container input[type="submit"]:hover {
            background-color: #218838;
        }
        .login-container .error {
            color: red;
            margin-top: 10px;
        }
        .login-container .bloqueado {
            color: red;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <img src="images_logo.png" alt="Logo Refaccionaria Servipartes">
        <h2>Iniciar Sesión</h2>
        <?php if (isset($_SESSION['bloqueado_hasta']) && time() < $_SESSION['bloqueado_hasta']): ?>
            <div class="bloqueado">
                Demasiados intentos fallidos. Intente nuevamente en <span id="contador"><?php echo $_SESSION['bloqueado_hasta'] - time(); ?></span> segundos.
            </div>
            <script>
                // Contador regresivo
                let tiempoRestante = <?php echo $_SESSION['bloqueado_hasta'] - time(); ?>;
                const contador = document.getElementById('contador');

                function actualizarContador() {
                    if (tiempoRestante > 0) {
                        contador.textContent = tiempoRestante;
                        tiempoRestante--;
                        setTimeout(actualizarContador, 1000);
                    } else {
                        window.location.reload(); // Recargar la página cuando el tiempo termine
                    }
                }

                actualizarContador();
            </script>
        <?php else: ?>
            <?php if (isset($_SESSION['intentos']) && $_SESSION['intentos'] > 0): ?>
                <div class="error">Usuario o contraseña incorrectos. Intento <?php echo $_SESSION['intentos']; ?> de <?php echo $max_intentos; ?></div>
            <?php endif; ?>
            <form method="post">
                <input type="text" name="usuario" placeholder="Usuario" required>
                <input type="password" name="contraseña" placeholder="Contraseña" required>
                <input type="submit" value="Entrar">
            </form>
        <?php endif; ?>
    </div>
</body>
</html>