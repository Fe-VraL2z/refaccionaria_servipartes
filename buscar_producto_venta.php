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

// Buscar producto por ID (numérico o con ceros) o por nombre
$query = "SELECT * FROM producto WHERE `Id Producto` = ? OR `Nombre del producto` LIKE ?";
$stmt = $conn->prepare($query);
$busquedaLike = "%$busqueda%";

// Manejar búsqueda numérica (convertir a entero si es numérico)
if (is_numeric($busqueda)) {
    $busquedaNum = (int)$busqueda;
    $stmt->bind_param("is", $busquedaNum, $busquedaLike);
} else {
    $stmt->bind_param("ss", $busqueda, $busquedaLike);
}

$stmt->execute();
$result = $stmt->get_result();

header('Content-Type: application/json');
if ($result->num_rows > 0) {
    $producto = $result->fetch_assoc();
    
    // Formatear datos del producto
    $productoData = [
        'ID del producto' => $producto['Id Producto'],
        'Nombre del producto' => $producto['Nombre del producto'],
        'Marca' => $producto['Marca'],
        'Modelo' => $producto['Modelo'],
        'Precio' => $producto['Precio'],
        'Cantidad de existencia' => $producto['Cantidad de existencia'],
        'Descripcion' => $producto['Descripcion'],
        'Codigo de pieza' => $producto['Codigo de pieza']
    ];
    
    echo json_encode([
        'existe' => true,
        'datos' => $productoData
    ]);
} else {
    echo json_encode([
        'existe' => false,
        'mensaje' => 'Producto no encontrado'
    ]);
}

$conn->close();
?>