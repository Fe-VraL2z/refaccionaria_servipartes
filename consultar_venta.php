<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: index.php");
    exit;
}

// Include the function from your previous request
include 'consultar_venta.php'; // Ensure this file exists with the function I provided earlier

// Display the search form
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consultar Ventas - Refaccionaria Servipartes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Consultar Ventas</h1>
        <form method="POST" action="">
            <div class="mb-3">
                <label for="id_venta" class="form-label">ID de Venta</label>
                <input type="text" class="form-control" id="id_venta" name="id_venta" placeholder="Ej. 1005" value="<?php echo isset($_POST['id_venta']) ? $_POST['id_venta'] : ''; ?>">
            </div>
            <div class="mb-3">
                <label for="fecha_venta" class="form-label">Fecha de Venta</label>
                <input type="date" class="form-control" id="fecha_venta" name="fecha_venta" value="<?php echo isset($_POST['fecha_venta']) ? $_POST['fecha_venta'] : ''; ?>">
            </div>
            <button type="submit" class="btn btn-primary">Buscar Venta</button>
            <a href="dashboard.php" class="btn btn-secondary">Regresar</a>
        </form>

        <?php
        // Display results if available
        if (isset($_SESSION['resultado_venta'])) {
            $resultado = $_SESSION['resultado_venta'];
            $venta = $resultado['venta'];
            $productos = $resultado['productos'];
            ?>
            <div class="mt-5">
                <h2>Detalles de la Venta</h2>
                <h4>Información General</h4>
                <p><strong># Folio:</strong> <?php echo $venta['id_venta']; ?></p>
                <p><strong>Fecha:</strong> <?php echo $venta['fecha']; ?></p>
                <p><strong>Atendido por:</strong> <?php echo $venta['atendido_por']; ?></p>

                <h4>Productos Vendidos</h4>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Marca</th>
                            <th>Modelo</th>
                            <th>Cantidad</th>
                            <th>Precio Unitario</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $total = 0;
                        foreach ($productos as $producto) {
                            $subtotal = $producto['cantidad'] * $producto['precio_unitario'];
                            $total += $subtotal;
                            ?>
                            <tr>
                                <td><?php echo $producto['producto']; ?></td>
                                <td><?php echo $producto['marca']; ?></td>
                                <td><?php echo $producto['modelo']; ?></td>
                                <td><?php echo $producto['cantidad']; ?></td>
                                <td>$<?php echo number_format($producto['precio_unitario'], 2); ?></td>
                                <td>$<?php echo number_format($subtotal, 2); ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="5" class="text-end"><strong>Total:</strong></td>
                            <td>$<?php echo number_format($total, 2); ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <?php
            unset($_SESSION['resultado_venta']);
        }
        ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>