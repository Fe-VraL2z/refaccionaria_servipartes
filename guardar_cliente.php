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
    $nombre = $conn->real_escape_string($_POST['nombre']);
    $telefono = $conn->real_escape_string($_POST['telefono']);
    $direccion = $conn->real_escape_string($_POST['direccion']);
    $rfc = $conn->real_escape_string($_POST['rfc']);

    // Verificar si el ID del cliente ya existe (doble verificación por seguridad)
    $stmt = $conn->prepare("SELECT `ID del cliente` FROM cliente WHERE `ID del cliente` = ?");
    $stmt->bind_param("s", $id_cliente);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // El ID ya existe, redirigir con mensaje de error
        header("Location: formulario_insertar.php?error=id_existente");
        exit;
    }

    // Insertar el nuevo cliente usando consultas preparadas
    $stmt = $conn->prepare("INSERT INTO cliente (`ID del cliente`, `Nombre`, `Telefono`, `Direccion`, `RFC`) 
                           VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $id_cliente, $nombre, $telefono, $direccion, $rfc);

    if ($stmt->execute()) {
        // Mostrar mensaje de éxito
        echo "<script>
                alert('Cliente registrado exitosamente');
                window.location.href = 'dashboard.php';
              </script>";
    } else {
        echo "<script>
                alert('Error al registrar el cliente');
                window.location.href = 'formulario_insertar.php';
              </script>";
    }
}

$conn->close();
?>