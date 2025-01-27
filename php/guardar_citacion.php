<?php

include('conexion.php'); // Asegúrate de tener el archivo de conexión correcto

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $Den_codig = $_POST['Den_codig'];
    $Cit_fecha = $_POST['Cit_fecha'];
    $Cit_horad = $_POST['Cit_horad'];
    $Cit_statu = 'pendiente'; // Estado inicial de la citación

    // Verificar si existe alguna citación pendiente para el denunciado en esta denuncia
    $sql_citaciones_pendientes = $conexion->prepare("
        SELECT COUNT(*) as total_pendientes
        FROM prefttcit
        WHERE Cit_perde IN (
            SELECT Dtd_codig
            FROM prefttdtd
            WHERE Dtd_denun = ? AND Dtd_rolde = 2
        ) AND Cit_statu = 'pendiente'
    ");
    $sql_citaciones_pendientes->bind_param('i', $Den_codig);
    $sql_citaciones_pendientes->execute();
    $result_citaciones_pendientes = $sql_citaciones_pendientes->get_result();
    $row_citaciones_pendientes = $result_citaciones_pendientes->fetch_assoc();

    if ($row_citaciones_pendientes['total_pendientes'] > 0) {
        // Si hay una citación pendiente, no se puede agregar otra
        echo "No puede realizar otra citación hasta que la anterior sea resuelta.";
        exit();
    } else {
        // Obtener el código del denunciado (Dtd_ropde = 2)
        $sql_personas = $conexion->prepare("
            SELECT Dtd_codig 
            FROM prefttdtd
            WHERE Dtd_denun = ? AND Dtd_rolde = 2
        ");
        $sql_personas->bind_param('i', $Den_codig);
        $sql_personas->execute();
        $result_personas = $sql_personas->get_result();
        
        while ($persona = $result_personas->fetch_assoc()) {
            $Cit_perde = $persona['Dtd_codig'];
            
            // Insertar la citación
            $sql_insert = $conexion->prepare("
                INSERT INTO prefttcit (Cit_perde, Cit_fecha, Cit_horad, Cit_statu)
                VALUES (?, ?, ?, ?)
            ");
            $sql_insert->bind_param('isss', $Cit_perde, $Cit_fecha, $Cit_horad, $Cit_statu);
            $sql_insert->execute();
        }

        header("Location: sesion_admin.php#about");
        exit;
    }
}

?>
