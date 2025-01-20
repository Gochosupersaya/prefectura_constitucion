<?php
include '../../conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sbc_codig = $_POST['Sbc_codig'];
    $nuevo_estado = $_POST['Sbc_statu'];
    $sedeb_archivo = $_POST['Sbc_sedeb'];
    $motivo_rechazo = $_POST['motivo_rechazo'] ?? null;

    // Verificar si el nuevo estado es válido
    $query = "SELECT Sbc_statu FROM prefttsbc WHERE Sbc_codig = ?";
    $stmt = $conexion->prepare($query);
    $stmt->bind_param("i", $sbc_codig);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if ($row) {
        $estado_actual = $row['Sbc_statu'];

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
            $updateQuery = "UPDATE prefttsbc SET Sbc_statu = ? WHERE Sbc_codig = ?";
            $updateStmt = $conexion->prepare($updateQuery);
            $updateStmt->bind_param("si", $nuevo_estado, $sbc_codig);
            $updateStmt->execute();

            // Si el estado es "Rechazada", guardar el motivo y la fecha de rechazo
            if ($nuevo_estado === 'Rechazada') {
                $fecha_rechazo = date('Y-m-d');
                $rechazoQuery = "UPDATE prefttsbc SET Sbc_motir = ?, Sbc_frech = ? WHERE Sbc_codig = ?";
                $rechazoStmt = $conexion->prepare($rechazoQuery);
                $rechazoStmt->bind_param("ssi", $motivo_rechazo, $fecha_rechazo, $sbc_codig);
                $rechazoStmt->execute();
            }

            // Si el estado es "Finalizada", guardar la fecha actual en Sbc_femis
            if ($nuevo_estado === 'Finalizada') {
                $fecha_finalizacion = date('Y-m-d');
                $finalizacionQuery = "UPDATE prefttsbc SET Sbc_femis = ? WHERE Sbc_codig = ?";
                $finalizacionStmt = $conexion->prepare($finalizacionQuery);
                $finalizacionStmt->bind_param("si", $fecha_finalizacion, $sbc_codig);
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
