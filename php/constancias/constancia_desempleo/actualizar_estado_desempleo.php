<?php
include '../../conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sds_codig = $_POST['Sds_codig'];
    $nuevo_estado = $_POST['Sds_statu'];
    $sedeb_archivo = $_POST['Sds_sedeb'];
    $motivo_rechazo = $_POST['motivo_rechazo'] ?? null;

    // Verificar si el nuevo estado es válido
    $query = "SELECT Sds_statu FROM prefttsds WHERE Sds_codig = ?";
    $stmt = $conexion->prepare($query);
    $stmt->bind_param("i", $sds_codig);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if ($row) {
        $estado_actual = $row['Sds_statu'];

        $transiciones = [
            'Enviada' => ['En revision'],
            'En revision' => ['Rechazada', 'Aprobada pendiente de pago'],
            'Rechazada' => [],
            'Aprobada pendiente de pago' => $sedeb_archivo ? ['Pago en revision'] : ['Rechazada'],
            'Pago en revision' => ['Rechazada', 'Finalizada'],
            'Finalizada' => ['Rechazada']
        ];

        if (in_array($nuevo_estado, $transiciones[$estado_actual])) {
            // Actualizar el estado
            $updateQuery = "UPDATE prefttsds SET Sds_statu = ? WHERE Sds_codig = ?";
            $updateStmt = $conexion->prepare($updateQuery);
            $updateStmt->bind_param("si", $nuevo_estado, $sds_codig);
            $updateStmt->execute();

            // Si el estado es "Rechazada", guardar el motivo y la fecha de rechazo
            if ($nuevo_estado === 'Rechazada') {
                $fecha_rechazo = date('Y-m-d');
                $rechazoQuery = "UPDATE prefttsds SET Sds_motir = ?, Sds_frech = ? WHERE Sds_codig = ?";
                $rechazoStmt = $conexion->prepare($rechazoQuery);
                $rechazoStmt->bind_param("ssi", $motivo_rechazo, $fecha_rechazo, $sds_codig);
                $rechazoStmt->execute();
            }

            // Si el estado es "Finalizada", guardar la fecha actual en Sds_femis
            if ($nuevo_estado === 'Finalizada') {
                $fecha_finalizacion = date('Y-m-d');
                $finalizacionQuery = "UPDATE prefttsds SET Sds_femis = ? WHERE Sds_codig = ?";
                $finalizacionStmt = $conexion->prepare($finalizacionQuery);
                $finalizacionStmt->bind_param("si", $fecha_finalizacion, $sds_codig);
                $finalizacionStmt->execute();
            }

            echo "success";
            exit;
        }
    }
}
echo "error";
exit;
?>
