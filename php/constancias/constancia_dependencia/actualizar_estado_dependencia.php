<?php
include '../../conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sde_codig = $_POST['Sde_codig'];
    $nuevo_estado = $_POST['Sde_statu'];
    $sedeb_archivo = $_POST['Sde_sedeb'];
    $motivo_rechazo = $_POST['motivo_rechazo'] ?? null;

    // Verificar si el nuevo estado es válido
    $query = "SELECT Sde_statu FROM prefttsde WHERE Sde_codig = ?";
    $stmt = $conexion->prepare($query);
    $stmt->bind_param("i", $sde_codig);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if ($row) {
        $estado_actual = $row['Sde_statu'];

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
            $updateQuery = "UPDATE prefttsde SET Sde_statu = ? WHERE Sde_codig = ?";
            $updateStmt = $conexion->prepare($updateQuery);
            $updateStmt->bind_param("si", $nuevo_estado, $sde_codig);
            $updateStmt->execute();

            // Si el estado es "Rechazada", guardar el motivo y la fecha de rechazo
            if ($nuevo_estado === 'Rechazada') {
                $fecha_rechazo = date('Y-m-d');
                $rechazoQuery = "UPDATE prefttsde SET Sde_motir = ?, Sde_frech = ? WHERE Sde_codig = ?";
                $rechazoStmt = $conexion->prepare($rechazoQuery);
                $rechazoStmt->bind_param("ssi", $motivo_rechazo, $fecha_rechazo, $sde_codig);
                $rechazoStmt->execute();
            }

            // Si el estado es "Finalizada", guardar la fecha actual en Sde_femis
            if ($nuevo_estado === 'Finalizada') {
                $fecha_finalizacion = date('Y-m-d');
                $finalizacionQuery = "UPDATE prefttsde SET Sde_femis = ? WHERE Sde_codig = ?";
                $finalizacionStmt = $conexion->prepare($finalizacionQuery);
                $finalizacionStmt->bind_param("si", $fecha_finalizacion, $sde_codig);
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
