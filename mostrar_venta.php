<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: index.php");
    exit;
}

$conn = new mysqli('localhost', 'root', '', 'refaccionaria_servipartes');
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Obtener parámetros de búsqueda
$id_venta = $_GET['id_venta'] ?? '';
$fecha = $_GET['fecha'] ?? '';

// Consulta corregida - usando direccion_negocio en lugar de Email
$query_venta = "SELECT v.*, c.Nombre AS cliente_nombre, c.Telefono, v.direccion_negocio 
                FROM ventas v 
                JOIN cliente c ON v.cliente_id = c.`ID del cliente` 
                WHERE v.id_ventas = ? AND DATE(v.fecha) = ?";
$stmt = $conn->prepare($query_venta);
$stmt->bind_param("is", $id_venta, $fecha);
$stmt->execute();
$venta = $stmt->get_result()->fetch_assoc();

if (!$venta) {
    die("<script>alert('Venta no encontrada'); window.location.href='consultar_ventas.php';</script>");
}

// Consulta para obtener los productos de la venta
$query_productos = "SELECT p.`Nombre del producto`, p.Marca, p.Modelo, 
                           vp.cantidad, vp.precio_unitario, 
                           (vp.cantidad * vp.precio_unitario) AS subtotal
                    FROM ventas_productos vp
                    JOIN producto p ON vp.`Id Producto` = p.`Id Producto`
                    WHERE vp.id_ventas = ?";
$stmt = $conn->prepare($query_productos);
$stmt->bind_param("i", $id_venta);
$stmt->execute();
$productos = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$conn->close();

// Formatear la fecha
$fecha_formateada = date('d/m/Y h:i a', strtotime($venta['fecha']));
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalles de Venta - SERVIPARTES</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .container {
            max-width: 1000px;
            margin-top: 30px;
        }
        .card {
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .card-header {
            background-color: #2c3e50;
            color: white;
            border-radius: 10px 10px 0 0 !important;
        }
        .table {
            margin-top: 20px;
        }
        .table th {
            background-color: #3498db;
            color: white;
        }
        .total {
            font-size: 1.2rem;
            font-weight: bold;
            text-align: right;
            margin-top: 20px;
        }
        .btn-actions {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h3 class="text-center">Detalles de la Venta</h3>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h4>Información General</h4>
                        <p><strong>Folio:</strong> <?= htmlspecialchars($venta['folio']) ?></p>
                        <p><strong>Fecha:</strong> <?= $fecha_formateada ?></p>
                        <p><strong>Atendido por:</strong> <?= htmlspecialchars($venta['nombre_usuario']) ?></p>
                    </div>
                    <div class="col-md-6">
                        <h4>Datos del Cliente</h4>
                        <p><strong>Nombre:</strong> <?= htmlspecialchars($venta['cliente_nombre']) ?></p>
                        <p><strong>Teléfono:</strong> <?= htmlspecialchars($venta['Telefono']) ?></p>
                        <p><strong>Dirección:</strong> <?= htmlspecialchars($venta['direccion_negocio']) ?></p>
                    </div>
                </div>

                <h4>Productos Vendidos</h4>
                <table class="table table-bordered table-striped">
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
                            <td><?= htmlspecialchars($producto['Nombre del producto']) ?></td>
                            <td><?= htmlspecialchars($producto['Marca']) ?></td>
                            <td><?= htmlspecialchars($producto['Modelo']) ?></td>
                            <td><?= $producto['cantidad'] ?></td>
                            <td>$<?= number_format($producto['precio_unitario'], 2) ?></td>
                            <td>$<?= number_format($producto['subtotal'], 2) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <div class="total">
                    <strong>Total: $<?= number_format($venta['total'], 2) ?></strong>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <h4>Información de Pago</h4>
                        <p><strong>Método de Pago:</strong> <?= htmlspecialchars($venta['metodo_pago']) ?></p>
                        <p><strong>Garantía:</strong> <?= ($venta['garantia'] == 1) ? $venta['tiempo_garantia'].' meses' : 'No aplica' ?></p>
                    </div>
                </div>

                <div class="btn-actions d-flex justify-content-between">
                    <a href="consultar_ventas.php" class="btn btn-secondary">Nueva Búsqueda</a>
                    <div>
                        <a href="imprimir_ticket.php?id=<?= $id_venta ?>" class="btn btn-primary">Imprimir Ticket</a>
                        <button class="btn btn-success" onclick="enviarPorCorreo()">Enviar por Correo</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function enviarPorCorreo() {
            let email = prompt("Ingrese el correo electrónico del cliente:");
            if (email) {
                fetch('enviar_correo.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `email=${encodeURIComponent(email)}&venta_id=<?= $id_venta ?>`
                })
                .then(response => response.text())
                .then(data => {
                    alert(data);
                })
                .catch(error => {
                    alert('Error al enviar el correo');
                });
            }
        }
    </script>
</body>
</html>