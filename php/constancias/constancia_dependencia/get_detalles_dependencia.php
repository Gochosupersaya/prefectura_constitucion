<?php
include '../../conexion.php';

$sde_codig = $_GET['sde_codig'] ?? 0;

// Obtener información de la persona independiente
$sql_independiente = $conexion->prepare("
    SELECT p.Per_cedul, p.Per_nombr, p.Per_apell
    FROM prefttped pd
    JOIN prefttcli c ON pd.Ped_clien = c.Cli_codig
    JOIN preftmper p ON c.Cli_cedul = p.Per_cedul
    WHERE pd.Ped_depen = ? AND pd.Ped_rolcl = 7
");
$sql_independiente->bind_param("i", $sde_codig);
$sql_independiente->execute();
$independiente = $sql_independiente->get_result()->fetch_assoc();

// Obtener información de los testigos
$sql_testigos = $conexion->prepare("
    SELECT p.Per_cedul, p.Per_nombr, p.Per_apell
    FROM prefttped pd
    JOIN prefttcli c ON pd.Ped_clien = c.Cli_codig
    JOIN preftmper p ON c.Cli_cedul = p.Per_cedul
    WHERE pd.Ped_depen = ? AND pd.Ped_rolcl = 3
    LIMIT 2
");
$sql_testigos->bind_param("i", $sde_codig);
$sql_testigos->execute();
$result_testigos = $sql_testigos->get_result();

$testigos = [];
while ($row = $result_testigos->fetch_assoc()) {
    $testigos[] = $row;
}

echo json_encode(['independiente' => $independiente, 'testigos' => $testigos]);
?>
