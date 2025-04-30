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
    $id_producto = $_POST['id_producto'];
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $modelo = $_POST['modelo'];
    $precio = $_POST['precio'];
    $cantidad_existencia = $_POST['cantidad_existencia'];
    $codigo_pieza = $_POST['codigo_pieza'];
    $marca = $_POST['marca'];
    $categoria = $_POST['categoria'];
    $unidad_medida = $_POST['unidad_medida'];
    $dimensiones = $_POST['dimensiones'];

    $stmt = $conn->prepare("UPDATE producto SET 
                          `Nombre del producto` = ?,
                          `Descripcion` = ?,
                          `Modelo` = ?,
                          `Precio` = ?,
                          `Cantidad de existencia` = ?,
                          `Codigo de pieza` = ?,
                          `Marca` = ?,
                          `Categoria` = ?,
                          `Unidad de medida` = ?,
                          `Dimensiones` = ?
                          WHERE `Id Producto` = ?");
    
    $stmt->bind_param("sssdissssss", $nombre, $descripcion, $modelo, $precio, 
                     $cantidad_existencia, $codigo_pieza, $marca, $categoria, 
                     $unidad_medida, $dimensiones, $id_producto);

    if ($stmt->execute()) {
        $_SESSION['mensaje'] = "Producto actualizado exitosamente";
        $_SESSION['tipo_mensaje'] = "success";
        header("Location: dashboard.php");
        exit;
    } else {
        $_SESSION['mensaje'] = "Error al actualizar el producto: " . $conn->error;
        $_SESSION['tipo_mensaje'] = "error";
    }
}

$producto = null;
if ($busqueda) {
    $query = "SELECT * FROM producto WHERE `Id Producto` = ? OR `Nombre del producto` LIKE ?";
    $stmt = $conn->prepare($query);
    $busquedaLike = "%$busqueda%";
    $stmt->bind_param("ss", $busqueda, $busquedaLike);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $producto = $result->fetch_assoc();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Producto - SERVIPARTES</title>
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
        <h1>Editar Producto</h1>
        
        <?php if ($producto): ?>
            <div class="status-message success">
                Producto encontrado, puedes editar
            </div>
            
            <form method="post">
                <input type="hidden" name="id_producto" value="<?php echo htmlspecialchars($producto['Id Producto']); ?>">
                
                <div class="form-group">
                    <label for="id_producto">ID del Producto:</label>
                    <input type="text" id="id_producto" value="<?php echo htmlspecialchars($producto['Id Producto']); ?>" disabled>
                </div>
                
                <div class="form-group">
                    <label for="nombre">Nombre del producto:</label>
                    <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($producto['Nombre del producto']); ?>" maxlength="50" required>
                </div>
                
                <div class="form-group">
                    <label for="descripcion">Descripción:</label>
                    <input type="text" id="descripcion" name="descripcion" value="<?php echo htmlspecialchars($producto['Descripcion']); ?>" maxlength="50">
                </div>
                
                <div class="form-group">
                    <label for="modelo">Modelo:</label>
                    <input type="text" id="modelo" name="modelo" value="<?php echo htmlspecialchars($producto['Modelo']); ?>" maxlength="30">
                </div>
                
                <div class="form-group">
                    <label for="precio">Precio ($):</label>
                    <input type="number" id="precio" name="precio" value="<?php echo htmlspecialchars($producto['Precio']); ?>" step="0.01" min="0" required>
                </div>
                
                <div class="form-group">
                    <label for="cantidad_existencia">Cantidad de existencia:</label>
                    <input type="number" id="cantidad_existencia" name="cantidad_existencia" value="<?php echo htmlspecialchars($producto['Cantidad de existencia']); ?>" min="0" required>
                </div>
                
                <div class="form-group">
                    <label for="codigo_pieza">Código de pieza:</label>
                    <input type="text" id="codigo_pieza" name="codigo_pieza" value="<?php echo htmlspecialchars($producto['Codigo de pieza']); ?>" maxlength="50">
                </div>
                
                <div class="form-group">
                    <label for="marca">Marca:</label>
                    <input type="text" id="marca" name="marca" value="<?php echo htmlspecialchars($producto['Marca']); ?>" maxlength="30">
                </div>
                
                <div class="form-group">
                    <label for="categoria">Categoría:</label>
                    <input type="text" id="categoria" name="categoria" value="<?php echo htmlspecialchars($producto['Categoria']); ?>" maxlength="25">
                </div>
                
                <div class="form-group">
                    <label for="unidad_medida">Unidad de medida:</label>
                    <input type="text" id="unidad_medida" name="unidad_medida" value="<?php echo htmlspecialchars($producto['Unidad de medida']); ?>" maxlength="30">
                </div>
                
                <div class="form-group">
                    <label for="dimensiones">Dimensiones:</label>
                    <input type="text" id="dimensiones" name="dimensiones" value="<?php echo htmlspecialchars($producto['Dimensiones']); ?>" maxlength="50" placeholder="Ej: 10x20x15 cm">
                </div>
                
                <div class="button-group">
                    <button type="submit" class="btn-guardar">Guardar cambios</button>
                    <button type="button" class="btn-cancelar" onclick="window.location.href='dashboard.php'">Cancelar</button>
                </div>
            </form>
            
        <?php else: ?>
            <?php if ($busqueda): ?>
                <div class="status-message error">
                    Producto no encontrado
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