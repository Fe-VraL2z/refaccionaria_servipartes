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
    $id_proveedor = $conn->real_escape_string($_POST['id_proveedor']);
    $nombre = $conn->real_escape_string($_POST['nombre']);
    $telefono = $conn->real_escape_string($_POST['telefono']);
    $direccion = $conn->real_escape_string($_POST['direccion']);
    $email = $conn->real_escape_string($_POST['email']);
    $rfc = $conn->real_escape_string($_POST['rfc']);
    $contacto_principal = $conn->real_escape_string($_POST['contacto_principal']);
    $metodo_pago = $conn->real_escape_string($_POST['metodo_pago']);
    $plazo_entrega = $conn->real_escape_string($_POST['plazo_entrega']);
    $forma_envio = $conn->real_escape_string($_POST['forma_envio']);

    // Verificar si el ID ya existe (doble verificación por seguridad)
    $stmt = $conn->prepare("SELECT `ID Proveedor` FROM proveedor WHERE `ID Proveedor` = ?");
    $stmt->bind_param("i", $id_proveedor);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        header("Location: formulario_insertar_proveedor.php?error=id_existente");
        exit;
    }

    // Insertar el nuevo proveedor
    $stmt = $conn->prepare("INSERT INTO proveedor (
        `ID Proveedor`, `Nombre`, `Telefono`, `Direccion`, `Email`, `RFC`, 
        `Contacto principal`, `Metodo Pago`, `Plazo Entrega`, `Forma Envio`
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    $stmt->bind_param(
        "isssssssss", 
        $id_proveedor, $nombre, $telefono, $direccion, $email, $rfc,
        $contacto_principal, $metodo_pago, $plazo_entrega, $forma_envio
    );

    if ($stmt->execute()) {
        echo "<script>
            alert('Proveedor registrado exitosamente');
            window.location.href = 'dashboard.php';
        </script>";
    } else {
        echo "<script>
            alert('Error al registrar el proveedor');
            window.location.href = 'formulario_insertar_proveedor.php';
        </script>";
    }
}

$conn->close();
?>