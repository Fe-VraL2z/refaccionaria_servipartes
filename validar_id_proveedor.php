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

$id_proveedor = $_POST['id_proveedor'] ?? '';

$stmt = $conn->prepare("SELECT * FROM proveedor WHERE `ID Proveedor` = ?");
$stmt->bind_param("i", $id_proveedor);
$stmt->execute();
$result = $stmt->get_result();

header('Content-Type: application/json');
echo json_encode(['existe' => $result->num_rows > 0]);

$conn->close();
?>