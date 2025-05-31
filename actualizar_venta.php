<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: index.php");
    exit;
}

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: editar_venta.php");
    exit;
}

// Database connection
try {
    $host = '127.0.0.1';
    $dbname = 'refaccionaria_servipartes';
    $username = 'root'; // Default XAMPP username
    $password = '';     // Default XAMPP password (empty)
    
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $_SESSION['mensaje'] = "Error de conexión a la base de datos: " . $e->getMessage();
    $_SESSION['tipo_mensaje'] = "error";
    header("Location: lista_ventas.php");
    exit;
}

try {
    $pdo->beginTransaction();
    
    $id_venta = $_POST['id_venta'];
    $metodo_pago = $_POST['metodo_pago'];
    $total = $_POST['total'];
    $productos = $_POST['productos'];
    
    // Validate that products exist
    if (empty($productos)) {
        throw new Exception("Debe haber al menos un producto en la venta");
    }
    
    // Get current products from the sale to restore stock
    $stmt = $pdo->prepare("SELECT `Id Producto`, cantidad FROM ventas_productos WHERE id_ventas = ?");
    $stmt->execute([$id_venta]);
    $productosActuales = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Restore stock for current products
    foreach ($productosActuales as $producto) {
        $stmt = $pdo->prepare("UPDATE producto SET `Cantidad de existencia` = `Cantidad de existencia` + ? WHERE `Id Producto` = ?");
        $stmt->execute([$producto['cantidad'], $producto['Id Producto']]);
    }
    
    // Delete current products from the sale
    $stmt = $pdo->prepare("DELETE FROM ventas_productos WHERE id_ventas = ?");
    $stmt->execute([$id_venta]);
    
    // Insert updated products
    $totalCalculado = 0;
    foreach ($productos as $producto) {
        if (!isset($producto['id']) || !isset($producto['cantidad'])) {
            continue;
        }
        
        $idProducto = $producto['id'];
        $cantidad = intval($producto['cantidad']);
        $nombreProducto = $producto['producto'] ?? '';
        $precioUnitario = floatval($producto['precio_unitario'] ?? 0);
        $subtotal = $precioUnitario * $cantidad;
        
        // Verify available stock
        $stmt = $pdo->prepare("SELECT `Cantidad de existencia` FROM producto WHERE `Id Producto` = ?");
        $stmt->execute([$idProducto]);
        $existencia = $stmt->fetchColumn();
        
        if ($existencia < $cantidad) {
            throw new Exception("No hay suficiente existencia para el producto: $nombreProducto");
        }
        
        // Reduce stock
        $stmt = $pdo->prepare("UPDATE producto SET `Cantidad de existencia` = `Cantidad de existencia` - ? WHERE `Id Producto` = ?");
        $stmt->execute([$cantidad, $idProducto]);
        
        // Insert into ventas_productos
        $stmt = $pdo->prepare("INSERT INTO ventas_productos (id_ventas, `Id Producto`, producto, cantidad, precio_unitario, subtotal) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$id_venta, $idProducto, $nombreProducto, $cantidad, $precioUnitario, $subtotal]);
        
        $totalCalculado += $subtotal;
    }
    
    // Update main sale
    $stmt = $pdo->prepare("UPDATE ventas SET metodo_pago = ?, total = ? WHERE id_ventas = ?");
    $stmt->execute([$metodo_pago, $totalCalculado, $id_venta]);
    
    $pdo->commit();
    
    // Clear session
    unset($_SESSION['venta_editar']);
    
    // Redirect with success message
    $_SESSION['mensaje'] = "Venta actualizada correctamente";
    $_SESSION['tipo_mensaje'] = "success";
    header("Location: editar_venta.php");
    exit;
    
} catch (Exception $e) {
    if (isset($pdo)) {
        $pdo->rollBack();
    }
    $_SESSION['mensaje'] = "Error al actualizar la venta: " . $e->getMessage();
    $_SESSION['tipo_mensaje'] = "error";
    header("Location: formulario_editar.php");
    exit;
}
?>