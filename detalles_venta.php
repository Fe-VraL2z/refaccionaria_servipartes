// Archivo: detalles_venta.php
<?php
session_start();
require_once 'consultar_venta.php';

if (!isset($_GET['id_ventas']) || !isset($_GET['fecha'])) {
    header("Location: consultar_ventas.php");
    exit;
}

$id_ventas = $_GET['id_ventas'];
$fecha = $_GET['fecha'];
$datos = consultarVentaPorIdYFecha($id_ventas, $fecha);

if (!$datos) {
    die("Venta no encontrada");
}

$venta = $datos['venta'];
$productos = $datos['productos'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Detalles de Venta</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h1 class="mb-4">Detalles de la Venta</h1>
        
        <!-- Información General -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5>Información General</h5>
            </div>
            <div class="card-body">
                <p><strong>Folio:</strong> <?= $venta['folio'] ?></p>
                <p><strong>Fecha:</strong> <?= $venta['fecha_formateada'] ?></p>
                <p><strong>Atendido por:</strong> <?= $venta['nombre_usuario'] ?></p>
            </div>
        </div>

        <!-- Datos del Cliente -->
        <div class="card mb-4">
            <div class="card-header bg-info text-white">
                <h5>Datos del Cliente</h5>
            </div>
            <div class="card-body">
                <p><strong>Nombre:</strong> <?= $venta['cliente_nombre'] ?></p>
                <p><strong>Teléfono:</strong> <?= $venta['cliente_telefono'] ?></p>
                <p><strong>Dirección:</strong> <?= $venta['cliente_direccion'] ?></p>
            </div>
        </div>

        <!-- Productos Vendidos -->
        <div class="card mb-4">
            <div class="card-header bg-success text-white">
                <h5>Productos Vendidos</h5>
            </div>
            <div class="card-body">
                <table class="table table-striped">
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
                        <?php foreach ($productos as $producto): ?>
                        <tr>
                            <td><?= $producto['producto'] ?></td>
                            <td><?= $producto['Marca'] ?></td>
                            <td><?= $producto['Modelo'] ?></td>
                            <td><?= $producto['cantidad'] ?></td>
                            <td>$<?= number_format($producto['precio_unitario'], 2) ?></td>
                            <td>$<?= number_format($producto['subtotal'], 2) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <h4 class="text-end">Total: $<?= number_format($venta['total'], 2) ?></h4>
            </div>
        </div>

        <!-- Información de Pago -->
        <div class="card mb-4">
            <div class="card-header bg-warning text-dark">
                <h5>Información de Pago</h5>
            </div>
            <div class="card-body">
                <p><strong>Método de Pago:</strong> <?= $venta['metodo_pago'] ?></p>
                <p><strong>Garantía:</strong> <?= ($venta['garantia'] == 1) ? $venta['tiempo_garantia_valor'] . ' meses' : 'No aplica' ?></p>
            </div>
        </div>

        <!-- Botones de Acción -->
        <div class="text-center">
            <a href="imprimir_ticket.php?id=<?= $id_ventas ?>" class="btn btn-primary">Imprimir Ticket</a>
            <a href="enviar_correo.php?id=<?= $id_ventas ?>" class="btn btn-secondary">Enviar por Correo</a>
            <a href="consultar_ventas.php" class="btn btn-dark">Nueva Búsqueda</a>
        </div>
    </div>
</body>
</html>