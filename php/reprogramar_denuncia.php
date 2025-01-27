<?php
// Incluir el archivo de conexión a la base de datos
include 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reprogramar'])) {
    // Obtener el ID de la citación desde el formulario
    $id_citacion = $_POST['id_citacion'];

    // Actualizar el estado de la citación a "Reprogramada"
    $query = "UPDATE prefttcit SET Cit_statu = 'Reprogramada' WHERE Cit_codig = ?";
    $stmt = $conexion->prepare($query);
    $stmt->bind_param('i', $id_citacion);

    if ($stmt->execute()) {
        header("Location: sesion_admin.php#about");
        exit;
    } else {
        echo "Error al reprogramar la citación: " . $conexion->error;
    }

    $stmt->close();
    $conexion->close();
}
?>
