<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Venta - SERVIPARTES</title>
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
        .search-box {
            background-color: #e9ecef;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h3 class="text-center">Editar Venta</h3>
            </div>
            <div class="card-body">
                <div class="search-box">
                    <h4>Buscar Venta a Editar</h4>
                    <form action="buscar_venta.php" method="post">
                        <div class="mb-3">
                            <label for="folio" class="form-label">Folio de Venta</label>
                            <input type="text" class="form-control" id="folio" name="folio" 
                                   placeholder="Ej: MINE202505129746" required>
                        </div>
                        <div class="d-flex justify-content-between">
                            <a href="dashboard.php" class="btn btn-secondary">Cancelar</a>
                            <button type="submit" class="btn btn-primary">Buscar Venta</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>