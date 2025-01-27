<?php
include('conexion.php'); // Asegúrate de tener el archivo de conexión correcto

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $Cit_codig = $_POST['Cit_codig'];
    $Ttm_descr = $_POST['Tmt_descr'];
    $fin_denuncia = $_POST['fin_denuncia'];

    // Insertar testimonio
    $sql_testimonio = $conexion->prepare("INSERT INTO prefttttm (Ttm_citac, Ttm_descr) VALUES (?, ?)");
    $sql_testimonio->bind_param('is', $Cit_codig, $Ttm_descr);
    $sql_testimonio->execute();

    // Insertar pruebas
    foreach ($_POST['Pru_descr'] as $key => $Pru_descr) {
        $Pru_fotop = $_FILES['Pru_fotop']['name'][$key];
        $Pru_fotop_tmp = $_FILES['Pru_fotop']['tmp_name'][$key];
        $Pru_fotop_folder = 'uploads/' . $Pru_fotop;

        // Mover la foto al directorio de uploads
        move_uploaded_file($Pru_fotop_tmp, $Pru_fotop_folder);

        $sql_prueba = $conexion->prepare("INSERT INTO prefttpru (Pru_citac, Pru_fotop, Pru_descr) VALUES (?, ?, ?)");
        $sql_prueba->bind_param('iss', $Cit_codig, $Pru_fotop_folder, $Pru_descr);
        $sql_prueba->execute();
    }

    // Procesar fin de la denuncia
    if ($fin_denuncia == 'conciliacion') {
        // Insertar acuerdos
        foreach ($_POST['Acuerdos'] as $Acuerdos) {
            // Insertar en prefttseg
            $sql_seg = $conexion->prepare("INSERT INTO prefttseg (Seg_citac) VALUES (?)");
            $sql_seg->bind_param('i', $Cit_codig);
            $sql_seg->execute();
            $Seg_codig = $conexion->insert_id; // Obtener el código del seguimiento

            // Insertar acuerdos en prefttasg
            $sql_acuerdo = $conexion->prepare("INSERT INTO prefttasg (Asg_segur, Asg_descri) VALUES (?, ?)");
            $sql_acuerdo->bind_param('is', $Seg_codig, $Acuerdos);
            $sql_acuerdo->execute();
        }
    } elseif ($fin_denuncia == 'sin_conciliacion') {
        // Obtener datos para el ministerio y motivo
        $ministerio = $_POST['ministerio'];
        $motivo = $_POST['motivo'];

        // Insertar en prefttcmo
        $sql_cmo = $conexion->prepare("INSERT INTO prefttcmo (Cmo_denun, Cmo_direm, Cmo_motiv) VALUES (?, ?, ?)");
        $sql_cmo->bind_param('iis', $Den_codig, $ministerio, $motivo);
        $sql_cmo->execute();
    }

    // Actualizar el estado de la citación a "Culminada"
    $sql_update_citacion = $conexion->prepare("UPDATE prefttcit SET Cit_statu = 'culminada' WHERE Cit_codig = ?");
    $sql_update_citacion->bind_param('i', $Cit_codig);
    $sql_update_citacion->execute();

    // Obtener el código de la denuncia asociada a la citación
    $sql_denuncia = $conexion->prepare("SELECT Dtd_denun FROM prefttdtd INNER JOIN prefttcit ON prefttdtd.Dtd_codig = prefttcit.Cit_perde WHERE prefttcit.Cit_codig = ?");
    $sql_denuncia->bind_param('i', $Cit_codig);
    $sql_denuncia->execute();
    $sql_denuncia->bind_result($Den_codig);
    $sql_denuncia->fetch();
    $sql_denuncia->close();

    // Actualiza el estado de la denuncia a "culminada"
    if ($Den_codig) {
        $sql_update = $conexion->prepare("UPDATE prefttden SET Den_statu = 'culminada' WHERE Den_codig = ?");
        $sql_update->bind_param('i', $Den_codig);
        if ($sql_update->execute()) {
            header("Location: sesion_admin.php#about");
            exit;
        } else {
            echo "Error al actualizar el estado de la denuncia: " . $conexion->error;
        }
        $sql_update->close();
    } else {
        echo "No se encontró la denuncia asociada a la citación.";
    }

    $sql_update_citacion->close();
    $conexion->close();
}
?>

