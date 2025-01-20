<?php
include '../../conexion.php';

$sds_codig = $_GET['sds_codig'] ?? 0;
$sql_testigos = $conexion->prepare("
    SELECT p.Per_cedul, p.Per_nombr, p.Per_apell
    FROM Prefttpds pd
    JOIN Prefttcli c ON pd.Pds_clien = c.Cli_codig
    JOIN Preftmper p ON c.Cli_cedul = p.Per_cedul
    WHERE pd.Pds_desem = ? AND pd.Pds_rolcl = 3
    LIMIT 2
");
$sql_testigos->bind_param("i", $sds_codig);
$sql_testigos->execute();
$result = $sql_testigos->get_result();

$testigos = [];
while ($row = $result->fetch_assoc()) {
    $testigos[] = $row;
}

echo json_encode($testigos);
?>
