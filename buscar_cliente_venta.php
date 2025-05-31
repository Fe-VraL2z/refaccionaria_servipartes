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

$busqueda = $_POST['busqueda'] ?? '';

// Buscar cliente por ID o nombre
$query = "SELECT * FROM cliente WHERE `ID del cliente` = ? OR Nombre LIKE ?";
$stmt = $conn->prepare($query);
$busquedaLike = "%$busqueda%";
$stmt->bind_param("ss", $busqueda, $busquedaLike);
$stmt->execute();
$result = $stmt->get_result();

header('Content-Type: application/json');
if ($result->num_rows > 0) {
    echo json_encode(['existe' => true, 'datos' => $result->fetch_assoc()]);
} else {
    echo json_encode(['existe' => false]);
}

$conn->close();
?>