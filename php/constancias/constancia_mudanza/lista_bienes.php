<?php
include '../../conexion.php';

$smd_codig = $_GET['smd_codig'] ?? 0;

// Consulta para obtener los bienes asociados a la mudanza
$sql_bienes = $conexion->prepare("
    SELECT 
        b.Bie_nombr AS nombre_bien, 
        l.Lis_canti AS cantidad
    FROM Prefttlis l
    JOIN Preftmbie b ON l.Lis_nbien = b.Bie_codig
    WHERE l.Lis_mudan = ?
");
$sql_bienes->bind_param("i", $smd_codig);
$sql_bienes->execute();
$result = $sql_bienes->get_result();

$bienes = [];
while ($row = $result->fetch_assoc()) {
    $bienes[] = $row;
}

echo json_encode($bienes);
?>
