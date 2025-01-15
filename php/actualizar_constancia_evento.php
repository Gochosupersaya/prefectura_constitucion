<?php
include 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sep_codig = $_POST['Sep_codig'];
    $nuevo_estado = $_POST['Sep_statu'];
    $sedeb_archivo = $_POST['Sep_sedeb'];

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
            $updateQuery = "UPDATE Prefttsep SET Sep_statu = ? WHERE Sep_codig = ?";
            $updateStmt = $conexion->prepare($updateQuery);
            $updateStmt->bind_param("si", $nuevo_estado, $sep_codig);
            $updateStmt->execute();

            if ($nuevo_estado === 'rechazada') {
                $motivo_rechazo = $_POST['motivo_rechazo'] ?? 'Motivo no especificado';
                $fecha_rechazo = date('Y-m-d');
                $rechazoQuery = "UPDATE Prefttsep SET Sep_motir = ?, Sep_frech = ? WHERE Sep_codig = ?";
                $rechazoStmt = $conexion->prepare($rechazoQuery);
                $rechazoStmt->bind_param("ssi", $motivo_rechazo, $fecha_rechazo, $sep_codig);
                $rechazoStmt->execute();
            }
            echo "success";
            exit;
        }
    }
}
echo "error";
exit;
