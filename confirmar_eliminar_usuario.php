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

// Buscar usuario por ID o nombre
$query = "SELECT * FROM usuarios WHERE id = ? OR usuario LIKE ?";
$stmt = $conn->prepare($query);
$busquedaLike = "%$busqueda%";
$stmt->bind_param("ss", $busqueda, $busquedaLike);
$stmt->execute();
$result = $stmt->get_result();
$usuario = $result->fetch_assoc();

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmar Eliminar Usuario - SERVIPARTES</title>
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
            max-width: 500px;
            text-align: center;
        }
        .form-container h1 {
            margin-bottom: 20px;
            font-size: 24px;
            color: #333;
        }
        .usuario-info {
            background-color: rgba(240, 240, 240, 0.8);
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: left;
        }
        .usuario-info p {
            margin: 5px 0;
            color: #555;
        }
        .button-group {
            display: flex;
            justify-content: space-between;
        }
        .button-group button {
            width: 48%;
            padding: 10px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
        }
        .btn-eliminar {
            background-color: #dc3545;
            color: white;
        }
        .btn-cancelar {
            background-color: #6c757d;
            color: white;
        }
        .button-group button:hover {
            opacity: 0.9;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <?php if ($usuario): ?>
            <h1>¿Eliminar este usuario?</h1>
            <div class="usuario-info">
                <p><strong>ID:</strong> <?php echo htmlspecialchars($usuario['id']); ?></p>
                <p><strong>Usuario:</strong> <?php echo htmlspecialchars($usuario['usuario']); ?></p>
            </div>
            <form action="eliminar_usuario.php" method="POST">
                <input type="hidden" name="id_usuario" value="<?php echo htmlspecialchars($usuario['id']); ?>">
                <div class="button-group">
                    <button type="submit" class="btn-eliminar">Confirmar Eliminar</button>
                    <button type="button" class="btn-cancelar" onclick="window.location.href='dashboard.php'">Cancelar</button>
                </div>
            </form>
        <?php else: ?>
            <h1>Usuario no encontrado</h1>
            <p>No se encontró ningún usuario con los criterios de búsqueda.</p>
            <button type="button" class="btn-cancelar" onclick="window.location.href='buscar_usuario_eliminar.php'">Volver</button>
        <?php endif; ?>
    </div>
</body>
</html>