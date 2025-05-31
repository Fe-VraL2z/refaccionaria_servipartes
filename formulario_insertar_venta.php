<?php
session_start();
date_default_timezone_set('America/Mexico_City');
// Check if user is logged in
if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit;
}
// Database connection
$conn = new mysqli("localhost", "root", "", "refaccionaria_servipartes");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize variables
$folio = substr(uniqid(''), 0, 12); // Generate 12-character folio
$fecha = date('Y-m-d H:i:s');
$usuario = $_SESSION['usuario'];
$cliente = isset($_SESSION['cliente']) ? $_SESSION['cliente'] : [];
$productos_carrito = isset($_SESSION['carrito']) ? $_SESSION['carrito'] : [];
$total = 0;

// Initialize form variables
$direccion_negocio = '';
$numero_atencion = '';
$metodo_pago = '';
$garantia = false;
$tiempo_garantia_valor = '';

// Handle client search
if (isset($_POST['buscar_cliente'])) {
    $busqueda = $conn->real_escape_string($_POST['busqueda_cliente']);
    if (!empty($busqueda)) {
        $sql = "SELECT * FROM cliente WHERE `ID del cliente` = '$busqueda' OR `Nombre` LIKE '%$busqueda%'";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            $cliente = $result->fetch_assoc();
            $_SESSION['cliente'] = $cliente; // Store client in session
        } else {
            echo "<div class='alert alert-warning'>Cliente no encontrado.</div>";
        }
    } else {
        echo "<div class='alert alert-warning'>Por favor ingrese un término de búsqueda para el cliente.</div>";
    }
}

// Handle clear client
if (isset($_POST['limpiar_cliente'])) {
    unset($_SESSION['cliente']);
    $cliente = [];
}

// Handle product quantity update
if (isset($_POST['actualizar_cantidad'])) {
    $index = (int)$_POST['producto_index'];
    $nueva_cantidad = (int)$_POST['nueva_cantidad'];
    
    if (isset($_SESSION['carrito'][$index]) && $nueva_cantidad > 0) {
        $producto_id = $_SESSION['carrito'][$index]['Id Producto'];
        
        // Verificar existencia disponible
        $sql_check = "SELECT `Cantidad de existencia` FROM producto WHERE `Id Producto` = $producto_id";
        $result = $conn->query($sql_check);
        if ($result->num_rows > 0) {
            $stock = $result->fetch_assoc();
            
            // Calcular cantidad total en carrito (excluyendo el item actual)
            $cantidad_otros = 0;
            foreach ($_SESSION['carrito'] as $i => $item) {
                if ($i != $index && $item['Id Producto'] == $producto_id) {
                    $cantidad_otros += $item['cantidad'];
                }
            }
            
            if (($cantidad_otros + $nueva_cantidad) <= $stock['Cantidad de existencia']) {
                $_SESSION['carrito'][$index]['cantidad'] = $nueva_cantidad;
                $_SESSION['carrito'][$index]['subtotal'] = $_SESSION['carrito'][$index]['precio_unitario'] * $nueva_cantidad;
                echo "<div class='alert alert-success'>Cantidad actualizada correctamente.</div>";
            } else {
                echo "<div class='alert alert-warning'>No hay suficiente existencia. Disponible: " . $stock['Cantidad de existencia'] . ", en carrito (otros): $cantidad_otros</div>";
            }
        }
    }
    $productos_carrito = $_SESSION['carrito'];
}

// Handle product search and add to cart
if (isset($_POST['buscar_producto'])) {
    $busqueda = $conn->real_escape_string($_POST['busqueda_producto']);
    if (!empty($busqueda)) {
        $sql = "SELECT * FROM producto WHERE (`Id Producto` = '$busqueda' OR `Nombre del producto` LIKE '%$busqueda%') AND `Cantidad de existencia` > 0";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            $producto = $result->fetch_assoc();

            // Verificar si el producto ya está en el carrito
            $producto_encontrado = false;
            $cantidad_total_en_carrito = 0;
            
            foreach ($productos_carrito as $index => $item) {
                if ($item['Id Producto'] == $producto['Id Producto']) {
                    $cantidad_total_en_carrito += $item['cantidad'];
                }
            }

            // Verificar que la cantidad total no exceda la existencia
            if (($cantidad_total_en_carrito + 1) <= $producto['Cantidad de existencia']) {
                // Buscar si ya existe en el carrito para sumar cantidad
                foreach ($productos_carrito as $index => $item) {
                    if ($item['Id Producto'] == $producto['Id Producto']) {
                        $productos_carrito[$index]['cantidad']++;
                        $productos_carrito[$index]['subtotal'] = $productos_carrito[$index]['cantidad'] * $productos_carrito[$index]['precio_unitario'];
                        $producto_encontrado = true;
                        echo "<div class='alert alert-info'>Se agregó una unidad más del producto existente en el carrito.</div>";
                        break;
                    }
                }
                
                // Si no existe en el carrito, agregarlo
                if (!$producto_encontrado) {
                    $productos_carrito[] = [
                        'Id Producto' => $producto['Id Producto'],
                        'producto' => $producto['Nombre del producto'],
                        'cantidad' => 1,
                        'precio_unitario' => $producto['Precio'],
                        'subtotal' => $producto['Precio']
                    ];
                    echo "<div class='alert alert-success'>Producto agregado al carrito.</div>";
                }
                
                $_SESSION['carrito'] = $productos_carrito;
            } else {
                echo "<div class='alert alert-warning'>No hay suficiente existencia para agregar más unidades de este producto. Existencia disponible: " . $producto['Cantidad de existencia'] . ", cantidad en carrito: $cantidad_total_en_carrito</div>";
            }
        } else {
            // Verificar si existe pero no tiene existencia
            $sql_check = "SELECT * FROM producto WHERE `Id Producto` = '$busqueda' OR `Nombre del producto` LIKE '%$busqueda%'";
            $check_result = $conn->query($sql_check);
            if ($check_result->num_rows > 0) {
                echo "<div class='alert alert-warning'>El producto existe pero no tiene suficiente existencia en inventario.</div>";
            } else {
                echo "<div class='alert alert-warning'>Producto no encontrado.</div>";
            }
        }
    } else {
        echo "<div class='alert alert-warning'>Por favor ingrese un término de búsqueda para el producto.</div>";
    }
}

// Handle remove single product from cart
if (isset($_GET['eliminar_producto'])) {
    $index = (int)$_GET['eliminar_producto'];
    if (isset($_SESSION['carrito'][$index])) {
        unset($_SESSION['carrito'][$index]);
        $_SESSION['carrito'] = array_values($_SESSION['carrito']); // Reindex array
        header("Location: " . strtok($_SERVER['REQUEST_URI'], '?'));
        exit;
    }
}

// Handle clear cart
if (isset($_POST['vaciar_carrito'])) {
    unset($_SESSION['carrito']);
    $productos_carrito = [];
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Calculate total
foreach ($productos_carrito as $item) {
    $total += $item['subtotal'];
}

// Handle sale submission
if (isset($_POST['guardar_venta'])) {
    $errors = [];
    // Validar existencia de productos antes de procesar
    foreach ($productos_carrito as $item) {
        $sql_check = "SELECT `Cantidad de existencia` FROM producto WHERE `Id Producto` = " . $item['Id Producto'];
        $result = $conn->query($sql_check);
        if ($result->num_rows > 0) {
            $stock = $result->fetch_assoc();
            if ($item['cantidad'] > $stock['Cantidad de existencia']) {
                $errors[] = "No hay suficiente existencia del producto '" . $item['producto'] . "'. Existencia disponible: " . $stock['Cantidad de existencia'] . ", cantidad solicitada: " . $item['cantidad'];
            }
        } else {
            $errors[] = "El producto '" . $item['producto'] . "' ya no existe en el inventario";
        }
    }

    // Validate required fields
    if (empty($_SESSION['cliente'])) {
        $errors[] = "Por favor, seleccione un cliente antes de guardar la venta.";
    }

    if (empty($_POST['direccion_negocio'])) {
        $errors[] = "La dirección del negocio es requerida.";
    }

    if (empty($_POST['numero_atencion'])) {
        $errors[] = "El número de atención a cliente es requerido.";
    }

    if (empty($productos_carrito)) {
        $errors[] = "Debe agregar al menos un producto a la venta.";
    }

    if (empty($_POST['metodo_pago'])) {
        $errors[] = "El método de pago es requerido.";
    }

    if (isset($_POST['garantia']) && $_POST['garantia'] && empty($_POST['tiempo_garantia_valor'])) {
        $errors[] = "Debe especificar el tiempo de garantía cuando se selecciona garantía.";
    }

    if (empty($errors)) {
        $cliente_id = $conn->real_escape_string($_SESSION['cliente']['ID del cliente']);
        $direccion_negocio_db = $conn->real_escape_string($_POST['direccion_negocio']);
        $numero_atencion_db = $conn->real_escape_string($_POST['numero_atencion']);
        $garantia_db = isset($_POST['garantia']) ? 1 : 0;
        $tiempo_garantia_valor_db = $garantia_db ? (int)$_POST['tiempo_garantia_valor'] : 0;
        $tiempo_garantia_unidad = $garantia_db ? 'Meses' : '';
        $metodo_pago_db = $conn->real_escape_string($_POST['metodo_pago']);

        // Insert into ventas
        $sql_venta = "INSERT INTO ventas (folio, cliente_id, direccion_negocio, numero_atencion_cliente, garantia, tiempo_garantia_valor, tiempo_garantia_unidad, metodo_pago, nombre_usuario, fecha, total) 
                      VALUES ('$folio', '$cliente_id', '$direccion_negocio_db', '$numero_atencion_db', $garantia_db, $tiempo_garantia_valor_db, '$tiempo_garantia_unidad', '$metodo_pago_db', '$usuario', '$fecha', $total)";
        if ($conn->query($sql_venta)) {
            $venta_id = $conn->insert_id;
            // Insert products into ventas_productos
            foreach ($productos_carrito as $item) {
                $producto_id = $item['Id Producto'];
                $producto_nombre = $conn->real_escape_string($item['producto']);
                $cantidad = $item['cantidad'];
                $precio_unitario = $item['precio_unitario'];
                $subtotal = $item['subtotal'];
                $sql_producto = "INSERT INTO ventas_productos (id_ventas, `Id Producto`, producto, cantidad, precio_unitario, subtotal) 
                                VALUES ($venta_id, $producto_id, '$producto_nombre', $cantidad, $precio_unitario, $subtotal)";
                $conn->query($sql_producto);
                // Update product stock
                $sql_update_stock = "UPDATE producto SET `Cantidad de existencia` = `Cantidad de existencia` - $cantidad WHERE `Id Producto` = $producto_id";
                $conn->query($sql_update_stock);
            }
            // Clear cart, client, and form data
            unset($_SESSION['carrito']);
            unset($_SESSION['cliente']);
            echo "<div class='alert alert-success'>
                      <strong>¡Venta registrada con éxito!</strong><br>
                      <strong>Folio:</strong> $folio<br>
                      <strong>ID de Venta:</strong> $venta_id<br>
                      <button type='button' class='btn btn-sm btn-primary mt-2' onclick='abrirTicket($venta_id)'>
                          <i class='bi bi-printer'></i> Reimprimir Ticket
                      </button>
                      <script>
                          // Abrir automáticamente el ticket en nueva pestaña
                          setTimeout(function() {
window.open('imprimir_ticket.php?id_venta=$venta_id&auto_print=1', '_blank', 'width=800,height=600,scrollbars=yes');
                          }, 500);
                      </script>
                  </div>";

            // Reset ALL variables after successful sale
            $folio = substr(uniqid(''), 0, 12);
            $fecha = date('Y-m-d H:i:s');
            $cliente = [];
            $productos_carrito = [];
            $total = 0;
            // Clear form variables
            $direccion_negocio = '';
            $numero_atencion = '';
            $metodo_pago = '';
            $garantia = false;
            $tiempo_garantia_valor = '';
        } else {
            echo "<div class='alert alert-danger'>Error al registrar la venta: " . $conn->error . "</div>";
        }
    } else {
        // If there are errors, preserve form values
        $direccion_negocio = isset($_POST['direccion_negocio']) ? $_POST['direccion_negocio'] : '';
        $numero_atencion = isset($_POST['numero_atencion']) ? $_POST['numero_atencion'] : '';
        $metodo_pago = isset($_POST['metodo_pago']) ? $_POST['metodo_pago'] : '';
        $garantia = isset($_POST['garantia']) && $_POST['garantia'];
        $tiempo_garantia_valor = isset($_POST['tiempo_garantia_valor']) ? $_POST['tiempo_garantia_valor'] : '';

        foreach ($errors as $error) {
            echo "<div class='alert alert-danger'>$error</div>";
        }
    }
} else {
    // If not processing sale, preserve form values if they exist
    $direccion_negocio = isset($_POST['direccion_negocio']) ? $_POST['direccion_negocio'] : '';
    $numero_atencion = isset($_POST['numero_atencion']) ? $_POST['numero_atencion'] : '';
    $metodo_pago = isset($_POST['metodo_pago']) ? $_POST['metodo_pago'] : '';
    $garantia = isset($_POST['garantia']) && $_POST['garantia'];
    $tiempo_garantia_valor = isset($_POST['tiempo_garantia_valor']) ? $_POST['tiempo_garantia_valor'] : '';
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Módulo de Ventas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="style.css" >
</head>
<body>
    <div class="container mt-5">
        <!-- Botón Volver -->
        <a href="dashboard.php" class="btn-volver">Volver</a>

        <div class="main-header">
            <h2>Módulo de Ventas</h2>
        </div>

        <!-- Información General -->
        <div class="card mb-4">
            <div class="card-header">
                <h4>Información General</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <label>Folio</label>
                        <input type="text" class="form-control" value="<?php echo $folio; ?>" readonly>
                    </div>
                    <div class="col-md-4">
                        <label>Fecha</label>
                        <input type="text" class="form-control" value="<?php echo $fecha; ?>" readonly>
                    </div>
                    <div class="col-md-4">
                        <label>Atendido por</label>
                        <input type="text" class="form-control" value="<?php echo htmlspecialchars($usuario); ?>" readonly>
                    </div>
                </div>
            </div>
        </div>

        <!-- Búsqueda de Cliente -->
        <div class="card mb-4">
            <div class="card-header">
                <h4>Búsqueda de Cliente</h4>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label>Buscar Cliente (ID o Nombre)</label>
                        <div class="input-group">
                            <input type="text" name="busqueda_cliente" class="form-control" <?php echo !empty($cliente) ? 'disabled' : ''; ?>>
                            <button type="submit" name="buscar_cliente" class="btn btn-primary" <?php echo !empty($cliente) ? 'disabled' : ''; ?>>Buscar</button>
                            <?php if (!empty($cliente)): ?>
                                <button type="submit" name="limpiar_cliente" class="btn btn-secondary">Cambiar Cliente</button>
                            <?php endif; ?>
                        </div>
                    </div>
                </form>

                <?php if (!empty($cliente)): ?>
                    <div class="alert alert-success">
                        <strong>Cliente seleccionado:</strong><br>
                        <strong>ID:</strong> <?php echo htmlspecialchars($cliente['ID del cliente']); ?><br>
                        <strong>Nombre:</strong> <?php echo htmlspecialchars($cliente['Nombre']); ?><br>
                        <strong>Teléfono:</strong> <?php echo htmlspecialchars($cliente['Telefono']); ?><br>
                        <strong>Dirección:</strong> <?php echo htmlspecialchars($cliente['Direccion']); ?><br>
                        <strong>RFC:</strong> <?php echo htmlspecialchars($cliente['RFC']); ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Búsqueda de Productos -->
        <div class="card mb-4">
            <div class="card-header">
                <h4>Búsqueda de Productos</h4>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label>Buscar Producto (ID o Nombre)</label>
                        <div class="input-group">
                            <input type="text" name="busqueda_producto" class="form-control">
                            <button type="submit" name="buscar_producto" class="btn btn-primary">Buscar y Añadir al Carrito</button>
                        </div>
                    </div>
                </form>

                <?php if (!empty($productos_carrito)): ?>
                    <form method="POST" class="mb-3">
                        <button type="submit" name="vaciar_carrito" class="btn btn-danger" onclick="return confirm('¿Está seguro de vaciar el carrito?')">Vaciar Carrito</button>
                    </form>

                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>ID Producto</th>
                                <th>Nombre</th>
                                <th>Cantidad</th>
                                <th>Precio Unitario</th>
                                <th>Subtotal</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($productos_carrito as $index => $item): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($item['Id Producto']); ?></td>
                                    <td><?php echo htmlspecialchars($item['producto']); ?></td>
                                    <td>
                                        <form method="POST" class="d-inline">
                                            <div class="input-group" style="width: 120px;">
                                                <input type="number" name="nueva_cantidad" class="form-control form-control-sm" 
                                                       value="<?php echo $item['cantidad']; ?>" min="1" max="999">
                                                <input type="hidden" name="producto_index" value="<?php echo $index; ?>">
                                                <button type="submit" name="actualizar_cantidad" class="btn btn-sm btn-outline-primary" 
                                                        title="Actualizar cantidad">
                                                    <i class="bi bi-check"></i>
                                                </button>
                                            </div>
                                        </form>
                                    </td>
                                    <td>$<?php echo number_format($item['precio_unitario'], 2); ?></td>
                                    <td>$<?php echo number_format($item['subtotal'], 2); ?></td>
                                    <td>
                                        <a href="?eliminar_producto=<?php echo $index; ?>" class="btn btn-sm btn-danger" 
                                           onclick="return confirm('¿Está seguro de eliminar este producto?')" title="Eliminar producto">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <tr class="table-info">
                                <td colspan="4" class="text-end"><strong>Total General</strong></td>
                                <td><strong>$<?php echo number_format($total, 2); ?></strong></td>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="alert alert-info">No hay productos en el carrito.</div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Formulario de Venta -->
        <div class="card mb-4">
            <div class="card-header">
                <h4>Procesar Venta</h4>
            </div>
            <div class="card-body">
                <form method="POST">
                    <!-- Datos adicionales del negocio -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label>Dirección del Negocio <span class="text-danger">*</span></label>
                            <input type="text" name="direccion_negocio" class="form-control" value="<?php echo htmlspecialchars($direccion_negocio); ?>">
                        </div>
                        <div class="col-md-6">
                            <label>Número de Atención a Cliente <span class="text-danger">*</span></label>
                            <input type="text" name="numero_atencion" class="form-control" value="<?php echo htmlspecialchars($numero_atencion); ?>">
                        </div>
                    </div>

                    <!-- Método de Pago -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label>Método de Pago <span class="text-danger">*</span></label>
                            <select name="metodo_pago" class="form-control">
                                <option value="">Seleccione un método</option>
                                <option value="Efectivo" <?php echo ($metodo_pago == 'Efectivo') ? 'selected' : ''; ?>>Efectivo</option>
                                <option value="Tarjeta" <?php echo ($metodo_pago == 'Tarjeta') ? 'selected' : ''; ?>>Tarjeta</option>
                                <option value="Transferencia" <?php echo ($metodo_pago == 'Transferencia') ? 'selected' : ''; ?>>Transferencia</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check mt-4">
                                <input type="checkbox" name="garantia" id="garantia" class="form-check-input" <?php echo $garantia ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="garantia">¿Incluye Garantía?</label>
                            </div>
                        </div>
                    </div>

                    <!-- Garantía -->
                    <div class="mb-3" id="garantia_detalle" style="display: <?php echo $garantia ? 'block' : 'none'; ?>;">
                        <label>Tiempo de Garantía (Meses) <span class="text-danger">*</span></label>
                        <input type="number" name="tiempo_garantia_valor" class="form-control" min="1" max="60" value="<?php echo htmlspecialchars($tiempo_garantia_valor); ?>">
                    </div>

                    <!-- Resumen de venta -->
                    <div class="alert alert-light">
                        <h5>Resumen de la Venta:</h5>
                        <p><strong>Cliente:</strong> <?php echo !empty($cliente) ? htmlspecialchars($cliente['Nombre']) : '<span class="text-danger">No seleccionado</span>'; ?></p>
                        <p><strong>Productos:</strong> <?php echo count($productos_carrito); ?> artículo(s)</p>
                        <p><strong>Total:</strong> $<?php echo number_format($total, 2); ?></p>
                    </div>

                    <div class="d-grid">
                        <button type="submit" name="guardar_venta" class="btn btn-success btn-lg">
                            <i class="bi bi-check-circle"></i> Procesar Venta
                        </button>
                    </div>

                    <div class="mt-2">
                        <small class="text-muted">
                            <span class="text-danger">*</span> Campos obligatorios.
                            Asegúrese de tener un cliente seleccionado y productos en el carrito antes de procesar la venta.
                        </small>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Show/hide warranty details
        $('#garantia').change(function() {
            if ($(this).is(':checked')) {
                $('#garantia_detalle').show();
            } else {
                $('#garantia_detalle').hide();
            }
        });

        // Auto-focus on search inputs
        $(document).ready(function() {
            $('input[name="busqueda_cliente"]').focus();
        });

        // Function to open ticket printing
        function abrirTicket(idVenta) {
            window.open('imprimir_ticket.php?id_venta=' + idVenta, '_blank', 'width=800,height=600,scrollbars=yes');
        }

        // Allow Enter key to submit quantity update
        $('input[name="nueva_cantidad"]').keypress(function(e) {
            if (e.which == 13) {
                $(this).closest('form').submit();
            }
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>