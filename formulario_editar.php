<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || !isset($_SESSION['venta_editar'])) {
    header("Location: index.php");
    exit;
}

$venta = $_SESSION['venta_editar'];
$fecha_formateada = date('d/m/Y H:i', strtotime($venta['fecha']));
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editando Venta - SERVIPARTES</title>
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
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .card-header {
            background-color: #2c3e50;
            color: white;
            border-radius: 10px 10px 0 0 !important;
        }

        .info-box {
            background-color: #e9ecef;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .producto-item {
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 15px;
        }

        .btn-remove {
            color: #dc3545;
            cursor: pointer;
        }

        .total-box {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-top: 20px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h3 class="text-center">Editando Venta</h3>
            </div>
            <div class="card-body">
                <form action="actualizar_venta.php" method="post" id="form-editar-venta">
                    <input type="hidden" name="id_venta" value="<?= $venta['id_venta'] ?>">

                    <!-- Información General -->
                    <div class="info-box">
                        <h4>Información General</h4>
                        <div class="row">
                            <div class="col-md-4">
                                <p><strong>Folio:</strong> <?= htmlspecialchars($venta['folio']) ?></p>
                            </div>
                            <div class="col-md-4">
                                <p><strong>Fecha:</strong> <?= $fecha_formateada ?></p>
                            </div>
                            <div class="col-md-4">
                                <p><strong>Atendido por:</strong> <?= htmlspecialchars($_SESSION['usuario']) ?></p>
                            </div>
                        </div>
                    </div>

                    <!-- Datos del Cliente -->
                    <div class="info-box">
                        <h4>Datos del Cliente</h4>
                        <div class="row">
                            <div class="col-md-4">
                                <label class="form-label">Nombre</label>
                                <input type="text" class="form-control" value="<?= htmlspecialchars($venta['cliente']['nombre']) ?>" readonly>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Teléfono</label>
                                <input type="text" class="form-control" value="<?= htmlspecialchars($venta['cliente']['telefono']) ?>" readonly>
                            </div>
                        </div>
                    </div>

                    <!-- Productos -->
                    <div class="info-box">
                        <h4>Productos</h4>
                        <div id="productos-container">
                            <?php foreach ($venta['productos'] as $index => $producto):
                                // Asegurarnos de que todas las claves necesarias existan
                                $nombreProducto = $producto['producto'] ?? $producto['Nombre del producto'] ?? 'Producto desconocido';
                                $idProducto = $producto['Id Producto'] ?? $producto['id'] ?? '';
                                $precioUnitario = $producto['precio_unitario'] ?? $producto['Precio'] ?? 0;
                                $existenciaActual = $producto['existencia_actual'] ?? $producto['Cantidad de existencia'] ?? 0;
                                $cantidad = $producto['cantidad'] ?? 1;
                            ?>
                                <div class="producto-item" data-producto-index="<?= $index ?>">
                                    <div class="row">
                                        <div class="col-md-5">
                                            <label class="form-label">Producto</label>
                                            <input type="text" class="form-control"
                                                value="<?= htmlspecialchars($nombreProducto) ?>" readonly>
                                            <input type="hidden" name="productos[<?= $index ?>][id]" value="<?= $idProducto ?>">
                                            <input type="hidden" name="productos[<?= $index ?>][producto]" value="<?= htmlspecialchars($nombreProducto) ?>">
                                            <input type="hidden" name="productos[<?= $index ?>][precio_unitario]" value="<?= $precioUnitario ?>">
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label">Precio unitario</label>
                                            <input type="text" class="form-control precio-unitario"
                                                value="$<?= number_format($precioUnitario, 2) ?>" readonly>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label">Existencia</label>
                                            <input type="text" class="form-control existencia"
                                                value="<?= $existenciaActual ?>" readonly>
                                            <input type="hidden" class="existencia-max"
                                                value="<?= $existenciaActual + $cantidad ?>">
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label">Cantidad</label>
                                            <input type="number" class="form-control cantidad"
                                                name="productos[<?= $index ?>][cantidad]"
                                                min="1" max="<?= $existenciaActual + $cantidad ?>"
                                                value="<?= $cantidad ?>" required>
                                        </div>
                                        <div class="col-md-1 d-flex align-items-end">
                                            <button type="button" class="btn btn-danger btn-sm btn-remove">
                                                <i class="fas fa-times"></i> X
                                            </button>
                                        </div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-md-3 offset-md-9">
                                            <label class="form-label">Subtotal</label>
                                            <input type="text" class="form-control subtotal"
                                                value="$<?= number_format($precioUnitario * $cantidad, 2) ?>" readonly>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Método de Pago y Total -->
                    <div class="total-box">
                        <div class="row">
                            <div class="col-md-4">
                                <label class="form-label">Método de Pago</label>
                                <select class="form-select" name="metodo_pago" required>
                                    <option value="Efectivo" <?= $venta['metodo_pago'] == 'Efectivo' ? 'selected' : '' ?>>Efectivo</option>
                                    <option value="Tarjeta" <?= $venta['metodo_pago'] == 'Tarjeta' ? 'selected' : '' ?>>Tarjeta</option>
                                    <option value="Transferencia" <?= $venta['metodo_pago'] == 'Transferencia' ? 'selected' : '' ?>>Transferencia</option>
                                </select>
                            </div>
                            <div class="col-md-4 offset-md-4">
                                <label class="form-label">Total</label>
                                <input type="text" class="form-control" id="total-venta"
                                    value="$<?= number_format($venta['total'], 2) ?>" readonly>
                                <input type="hidden" name="total" id="total-hidden" value="<?= $venta['total'] ?>">
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <a href="editar_venta.php" class="btn btn-secondary">Cancelar</a>
                        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Calcular totales al cambiar cantidad
            $(document).on('input', '.cantidad', function() {
                const parent = $(this).closest('.producto-item');
                const precioStr = parent.find('.precio-unitario').val().replace('$', '').replace(',', '');
                const precio = parseFloat(precioStr);
                const cantidad = parseInt($(this).val());
                const max = parseInt($(this).attr('max'));

                // Validar cantidad máxima
                if (cantidad > max) {
                    $(this).val(max);
                    alert('No hay suficiente existencia para esta cantidad');
                    return;
                }

                // Validar cantidad mínima
                if (cantidad < 1 || isNaN(cantidad)) {
                    $(this).val(1);
                    return;
                }

                // Calcular subtotal
                const subtotal = precio * cantidad;
                parent.find('.subtotal').val('$' + subtotal.toLocaleString('es-MX', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
                
                // Recalcular total
                calcularTotal();
            });

            // Eliminar producto
            $(document).on('click', '.btn-remove', function() {
                if ($('.producto-item').length <= 1) {
                    alert('No se puede eliminar el último producto de la venta');
                    return;
                }
                
                if (confirm('¿Está seguro de eliminar este producto de la venta?')) {
                    $(this).closest('.producto-item').remove();
                    reindexarProductos();
                    calcularTotal();
                }
            });

            // Función para reindexar productos después de eliminar
            function reindexarProductos() {
                $('.producto-item').each(function(index) {
                    $(this).attr('data-producto-index', index);
                    $(this).find('input[name*="productos["]').each(function() {
                        const name = $(this).attr('name');
                        const newName = name.replace(/productos\[\d+\]/, 'productos[' + index + ']');
                        $(this).attr('name', newName);
                    });
                });
            }

            // Función para calcular el total
            function calcularTotal() {
                let total = 0;
                $('.subtotal').each(function() {
                    const subtotalStr = $(this).val().replace('$', '').replace(',', '');
                    total += parseFloat(subtotalStr) || 0;
                });
                
                $('#total-venta').val('$' + total.toLocaleString('es-MX', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
                $('#total-hidden').val(total.toFixed(2));
            }

            // Validar formulario antes de enviar
            $('#form-editar-venta').on('submit', function(e) {
                if ($('.producto-item').length === 0) {
                    e.preventDefault();
                    alert('Debe haber al menos un producto en la venta');
                    return false;
                }

                // Validar que todas las cantidades sean válidas
                let validForm = true;
                $('.cantidad').each(function() {
                    const cantidad = parseInt($(this).val());
                    const max = parseInt($(this).attr('max'));
                    if (cantidad < 1 || cantidad > max || isNaN(cantidad)) {
                        validForm = false;
                        $(this).focus();
                        alert('Por favor revise las cantidades de los productos');
                        return false;
                    }
                });

                if (!validForm) {
                    e.preventDefault();
                    return false;
                }

                // Confirmar antes de guardar
                return confirm('¿Está seguro de guardar los cambios en esta venta?');
            });

            // Calcular total inicial
            calcularTotal();
        });
    </script>
</body>

</html>