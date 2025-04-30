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

$busqueda = $_GET['busqueda'];

// Buscar cliente por ID o nombre
$query = "SELECT * FROM cliente WHERE `ID del cliente` = ? OR Nombre LIKE ?";
$stmt = $conn->prepare($query);
$busquedaLike = "%$busqueda%"; // Para buscar coincidencias parciales en el nombre
$stmt->bind_param("ss", $busqueda, $busquedaLike);
$stmt->execute();
$result = $stmt->get_result();
$clientes = $result->fetch_all(MYSQLI_ASSOC);

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Información del Cliente - SERVIPARTES</title>
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
            max-width: 600px;
            text-align: left;
        }

        .form-container h1 {
            margin-bottom: 20px;
            font-size: 24px;
            color: #333;
            text-align: center;
        }

        .form-container p {
            font-size: 18px;
            color: #555;
            margin-bottom: 10px;
        }

        .form-container button {
            width: 100%;
            padding: 10px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            margin-top: 20px;
        }

        .form-container button.btn-volver {
            background-color: #6c757d;
            color: white;
        }

        .form-container button:hover {
            opacity: 0.9;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h1>Información del Cliente</h1>
        <?php if (count($clientes) > 0): ?>
            <?php foreach ($clientes as $cliente): ?>
                <p><strong>ID del cliente:</strong> <?php echo htmlspecialchars($cliente['ID del cliente']); ?></p>
                <p><strong>Nombre:</strong> <?php echo htmlspecialchars($cliente['Nombre']); ?></p>
                <p><strong>Teléfono:</strong> <?php echo htmlspecialchars($cliente['Telefono']); ?></p>
                <p><strong>Dirección:</strong> <?php echo htmlspecialchars($cliente['Direccion']); ?></p>
                <p><strong>RFC:</strong> <?php echo htmlspecialchars($cliente['RFC']); ?></p>
                <hr>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No se encontraron clientes.</p>
        <?php endif; ?>
        <button type="button" class="btn-volver" onclick="window.location.href='consultar_cliente.php'">Volver</button>
    </div>
</body>
</html>