<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: index.php");
    exit;
}

// Conexión a la base de datos
$conn = new mysqli('localhost', 'root', '', 'refaccionaria_servipartes');
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

$folio = $_POST['folio'];

// Buscar la venta
$query = "SELECT v.*, c.Nombre AS cliente_nombre, c.Telefono, c.Direccion
          FROM ventas v 
          JOIN cliente c ON v.cliente_id = c.`ID del cliente`
          WHERE v.folio = ? ";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $folio);
$stmt->execute();
$venta = $stmt->get_result()->fetch_assoc();

if (!$venta) {
    $_SESSION['error'] = "Venta no encontrada";
    header("Location: editar_venta.php");
    exit;
}

// Obtener productos de la venta
$query_productos = "SELECT p.`Id Producto`, p.`Nombre del producto`, p.Marca, p.Modelo, 
                           p.Precio, p.`Cantidad de existencia`, vp.cantidad
                    FROM ventas_productos vp
                    JOIN producto p ON vp.`Id Producto` = p.`Id Producto`
                    WHERE vp.id_ventas = ?";
$stmt = $conn->prepare($query_productos);
$stmt->bind_param("i", $venta['id_ventas']);
$stmt->execute();
$productos = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$conn->close();

// Guardar datos en sesión para edición
$_SESSION['venta_editar'] = [
    'id_venta' => $venta['id_ventas'],
    'folio' => $venta['folio'],
    'fecha' => $venta['fecha'],
    'cliente' => [
        'id' => $venta['cliente_id'],
        'nombre' => $venta['cliente_nombre'],
        'telefono' => $venta['Telefono'],
        'direccion' => $venta['email']
    ],
    'productos' => $productos,
    'metodo_pago' => $venta['metodo_pago'],
    'total' => $venta['total']
];

header("Location: formulario_editar.php");
exit;
?>