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
    // Procesar el formulario de edición
    $id_proveedor = $_POST['id_proveedor'];
    $nombre = $_POST['nombre'];
    $telefono = $_POST['telefono'];
    $direccion = $_POST['direccion'];
    $email = $_POST['email'];
    $rfc = $_POST['rfc'];
    $contacto_principal = $_POST['contacto_principal'];
    $metodo_pago = $_POST['metodo_pago'];
    $plazo_entrega = $_POST['plazo_entrega'];
    $forma_envio = $_POST['forma_envio'];

    $stmt = $conn->prepare("UPDATE proveedor SET 
                          `Nombre` = ?,
                          `Telefono` = ?,
                          `Direccion` = ?,
                          `Email` = ?,
                          `RFC` = ?,
                          `Contacto principal` = ?,
                          `Metodo Pago` = ?,
                          `Plazo Entrega` = ?,
                          `Forma Envio` = ?
                          WHERE `ID Proveedor` = ?");
    
    $stmt->bind_param("sssssssssi", $nombre, $telefono, $direccion, $email, 
                     $rfc, $contacto_principal, $metodo_pago, $plazo_entrega, 
                     $forma_envio, $id_proveedor);

    if ($stmt->execute()) {
        $_SESSION['mensaje'] = "Proveedor actualizado exitosamente";
        $_SESSION['tipo_mensaje'] = "success";
        header("Location: dashboard.php");
        exit;
    } else {
        $_SESSION['mensaje'] = "Error al actualizar el proveedor: " . $conn->error;
        $_SESSION['tipo_mensaje'] = "error";
    }
}

$proveedor = null;
if ($busqueda) {
    $query = "SELECT * FROM proveedor WHERE `ID Proveedor` = ? OR `Nombre` LIKE ?";
    $stmt = $conn->prepare($query);
    $busquedaLike = "%$busqueda%";
    $stmt->bind_param("is", $busqueda, $busquedaLike);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $proveedor = $result->fetch_assoc();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Proveedor - SERVIPARTES</title>
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
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 500px;
            max-height: 80vh;
            overflow-y: auto;
        }
        .form-container h1 {
            margin-bottom: 20px;
            font-size: 24px;
            color: #333;
            text-align: center;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #444;
        }
        .form-group input, .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }
        .button-group {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }
        .button-group button {
            width: 48%;
            padding: 12px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
        }
        .btn-guardar {
            background-color: #28a745;
            color: white;
        }
        .btn-cancelar {
            background-color: #dc3545;
            color: white;
        }
        .status-message {
            margin-bottom: 15px;
            padding: 10px;
            border-radius: 5px;
            text-align: center;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h1>Editar Proveedor</h1>
        
        <?php if ($proveedor): ?>
            <div class="status-message success">
                Proveedor encontrado, puedes editar
            </div>
            
            <form method="post">
                <input type="hidden" name="id_proveedor" value="<?php echo $proveedor['ID Proveedor']; ?>">
                
                <div class="form-group">
                    <label for="id_proveedor">ID Proveedor:</label>
                    <input type="text" id="id_proveedor" value="<?php echo $proveedor['ID Proveedor']; ?>" disabled>
                </div>
                
                <div class="form-group">
                    <label for="nombre">Nombre:</label>
                    <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($proveedor['Nombre']); ?>" maxlength="50" required>
                </div>
                
                <div class="form-group">
                    <label for="telefono">Teléfono:</label>
                    <input type="text" id="telefono" name="telefono" value="<?php echo htmlspecialchars($proveedor['Telefono']); ?>" maxlength="10" required>
                </div>
                
                <div class="form-group">
                    <label for="direccion">Dirección:</label>
                    <input type="text" id="direccion" name="direccion" value="<?php echo htmlspecialchars($proveedor['Direccion']); ?>" maxlength="50" required>
                </div>
                
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($proveedor['Email']); ?>" maxlength="100">
                </div>
                
                <div class="form-group">
                    <label for="rfc">RFC:</label>
                    <input type="text" id="rfc" name="rfc" value="<?php echo htmlspecialchars($proveedor['RFC']); ?>" maxlength="14">
                </div>
                
                <div class="form-group">
                    <label for="contacto_principal">Contacto principal:</label>
                    <input type="text" id="contacto_principal" name="contacto_principal" value="<?php echo htmlspecialchars($proveedor['Contacto principal']); ?>" maxlength="10">
                </div>
                
                <div class="form-group">
                    <label for="metodo_pago">Método de Pago:</label>
                    <select id="metodo_pago" name="metodo_pago">
                        <option value="">Seleccione...</option>
                        <option value="Efectivo" <?php echo ($proveedor['Metodo Pago'] == 'Efectivo') ? 'selected' : ''; ?>>Efectivo</option>
                        <option value="Transferencia" <?php echo ($proveedor['Metodo Pago'] == 'Transferencia') ? 'selected' : ''; ?>>Transferencia</option>
                        <option value="Cheque" <?php echo ($proveedor['Metodo Pago'] == 'Cheque') ? 'selected' : ''; ?>>Cheque</option>
                        <option value="Tarjeta" <?php echo ($proveedor['Metodo Pago'] == 'Tarjeta') ? 'selected' : ''; ?>>Tarjeta</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="plazo_entrega">Plazo de Entrega:</label>
                    <input type="text" id="plazo_entrega" name="plazo_entrega" value="<?php echo htmlspecialchars($proveedor['Plazo Entrega']); ?>" maxlength="30">
                </div>
                
                <div class="form-group">
                    <label for="forma_envio">Forma de Envío:</label>
                    <select id="forma_envio" name="forma_envio">
                        <option value="">Seleccione...</option>
                        <option value="Recoge en local" <?php echo ($proveedor['Forma Envio'] == 'Recoge en local') ? 'selected' : ''; ?>>Recoge en local</option>
                        <option value="Envío estándar" <?php echo ($proveedor['Forma Envio'] == 'Envío estándar') ? 'selected' : ''; ?>>Envío estándar</option>
                        <option value="Envío express" <?php echo ($proveedor['Forma Envio'] == 'Envío express') ? 'selected' : ''; ?>>Envío express</option>
                    </select>
                </div>
                
                <div class="button-group">
                    <button type="submit" class="btn-guardar">Guardar cambios</button>
                    <button type="button" class="btn-cancelar" onclick="window.location.href='dashboard.php'">Cancelar</button>
                </div>
            </form>
            
        <?php else: ?>
            <?php if ($busqueda): ?>
                <div class="status-message error">
                    Proveedor no encontrado
                </div>
            <?php else: ?>
                <div class="status-message error">
                    Por favor, inserta un ID o nombre
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</body>
</html>