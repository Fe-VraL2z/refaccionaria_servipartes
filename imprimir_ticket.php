<?php
session_start();
date_default_timezone_set('America/Mexico_City');

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: index.php");
    exit;
}
date_default_timezone_set('America/Mexico_City');
$conn = new mysqli('localhost', 'root', '', 'refaccionaria_servipartes');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Validate and sanitize the venta_id - Compatible with both 'id' and 'id_venta' parameters
$id_ventas = 0;
if (isset($_GET['id_venta'])) {
    $id_ventas = intval($_GET['id_venta']);
} elseif (isset($_GET['id'])) {
    $id_ventas = intval($_GET['id']);
}

if ($id_ventas <= 0) {
    die("ID de venta no especificado o inválido");
}

// Obtener datos de la venta
$query_venta = "SELECT v.*, c.Nombre AS cliente_nombre 
                FROM ventas v 
                JOIN cliente c ON v.cliente_id = c.`ID del cliente` 
                WHERE v.id_ventas = ?";
$stmt = $conn->prepare($query_venta);
$stmt->bind_param("i", $id_ventas);
$stmt->execute();
$result = $stmt->get_result();
$venta = $result->fetch_assoc();

if (!$venta) {
    die("Venta no encontrada");
}

// Obtener productos de la venta
$query_productos = "SELECT vp.cantidad, vp.producto AS `Nombre del producto`, p.Marca, p.Modelo, vp.precio_unitario AS Precio 
                    FROM ventas_productos vp 
                    JOIN producto p ON vp.`Id Producto` = p.`Id Producto` 
                    WHERE vp.id_ventas = ?";
$stmt = $conn->prepare($query_productos);
$stmt->bind_param("i", $id_ventas);
$stmt->execute();
$productos = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket de Venta - SERVIPARTES</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @media print {
            body * {
                visibility: hidden;
            }

            .ticket-container,
            .ticket-container * {
                visibility: visible;
            }

            .ticket-container {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                margin: 0;
                padding: 15px;
                box-shadow: none;
            }

            .no-print {
                display: none !important;
            }
        }

        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f8f9fa;
        }

        .ticket-container {
            background-color: white;
            width: 300px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .ticket-header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 1px dashed #ccc;
            padding-bottom: 10px;
        }

        .ticket-header h2 {
            font-size: 20px;
            margin: 5px 0;
            color: #333;
        }

        .ticket-header p {
            margin: 5px 0;
            font-size: 14px;
            color: #666;
        }

        .ticket-info p {
            margin: 5px 0;
            font-size: 14px;
        }

        .ticket-items {
            margin: 15px 0;
            border-top: 1px dashed #ccc;
            border-bottom: 1px dashed #ccc;
            padding: 10px 0;
        }

        .ticket-item {
            display: flex;
            justify-content: space-between;
            margin: 5px 0;
            font-size: 13px;
        }

        .ticket-total {
            font-weight: bold;
            font-size: 16px;
            margin-top: 10px;
            text-align: right;
        }

        .ticket-footer {
            text-align: center;
            margin-top: 20px;
            font-size: 12px;
            color: #666;
            border-top: 1px dashed #ccc;
            padding-top: 10px;
        }

        .button-group {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }

        .button-group button {
            width: 48%;
            padding: 10px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
        }

        .btn-print {
            background-color: #007bff;
            color: white;
        }

        .btn-email {
            background-color: #28a745;
            color: white;
        }

        .btn-back {
            background-color: #6c757d;
            color: white;
            width: 100%;
            margin-top: 10px;
        }
    </style>
</head>

<body>
    <div class="ticket-container">
        <div class="ticket-header">
            <h2>SERVIPARTES</h2>
            <p>Ticket de Venta</p>
            <p>Fecha: <?php echo date('d/m/Y H:i', strtotime($venta['fecha'])); ?></p>
        </div>

        <div class="ticket-info">
            <p><strong>Folio:</strong> <?php echo htmlspecialchars($venta['folio']); ?></p>
            <p><strong>Cliente:</strong> <?php echo htmlspecialchars($venta['cliente_nombre']); ?></p>
            <p><strong>Dirección:</strong> <?php echo htmlspecialchars($venta['direccion_negocio']); ?></p>
            <p><strong>Teléfono:</strong> <?php echo htmlspecialchars($venta['numero_atencion_cliente']); ?></p>
        </div>

        <div class="ticket-items">
            <?php foreach ($productos as $producto): ?>
                <div class="ticket-item">
                    <span><?php echo htmlspecialchars($producto['Nombre del producto']); ?> x<?php echo $producto['cantidad']; ?></span>
                    <span>$<?php echo number_format($producto['Precio'] * $producto['cantidad'], 2); ?></span>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="ticket-total">
            <p><strong>Total: $<?php echo number_format($venta['total'], 2); ?></strong></p>
        </div>

        <div class="ticket-info">
            <p><strong>Método de Pago:</strong> <?php echo htmlspecialchars($venta['metodo_pago']); ?></p>
            <?php if ($venta['garantia'] == 1): ?>
                <p><strong>Garantía:</strong> <?php echo $venta['tiempo_garantia_valor'] . ' ' . htmlspecialchars($venta['tiempo_garantia_unidad']); ?></p>
            <?php endif; ?>
            <p><strong>Atendido por:</strong> <?php echo htmlspecialchars($venta['nombre_usuario']); ?></p>
        </div>

        <div class="ticket-footer">
            <p>Gracias por su compra</p>
            <p>Válido como comprobante fiscal</p>
        </div>

        <div class="button-group no-print">
            <button type="button" class="btn-print" onclick="window.print()">Imprimir</button>
            <button type="button" class="btn-email" onclick="enviarPorCorreo()">Enviar por Correo</button>
        </div>

        <div class="no-print">
            <button type="button" class="btn-back" onclick="cerrarVentana()">Cerrar</button>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function enviarPorCorreo() {
            let email = prompt("Ingrese el correo electrónico del cliente:", "<?= $venta['cliente_email'] ?? '' ?>");
            if (email && email.includes('@')) {
                // Mostrar carga
                $('.btn-email').html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Enviando...');

                $.ajax({
                    url: 'send_ticket_email.php',
                    type: 'POST',
                    data: {
                        email: email,
                        venta_id: <?= $id_ventas ?>,
                        cliente: '<?= htmlspecialchars($venta['cliente_nombre']) ?>'
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            alert(response.message);
                        } else {
                            alert(response.error || 'Error al enviar el ticket');
                        }
                    },
                    error: function(xhr) {
                        try {
                            const response = JSON.parse(xhr.responseText);
                            alert(response.error || 'Error de conexión');
                        } catch (e) {
                            alert('Error al procesar la respuesta');
                        }
                    },
                    complete: function() {
                        $('.btn-email').html('Enviar por Correo');
                    }
                });
            } else if (email !== null) {
                alert('Por favor ingrese un correo electrónico válido');
            }
        }

        function cerrarVentana() {
            // Si es una ventana popup, la cierra
            if (window.opener) {
                window.close();
            } else {
                // Si no, regresa a la página anterior
                window.history.back();
            }
        }

        // Auto-imprimir si viene de una venta recién procesada
        <?php if (isset($_GET['auto_print']) && $_GET['auto_print'] == '1'): ?>
            window.onload = function() {
                setTimeout(function() {
                    window.print();
                }, 500);
            };
        <?php endif; ?>
    </script>
</body>

</html>