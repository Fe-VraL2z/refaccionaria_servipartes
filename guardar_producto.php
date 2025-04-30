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
    $id_producto = $conn->real_escape_string($_POST['id_producto']);
    $nombre = $conn->real_escape_string($_POST['nombre']);
    $descripcion = $conn->real_escape_string($_POST['descripcion']);
    $modelo = $conn->real_escape_string($_POST['modelo']);
    $precio = $conn->real_escape_string($_POST['precio']);
    $cantidad_existencia = $conn->real_escape_string($_POST['cantidad_existencia']);
    $codigo_pieza = $conn->real_escape_string($_POST['codigo_pieza']);
    $marca = $conn->real_escape_string($_POST['marca']);
    $categoria = $conn->real_escape_string($_POST['categoria']);
    $unidad_medida = $conn->real_escape_string($_POST['unidad_medida']);
    $dimensiones = $conn->real_escape_string($_POST['dimensiones']);

    // Verificar si el ID del producto ya existe (doble verificación por seguridad)
    $stmt = $conn->prepare("SELECT `Id Producto` FROM producto WHERE `Id Producto` = ?");
    $stmt->bind_param("s", $id_producto);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // El ID ya existe, redirigir con mensaje de error
        header("Location: formulario_insertar_producto.php?error=id_existente");
        exit;
    }

    // Insertar el nuevo producto usando consultas preparadas
    $stmt = $conn->prepare("INSERT INTO producto (`Id Producto`, `Nombre del producto`, `Descripcion`, `Modelo`, `Precio`, `Cantidad de existencia`, `Codigo de pieza`, `Marca`, `Categoria`, `Unidad de medida`, `Dimensiones`) 
                           VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssdisssss", $id_producto, $nombre, $descripcion, $modelo, $precio, $cantidad_existencia, $codigo_pieza, $marca, $categoria, $unidad_medida, $dimensiones);

    if ($stmt->execute()) {
        // Mostrar mensaje de éxito
        echo "<script>
            alert('Producto registrado exitosamente');
            window.location.href = 'dashboard.php';
        </script>";
    } else {
        echo "<script>
            alert('Error al registrar el producto');
            window.location.href = 'formulario_insertar_producto.php';
        </script>";
    }
}

$conn->close();
?>