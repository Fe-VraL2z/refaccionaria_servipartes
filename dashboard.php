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

// Obtener datos de las tablas
$tablas = ['cliente', 'producto', 'proveedor', 'ventas', 'usuarios'];
$datos_tablas = [];

foreach ($tablas as $tabla) {
    $result = $conn->query("SELECT * FROM $tabla");
    $datos_tablas[$tabla] = $result->fetch_all(MYSQLI_ASSOC);
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Refaccionaria Servipartes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            margin: 0;
            padding: 0;
            height: 100vh;
            background: url('images/paginaprinsipalservipartes.png') no-repeat center center fixed;
            background-size: cover;
            position: relative;
        }

        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(to bottom, rgba(255, 255, 255, 0.5) 0%, rgba(255, 255, 255, 0) 50%, rgba(255, 255, 255, 1) 100%);
            z-index: -1;
        }

        .navbar {
            background-color: rgba(52, 58, 64, 0.8);
            z-index: 1030; /* Bootstrap navbar default z-index */
        }

        .navbar-brand {
            font-weight: bold;
            color: #fff !important;
        }

        .dropdown-menu {
            background-color: rgba(52, 58, 64, 0.9);
        }

        .dropdown-item {
            color: #fff !important;
        }

        .dropdown-item:hover {
            background-color: rgba(73, 80, 87, 0.8);
        }

        .container {
            background-color: rgba(255, 255, 255, 0.8);
            padding: 20px;
            border-radius: 8px;
            margin-top: 20px;
            position: relative;
            z-index: 1;
        }

        .menu-acciones {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .menu-acciones a {
            margin-right: 10px;
        }

        .table-container {
            margin-bottom: 40px;
        }

        .table-container h2 {
            margin-bottom: 20px;
        }

        .imagen-principal {
            max-width: 100%;
            height: auto;
            margin-top: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        @keyframes bounce {
            0%, 100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-5px);
            }
        }

        .icono-animado {
            animation: bounce 1s infinite;
        }

        /* Estilos para el mensaje de alerta */
        .alert-fixed {
            position: fixed;
            top: 80px; /* Debajo del navbar que tiene aproximadamente 56px de altura + margen */
            right: 20px;
            z-index: 1050; /* Mayor que el navbar (1030) */
            max-width: 400px;
            min-width: 300px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            border: none;
            animation: slideInRight 0.5s ease-out;
        }

        /* Animación para la entrada del mensaje */
        @keyframes slideInRight {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        /* Estilos específicos para cada tipo de alerta */
        .alert-success.alert-fixed {
            background-color: #d1eddd;
            border-left: 4px solid #28a745;
            color: #155724;
        }

        .alert-error.alert-fixed,
        .alert-danger.alert-fixed {
            background-color: #f8d7da;
            border-left: 4px solid #dc3545;
            color: #721c24;
        }

        .alert-warning.alert-fixed {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            color: #856404;
        }

        .alert-info.alert-fixed {
            background-color: #d1ecf1;
            border-left: 4px solid #17a2b8;
            color: #0c5460;
        }

        /* Responsive para pantallas pequeñas */
        @media (max-width: 768px) {
            .alert-fixed {
                top: 70px;
                right: 10px;
                left: 10px;
                max-width: none;
                min-width: auto;
            }
        }
    </style>
</head>
<body>
    <!-- Menú superior -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <img src="images/logo_servipartes.jpg" alt="Logo Servipartes" width="30" height="30" class="d-inline-block align-text-top">
                SERVIPARTES
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <!-- Menú Cliente -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownCliente" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="images/icono_cliente.png" alt="Cliente" width="20" height="20" class="icono-animado"> Cliente
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdownCliente">
                            <li><a class="dropdown-item" href="acciones.php?accion=agregar&tabla=cliente"><img src="images/icono_agregar.png" alt="Agregar" width="20" height="20"> Agregar</a></li>
                            <li><a class="dropdown-item" href="consultar_cliente.php"><img src="images/icono_consultar.png" alt="Consultar" width="20" height="20"> Consultar</a></li>
                            <li><a class="dropdown-item" href="buscar_cliente_editar.php"><img src="images/icono_editar.png" alt="Editar" width="20" height="20"> Editar</a></li>
                            <li><a class="dropdown-item" href="buscar_cliente_eliminar.php"><img src="images/icono_quitar.png" alt="Quitar" width="20" height="20"> Eliminar</a></li>
                        </ul>
                    </li>

                    <!-- Menú Producto -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownProducto" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="images/icono_producto.png" alt="Producto" width="20" height="20" class="icono-animado"> Producto
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdownProducto">
                            <li><a class="dropdown-item" href="formulario_insertar_producto.php"><img src="images/icono_agregar.png" alt="Agregar" width="20" height="20"> Agregar</a></li>
                            <li><a class="dropdown-item" href="consultar_producto.php"><img src="images/icono_consultar.png" alt="Consultar" width="20" height="20"> Consultar</a></li>
                            <li><a class="dropdown-item" href="buscar_producto_editar.php"><img src="images/icono_editar.png" alt="Editar" width="20" height="20"> Editar</a></li>
                            <li><a class="dropdown-item" href="buscar_producto_eliminar.php"><img src="images/icono_quitar.png" alt="Quitar" width="20" height="20"> Eliminar</a></li>
                        </ul>
                    </li>

                    <!-- Menú Proveedor -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownProveedor" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="images/icono_proveedor.png" alt="Proveedor" width="20" height="20" class="icono-animado"> Proveedor
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdownProveedor">
                            <li><a class="dropdown-item" href="formulario_insertar_proveedor.php"><img src="images/icono_agregar.png" alt="Agregar" width="20" height="20"> Agregar</a></li>
                            <li><a class="dropdown-item" href="consultar_proveedor.php"><img src="images/icono_consultar.png" alt="Consultar" width="20" height="20"> Consultar</a></li>
                            <li><a class="dropdown-item" href="buscar_proveedor_editar.php"><img src="images/icono_editar.png" alt="Editar" width="20" height="20"> Editar</a></li>
                            <li><a class="dropdown-item" href="buscar_proveedor_eliminar.php"><img src="images/icono_quitar.png" alt="Quitar" width="20" height="20"> Eliminar</a></li>
                        </ul>
                    </li>

                    <!-- Menú Ventas -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownVentas" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="images/icono_ventas.png" alt="Ventas" width="20" height="20" class="icono-animado"> Ventas
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdownVentas">
                            <li><a class="dropdown-item" href="formulario_insertar_venta.php"><img src="images/icono_agregar.png" alt="Agregar" width="20" height="20"> Agregar</a></li>
                            <li><a class="dropdown-item" href="consultar_ventas.php"><img src="images/icono_consultar.png" alt="Consultar" width="20" height="20"> Consultar</a></li>
                            <li><a class="dropdown-item" href="corte_caja.php"><img src="images/icono_reporte.png" alt="Corte de caja" width="20" height="20"> Corte de caja</a></li>
                            <li><a class="dropdown-item" href="editar_venta.php"><img src="images/icono_editar.png" alt="Editar venta" width="20" height="20"> Editar venta</a></li>
                        </ul>
                    </li>
                    
                    <!-- Nuevo Menú Usuarios -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownUsuarios" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="images/icono_usuario.png" alt="Usuarios" width="20" height="20" class="icono-animado"> Usuarios
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdownUsuarios">
                            <li><a class="dropdown-item" href="formulario_insertar_usuario.php"><img src="images/icono_agregar.png" alt="Agregar" width="20" height="20"> Agregar</a></li>
                            <li><a class="dropdown-item" href="consultar_usuario.php"><img src="images/icono_consultar.png" alt="Consultar" width="20" height="20"> Consultar</a></li>
                            <li><a class="dropdown-item" href="buscar_usuario_editar.php"><img src="images/icono_editar.png" alt="Editar" width="20" height="20"> Editar</a></li>
                            <li><a class="dropdown-item" href="buscar_usuario_eliminar.php"><img src="images/icono_quitar.png" alt="Quitar" width="20" height="20"> Eliminar</a></li>
                        </ul>
                    </li>
                </ul>
                <a href="logout.php" class="btn btn-danger">
                    <img src="images/icono_cerrar_sesion.png" alt="Cerrar sesión" width="20" height="20"> Cerrar sesión
                </a>
            </div>
        </div>
    </nav>

    <!-- Mostrar mensajes de éxito/error -->
    <?php if (isset($_SESSION['mensaje'])) {
        $tipo = $_SESSION['tipo_mensaje'] ?? 'info';
        // Convertir 'error' a 'danger' para Bootstrap
        if ($tipo == 'error') $tipo = 'danger';
        
        echo "<div class='alert alert-$tipo alert-dismissible fade show alert-fixed' role='alert'>
                <strong>";
        
        // Agregar iconos según el tipo
        switch($tipo) {
            case 'success':
                echo "<i class='fas fa-check-circle'></i> ¡Éxito! ";
                break;
            case 'danger':
                echo "<i class='fas fa-exclamation-triangle'></i> ¡Error! ";
                break;
            case 'warning':
                echo "<i class='fas fa-exclamation-circle'></i> ¡Atención! ";
                break;
            case 'info':
                echo "<i class='fas fa-info-circle'></i> Información: ";
                break;
        }
        
        echo "</strong><br>" . nl2br(htmlspecialchars($_SESSION['mensaje'])) . "
                <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
              </div>";
        unset($_SESSION['mensaje']);
        unset($_SESSION['tipo_mensaje']);
    } ?>

    <!-- Contenido principal -->
    <div class="container mt-5">
        <h1>Bienvenida, <?php echo $_SESSION['usuario']; ?></h1>
        <p>Selecciona una opción del menú para comenzar.</p>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Script para auto-ocultar las alertas después de 5 segundos -->
    <script>
        // Auto-hide alerts after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.alert-fixed');
            alerts.forEach(function(alert) {
                setTimeout(function() {
                    if (alert && alert.classList.contains('show')) {
                        const bsAlert = new bootstrap.Alert(alert);
                        bsAlert.close();
                    }
                }, 5000); // 5 segundos
            });
        });
    </script>
</body>
</html>