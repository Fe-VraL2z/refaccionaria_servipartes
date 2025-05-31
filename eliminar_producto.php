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

try {
    // Obtener información del producto
    $query_producto = "SELECT `Nombre del producto`, `Modelo` FROM producto WHERE `Id Producto` = ?";
    $stmt_producto = $conn->prepare($query_producto);
    $stmt_producto->bind_param("i", $id_producto);
    $stmt_producto->execute();
    $result_producto = $stmt_producto->get_result();
    $producto_info = $result_producto->fetch_assoc();
    
    if (!$producto_info) {
        $_SESSION['mensaje'] = "Producto no encontrado";
        $_SESSION['tipo_mensaje'] = "error";
        $stmt_producto->close();
        $conn->close();
        header("Location: dashboard.php");
        exit;
    }
    
    $nombre_producto = $producto_info['Nombre del producto'];
    $modelo_producto = $producto_info['Modelo'];
    
    // Verificar si el producto tiene ventas asociadas
    $query_check = "
        SELECT COUNT(*) as total_ventas, 
               GROUP_CONCAT(DISTINCT v.folio SEPARATOR ', ') as folios
        FROM ventas_productos vp 
        INNER JOIN ventas v ON vp.id_ventas = v.id_ventas 
        WHERE vp.`Id Producto` = ?
    ";
    $stmt_check = $conn->prepare($query_check);
    $stmt_check->bind_param("i", $id_producto);
    $stmt_check->execute();
    $result = $stmt_check->get_result();
    $row = $result->fetch_assoc();
    
    if ($row['total_ventas'] > 0) {
        // El producto tiene ventas asociadas, no se puede eliminar
        $mensaje = "⚠️ No se puede eliminar el producto '$nombre_producto' (Modelo: $modelo_producto) porque está asociado a " . $row['total_ventas'] . " venta(s).";
        
        $_SESSION['mensaje'] = $mensaje;
        $_SESSION['tipo_mensaje'] = "warning";
    } else {
        // El producto no tiene ventas asociadas, se puede eliminar
        $query = "DELETE FROM producto WHERE `Id Producto` = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $id_producto);
        
        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                $_SESSION['mensaje'] = "✅ Producto '$nombre_producto' (Modelo: $modelo_producto) eliminado exitosamente";
                $_SESSION['tipo_mensaje'] = "success";
            } else {
                $_SESSION['mensaje'] = "❌ Error al eliminar el producto o producto no encontrado";
                $_SESSION['tipo_mensaje'] = "error";
            }
        } else {
            throw new Exception("Error al ejecutar la consulta de eliminación");
        }
        $stmt->close();
    }
    
    $stmt_check->close();
    $stmt_producto->close();
    
} catch (Exception $e) {
    $_SESSION['mensaje'] = "💥 Error inesperado: " . $e->getMessage();
    $_SESSION['tipo_mensaje'] = "error";
}

$conn->close();
header("Location: dashboard.php");
exit;
?>