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

$id = $_POST['id'];

// Eliminar el cliente
$query = "DELETE FROM cliente WHERE `ID del cliente` = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $id);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    $_SESSION['mensaje'] = "Se ha eliminado el cliente de forma exitosa.";
} else {
    $_SESSION['mensaje'] = "Error al eliminar el cliente.";
}

$conn->close();
header("Location: dashboard.php");
exit;
?>