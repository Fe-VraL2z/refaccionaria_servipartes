<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("HTTP/1.1 403 Forbidden");
    exit;
}

$conn = new mysqli('localhost', 'root', '', 'refaccionaria_servipartes');
if ($conn->connect_error) {
    die(json_encode(['error' => 'Error de conexión']));
}

$id_cliente = $_POST['id_cliente'] ?? '';

// Prepara la consulta para evitar inyección SQL
$stmt = $conn->prepare("SELECT * FROM cliente WHERE `ID del cliente` = ?");
$stmt->bind_param("s", $id_cliente);
$stmt->execute();
$result = $stmt->get_result();

header('Content-Type: application/json');
echo json_encode(['existe' => $result->num_rows > 0]);

$conn->close();
?>