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

$id_usuario = $_POST['id_usuario'];

// No permitir eliminar el usuario actual
if ($_SESSION['usuario'] == $id_usuario) {
    $_SESSION['mensaje'] = "No puedes eliminar tu propio usuario mientras estás conectado";
    $_SESSION['tipo_mensaje'] = "error";
    header("Location: dashboard.php");
    exit;
}

// Eliminar el usuario
$query = "DELETE FROM usuarios WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $id_usuario);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    $_SESSION['mensaje'] = "Usuario eliminado exitosamente";
    $_SESSION['tipo_mensaje'] = "success";
} else {
    $_SESSION['mensaje'] = "Error al eliminar el usuario o usuario no encontrado";
    $_SESSION['tipo_mensaje'] = "error";
}

$conn->close();
header("Location: dashboard.php");
exit;
?>