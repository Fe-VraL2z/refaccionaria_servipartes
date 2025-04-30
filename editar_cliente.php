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

$busqueda = $_GET['busqueda'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_cliente = $_POST['id_cliente'];
    $nombre = $_POST['nombre'];
    $telefono = $_POST['telefono'];
    $direccion = $_POST['direccion'];
    $rfc = $_POST['rfc'];

    $sql = "UPDATE cliente SET 
            Nombre = '$nombre', 
            Telefono = '$telefono', 
            Direccion = '$direccion', 
            RFC = '$rfc' 
            WHERE `ID del cliente` = '$id_cliente'";

    if ($conn->query($sql)) {
        echo "<script>
                alert('Se han modificado los datos exitosamente.');
                window.location.href = 'dashboard.php';
              </script>";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$cliente = null;
if ($busqueda) {
    $sql = "SELECT * FROM cliente WHERE `ID del cliente` = ? OR Nombre LIKE ?";
    $stmt = $conn->prepare($sql);
    $busquedaLike = "%$busqueda%";
    $stmt->bind_param("ss", $busqueda, $busquedaLike);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $cliente = $result->fetch_assoc();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Cliente - SERVIPARTES</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            margin: 0;
            padding: 0;
            height: 100vh;
            background: url('images/paginaprinsipalservipartes.png') no-repeat center center fixed;
            background-size: cover;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: inherit;
            filter: blur(8px);
            z-index: -1;
        }

        .form-container {
            background-color: rgba(255, 255, 255, 0.9);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }

        .form-container h1 {
            margin-bottom: 20px;
            font-size: 24px;
            color: #333;
        }

        .form-container input {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }

        .form-container button {
            width: 48%;
            padding: 10px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
        }

        .form-container button.btn-guardar {
            background-color: #28a745;
            color: white;
        }

        .form-container button.btn-cancelar {
            background-color: #dc3545;
            color: white;
        }

        .form-container button:hover {
            opacity: 0.9;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h1>Editar Cliente</h1>
        <?php if ($cliente): ?>
            <p style="color: green;">Cliente encontrado, puedes editar</p>
            <form action="editar_cliente.php" method="post">
                <input type="hidden" name="id_cliente" value="<?php echo $cliente['ID del cliente']; ?>">
                <div class="mb-3">
                    <label for="nombre">Nombre:</label>
                    <input type="text" id="nombre" name="nombre" value="<?php echo $cliente['Nombre']; ?>" required>
                </div>
                <div class="mb-3">
                    <label for="telefono">Teléfono:</label>
                    <input type="text" id="telefono" name="telefono" value="<?php echo $cliente['Telefono']; ?>" required>
                </div>
                <div class="mb-3">
                    <label for="direccion">Dirección:</label>
                    <input type="text" id="direccion" name="direccion" value="<?php echo $cliente['Direccion']; ?>" required>
                </div>
                <div class="mb-3">
                    <label for="rfc">RFC:</label>
                    <input type="text" id="rfc" name="rfc" value="<?php echo $cliente['RFC']; ?>" required>
                </div>
                <div class="button-group">
                    <button type="submit" class="btn-guardar">Guardar cambios</button>
                    <button type="button" class="btn-cancelar" onclick="window.location.href='dashboard.php'">Cancelar</button>
                </div>
            </form>
        <?php else: ?>
            <?php if ($busqueda): ?>
                <p style="color: red;">Cliente no encontrado</p>
            <?php else: ?>
                <p style="color: red;">Por favor, inserta un ID o nombre</p>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</body>
</html>