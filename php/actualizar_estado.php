<?php
session_start();
include('conexion.php'); // Asegúrate de tener el archivo de conexión correcto

// Verificar si el usuario está logueado
if (!isset($_SESSION['cedula'])) {
    echo "Debe iniciar sesión para actualizar el estado de las denuncias.";
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" || $_SERVER["REQUEST_METHOD"] == "GET") {
    // Obtener los valores de Den_codig y Den_statu
    $denuncia_id = $_SERVER["REQUEST_METHOD"] == "POST" ? $_POST['Den_codig'] : $_GET['Den_codig'];
    $nuevo_estado = $_SERVER["REQUEST_METHOD"] == "POST" ? $_POST['Den_statu'] : $_GET['Den_statu'];

    // Validar y sanitizar los valores
    if (isset($denuncia_id) && isset($nuevo_estado)) {
        // Actualizar el estado de la denuncia
        $sql_update_estado = $conexion->prepare("UPDATE Prefttden SET Den_statu = ? WHERE Den_codig = ?");
        $sql_update_estado->bind_param("si", $nuevo_estado, $denuncia_id);

        if ($sql_update_estado->execute()) {
            echo "Estado de la denuncia actualizado correctamente.";
        } else {
            echo "Error al actualizar el estado de la denuncia.";
        }

        // Redirigir de vuelta a la página de mostrar denuncias (o a la página correspondiente)
        header("Location: sesion_admin.php#about");
        exit;
    } else {
        echo "Datos faltantes o incorrectos.";
    }
} else {
    echo "Método no permitido.";
}
?>
