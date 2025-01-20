<?php
include '../../conexion.php';

$sbc_codig = $_GET['sbc_codig'] ?? 0;

// Obtener información de los testigos
$sql_testigos = $conexion->prepare("
    SELECT p.Per_cedul, p.Per_nombr, p.Per_apell
    FROM prefttpbc pb
    JOIN prefttcli c ON pb.Pbc_clien = c.Cli_codig
    JOIN preftmper p ON c.Cli_cedul = p.Per_cedul
    WHERE pb.Pbc_buena = ? AND pb.Pbc_rolcl = 3
    LIMIT 2
");
$sql_testigos->bind_param("i", $sbc_codig);
$sql_testigos->execute();
$result_testigos = $sql_testigos->get_result();

$testigos = [];
while ($row = $result_testigos->fetch_assoc()) {
    $testigos[] = $row;
}

echo json_encode(['testigos' => $testigos]);
?>
