<?php
session_start();
require_once __DIR__ . '/vendor/autoload.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("HTTP/1.1 403 Forbidden");
    exit;
}

use Mpdf\Mpdf;
use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

$conn = new mysqli('localhost', 'root', '', 'refaccionaria_servipartes');
if ($conn->connect_error) {
    die(json_encode(['error' => 'Error de conexión a la base de datos']));
}

$venta_id = $_POST['venta_id'] ?? '';
$email = $_POST['email'] ?? '';
$cliente = $_POST['cliente'] ?? '';

if (empty($venta_id) || empty($email)) {
    die(json_encode(['error' => 'Datos incompletos']));
}

// Validar formato de email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die(json_encode(['error' => 'Correo electrónico no válido']));
}

// Obtener datos de la venta
$query_venta = "SELECT v.*, c.Nombre AS cliente_nombre 
                FROM ventas v 
                JOIN cliente c ON v.cliente_id = c.`ID del cliente` 
                WHERE v.id_ventas = ?";
$stmt = $conn->prepare($query_venta);
$stmt->bind_param("i", $venta_id);
$stmt->execute();
$result = $stmt->get_result();
$venta = $result->fetch_assoc();

if (!$venta) {
    die(json_encode(['error' => 'Venta no encontrada']));
}

// Obtener productos de la venta
$query_productos = "SELECT vp.cantidad, p.`Nombre del producto`, p.Marca, p.Modelo, vp.precio_unitario AS Precio 
                    FROM ventas_productos vp 
                    JOIN producto p ON vp.`Id Producto` = p.`Id Producto` 
                    WHERE vp.id_ventas = ?";
$stmt = $conn->prepare($query_productos);
$stmt->bind_param("i", $venta_id);
$stmt->execute();
$productos = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$conn->close();

// Generar HTML para el PDF
ob_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket de Venta - SERVIPARTES</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; }
        .ticket-container { width: 100%; max-width: 300px; margin: 0 auto; padding: 15px; border: 1px solid #ddd; }
        .ticket-header { text-align: center; margin-bottom: 15px; border-bottom: 1px dashed #ccc; padding-bottom: 10px; }
        .ticket-header h2 { font-size: 20px; margin: 5px 0; }
        .ticket-info p { margin: 5px 0; font-size: 12px; }
        .ticket-items { margin: 15px 0; border-top: 1px dashed #ccc; border-bottom: 1px dashed #ccc; padding: 10px 0; }
        .ticket-item { display: flex; justify-content: space-between; margin: 5px 0; font-size: 12px; }
        .ticket-total { font-weight: bold; font-size: 14px; margin-top: 10px; text-align: right; }
        .ticket-footer { text-align: center; margin-top: 15px; font-size: 11px; color: #666; border-top: 1px dashed #ccc; padding-top: 10px; }
    </style>
</head>
<body>
    <div class="ticket-container">
        <div class="ticket-header">
            <h2>SERVIPARTES</h2>
            <p>Ticket de Venta</p>
            <p>Fecha: <?= date('d/m/Y H:i', strtotime($venta['fecha'])) ?></p>
        </div>
        
        <div class="ticket-info">
            <p><strong>Folio:</strong> <?= htmlspecialchars($venta['folio']) ?></p>
            <p><strong>Cliente:</strong> <?= htmlspecialchars($venta['cliente_nombre']) ?></p>
            <p><strong>Dirección:</strong> <?= htmlspecialchars($venta['direccion_negocio']) ?></p>
            <p><strong>Teléfono:</strong> <?= htmlspecialchars($venta['numero_atencion_cliente']) ?></p>
        </div>
        
        <div class="ticket-items">
            <?php foreach ($productos as $producto): ?>
                <div class="ticket-item">
                    <span><?= htmlspecialchars($producto['Nombre del producto']) ?> x<?= $producto['cantidad'] ?></span>
                    <span>$<?= number_format($producto['Precio'] * $producto['cantidad'], 2) ?></span>
                </div>
            <?php endforeach; ?>
        </div>
        
        <div class="ticket-total">
            <p><strong>Total: $<?= number_format($venta['total'], 2) ?></strong></p>
        </div>
        
        <div class="ticket-info">
            <p><strong>Método de Pago:</strong> <?= htmlspecialchars($venta['metodo_pago']) ?></p>
            <?php if ($venta['garantia'] == 1): ?>
                <p><strong>Garantía:</strong> <?= $venta['tiempo_garantia_valor'] ?> <?= htmlspecialchars($venta['tiempo_garantia_unidad']) ?></p>
            <?php endif; ?>
            <p><strong>Atendido por:</strong> <?= htmlspecialchars($venta['nombre_usuario']) ?></p>
        </div>
        
        <div class="ticket-footer">
            <p>Gracias por su compra</p>
            <p>Válido como comprobante fiscal</p>
        </div>
    </div>
</body>
</html>
<?php
$html = ob_get_clean();

// Crear PDF
try {
    $mpdf = new Mpdf([
        'mode' => 'utf-8',
        'format' => [80, 297],
        'margin_left' => 5,
        'margin_right' => 5,
        'margin_top' => 10,
        'margin_bottom' => 10,
        'margin_header' => 0,
        'margin_footer' => 0
    ]);
    
    $mpdf->WriteHTML($html);
    $pdfContent = $mpdf->Output('', 'S'); // Salida como string
    
    // Configurar PHPMailer
    $mail = new PHPMailer(true);
    
    try {
        // Configuración del servidor SMTP para Gmail
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'servipartes4@gmail.com'; // Tu correo Gmail
        $mail->Password = 'nxgmqguesjqaoqne'; // Contraseña de aplicación
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        
        // Configuración de caracteres
        $mail->CharSet = 'UTF-8';
        
        // Remitente
        $mail->setFrom('servipartes4@gmail.com', 'Servipartes');
        $mail->addAddress($email, $cliente);
        
        // Asunto y cuerpo
        $mail->Subject = 'Ticket de compra - Servipartes (Folio: ' . $venta['folio'] . ')';
        $mail->Body = "Estimado $cliente,\n\nAdjuntamos el ticket de su compra reciente en Servipartes.\n\nFolio: " . $venta['folio'] . "\nFecha: " . date('d/m/Y H:i', strtotime($venta['fecha'])) . "\nTotal: $" . number_format($venta['total'], 2) . "\n\nGracias por su preferencia.";
        $mail->AltBody = "Estimado $cliente,\n\nAdjuntamos el ticket de su compra reciente en Servipartes.\n\nFolio: " . $venta['folio'] . "\nFecha: " . date('d/m/Y H:i', strtotime($venta['fecha'])) . "\nTotal: $" . number_format($venta['total'], 2) . "\n\nGracias por su preferencia.";
        
        // Adjuntar PDF
        $mail->addStringAttachment($pdfContent, 'Ticket_Servipartes_' . $venta['folio'] . '.pdf');
        
        $mail->send();
        echo json_encode(['success' => true, 'message' => 'Ticket enviado correctamente a ' . $email]);
    } catch (Exception $e) {
        echo json_encode(['error' => "Error al enviar correo: {$mail->ErrorInfo}"]);
    }
} catch (Exception $e) {
    echo json_encode(['error' => "Error al generar PDF: {$e->getMessage()}"]);
}
?>