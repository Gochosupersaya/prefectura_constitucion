<?php
include '../conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sep_codig = $_POST['Sep_codig'];
    $nuevo_estado = $_POST['Sep_statu'];
    $sedeb_archivo = $_POST['Sep_sedeb'];
    $motivo_rechazo = $_POST['motivo_rechazo'] ?? null;

    // Verificar si el nuevo estado es válido
    $query = "SELECT Sep_statu FROM Prefttsep WHERE Sep_codig = ?";
    $stmt = $conexion->prepare($query);
    $stmt->bind_param("i", $sep_codig);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if ($row) {
        $estado_actual = $row['Sep_statu'];

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
            $updateQuery = "UPDATE Prefttsep SET Sep_statu = ? WHERE Sep_codig = ?";
            $updateStmt = $conexion->prepare($updateQuery);
            $updateStmt->bind_param("si", $nuevo_estado, $sep_codig);
            $updateStmt->execute();

            // Si el estado es "Rechazada", guardar el motivo y la fecha de rechazo
            if ($nuevo_estado === 'Rechazada') {
                $fecha_rechazo = date('Y-m-d');
                $rechazoQuery = "UPDATE Prefttsep SET Sep_motir = ?, Sep_frech = ? WHERE Sep_codig = ?";
                $rechazoStmt = $conexion->prepare($rechazoQuery);
                $rechazoStmt->bind_param("ssi", $motivo_rechazo, $fecha_rechazo, $sep_codig);
                $rechazoStmt->execute();
            }

            // Si el estado es "Finalizada", guardar la fecha actual en Sep_femis
            if ($nuevo_estado === 'Finalizada') {
                $fecha_finalizacion = date('Y-m-d');
                $finalizacionQuery = "UPDATE Prefttsep SET Sep_femis = ? WHERE Sep_codig = ?";
                $finalizacionStmt = $conexion->prepare($finalizacionQuery);
                $finalizacionStmt->bind_param("si", $fecha_finalizacion, $sep_codig);
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

