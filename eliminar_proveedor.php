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

$id_proveedor = $_POST['id_proveedor'];

// Eliminar el proveedor
$query = "DELETE FROM proveedor WHERE `ID Proveedor` = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id_proveedor);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    $_SESSION['mensaje'] = "Proveedor eliminado exitosamente";
    $_SESSION['tipo_mensaje'] = "success";
} else {
    $_SESSION['mensaje'] = "Error al eliminar el proveedor o proveedor no encontrado";
    $_SESSION['tipo_mensaje'] = "error";
}

$conn->close();
header("Location: dashboard.php");
exit;
?>