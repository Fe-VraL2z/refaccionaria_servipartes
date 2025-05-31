<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: index.php");
    exit;
}

$usuario_actual = $_SESSION['usuario']; // Nombre del usuario logueado
$pagina_anterior = $_SERVER['HTTP_REFERER'] ?? 'dashboard.php'; // Página para regresar
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Corte de Caja - SERVIPARTES</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .container {
            max-width: 600px;
            margin-top: 50px;
        }
        .card {
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }
        .card-header {
            background-color: #2c3e50;
            color: white;
            border-radius: 10px 10px 0 0 !important;
        }
        .btn-primary {
            background-color: #3498db;
            border-color: #3498db;
        }
        .usuario-info, .fecha-info {
            background-color: #e9ecef;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h3 class="text-center">Corte de Caja</h3>
            </div>
            <div class="card-body">
                <form action="generar_corte.php" method="post">
                    <div class="usuario-info">
                        <label class="form-label"><strong>Usuario:</strong></label>
                        <p><?= htmlspecialchars($usuario_actual) ?></p>
                        <input type="hidden" name="usuario" value="<?= htmlspecialchars($usuario_actual) ?>">
                    </div>
                    
                    <div class="fecha-info">
                        <label for="fecha_corte" class="form-label"><strong>Fecha del Corte:</strong></label>
                        <input type="date" class="form-control" id="fecha_corte" name="fecha_corte" 
                               value="<?= date('Y-m-d') ?>" required>
                    </div>
                    
                    <div class="d-flex justify-content-between mt-4">
                       <a href="dashboard.php" class="btn btn-secondary">Regresar</a>
                        <button type="submit" class="btn btn-primary">Generar Reporte</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Opcional: Validación de fecha futura
        document.getElementById('fecha_corte').addEventListener('change', function() {
            const fechaSeleccionada = new Date(this.value);
            const fechaActual = new Date();
            fechaActual.setHours(0, 0, 0, 0);
            
            if (fechaSeleccionada > fechaActual) {
                alert('No puede seleccionar una fecha futura');
                this.value = fechaActual.toISOString().split('T')[0];
            }
        });
    </script>
</body>
</html>