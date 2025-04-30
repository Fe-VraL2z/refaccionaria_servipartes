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

$accion = $_GET['accion'] ?? '';
$tabla = $_GET['tabla'] ?? '';
$id = $_GET['id'] ?? 0;

switch ($accion) {
    case 'agregar':
        // Redirige al formulario de agregar según la tabla
        header("Location: formulario_insertar.php?tabla=$tabla");
        exit;
        break;
        
    case 'mostrar':
        // Redirige para mostrar los registros de la tabla
        header("Location: mostrar_tabla.php?tabla=$tabla");
        exit;
        break;
        
    case 'editar':
        // Redirige al formulario de edición con el ID
        header("Location: formulario_editar.php?tabla=$tabla&id=$id");
        exit;
        break;
        
    case 'quitar':
        // Elimina un registro de cualquier tabla
        if ($id > 0 && in_array($tabla, ['cliente', 'producto', 'proveedor', 'ventas'])) {
            // Verificar si el registro existe antes de eliminar
            $query = "SELECT id FROM $tabla WHERE id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                // Eliminar el registro
                $deleteQuery = "DELETE FROM $tabla WHERE id = ?";
                $deleteStmt = $conn->prepare($deleteQuery);
                $deleteStmt->bind_param("i", $id);
                $deleteStmt->execute();

                if ($deleteStmt->affected_rows > 0) {
                    $_SESSION['mensaje'] = "Registro eliminado correctamente.";
                } else {
                    $_SESSION['mensaje'] = "Error al eliminar el registro.";
                }
            } else {
                $_SESSION['mensaje'] = "Registro no encontrado.";
            }
        }
        header("Location: dashboard.php");
        exit;
        break;
        
    case 'consultar':
        // Redirige a la consulta normal para tablas que no son ventas
        if ($tabla !== 'ventas') {
            header("Location: consultar.php?tabla=$tabla");
            exit;
        }
        break;
        
    case 'imprimir_ticket':
        // Acción especial para imprimir ticket de ventas
        if ($tabla === 'ventas') {
            header("Location: imprimir_ticket.php?id=$id");
            exit;
        }
        break;
}

$conn->close();
header("Location: dashboard.php");
exit;
?>