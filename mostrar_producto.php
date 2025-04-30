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

// Buscar producto por ID o nombre
$query = "SELECT * FROM producto WHERE `Id Producto` = ? OR `Nombre del producto` LIKE ?";
$stmt = $conn->prepare($query);
$busquedaLike = "%$busqueda%";
$stmt->bind_param("ss", $busqueda, $busquedaLike);
$stmt->execute();
$result = $stmt->get_result();
$productos = $result->fetch_all(MYSQLI_ASSOC);

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Información del Producto - SERVIPARTES</title>
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
        .producto-info {
            margin-bottom: 20px;
            padding: 15px;
            background-color: rgba(240, 240, 240, 0.8);
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h1>Información del Producto</h1>
        
        <?php if (count($productos) > 0): ?>
            <?php foreach ($productos as $producto): ?>
                <div class="producto-info">
                    <p><strong>ID del Producto:</strong> <?php echo htmlspecialchars($producto['Id Producto']); ?></p>
                    <p><strong>Nombre:</strong> <?php echo htmlspecialchars($producto['Nombre del producto']); ?></p>
                    <p><strong>Descripción:</strong> <?php echo htmlspecialchars($producto['Descripcion']); ?></p>
                    <p><strong>Modelo:</strong> <?php echo htmlspecialchars($producto['Modelo']); ?></p>
                    <p><strong>Precio:</strong> $<?php echo number_format($producto['Precio'], 2); ?></p>
                    <p><strong>Existencia:</strong> <?php echo htmlspecialchars($producto['Cantidad de existencia']); ?></p>
                    <p><strong>Código de pieza:</strong> <?php echo htmlspecialchars($producto['Codigo de pieza']); ?></p>
                    <p><strong>Marca:</strong> <?php echo htmlspecialchars($producto['Marca']); ?></p>
                    <p><strong>Categoría:</strong> <?php echo htmlspecialchars($producto['Categoria']); ?></p>
                </div>
                <hr>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No se encontraron productos.</p>
        <?php endif; ?>
        
        <button type="button" class="btn-volver" onclick="window.location.href='consultar_producto.php'">Volver</button>
    </div>
</body>
</html>