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

$id_producto = $_POST['id_producto'];

// Eliminar el producto
$query = "DELETE FROM producto WHERE `Id Producto` = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $id_producto);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    $_SESSION['mensaje'] = "Producto eliminado exitosamente";
    $_SESSION['tipo_mensaje'] = "success";
} else {
    $_SESSION['mensaje'] = "Error al eliminar el producto o producto no encontrado";
    $_SESSION['tipo_mensaje'] = "error";
}

$conn->close();
header("Location: dashboard.php");
exit;
?>