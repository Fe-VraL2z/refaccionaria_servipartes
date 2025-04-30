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

$usuario = $_POST['usuario'] ?? '';

$stmt = $conn->prepare("SELECT * FROM usuarios WHERE usuario = ?");
$stmt->bind_param("s", $usuario);
$stmt->execute();
$result = $stmt->get_result();

header('Content-Type: application/json');
echo json_encode(['existe' => $result->num_rows > 0]);

$conn->close();
?>