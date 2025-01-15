<?php
include 'conexion.php';

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
                Sep_asist = ?
            WHERE Sep_codig = ?";

    $stmt = $conexion->prepare($sql);
    $stmt->bind_param(
        "ssssssssssssi", 
        $tipo_evento, $motivo, $aldea, $calle, $carrera, $lugar, 
        $fecha_inicio, $hora_inicio, $fecha_fin, $hora_fin, 
        $duracion, $asistencia, $codigo
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
