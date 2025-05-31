<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: index.php");
    exit;
}

// Configuración de la conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "refaccionaria_servipartes";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Habilitar reporte de errores para debugging
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Obtener datos del formulario
        $folio = $conn->real_escape_string($_POST['folio']);
        $cliente_id = $conn->real_escape_string($_POST['cliente']);
        $direccion_negocio = $conn->real_escape_string($_POST['direccion_negocio']);
        $numero_atencion_cliente = $conn->real_escape_string($_POST['numero_atencion']);
        $garantia = isset($_POST['garantia']) ? intval($_POST['garantia']) : 0;
        $tiempo_garantia_valor = ($garantia == 1) ? intval($_POST['tiempo_garantia']) : 0;
        $tiempo_garantia_unidad = ($garantia == 1) ? $conn->real_escape_string($_POST['tiempo_garantia_unidad']) : '';
        $metodo_pago = $conn->real_escape_string($_POST['metodo_pago']);
        $nombre_usuario = $conn->real_escape_string($_POST['nombre_usuario']);
        $fecha = isset($_POST['fecha']) ? $conn->real_escape_string($_POST['fecha']) : date('Y-m-d H:i:s');
        
        // Verificar folio único
        $stmt = $conn->prepare("SELECT folio FROM ventas WHERE folio = ?");
        $stmt->bind_param("s", $folio);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            throw new Exception("El folio ya está en uso");
        }

        // Verificar cliente
        $stmt = $conn->prepare("SELECT `ID del cliente` FROM cliente WHERE `ID del cliente` = ?");
        $stmt->bind_param("s", $cliente_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows == 0) {
            throw new Exception("Cliente no válido");
        }

        // Validar productos
        if (!isset($_POST['products']) || empty($_POST['products'])) {
            throw new Exception("Debe agregar al menos un producto");
        }
        
        $products = json_decode($_POST['products'], true);
        if (json_last_error() !== JSON_ERROR_NONE || !is_array($products)) {
            throw new Exception("Datos de productos no válidos");
        }

        // Iniciar transacción
        $conn->begin_transaction();

        // Insertar venta
        $stmt = $conn->prepare("INSERT INTO ventas 
            (folio, cliente_id, direccion_negocio, numero_atencion_cliente, 
             garantia, tiempo_garantia_valor, tiempo_garantia_unidad, metodo_pago, nombre_usuario, 
             fecha, total) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0)");
        $stmt->bind_param("ssssiissss",
            $folio, 
            $cliente_id, 
            $direccion_negocio, 
            $numero_atencion_cliente, 
            $garantia, 
            $tiempo_garantia_valor, 
            $tiempo_garantia_unidad, 
            $metodo_pago, 
            $nombre_usuario, 
            $fecha
        );

        if (!$stmt->execute()) {
            throw new Exception("Error al registrar la venta: " . $stmt->error);
        }

        $venta_id = $conn->insert_id;
        $total_venta = 0;
        
        // Procesar productos
        foreach ($products as $product) {
            $product_id = intval($product['id']);
            $cantidad = intval($product['quantity']);
            $precio = floatval($product['price']);
            
            if ($product_id <= 0 || $cantidad <= 0 || $precio <= 0) {
                throw new Exception("Datos de producto no válidos");
            }

            // Verificar existencia y obtener datos del producto
            $stmt_check = $conn->prepare("SELECT `Cantidad de existencia`, `Precio`, `Nombre del producto` FROM producto WHERE `Id Producto` = ?");
            $stmt_check->bind_param("i", $product_id);
            $stmt_check->execute();
            $result_check = $stmt_check->get_result();
            
            if ($result_check->num_rows == 0) {
                throw new Exception("Producto no encontrado: ID " . $product_id);
            }
            
            $product_data = $result_check->fetch_assoc();
            $existencia = $product_data['Cantidad de existencia'];
            $precio_real = $product_data['Precio'];
            $nombre_producto = $product_data['Nombre del producto'];
            
            if ($existencia < $cantidad) {
                throw new Exception("No hay suficiente existencia para: " . $nombre_producto);
            }

            // Calcular subtotal
            $subtotal = $precio_real * $cantidad;
            $total_venta += $subtotal;

            // Insertar producto en ventas_productos
            $stmt_product = $conn->prepare("INSERT INTO ventas_productos 
                (id_ventas, `Id Producto`, producto, cantidad, precio_unitario, subtotal) 
                VALUES (?, ?, ?, ?, ?, ?)");
            $stmt_product->bind_param("iisidd", $venta_id, $product_id, $nombre_producto, $cantidad, $precio_real, $subtotal);
            
            if (!$stmt_product->execute()) {
                throw new Exception("Error al registrar producto en venta: " . $stmt_product->error);
            }

            // Actualizar stock
            $stmt_update = $conn->prepare("UPDATE producto SET `Cantidad de existencia` = `Cantidad de existencia` - ? WHERE `Id Producto` = ?");
            $stmt_update->bind_param("ii", $cantidad, $product_id);
            
            if (!$stmt_update->execute()) {
                throw new Exception("Error al actualizar existencia: " . $stmt_update->error);
            }
        }
        
        // Actualizar el total en la venta
        $stmt_update_venta = $conn->prepare("UPDATE ventas SET total = ? WHERE id_ventas = ?");
        $stmt_update_venta->bind_param("di", $total_venta, $venta_id);
        if (!$stmt_update_venta->execute()) {
            throw new Exception("Error al actualizar total de venta: " . $stmt_update_venta->error);
        }
        
        // Confirmar transacción
        $conn->commit();
        
        // Redirigir a imprimir ticket
        $_SESSION['mensaje'] = "Venta registrada exitosamente. Folio: " . htmlspecialchars($folio);
        $_SESSION['tipo_mensaje'] = "success";
        $_SESSION['venta_id'] = $venta_id;
        header("Location: imprimir_ticket.php?id=" . $venta_id);
        exit;
        
    } catch (Exception $e) {
        // Revertir transacción en caso de error
        if (isset($conn) && method_exists($conn, 'rollback')) {
            $conn->rollback();
        }
        
        $_SESSION['mensaje'] = "Error al procesar la venta: " . $e->getMessage();
        $_SESSION['tipo_mensaje'] = "error";
        header("Location: formulario_insertar_venta.php");
        exit;
    }
} else {
    // Si no es POST, redirigir
    header("Location: formulario_insertar_venta.php");
    exit;
}

// Cerrar conexión
$conn->close();
?>  