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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener y sanitizar los datos
    $id_cliente = $conn->real_escape_string($_POST['id_cliente']);
    $folio = $conn->real_escape_string($_POST['folio']);
    $direccion_negocio = $conn->real_escape_string($_POST['direccion_negocio']);
    $numero_atencion = $conn->real_escape_string($_POST['numero_atencion']);
    $id_producto = $conn->real_escape_string($_POST['id_producto']);
    $nombre_producto = $conn->real_escape_string($_POST['nombre_producto']);
    $precio_venta = $conn->real_escape_string($_POST['precio_venta']);
    $marca = $conn->real_escape_string($_POST['marca']);
    $modelo = $conn->real_escape_string($_POST['modelo']);
    $garantia = $conn->real_escape_string($_POST['garantia']);
    $tiempo_garantia = isset($_POST['tiempo_garantia']) ? $conn->real_escape_string($_POST['tiempo_garantia']) : '';

    // Verificar si los IDs existen (doble verificación por seguridad)
    $stmt = $conn->prepare("SELECT `ID del cliente` FROM cliente WHERE `ID del cliente` = ?");
    $stmt->bind_param("s", $id_cliente);
    $stmt->execute();
    $result_cliente = $stmt->get_result();

    $stmt = $conn->prepare("SELECT `Id Producto` FROM producto WHERE `Id Producto` = ?");
    $stmt->bind_param("s", $id_producto);
    $stmt->execute();
    $result_producto = $stmt->get_result();

    if ($result_cliente->num_rows === 0 || $result_producto->num_rows === 0) {
        $_SESSION['mensaje'] = "Error: Cliente o producto no encontrado";
        $_SESSION['tipo_mensaje'] = "error";
        header("Location: formulario_insertar_venta.php");
        exit;
    }

    // Insertar la nueva venta
    $stmt = $conn->prepare("INSERT INTO ventas (
        `ID del cliente`, `Folio`, `Direccion del negocio`, `Numero de atención a cliente`,
        `ID del producto`, `Nombre del producto`, `Precio/ventas`, `Marca`, `Modelo`,
        `Garantía`, `Tiempo de garantía`
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    $stmt->bind_param(
        "ssssssdssis", 
        $id_cliente, $folio, $direccion_negocio, $numero_atencion,
        $id_producto, $nombre_producto, $precio_venta, $marca, $modelo,
        $garantia, $tiempo_garantia
    );

    if ($stmt->execute()) {
        $_SESSION['mensaje'] = "Venta registrada exitosamente";
        $_SESSION['tipo_mensaje'] = "success";
        header("Location: dashboard.php");
        exit;
    } else {
        $_SESSION['mensaje'] = "Error al registrar la venta: " . $conn->error;
        $_SESSION['tipo_mensaje'] = "error";
        header("Location: formulario_insertar_venta.php");
        exit;
    }
}

$conn->close();
header("Location: formulario_insertar_venta.php");
exit;
?>