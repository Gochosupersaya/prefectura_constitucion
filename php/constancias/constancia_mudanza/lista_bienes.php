<?php
include('../../conexion.php');

if (isset($_GET['mudanza_id'])) {
    $mudanzaId = intval($_GET['mudanza_id']);

    $sql_lista_bienes = $conexion->prepare("
        SELECT 
            l.Lis_codig, b.Bie_nombr AS nombre_bien, l.Lis_canti AS cantidad
        FROM Prefttlis l
        JOIN Preftmbie b ON l.Lis_nbien = b.Bie_codig
        WHERE l.Lis_mudan = ?
    ");
    $sql_lista_bienes->bind_param("i", $mudanzaId);
    $sql_lista_bienes->execute();
    $result_lista_bienes = $sql_lista_bienes->get_result();

    $bienes = [];
    while ($row = $result_lista_bienes->fetch_assoc()) {
        $bienes[] = $row;
    }

    echo json_encode($bienes);
} else {
    echo json_encode([]);
}
?>
