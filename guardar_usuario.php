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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener y sanitizar los datos
    $id_usuario = $conn->real_escape_string($_POST['id_usuario']);
    $usuario = $conn->real_escape_string($_POST['usuario']);
    $contraseña = md5($conn->real_escape_string($_POST['contraseña'])); // Encriptar con MD5

    // Verificar si el ID de usuario ya existe
    $stmt = $conn->prepare("SELECT * FROM usuarios WHERE id = ?");
    $stmt->bind_param("s", $id_usuario);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $_SESSION['mensaje'] = "Error: El ID de usuario ya existe";
        $_SESSION['tipo_mensaje'] = "error";
        header("Location: formulario_insertar_usuario.php");
        exit;
    }

    // Verificar si el nombre de usuario ya existe
    $stmt = $conn->prepare("SELECT * FROM usuarios WHERE usuario = ?");
    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $_SESSION['mensaje'] = "Error: El nombre de usuario ya existe";
        $_SESSION['tipo_mensaje'] = "error";
        header("Location: formulario_insertar_usuario.php");
        exit;
    }

    // Insertar el nuevo usuario
    $stmt = $conn->prepare("INSERT INTO usuarios (id, usuario, contraseña) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $id_usuario, $usuario, $contraseña);

    if ($stmt->execute()) {
        $_SESSION['mensaje'] = "Usuario registrado exitosamente";
        $_SESSION['tipo_mensaje'] = "success";
        header("Location: dashboard.php");
        exit;
    } else {
        $_SESSION['mensaje'] = "Error al registrar el usuario: " . $conn->error;
        $_SESSION['tipo_mensaje'] = "error";
        header("Location: formulario_insertar_usuario.php");
        exit;
    }
}

$conn->close();
?>