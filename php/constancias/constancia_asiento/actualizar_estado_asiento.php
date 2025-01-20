<?php
include '../../conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sas_codig = $_POST['Sas_codig'];
    $nuevo_estado = $_POST['Sas_statu'];
    $sedeb_archivo = $_POST['Sas_sedeb'];
    $motivo_rechazo = $_POST['motivo_rechazo'] ?? null;

    // Verificar si el nuevo estado es válido
    $query = "SELECT Sas_statu FROM prefttsas WHERE Sas_codig = ?";
    $stmt = $conexion->prepare($query);
    $stmt->bind_param("i", $sas_codig);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if ($row) {
        $estado_actual = $row['Sas_statu'];

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
            $updateQuery = "UPDATE prefttsas SET Sas_statu = ? WHERE Sas_codig = ?";
            $updateStmt = $conexion->prepare($updateQuery);
            $updateStmt->bind_param("si", $nuevo_estado, $sas_codig);
            $updateStmt->execute();

            // Si el estado es "Rechazada", guardar el motivo y la fecha de rechazo
            if ($nuevo_estado === 'Rechazada') {
                $fecha_rechazo = date('Y-m-d');
                $rechazoQuery = "UPDATE prefttsas SET Sas_motir = ?, Sas_frech = ? WHERE Sas_codig = ?";
                $rechazoStmt = $conexion->prepare($rechazoQuery);
                $rechazoStmt->bind_param("ssi", $motivo_rechazo, $fecha_rechazo, $sas_codig);
                $rechazoStmt->execute();
            }

            // Si el estado es "Finalizada", guardar la fecha actual en Sas_femis
            if ($nuevo_estado === 'Finalizada') {
                $fecha_finalizacion = date('Y-m-d');
                $finalizacionQuery = "UPDATE prefttsas SET Sas_femis = ? WHERE Sas_codig = ?";
                $finalizacionStmt = $conexion->prepare($finalizacionQuery);
                $finalizacionStmt->bind_param("si", $fecha_finalizacion, $sas_codig);
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
