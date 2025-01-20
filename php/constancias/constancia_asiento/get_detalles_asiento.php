<?php
include '../../conexion.php';

$sas_codig = $_GET['sas_codig'] ?? 0;

// Obtener información de la persona difunta
$sql_difunto = $conexion->prepare("
    SELECT p.Per_cedul, p.Per_nombr, p.Per_apell, pd.Pdi_ffall, pd.Pdi_hfall, pd.Pdi_nacta, pd.Pdi_fotoa
    FROM prefttpdi pd
    JOIN prefttcli c ON pd.Pdi_clien = c.Cli_codig
    JOIN preftmper p ON c.Cli_cedul = p.Per_cedul
    WHERE pd.Pdi_asien = ?
");
$sql_difunto->bind_param("i", $sas_codig);
$sql_difunto->execute();
$difunto = $sql_difunto->get_result()->fetch_assoc();

// Obtener información de los testigos
$sql_testigos = $conexion->prepare("
    SELECT p.Per_cedul, p.Per_nombr, p.Per_apell
    FROM prefttpas pas
    JOIN prefttcli c ON pas.Pas_clien = c.Cli_codig
    JOIN preftmper p ON c.Cli_cedul = p.Per_cedul
    WHERE pas.Pas_asien = ? AND pas.Pas_rolcl = 3
    LIMIT 2
");
$sql_testigos->bind_param("i", $sas_codig);
$sql_testigos->execute();
$result_testigos = $sql_testigos->get_result();

$testigos = [];
while ($row = $result_testigos->fetch_assoc()) {
    $testigos[] = $row;
}

echo json_encode(['difunto' => $difunto, 'testigos' => $testigos]);
?>
