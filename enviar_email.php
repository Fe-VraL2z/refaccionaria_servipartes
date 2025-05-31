<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $folio = $_POST['folio'];
    
    // Aquí iría el código para enviar el email con el ticket
    // Este es un ejemplo básico, en producción usarías una librería como PHPMailer
    
    // Simulamos el envío del email
    $asunto = "Ticket de compra - Refaccionaria Servipartes - Folio: $folio";
    $mensaje = "Gracias por su compra. Adjunto encontrará el ticket de su compra con folio: $folio";
    
    // En un entorno real, aquí generarías el PDF o HTML del ticket y lo adjuntarías
    
    // Para este ejemplo, simplemente redireccionamos con un mensaje
    $_SESSION['mensaje'] = "Se ha enviado el ticket al correo: $email";
    $_SESSION['tipo_mensaje'] = "success";
    header("Location: imprimir_ticket.php");
    exit;
}

header("Location: dashboard.php");
exit;
?>