<?php
session_start();
date_default_timezone_set('America/Mexico_City');

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: index.php");
    exit;
}

// Conexión a la base de datos
$conn = new mysqli('localhost', 'root', '', 'refaccionaria_servipartes');
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Obtener parámetros
$usuario = $_POST['usuario'];
$fecha = $_POST['fecha_corte'];

// Primero verificamos si la tabla venta_productos existe
$tabla_existe = $conn->query("SHOW TABLES LIKE 'ventas_productos'")->num_rows > 0;
if (!$tabla_existe) {
    die("Error: La tabla 'ventas_productos' no existe en la base de datos");
}

// Consulta para obtener ventas del día
$query_ventas = "SELECT v.id_ventas, v.folio, v.fecha, v.total, v.metodo_pago, 
                        c.Nombre AS cliente_nombre
                 FROM ventas v
                 JOIN cliente c ON v.cliente_id = c.`ID del cliente`
                 WHERE DATE(v.fecha) = ? AND v.nombre_usuario = ?
                 ORDER BY v.fecha";
$stmt = $conn->prepare($query_ventas);
$stmt->bind_param("ss", $fecha, $usuario);
$stmt->execute();
$ventas = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Si no hay ventas, mostramos mensaje y terminamos
if (empty($ventas)) {
    $conn->close();
    die("<div class='alert alert-info text-center' style='margin-top:50px;'>
            <h3>No se han registrado ventas</h3>
            <p>No se encontraron ventas para el usuario <strong>".htmlspecialchars($usuario)."</strong> en la fecha <strong>".date('d/m/Y', strtotime($fecha))."</strong></p>
            <a href='corte_caja.php' class='btn btn-primary'>Volver</a>
        </div>");
}

// Consulta para productos vendidos (usando el nombre correcto de la tabla: ventas_productos)
$query_productos = "SELECT p.`Nombre del producto`, p.Marca, p.Modelo, 
                           SUM(vp.cantidad) AS cantidad_vendida,
                           p.`Cantidad de existencia` AS existencia_actual,
                           (SELECT SUM(cantidad) FROM ventas_productos vp2 
                            JOIN ventas v2 ON vp2.id_ventas = v2.id_ventas 
                            WHERE vp2.`Id Producto` = p.`Id Producto` 
                            AND DATE(v2.fecha) = ?) AS total_vendido,
                           SUM(vp.cantidad * vp.precio_unitario) AS total_venta
                    FROM ventas_productos vp
                    JOIN producto p ON vp.`Id Producto` = p.`Id Producto`
                    JOIN ventas v ON vp.id_ventas = v.id_ventas
                    WHERE DATE(v.fecha) = ? AND v.nombre_usuario = ?
                    GROUP BY p.`Id Producto`";
$stmt = $conn->prepare($query_productos);
$stmt->bind_param("sss", $fecha, $fecha, $usuario);
$stmt->execute();
$productos = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Cálculos resumen
$total_ventas = count($ventas);
$monto_total = array_sum(array_column($ventas, 'total'));
$total_productos = array_sum(array_column($productos, 'cantidad_vendida'));

$conn->close();

// Formatear fecha
$fecha_formateada = date('d/m/Y', strtotime($fecha));
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Corte - SERVIPARTES</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #fff;
        }
        .container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .resumen {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
        }
        .resumen-item {
            margin-bottom: 10px;
        }
        table {
            width: 100%;
            margin-bottom: 30px;
            border-collapse: collapse;
        }
        th {
            background-color: #2c3e50;
            color: white;
            padding: 10px;
            text-align: left;
        }
        td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
        }
        .total-general {
            font-size: 1.2rem;
            font-weight: bold;
            text-align: right;
            margin: 20px 0;
        }
        .footer {
            margin-top: 50px;
            display: flex;
            justify-content: space-between;
        }
        .btn-print {
            background-color: #3498db;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
        }
        .btn-pdf {
            background-color: #e74c3c;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
        }
        @media print {
            .no-print {
                display: none;
            }
            body {
                padding: 0;
                margin: 0;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Corte de Caja</h1>
            <p><strong>Fecha:</strong> <?= $fecha_formateada ?></p>
            <p><strong>Usuario:</strong> <?= htmlspecialchars($usuario) ?></p>
        </div>

        <div class="resumen">
            <h2>Resumen General</h2>
            <div class="row">
                <div class="col-md-4 resumen-item">
                    <h4>Total de Ventas</h4>
                    <p><?= $total_ventas ?></p>
                </div>
                <div class="col-md-4 resumen-item">
                    <h4>Monto Total</h4>
                    <p>$<?= number_format($monto_total, 2) ?></p>
                </div>
                <div class="col-md-4 resumen-item">
                    <h4>Productos Vendidos</h4>
                    <p><?= $total_productos ?></p>
                </div>
            </div>
        </div>

        <h2>Detalle de Ventas</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Folio</th>
                    <th>Hora</th>
                    <th>Cliente</th>
                    <th>Productos</th>
                    <th>Método Pago</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($ventas as $venta): ?>
                <?php
                // Obtener productos de esta venta
                $conn_temp = new mysqli('localhost', 'root', '', 'refaccionaria_servipartes');
                $query_productos_venta = "SELECT p.`Nombre del producto`, vp.cantidad, vp.precio_unitario
                                         FROM ventas_productos vp
                                         JOIN producto p ON vp.`Id Producto` = p.`Id Producto`
                                         WHERE vp.id_Ventas = ?";
                $stmt_temp = $conn_temp->prepare($query_productos_venta);
                $stmt_temp->bind_param("i", $venta['id_ventas']);
                $stmt_temp->execute();
                $productos_venta = $stmt_temp->get_result()->fetch_all(MYSQLI_ASSOC);
                $conn_temp->close();
                
                $productos_text = '';
                foreach ($productos_venta as $prod) {
                    $productos_text .= htmlspecialchars($prod['Nombre del producto']) . " (" . $prod['cantidad'] . ") - $" . 
                                     number_format($prod['cantidad'] * $prod['precio_unitario'], 2) . "<br>";
                }
                ?>
                <tr>
                    <td><?= htmlspecialchars($venta['folio']) ?></td>
                    <td><?= date('h:i a', strtotime($venta['fecha'])) ?></td>
                    <td><?= htmlspecialchars($venta['cliente_nombre']) ?></td>
                    <td><?= $productos_text ?></td>
                    <td><?= htmlspecialchars($venta['metodo_pago']) ?></td>
                    <td>$<?= number_format($venta['total'], 2) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="total-general">
            <strong>Total General: $<?= number_format($monto_total, 2) ?></strong>
        </div>

        <h2>Resumen de Productos Vendidos</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Marca</th>
                    <th>Modelo</th>
                    <th>Cantidad Vendida</th>
                    <th>Existencia Restante</th>
                    <th>Total Vendido</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($productos as $prod): ?>
                <tr>
                    <td><?= htmlspecialchars($prod['Nombre del producto']) ?></td>
                    <td><?= htmlspecialchars($prod['Marca']) ?></td>
                    <td><?= htmlspecialchars($prod['Modelo']) ?></td>
                    <td><?= $prod['cantidad_vendida'] ?></td>
                    <td><?= $prod['existencia_actual'] ?> (de <?= $prod['existencia_actual'] + $prod['cantidad_vendida'] ?>)</td>
                    <td>$<?= number_format($prod['total_venta'], 2) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="footer">
            <div>
                <p><strong>Elaboró:</strong><br><?= htmlspecialchars($usuario) ?></p>
                <p><strong>Revisó:</strong><br>Supervisor de Turno</p>
            </div>
           <div class="no-print">
    <button class="btn-print" onclick="window.print()">Imprimir Reporte</button>
    <button class="btn-pdf" onclick="generarPDF()">Exportar a PDF</button>
    <a href="corte_caja.php" class="btn btn-secondary">Salir</a>
</div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script>
        // Función para generar PDF
        function generarPDF() {
            const { jsPDF } = window.jspdf;
            
            // Obtener el elemento a convertir
            const element = document.querySelector('.container');
            
            html2canvas(element).then(canvas => {
                const imgData = canvas.toDataURL('image/png');
                const pdf = new jsPDF('p', 'mm', 'a4');
                const imgProps = pdf.getImageProperties(imgData);
                const pdfWidth = pdf.internal.pageSize.getWidth();
                const pdfHeight = (imgProps.height * pdfWidth) / imgProps.width;
                
                pdf.addImage(imgData, 'PNG', 0, 0, pdfWidth, pdfHeight);
                pdf.save('Corte_Caja_<?= $fecha ?>_<?= $usuario ?>.pdf');
            });
        }
    </script>
</body>
</html>