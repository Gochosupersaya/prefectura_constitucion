<?php
include '../conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $codigo = $_POST['codigo'];
    $tipo_evento = $_POST['tipo_evento'];
    $motivo = $_POST['motivo'];
    $aldea = $_POST['aldea'];
    $calle = $_POST['calle'];
    $carrera = $_POST['carrera'];
    $lugar = $_POST['lugar'];
    $fecha_inicio = $_POST['fecha_inicio'];
    $hora_inicio = $_POST['hora_inicio'];
    $fecha_fin = $_POST['fecha_fin'];
    $hora_fin = $_POST['hora_fin'];
    $duracion = $_POST['duracion'];
    $asistencia = $_POST['asistencia'];
    $sedeb_foto_path = NULL;

    $carpeta_sedeb = 'imagenes/constancias_sedebat/evento_publico/';

    // Guardar la foto del baucher SEDB
    if (isset($_FILES['sedeb_foto']) && $_FILES['sedeb_foto']['error'] == 0) {
        $sedeb_foto_nombre = $carpeta_sedeb . 'sedeb_' . $codigo . '_' . time() . '.jpg';
        if (move_uploaded_file($_FILES['sedeb_foto']['tmp_name'], $sedeb_foto_nombre)) {
            $sedeb_foto_path = $sedeb_foto_nombre;
        }
    }

    $sql = "UPDATE prefttsep SET 
                Sep_tipoe = ?, 
                Sep_motiv = ?, 
                Sep_aldea = ?, 
                Sep_calle = ?, 
                Sep_carre = ?, 
                Sep_delug = ?, 
                Sep_finic = ?, 
                Sep_hinic = ?, 
                Sep_ffinl = ?, 
                Sep_hfinl = ?, 
                Sep_durac = ?, 
                Sep_asist = ?, 
                Sep_sedeb = ?
            WHERE Sep_codig = ?";

    $stmt = $conexion->prepare($sql);
    $stmt->bind_param(
        "sssssssssssssi",
        $tipo_evento, $motivo, $aldea, $calle, $carrera, $lugar,
        $fecha_inicio, $hora_inicio, $fecha_fin, $hora_fin,
        $duracion, $asistencia, $sedeb_foto_path, $codigo
    );

    if ($stmt->execute()) {
        echo "Datos actualizados correctamente";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conexion->close();
}
?>
