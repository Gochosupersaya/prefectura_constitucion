<?php
session_start();
include('conexion.php'); // Asegúrate de tener el archivo de conexión correcto

// Verificar si el usuario está logueado
if (!isset($_SESSION['cedula'])) {
    echo "Debe iniciar sesión para ver las denuncias.";
    exit;
}

// Obtener todas las denuncias
$sql_denuncias = $conexion->prepare("
    SELECT 
        d.Den_codig, d.Den_tipod, d.Den_motiv, d.Den_fecha, d.Den_statu,
        c1.Cli_cedul AS cedula_denunciante,
        c2.Cli_cedul AS cedula_denunciado
    FROM prefttden d
    JOIN prefttdtd dt1 ON d.Den_codig = dt1.Dtd_denun AND dt1.Dtd_rolde = 1
    JOIN prefttcli c1 ON dt1.Dtd_clien = c1.Cli_codig
    JOIN prefttdtd dt2 ON d.Den_codig = dt2.Dtd_denun AND dt2.Dtd_rolde = 2
    JOIN prefttcli c2 ON dt2.Dtd_clien = c2.Cli_codig
");
$sql_denuncias->execute();
$result_denuncias = $sql_denuncias->get_result();

// Definir los estados de denuncia manualmente
$estados = [
    'enviada' => 'Enviada',
    'rechazada' => 'Rechazada',
    'aprobada' => 'Aprobada',
    'culminada' => 'Culminada',
    'en_revision' => 'En revisión', // Nuevo estado
	'directo_ministerio' => 'Directo al ministerio'
];

// Función para obtener el tipo de denuncia a partir de su código
function obtenerTipoDenuncia($codigo_tipo_denuncia, $conexion) {
    $sql = $conexion->prepare("SELECT Tdn_nombr FROM preftmtdn WHERE Tdn_codig = ?");
    $sql->bind_param("i", $codigo_tipo_denuncia);
    $sql->execute();
    $result = $sql->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['Tdn_nombr'];
    } else {
        return "Tipo de Denuncia Desconocido";
    }
}
?>

<?php

include('conexion.php'); // Asegúrate de tener el archivo de conexión correcto

// Verificar si el usuario está logueado
if (!isset($_SESSION['cedula'])) {
    echo "Debe iniciar sesión para ver las denuncias.";
    exit;
}

// Obtener todas las denuncias en revisión
$sql_denuncias_revision = $conexion->prepare("
    SELECT 
        d.Den_codig, d.Den_tipod, d.Den_motiv, d.Den_fecha, d.Den_statu,
        p1.Per_cedul AS cedula_denunciante, p1.Per_nombr AS nombre_denunciante, p1.Per_apell AS apellido_denunciante, p1.Per_telef AS telefono_denunciante, 
        a1.Ald_nombr AS aldea_denunciante, d1.Din_calle AS calle_denunciante, d1.Din_carre AS carrera_denunciante, d1.Din_ncasa AS casa_denunciante,
        p2.Per_cedul AS cedula_denunciado, p2.Per_nombr AS nombre_denunciado, p2.Per_apell AS apellido_denunciado, p2.Per_telef AS telefono_denunciado,
        a2.Ald_nombr AS aldea_denunciado, d2.Din_calle AS calle_denunciado, d2.Din_carre AS carrera_denunciado, d2.Din_ncasa AS casa_denunciado
    FROM prefttden d
    JOIN prefttdtd dt1 ON d.Den_codig = dt1.Dtd_denun AND dt1.Dtd_rolde = 1
    JOIN prefttcli c1 ON dt1.Dtd_clien = c1.Cli_codig
    JOIN preftmper p1 ON c1.Cli_cedul = p1.Per_cedul
    LEFT JOIN prefttdii d1 ON p1.Per_cedul = d1.Din_cedul
    LEFT JOIN preftmald a1 ON d1.Din_aldea = a1.Ald_codig
    LEFT JOIN prefttdie de1 ON p1.Per_cedul = de1.Die_cedul
    LEFT JOIN preftmmun m1 ON de1.Die_munic = m1.Mun_codig
    JOIN prefttdtd dt2 ON d.Den_codig = dt2.Dtd_denun AND dt2.Dtd_rolde = 2
    JOIN prefttcli c2 ON dt2.Dtd_clien = c2.Cli_codig
    JOIN preftmper p2 ON c2.Cli_cedul = p2.Per_cedul
    LEFT JOIN prefttdii d2 ON p2.Per_cedul = d2.Din_cedul
    LEFT JOIN preftmald a2 ON d2.Din_aldea = a2.Ald_codig
    LEFT JOIN prefttdie de2 ON p2.Per_cedul = de2.Die_cedul
    LEFT JOIN preftmmun m2 ON de2.Die_munic = m2.Mun_codig
    WHERE d.Den_statu = 'en_revision'
");
$sql_denuncias_revision->execute();
$result_denuncias_revision = $sql_denuncias_revision->get_result();


?>


<?php

include('conexion.php'); // Asegúrate de tener el archivo de conexión correcto

// Verificar si el usuario está logueado
if (!isset($_SESSION['cedula'])) {
    echo "Debe iniciar sesión para ver las citaciones.";
    exit;
}

// Obtener todas las denuncias aprobadas
$sql_denuncias_aprobadas = $conexion->prepare("
    SELECT 
        d.Den_codig, d.Den_tipod, d.Den_motiv, d.Den_fecha, d.Den_statu,
        p1.Per_cedul AS cedula_denunciante, p1.Per_nombr AS nombre_denunciante, p1.Per_apell AS apellido_denunciante, p1.Per_telef AS telefono_denunciante, 
        a1.Ald_nombr AS aldea_denunciante, d1.Din_calle AS calle_denunciante, d1.Din_carre AS carrera_denunciante, d1.Din_ncasa AS casa_denunciante,
        p2.Per_cedul AS cedula_denunciado, p2.Per_nombr AS nombre_denunciado, p2.Per_apell AS apellido_denunciado, p2.Per_telef AS telefono_denunciado,
        a2.Ald_nombr AS aldea_denunciado, d2.Din_calle AS calle_denunciado, d2.Din_carre AS carrera_denunciado, d2.Din_ncasa AS casa_denunciado
    FROM prefttden d
    JOIN prefttdtd dt1 ON d.Den_codig = dt1.Dtd_denun AND dt1.Dtd_rolde = 1
    JOIN prefttcli c1 ON dt1.Dtd_clien = c1.Cli_codig
    JOIN preftmper p1 ON c1.Cli_cedul = p1.Per_cedul
    LEFT JOIN prefttdii d1 ON p1.Per_cedul = d1.Din_cedul
    LEFT JOIN preftmald a1 ON d1.Din_aldea = a1.Ald_codig
    LEFT JOIN prefttdie de1 ON p1.Per_cedul = de1.Die_cedul
    LEFT JOIN preftmmun m1 ON de1.Die_munic = m1.Mun_codig
    JOIN prefttdtd dt2 ON d.Den_codig = dt2.Dtd_denun AND dt2.Dtd_rolde = 2
    JOIN prefttcli c2 ON dt2.Dtd_clien = c2.Cli_codig
    JOIN preftmper p2 ON c2.Cli_cedul = p2.Per_cedul
    LEFT JOIN prefttdii d2 ON p2.Per_cedul = d2.Din_cedul
    LEFT JOIN preftmald a2 ON d2.Din_aldea = a2.Ald_codig
    LEFT JOIN prefttdie de2 ON p2.Per_cedul = de2.Die_cedul
    LEFT JOIN preftmmun m2 ON de2.Die_munic = m2.Mun_codig
    WHERE d.Den_statu = 'aprobada'
");
$sql_denuncias_aprobadas->execute();
$result_denuncias_aprobadas = $sql_denuncias_aprobadas->get_result();

?>

<?php
include('conexion.php'); // Asegúrate de tener el archivo de conexión correcto

// Verificar si el usuario está logueado
if (!isset($_SESSION['cedula'])) {
    echo "Debe iniciar sesión para ver los usuarios.";
    exit;
}

// Consulta para los usuarios dentro de la parroquia
$sql_interior = $conexion->prepare("
    SELECT 
        p.Per_cedul, p.Per_nombr, p.Per_apell, p.Per_telef, p.Per_cfoto, p.Per_rifpe,
        d.Din_aldea, d.Din_calle, d.Din_carre, d.Din_ncasa,
        u.Usu_corre, u.Usu_contr, u.Usu_statu
    FROM preftmper p
    JOIN prefttdii d ON p.Per_cedul = d.Din_cedul
    JOIN prefttusu u ON p.Per_cedul = u.Usu_cedul
");
$sql_interior->execute();
$result_interior = $sql_interior->get_result();

// Consulta para los usuarios fuera de la parroquia
$sql_exterior = $conexion->prepare("
    SELECT 
        p.Per_cedul, p.Per_nombr, p.Per_apell, p.Per_telef, p.Per_cfoto, p.Per_rifpe,
        e.Die_munic, e.Die_calle, e.Die_carre, e.Die_ncasa,
        u.Usu_corre, u.Usu_contr, u.Usu_statu
    FROM preftmper p
    JOIN prefttdie e ON p.Per_cedul = e.Die_cedul
    JOIN prefttusu u ON p.Per_cedul = u.Usu_cedul
");
$sql_exterior->execute();
$result_exterior = $sql_exterior->get_result();

?>


<?php

include('conexion.php'); // Asegúrate de tener el archivo de conexión correcto

// Verificar si el usuario está logueado
if (!isset($_SESSION['cedula'])) {
    echo "Debe iniciar sesión para ver las personas sin usuario.";
    exit;
}

// Consulta para las personas registradas en la tabla de personas pero que no tienen un usuario
$sql_sin_usuario = $conexion->prepare("
    SELECT 
        p.Per_cedul, p.Per_nombr, p.Per_apell, p.Per_telef, p.Per_cfoto, p.Per_rifpe,
        d.Din_aldea, d.Din_calle, d.Din_carre, d.Din_ncasa
    FROM preftmper p
    JOIN prefttdii d ON p.Per_cedul = d.Din_cedul
    LEFT JOIN prefttusu u ON p.Per_cedul = u.Usu_cedul
    WHERE u.Usu_cedul IS NULL
");
$sql_sin_usuario->execute();
$result_sin_usuario = $sql_sin_usuario->get_result();
?>

<?php
include('conexion.php'); // Asegúrate de tener el archivo de conexión correcto

// Verificar si el usuario está logueado
if (!isset($_SESSION['cedula'])) {
    echo "Debe iniciar sesión para ver las constancias.";
    exit;
}

// Consulta para obtener las constancias de notificación de evento público
$sql_constancia_evento = $conexion->prepare("
    SELECT 
        p.Per_cedul, p.Per_nombr, p.Per_apell, p.Per_telef, 
        s.Sep_codig, s.Sep_tipoe, s.Sep_motiv, s.Sep_aldea, 
        s.Sep_calle, s.Sep_carre, s.Sep_delug, s.Sep_finic, 
        s.Sep_hinic, s.Sep_ffinl, s.Sep_hfinl, s.Sep_durac, 
        s.Sep_asist, s.Sep_statu, s.Sep_sedeb, s.Sep_fsoli, 
        s.Sep_femis, s.Sep_frech, s.Sep_motir,
        t.Tpe_nombr, a.Ald_nombr
    FROM prefttsep s
    JOIN prefttcli c ON s.Sep_clien = c.Cli_codig
    JOIN preftmper p ON c.Cli_cedul = p.Per_cedul
    JOIN preftmtpe t ON s.Sep_tipoe = t.Tpe_codig
    JOIN preftmald a ON s.Sep_aldea = a.Ald_codig
");
$sql_constancia_evento->execute();
$result_constancia_evento = $sql_constancia_evento->get_result();

?>

<?php
function getEstadoColor($estado) {
    $colores = [
        'Enviada' => 'blue',
        'En revision' => 'orange',
        'Rechazada' => 'red',
        'Aprobada pendiente de pago' => 'darkgreen',
        'Pago en revision' => 'lightgreen',
        'Finalizada' => 'white'
    ];
    return $colores[$estado] ?? 'gray';
}

function getEstadoOptions($estadoActual, $sedebArchivo) {
    $opciones = [
        'Enviada' => ['En revision'],
        'En revision' => ['Rechazada', 'Aprobada pendiente de pago'],
        'Rechazada' => [],
        'Aprobada pendiente de pago' => $sedebArchivo ? ['Pago en revision'] : ['Rechazada'],
        'Pago en revision' => ['Rechazada', 'Finalizada'],
        'Finalizada' => ['Rechazada']
    ];

    $html = "<option value='$estadoActual' selected>$estadoActual</option>";
    foreach ($opciones[$estadoActual] as $opcion) {
        $html .= "<option value='$opcion'>$opcion</option>";
    }
    return $html;
}
?>

<?php
function getEstadoColorSinPago($estado) {
    $colores = [
        'Enviada' => 'blue',
        'En revision' => 'orange',
        'Rechazada' => 'red',
        'Finalizada' => 'green'
    ];
    return $colores[$estado] ?? 'gray';
}

function getEstadoOptionsSinPago($estadoActual) {
    $opciones = [
        'Enviada' => ['En revision'],
        'En revision' => ['Rechazada', 'Finalizada'],
        'Rechazada' => [],
        'Finalizada' => ['Rechazada']
    ];

    $html = "<option value='$estadoActual' selected>$estadoActual</option>";
    foreach ($opciones[$estadoActual] as $opcion) {
        $html .= "<option value='$opcion'>$opcion</option>";
    }
    return $html;
}
?>


<?php
include('conexion.php'); // Archivo de conexión

// Verificar si el usuario está logueado
if (!isset($_SESSION['cedula'])) {
    echo "Debe iniciar sesión para ver las constancias.";
    exit;
}

// Consulta para obtener las constancias de desempleo
$sql_constancia_desempleo = $conexion->prepare("
    SELECT 
        p.Per_cedul, p.Per_nombr, p.Per_apell, 
        s.Sds_codig, s.Sds_motiv, s.Sds_fsoli, s.Sds_femis, s.Sds_frech, s.Sds_motir, 
        s.Sds_statu, s.Sds_sedeb
    FROM prefttsds s
    JOIN prefttpds pd ON s.Sds_codig = pd.Pds_desem
    JOIN prefttcli c ON pd.Pds_clien = c.Cli_codig
    JOIN preftmper p ON c.Cli_cedul = p.Per_cedul
    WHERE pd.Pds_rolcl = 4
");
$sql_constancia_desempleo->execute();
$result_constancia_desempleo = $sql_constancia_desempleo->get_result();


?>

<?php
include('conexion.php'); // Archivo de conexión

// Verificar si el usuario está logueado
if (!isset($_SESSION['cedula'])) {
    echo "Debe iniciar sesión para ver las constancias.";
    exit;
}

// Consulta para obtener las constancias de dependencia económica
$sql_constancia_dependencia = $conexion->prepare("
    SELECT 
        p.Per_cedul, p.Per_nombr, p.Per_apell, 
        s.Sde_codig, s.Sde_motiv, s.Sde_fsoli, s.Sde_femis, s.Sde_frech, s.Sde_motir, 
        s.Sde_statu, s.Sde_sedeb
    FROM prefttsde s
    JOIN prefttped pd ON s.Sde_codig = pd.Ped_depen
    JOIN prefttcli c ON pd.Ped_clien = c.Cli_codig
    JOIN preftmper p ON c.Cli_cedul = p.Per_cedul
    WHERE pd.Ped_rolcl = 6
");
$sql_constancia_dependencia->execute();
$result_constancia_dependencia = $sql_constancia_dependencia->get_result();
?>

<?php
include('conexion.php'); // Archivo de conexión

// Verificar si el usuario está logueado
if (!isset($_SESSION['cedula'])) {
    echo "Debe iniciar sesión para ver las constancias.";
    exit;
}

// Consulta para obtener las constancias de Asiento Permanente
$sql_constancia_asiento = $conexion->prepare("
    SELECT 
        p.Per_cedul, p.Per_nombr, p.Per_apell, 
        s.Sas_codig, s.Sas_motiv, s.Sas_fsoli, s.Sas_femis, s.Sas_frech, s.Sas_motir, 
        s.Sas_statu, s.Sas_sedeb
    FROM prefttsas s
    JOIN prefttpas pas ON s.Sas_codig = pas.Pas_asien
    JOIN prefttcli c ON pas.Pas_clien = c.Cli_codig
    JOIN preftmper p ON c.Cli_cedul = p.Per_cedul
    WHERE pas.Pas_rolcl = 8
");
$sql_constancia_asiento->execute();
$result_constancia_asiento = $sql_constancia_asiento->get_result();
?>

<?php
include('conexion.php'); // Archivo de conexión

// Verificar si el usuario está logueado
if (!isset($_SESSION['cedula'])) {
    echo "Debe iniciar sesión para ver las constancias.";
    exit;
}

// Consulta para obtener las constancias de buena conducta
$sql_constancia_buena = $conexion->prepare("
    SELECT 
        p.Per_cedul, p.Per_nombr, p.Per_apell, 
        s.Sbc_codig, s.Sbc_motiv, s.Sbc_fsoli, s.Sbc_femis, s.Sbc_frech, s.Sbc_motir, 
        s.Sbc_statu, s.Sbc_sedeb
    FROM prefttsbc s
    JOIN prefttpbc pb ON s.Sbc_codig = pb.Pbc_buena
    JOIN prefttcli c ON pb.Pbc_clien = c.Cli_codig
    JOIN preftmper p ON c.Cli_cedul = p.Per_cedul
    WHERE pb.Pbc_rolcl = 5
");
$sql_constancia_buena->execute();
$result_constancia_buena = $sql_constancia_buena->get_result();
?>

<?php
include('conexion.php'); // Archivo de conexión

// Verificar si el usuario está logueado
if (!isset($_SESSION['cedula'])) {
    echo "Debe iniciar sesión para ver los permisos de mudanza.";
    exit;
}

// Consulta para obtener los permisos de mudanza
$sql_permisos_mudanza = $conexion->prepare("
    SELECT 
        p.Per_cedul AS solicitante_cedula, p.Per_nombr AS solicitante_nombre, p.Per_apell AS solicitante_apellido,
        s.Smd_codig, s.Smd_fsoli, s.Smd_femis, s.Smd_frech, s.Smd_motir, s.Smd_statu, s.Smd_sedeb,
        v.Ald_nombr AS partida_aldea,
        s.Smd_lugar AS destino_lugar, m.Mun_nombr AS destino_municipio,
        c.Per_cedul AS conductor_cedula, c.Per_nombr AS conductor_nombre, c.Per_apell AS conductor_apellido, c.Per_telef AS conductor_telefono,
        vvh.Veh_añove AS vehiculo_año, vvh.Veh_color AS vehiculo_color, vvh.Veh_clase AS vehiculo_clase, vvh.Veh_placa AS vehiculo_placa, 
        vvh.Veh_smoto AS vehiculo_serial_motor, vvh.Veh_scarr AS vehiculo_serial_carroceria, 
        car.Car_marca AS vehiculo_marca, mdl.Mca_model AS vehiculo_modelo
    FROM prefttsmd s
    JOIN prefttpmd pm_solicitante ON s.Smd_codig = pm_solicitante.Pmd_mudan AND pm_solicitante.Pmd_rolcl = 9
    JOIN prefttcli cli_solicitante ON pm_solicitante.Pmd_clien = cli_solicitante.Cli_codig
    JOIN preftmper p ON cli_solicitante.Cli_cedul = p.Per_cedul
    LEFT JOIN preftmald v ON s.Smd_aldea = v.Ald_codig
    LEFT JOIN preftmmun m ON s.Smd_munll = m.Mun_codig
    LEFT JOIN prefttpmd pm_conductor ON s.Smd_codig = pm_conductor.Pmd_mudan AND pm_conductor.Pmd_rolcl = 10
    LEFT JOIN prefttcli cli_conductor ON pm_conductor.Pmd_clien = cli_conductor.Cli_codig
    LEFT JOIN preftmper c ON cli_conductor.Cli_cedul = c.Per_cedul
    LEFT JOIN prefttvyc vyc ON pm_conductor.Pmd_codig = vyc.Vyc_chofe
    LEFT JOIN prefttveh vvh ON vyc.Vyc_vehic = vvh.Veh_codig
    LEFT JOIN prefttmca mdl ON vvh.Veh_model = mdl.Mca_codig
    LEFT JOIN preftmcar car ON mdl.Mca_marca = car.Car_codig
");
$sql_permisos_mudanza->execute();
$result_permisos_mudanza = $sql_permisos_mudanza->get_result();


?>

<?php
include('conexion.php'); // Asegúrate de tener el archivo de conexión correcto

// Verificar si el usuario está logueado
if (!isset($_SESSION['cedula'])) {
    echo "Debe iniciar sesión para ver las constancias.";
    exit;
}

// Consulta para obtener las constancias de pobreza
$sql_constancia_pobreza = $conexion->prepare("
    SELECT 
        p.Per_cedul, p.Per_nombr, p.Per_apell, 
        s.Spo_codig, s.Spo_fsoli, s.Spo_statu, s.Spo_motiv, 
        s.Spo_femis, s.Spo_frech, s.Spo_motir
    FROM prefttspo s
    JOIN prefttcli c ON s.Spo_clien = c.Cli_codig
    JOIN preftmper p ON c.Cli_cedul = p.Per_cedul
");
$sql_constancia_pobreza->execute();
$result_constancia_pobreza = $sql_constancia_pobreza->get_result();
?>

<?php
include('conexion.php'); // Asegúrate de tener el archivo de conexión correcto

// Verificar si el usuario está logueado
if (!isset($_SESSION['cedula'])) {
    echo "Debe iniciar sesión para ver las constancias.";
    exit;
}

// Consulta para obtener las constancias de fe de vida
$sql_constancia_fe_vida = $conexion->prepare("
    SELECT 
        p.Per_cedul, p.Per_nombr, p.Per_apell, 
        f.Sfe_codig, f.Sfe_fsoli, f.Sfe_statu, f.Sfe_motiv, 
        f.Sfe_femis, f.Sfe_frech, f.Sfe_motir
    FROM prefttsfe f
    JOIN prefttcli c ON f.Sfe_clien = c.Cli_codig
    JOIN preftmper p ON c.Cli_cedul = p.Per_cedul
");
$sql_constancia_fe_vida->execute();
$result_constancia_fe_vida = $sql_constancia_fe_vida->get_result();
?>

<!DOCTYPE html>
<html lang="en">
    
<head>
		
		<!-- Meta -->
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
		
		<!-- Title -->
		<title>Parroquia Constitución</title>


		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-*****" crossorigin="anonymous" />
		<link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

		<!-- CSS Plugins -->
        <link rel="stylesheet" href="../assets/css/sesion/plugins/bootstrap.min.css">
        <link rel="stylesheet" href="../assets/css/sesion/plugins/font-awesome.css">
		<link rel="stylesheet" href="../assets/css/sesion/plugins/magnific-popup.css">
		<link rel="stylesheet" href="../assets/css/sesion/plugins/simplebar.css">
		<link rel="stylesheet" href="../assets/css/sesion/plugins/owl.carousel.min.css">
		<link rel="stylesheet" href="../assets/css/sesion/plugins/owl.theme.default.min.css">
		<link rel="stylesheet" href="../assets/css/sesion/plugins/jquery.animatedheadline.css">
		
		<!-- CSS Base -->
        <link rel="stylesheet" class="back-color" href="../assets/css/sesion/style-dark.css">
		<link rel="stylesheet" href="../assets/css/sesion/style-demo.css">
		
		<!-- Settings Style -->
		<link rel="stylesheet" class="posit-nav" href="../assets/css/sesion/settings/left-nav.css" />
		<link rel="stylesheet" class="theme-color" href="../assets/css/sesion/settings/green-color.css" />

		<!-- Icono de la pagina -->
        <link rel="icon" href="../assets/img/icono/logo-escudo.ico">

		<link rel="stylesheet" href="../assets/css/sesion/mostrar_denuncias_usu.css">
		<link rel="stylesheet" href="../assets/css/sesion/usuarios.css">
		
    </head>
    <body>
		
		<!--Theme Options Start-->
        <div class="style-options">
            <div class="toggle-btn">
                <span><i class="fas fa-cog"></i></span>
            </div>
            <div class="style-menu">
                
				<div class="style-back">
                    <h4 class="mt-85 mb-10">Modo</h4>
                    <ul>
                        <li><a href="../assets/css/sesion/style-dark.css"><i class="fas fa-moon"></i>Oscuro</a></li>
                        <li><a href="../assets/css/sesion/style-light.css"><i class="far fa-lightbulb"></i>Clarito</a></li>
                    </ul>
                </div>
				<div class="style-color">
                    <h4 class="mt-55 mb-10">Tema</h4>
                    <ul>
                        <li><a href="../assets/css/sesion/settings/green-color.css" style="background-color: #25ca7f;"></a></li>
						<li><a href="../assets/css/sesion/settings/blue-color.css" style="background-color: #00a3e1;"></a></li>
                        <li><a href="../assets/css/sesion/settings/red-color.css" style="background-color: #d94c48;"></a></li>
                        <li><a href="../assets/css/sesion/settings/purple-color.css" style="background-color: #bb68c8;"></a></li>
						<li><a href="../assets/css/sesion/settings/sea-color.css" style="background-color: #0dcdbd;"></a></li>
                        <li><a href="../assets/css/sesion/settings/yellow-color.css" style="background-color: #eae328;"></a></li>
                    </ul>
                </div>
            </div>
        </div>
		
		<!-- Preloader -->
		<div id="preloader">
  			<div class="loading-area">
    			<div class="circle"></div>
  			</div>
  			<div class="left-side"></div>
  			<div class="right-side"></div>
		</div>
		
		<!-- Main Site -->
		<div id="home">
  		<div id="about">
    	<div id="resume">
     	<div id="portfolio">
        <div id="blog">
		<div id="contact">
			
			<div class="header-mobile">
                <a class="header-toggle"><i class="fas fa-bars"></i></a>
                <h2>Prefectura</h2>
            </div>
			
			<!-- Left Block -->
			<nav class="header-main" data-simplebar>
		
				<!-- Logo -->
				<div class="logo">
            		<img src="../assets/img/logo-escudo.png" alt="">
            	</div>
				
          		<ul>
				  	<li data-tooltip="Inicio" data-position="top">
    					<a href="#home" class="icon-h fas fa-home"><br>Inicio</a>
					</li>

					<li data-tooltip="Denuncias" data-position="top">
    					<a href="#about" class="icon-a fas fa-exclamation-circle"><br>Denuncias</a>
					</li>

					<li data-tooltip="Trámites" data-position="top">
    					<a href="#resume" class="icon-r fas fa-file-alt"><br>Trámites</a>
					</li>

					<li data-tooltip="Usuarios" data-position="top">
    					<a href="#blog" class="icon-b fas fa-users"><br>Usuarios</a>
					</li>

					<li data-tooltip="Reportes estadísticos" data-position="top">
    					<a href="#portfolio" class="icon-p fas fa-chart-bar"><br>Reportes estadísticos</a>
					</li>

					<li data-tooltip="Mensajes" data-position="bottom">
    					<a href="#contact" class="icon-c fas fa-envelope"><br>Mensajes</a>
					</li>
					
					<li data-tooltip="Cerrar sesión" data-position="top">
    					<a href="cerrar_sesion.php" class="icon-logout fas fa-sign-out-alt">Cerrar Sesión</a>
					</li>

          		</ul>
				
				<!-- Sound wave -->
    			<!--<a class="music-bg">
      				<div class="lines">
        				<span></span>
        				<span></span>
        				<span></span>
        				<span></span>
						<span></span>
						<span></span>
      				</div>
					<p> Sound </p>
    			</a>-->
			 </nav>
			
			<!-- Home Section -->
          	<div class="pt-home" style="background-image: url('../assets/img/prefectura.jpg')">
             	<section>
					
					<!-- Banner -->
					<div class="banner">
  						<h1>Bienvenido Director@ Parroquial</h1>
						<p class="cd-headline rotate-1">
							<span>Un sistema</span>
							<span class="cd-words-wrapper">
								<b class="is-visible">Innovador</b>
								<b>Eficaz</b>
								<b>Práctico</b>
								<b>Seguro</b>
							</span>
						</p>
					</div>
					
					
		
					
			  	</section>  
          	</div>
			
			<!-- About Section -->
			<div class="page pt-about" data-simplebar>
				<section class="container">

				<div class="container_mostrar_denuncias_adm">
				    <h1>Listado de Denuncias</h1>
                    <?php if ($result_denuncias->num_rows > 0): ?>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
					            <th>N° Denuncia</th>
                                <th>Cédula del Denunciante</th>
                                <th>Cédula del Denunciado</th>
                                <th>Tipo de Denuncia</th>
                                <th>Motivo</th>
                                <th>Fecha</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $result_denuncias->fetch_assoc()): ?>
                            <tr>
						        <td><?php echo htmlspecialchars($row['Den_codig']); ?></td>
                                <td><?php echo htmlspecialchars($row['cedula_denunciante']); ?></td>
                                <td><?php echo htmlspecialchars($row['cedula_denunciado']); ?></td>
                                <td><?php echo obtenerTipoDenuncia($row['Den_tipod'], $conexion); ?></td>
                        <td><?php echo htmlspecialchars($row['Den_motiv']); ?></td>
                        <td><?php echo htmlspecialchars($row['Den_fecha']); ?></td>
                        <td>
						<form action="actualizar_estado.php" method="POST">
    						<input type="hidden" name="Den_codig" value="<?php echo $row['Den_codig']; ?>">
    						<select name="Den_statu" onchange="this.form.submit()">
        					<?php foreach ($estados as $codigo => $descripcion): ?>
            				<option value="<?php echo $codigo; ?>" <?php echo ($codigo == $row['Den_statu']) ? 'selected' : ''; ?>>
                			<?php echo $descripcion; ?>
            				</option>
        					<?php endforeach; ?>
    					</select>
						</form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No se encontraron denuncias.</p>
    <?php endif; ?>
    </div>
					
					<!-- Section Title -->
					<div class="header-page mt-70 mob-mt">
						<h2>En revision</h2>
						<span></span>
					</div>
				
					<!-- Personal Info Start -->
					<div class="row mt-100">
					
						<!-- Information Block -->
    <div class="container">
        
	<?php if ($result_denuncias_revision->num_rows > 0): ?>
    <?php while ($row = $result_denuncias_revision->fetch_assoc()): ?>
        <div class="col-lg-12 col-sm-12 en_revision">
            <div class="info box">
                <div class="row">
                    <div class="col-lg-3 col-sm-4">
                        <div class="photo">
                            <img alt="" src="../assets/img/icono/en_revision.png">        
                        </div>    
                    </div>
                    <div class="col-lg-9 col-sm-8">
                        <h4><?php echo htmlspecialchars($row['Den_codig']); ?></h4>
                        <div class="loc">
                            <i class="fas fa-map-marked-alt"></i> Número de denuncia
                        </div>
                        <div class="details-container">
                            <div class="details-box">
                                <p><strong>Datos del Denunciante:</strong><br>
                                Cédula: <?php echo htmlspecialchars($row['cedula_denunciante']); ?><br>
                                Nombre: <?php echo htmlspecialchars($row['nombre_denunciante'] ); ?><br>
                                Apellido: <?php echo htmlspecialchars($row['apellido_denunciante']); ?><br>
                                Teléfono: <?php echo htmlspecialchars($row['telefono_denunciante']); ?><br>
                                Dirección: <?php echo htmlspecialchars($row['aldea_denunciante'] . ', Calle ' . $row['calle_denunciante'] . ', Carrera ' . $row['carrera_denunciante'] . ', Casa ' . $row['casa_denunciante']); ?><br>
                                </p>
                            </div>
                            <div class="details-box">
                                <p><strong>Datos del Denunciado:</strong><br>
                                Cédula: <?php echo htmlspecialchars($row['cedula_denunciado']); ?><br>
                                Nombre: <?php echo htmlspecialchars($row['nombre_denunciado']); ?><br>
                                Apellido: <?php echo htmlspecialchars($row['apellido_denunciado']); ?><br>
                                Teléfono: <?php echo htmlspecialchars($row['telefono_denunciado']); ?><br>
                                Dirección: <?php echo htmlspecialchars($row['aldea_denunciado'] . ', Calle ' . $row['calle_denunciado'] . ', Carrera ' . $row['carrera_denunciado'] . ', Casa ' . $row['casa_denunciado']); ?><br>
                                </p>
                            </div>
                        </div>
                        <p><strong>Motivo de la Denuncia:</strong><br>
                        <?php echo htmlspecialchars($row['Den_motiv']); ?>
                        </p>
                    </div>
                    <!-- Icon Info -->
                    <div class="col-lg-3 col-sm-4">
                        <div class="info-icon">
                            <i class="fas fa-exclamation-circle"></i>
                            <div class="desc-icon">
                                <h6><?php echo htmlspecialchars(obtenerTipoDenuncia($row['Den_tipod'], $conexion)); ?></h6>
                                <p>Tipo de denuncia</p>
                            </div>
                        </div>
                    </div>
                    <!-- Icon Info -->
                    <div class="col-lg-3 col-sm-4">
                        <div class="info-icon">
                            <i class="fas fa-calendar"></i>
                            <div class="desc-icon">
                                <h6><?php echo htmlspecialchars($row['Den_fecha']); ?></h6>
                                <p>Fecha de la denuncia</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-12 pt-50">
                        <a href="actualizar_estado.php?Den_codig=<?php echo $row['Den_codig']; ?>&Den_statu=aprobada" class="btn-st">Aprobar</a>
                    </div>
                    <div class="col-lg-3 col-sm-12 pt-50">
                        <a href="actualizar_estado.php?Den_codig=<?php echo $row['Den_codig']; ?>&Den_statu=rechazada" class="btn-st">Rechazar</a>
                    </div>
					<div class="col-lg-3 col-sm-12 pt-50">
                            <a href="actualizar_estado.php?Den_codig=<?php echo $row['Den_codig']; ?>&Den_statu=directo_ministerio" class="btn-st">Directo al Ministerio</a>
                        </div>
                </div>
            </div>
        </div>
        <br><br>
    <?php endwhile; ?>
<?php else: ?>
    <p>No se encontraron denuncias en revisión.</p>
<?php endif; ?>

					

    </div>
					</div>

					<!-- Section Title -->
<div class="header-page mt-70 mob-mt">
    <h2>Citaciones</h2>
    <span></span>
</div>

<!-- Personal Info Start -->
<div class="row mt-100">

    <!-- Information Block -->
    <div class="container">

	<?php if ($result_denuncias_aprobadas->num_rows > 0): ?>
    <!-- Dentro del while loop -->
    <?php $index = 0; ?>
    <?php while ($row = $result_denuncias_aprobadas->fetch_assoc()): ?>
        <?php $index++; ?>
        <div class="col-lg-12 col-sm-12 en_revision">
            <div class="info box">
                <div class="row">
                    <!-- Detalles de la denuncia -->

                    <div class="col-lg-3 col-sm-4">
                        <div class="photo">
                            <img alt="" src="../assets/img/icono/calendario.png">
                        </div>
                    </div>

                    <div class="col-lg-9 col-sm-8">
                        <h4><?php echo htmlspecialchars($row['Den_codig']); ?></h4>
                        <div class="loc">
                            <i class="fas fa-map-marked-alt"></i> Número de denuncia
                        </div>
                        <p><strong>Motivo de la Denuncia:</strong><br>
                            <?php echo htmlspecialchars($row['Den_motiv']); ?>
                        </p>
                        <!-- Formulario para agregar citación -->
                        <h3>Realizar Citación</h3>
                        <form action="guardar_citacion.php" method="post">
                            <input type="hidden" name="Den_codig" value="<?php echo htmlspecialchars($row['Den_codig']); ?>">
                            <label for="Cit_fecha_<?php echo $index; ?>">Fecha de citación:</label>
                            <input type="date" id="Cit_fecha_<?php echo $index; ?>" name="Cit_fecha" required>
                            <label for="Cit_horad_<?php echo $index; ?>">Hora de citación:</label>
                            <input type="time" id="Cit_horad_<?php echo $index; ?>" name="Cit_horad" required>
                            <br><br>
                            <button type="submit" class="btn-st">Guardar citación</button>
                        </form>
                        <br><br><br>

                        <!-- Tabla de citaciones -->
                        <table class="table">
                            <thead>
                            <h3>Citaciones Realizadas</h3>
                            <tr>
                                <th>Fecha</th>
                                <th>Hora</th>
                                <th>Estatus</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            // Obtener citaciones asociadas con la denuncia y Dtd_rolde = 2
                            $sql_citaciones = $conexion->prepare("
                                SELECT 
                                    Cit_codig, Cit_fecha, Cit_horad, Cit_statu
                                FROM prefttcit 
                                WHERE Cit_perde IN (
                                    SELECT Dtd_codig
                                    FROM prefttdtd
                                    WHERE Dtd_denun = ? AND Dtd_rolde = 2
                                )
                            ");
                            $sql_citaciones->bind_param('i', $row['Den_codig']);
                            $sql_citaciones->execute();
                            $result_citaciones = $sql_citaciones->get_result();
                            $citacion_pendiente_codig = null;

                            if ($result_citaciones->num_rows > 0):
                                while ($citacion = $result_citaciones->fetch_assoc()):
                                    ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($citacion['Cit_fecha']); ?></td>
                                        <td><?php echo htmlspecialchars($citacion['Cit_horad']); ?></td>
                                        <td><?php echo htmlspecialchars($citacion['Cit_statu']); ?></td>
                                    </tr>
                                    <?php
                                    if ($citacion['Cit_statu'] == 'pendiente') {
                                        $citacion_pendiente_codig = $citacion['Cit_codig'];
                                    }
                                endwhile;
                            else:
                                ?>
                                <tr>
                                    <td colspan="4">No se encontraron citaciones para esta denuncia.</td>
                                </tr>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Apartado para procesar la citación pendiente -->
                    <?php if ($citacion_pendiente_codig): ?>
                        <div class="col-lg-12 pt-50">
                            <h3>Procesar Citación Pendiente</h3>
                            <!-- Testimonios -->
                            <form action="procesar_citacion.php" method="post" enctype="multipart/form-data">
                                <input type="hidden" name="Cit_codig"
                                       value="<?php echo htmlspecialchars($citacion_pendiente_codig); ?>">
                                <label for="Tmt_descr_<?php echo $index; ?>">Testimonios:</label>
                                <textarea id="Tmt_descr_<?php echo $index; ?>" name="Tmt_descr" rows="4" required></textarea>
                                <br><br>
                                <!-- Pruebas -->
                                <label for="Pru_descr_<?php echo $index; ?>">Descripción de la Prueba:</label>
                                <textarea id="Pru_descr_<?php echo $index; ?>" name="Pru_descr[]" rows="2" required></textarea>
                                <label for="Pru_fotop_<?php echo $index; ?>">Foto de la Prueba:</label>
                                <input type="file" id="Pru_fotop_<?php echo $index; ?>" name="Pru_fotop[]" accept="image/*" required>
                                <br><br>
                                <div id="mas_pruebas_<?php echo $index; ?>"></div>
                                <button type="button" onclick="agregarPrueba(<?php echo $index; ?>)">Agregar otra prueba</button>
                                <br><br>
                                <!-- Selección del fin de la denuncia -->
                                <label for="fin_denuncia_<?php echo $index; ?>">Seleccionar el fin de la denuncia:</label>
                                <select id="fin_denuncia_<?php echo $index; ?>" name="fin_denuncia" required onchange="mostrarFormulario(this.value, <?php echo $index; ?>)">
                                    <option value="">Seleccione una opción</option>
                                    <option value="conciliacion">Conciliación</option>
                                    <option value="sin_conciliacion">Sin conciliación</option>
                                </select>
                                <br><br>
                                <!-- Formulario de conciliación -->
                                <div id="form_conciliacion_<?php echo $index; ?>" style="display: none;">
                                    <label for="Acuerdos_<?php echo $index; ?>">Acuerdos:</label>
                                    <textarea id="Acuerdos_<?php echo $index; ?>" name="Acuerdos[]" rows="2"></textarea>
                                    <br><br>
                                    <div id="mas_acuerdos_<?php echo $index; ?>"></div>
                                    <button type="button" onclick="agregarAcuerdo(<?php echo $index; ?>)">Agregar otro acuerdo</button>
                                    <br><br>
                                </div>
                                <!-- Formulario sin conciliación -->
                                <div id="form_sin_conciliacion_<?php echo $index; ?>" style="display: none;">
                                    <label for="ministerio_<?php echo $index; ?>">Ministerio:</label>
                                    <select id="ministerio_<?php echo $index; ?>" name="ministerio">
                                        <?php
                                        // Obtener los ministerios de la tabla preftmdim
                                        $sql_ministerios = $conexion->query("SELECT Dim_codig, Dim_nombr FROM preftmdim");
                                        while ($ministerio = $sql_ministerios->fetch_assoc()) {
                                            echo '<option value="' . $ministerio['Dim_codig'] . '">' . $ministerio['Dim_nombr'] . '</option>';
                                        }
                                        ?>
                                    </select>
                                    <label for="motivo_<?php echo $index; ?>">Motivo:</label>
                                    <textarea id="motivo_<?php echo $index; ?>" name="motivo" rows="2"></textarea>
                                </div>
                                <br><br>
                                <button type="submit" class="btn-st">Procesar Citación</button>
								
								
                            </form>
							<div class="col-lg-3 col-sm-12 pt-50">
							<form action="reprogramar_denuncia.php" method="POST">
    							<input type="hidden" name="id_citacion" value="<?php echo htmlspecialchars($citacion_pendiente_codig); ?>">
    							<button type="submit" class="btn-st" name="reprogramar" value="Reprogramar">Reprogramar</button>
							</form>
							</div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <br><br>
    <?php endwhile; ?>

<?php else: ?>
    <p>No se encontraron denuncias para ser citadas.</p>
<?php endif; ?>

    </div>
</div>

<script>
    
    function agregarPrueba(index) {
        var div = document.createElement('div');
        div.innerHTML = '<label for="Pru_descr_' + index + '">Descripción de la Prueba:</label>' +
            '<textarea id="Pru_descr_' + index + '" name="Pru_descr[]" rows="2" required></textarea>' +
            '<label for="Pru_fotop_' + index + '">Foto de la Prueba:</label>' +
            '<input type="file" id="Pru_fotop_' + index + '" name="Pru_fotop[]" accept="image/*" required><br><br>';
        document.getElementById('mas_pruebas_' + index).appendChild(div);
    }

    function agregarAcuerdo(index) {
        var div = document.createElement('div');
        div.innerHTML = '<label for="Acuerdos_' + index + '">Acuerdos:</label>' +
            '<textarea id="Acuerdos_' + index + '" name="Acuerdos[]" rows="2"></textarea><br><br>';
        document.getElementById('mas_acuerdos_' + index).appendChild(div);
    }

    function mostrarFormulario(opcion, index) {
        var formConciliacion = document.getElementById('form_conciliacion_' + index);
        var formSinConciliacion = document.getElementById('form_sin_conciliacion_' + index);
        if (opcion === 'conciliacion') {
            formConciliacion.style.display = 'block';
            formSinConciliacion.style.display = 'none';
        } else if (opcion === 'sin_conciliacion') {
            formConciliacion.style.display = 'none';
            formSinConciliacion.style.display = 'block';
        } else {
            formConciliacion.style.display = 'none';
            formSinConciliacion.style.display = 'none';
        }
    }
</script>


			  	</section>
         	</div>

			<!-- fin about -->
			 
			<!-- Resume Section -->
          	<div class="page pt-resume" data-simplebar>
            	<section class="container">
					
					<!-- Section Title -->
					<div class="header-page mt-70 mob-mt">
						<h2>Trámites</h2>
    					<span></span>
					</div>

                    <div class="usuario">

                        
                    <div class="container_usuario">
    <div class="table-container">
        <button onclick="abrirModalAgregarConstancia()" class="btn-agregar-usuario">Agregar Constancia</button>
        <h2>Constancias de Notificación de Evento Público</h2>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Cédula del Solicitante</th>
                        <th>Nombre</th>
                        <th>Apellido</th>
                        <th>Fecha de solicitud</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result_constancia_evento->num_rows > 0): ?>
                        <?php while ($row = $result_constancia_evento->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['Per_cedul']); ?></td>
                                <td><?php echo htmlspecialchars($row['Per_nombr']); ?></td>
                                <td><?php echo htmlspecialchars($row['Per_apell']); ?></td>
                                <td><?php echo htmlspecialchars($row['Sep_fsoli']); ?></td>
                                <td>
                                    <div class="boton_de_estado_constancia">
                                        <select 
                                        style="background-color: <?php echo getEstadoColor($row['Sep_statu']); ?>;" 
                                        onchange="updateConstanciaStatusEvento('<?php echo $row['Sep_codig']; ?>', this, '<?php echo $row['Sep_sedeb']; ?>')">
                                        <?php echo getEstadoOptions($row['Sep_statu'], $row['Sep_sedeb']); ?>
                                    </select>

                                    </div>
                                </td>
                                
                                <td>
                                    <button class="btn-editar" onclick='openEditModal_3(<?php echo htmlspecialchars(json_encode($row)); ?>)'>Editar</button>
                                    <button class="btn-detalles" onclick="openDetailsModal_3(<?php echo htmlspecialchars(json_encode($row)); ?>)">Detalles</button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="6">No hay constancias de notificación de evento público.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="container_usuario">
    <div class="table-container">
        <button onclick="abrirModalAgregarConstancia()" class="btn-agregar-usuario">Agregar Constancia</button>
        <h2>Constancias de Desempleo</h2>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Cédula del Solicitante</th>
                        <th>Nombre</th>
                        <th>Apellido</th>
                        <th>Fecha de Solicitud</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result_constancia_desempleo->num_rows > 0): ?>
                        <?php while ($row = $result_constancia_desempleo->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['Per_cedul']); ?></td>
                                <td><?php echo htmlspecialchars($row['Per_nombr']); ?></td>
                                <td><?php echo htmlspecialchars($row['Per_apell']); ?></td>
                                <td><?php echo htmlspecialchars($row['Sds_fsoli']); ?></td>
                                <td>
                                    <div class="boton_de_estado_constancia">
                                        <select 
                                        style="background-color: <?php echo getEstadoColor($row['Sds_statu']); ?>;" 
                                        onchange="updateConstanciaStatusDesempleo('<?php echo $row['Sds_codig']; ?>', this, '<?php echo $row['Sds_sedeb']; ?>')">
                                        <?php echo getEstadoOptions($row['Sds_statu'], $row['Sds_sedeb']); ?>
                                    </select>
                                    </div>
                                </td>
                                <td>
                                    <button class="btn-editar" onclick='openEditModal_(<?php echo htmlspecialchars(json_encode($row)); ?>)'>Editar</button>
                                    <button class="btn-detalles" onclick="openDetailsModal_4(<?php echo htmlspecialchars(json_encode($row)); ?>)">Detalles</button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="6">No hay constancias de desempleo registradas.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="container_usuario">
    <div class="table-container">
        <h2>Constancias de Dependencia Económica</h2>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Cédula del Solicitante</th>
                        <th>Nombre</th>
                        <th>Apellido</th>
                        <th>Fecha de Solicitud</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result_constancia_dependencia->num_rows > 0): ?>
                        <?php while ($row = $result_constancia_dependencia->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['Per_cedul']); ?></td>
                                <td><?php echo htmlspecialchars($row['Per_nombr']); ?></td>
                                <td><?php echo htmlspecialchars($row['Per_apell']); ?></td>
                                <td><?php echo htmlspecialchars($row['Sde_fsoli']); ?></td>
                                <td>
                                    <div class="boton_de_estado_constancia">
                                        <select 
                                        style="background-color: <?php echo getEstadoColor($row['Sde_statu']); ?>;" 
                                        onchange="updateConstanciaStatusDependencia('<?php echo $row['Sde_codig']; ?>', this, '<?php echo $row['Sde_sedeb']; ?>')">
                                        <?php echo getEstadoOptions($row['Sde_statu'], $row['Sde_sedeb']); ?>
                                    </select>
                                    </div>
                                </td>
                                <td>
                                    <button class="btn-editar" onclick='openEditModal_(<?php echo htmlspecialchars(json_encode($row)); ?>)'>Editar</button>
                                    <button class="btn-detalles" onclick="openDetailsModalDependencia(<?php echo htmlspecialchars(json_encode($row)); ?>)">Detalles</button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="6">No hay constancias de dependencia económica registradas.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="container_usuario">
    <div class="table-container">
        <h2>Constancias de Asiento Permanente</h2>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Cédula del Solicitante</th>
                        <th>Nombre</th>
                        <th>Apellido</th>
                        <th>Fecha de Solicitud</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result_constancia_asiento->num_rows > 0): ?>
                        <?php while ($row = $result_constancia_asiento->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['Per_cedul']); ?></td>
                                <td><?php echo htmlspecialchars($row['Per_nombr']); ?></td>
                                <td><?php echo htmlspecialchars($row['Per_apell']); ?></td>
                                <td><?php echo htmlspecialchars($row['Sas_fsoli']); ?></td>
                                <td>
                                    <div class="boton_de_estado_constancia">
                                        <select 
                                        style="background-color: <?php echo getEstadoColor($row['Sas_statu']); ?>;" 
                                        onchange="updateConstanciaStatusAsiento('<?php echo $row['Sas_codig']; ?>', this, '<?php echo $row['Sas_sedeb']; ?>')">
                                        <?php echo getEstadoOptions($row['Sas_statu'], $row['Sas_sedeb']); ?>
                                    </select>
                                    </div>
                                </td>
                                <td>
                                    <button class="btn-editar" onclick='openEditModal_(<?php echo htmlspecialchars(json_encode($row)); ?>)'>Editar</button>
                                    <button class="btn-detalles" onclick="openDetailsModalAsiento(<?php echo htmlspecialchars(json_encode($row)); ?>)">Detalles</button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="6">No hay constancias de asiento permanente registradas.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="container_usuario">
    <div class="table-container">
        <h2>Constancias de Buena Conducta</h2>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Cédula del Solicitante</th>
                        <th>Nombre</th>
                        <th>Apellido</th>
                        <th>Fecha de Solicitud</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result_constancia_buena->num_rows > 0): ?>
                        <?php while ($row = $result_constancia_buena->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['Per_cedul']); ?></td>
                                <td><?php echo htmlspecialchars($row['Per_nombr']); ?></td>
                                <td><?php echo htmlspecialchars($row['Per_apell']); ?></td>
                                <td><?php echo htmlspecialchars($row['Sbc_fsoli']); ?></td>
                                <td>
                                    <div class="boton_de_estado_constancia">
                                        <select 
                                        style="background-color: <?php echo getEstadoColor($row['Sbc_statu']); ?>;" 
                                        onchange="updateConstanciaStatusBuena('<?php echo $row['Sbc_codig']; ?>', this, '<?php echo $row['Sbc_sedeb']; ?>')">
                                        <?php echo getEstadoOptions($row['Sbc_statu'], $row['Sbc_sedeb']); ?>
                                    </select>
                                    </div>
                                </td>
                                <td>
                                    <button class="btn-editar" onclick='openEditModal_(<?php echo htmlspecialchars(json_encode($row)); ?>)'>Editar</button>
                                    <button class="btn-detalles" onclick="openDetailsModalBuena(<?php echo htmlspecialchars(json_encode($row)); ?>)">Detalles</button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="6">No hay constancias de buena conducta registradas.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="container_usuario">
    <div class="table-container">
        <h2>Permisos de Mudanza</h2>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Cédula del Solicitante</th>
                        <th>Nombre</th>
                        <th>Apellido</th>
                        <th>Fecha de Solicitud</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result_permisos_mudanza->num_rows > 0): ?>
                        <?php while ($row = $result_permisos_mudanza->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['solicitante_cedula']); ?></td>
                                <td><?php echo htmlspecialchars($row['solicitante_nombre']); ?></td>
                                <td><?php echo htmlspecialchars($row['solicitante_apellido']); ?></td>
                                <td><?php echo htmlspecialchars($row['Smd_fsoli']); ?></td>
                                <td>
                                    <div class="boton_de_estado_constancia">
                                        <select 
                                            style="background-color: <?php echo getEstadoColor($row['Smd_statu']); ?>;" 
                                            onchange="updatePermisoMudanzaStatus('<?php echo $row['Smd_codig']; ?>', this, '<?php echo $row['Smd_sedeb']; ?>')">
                                            <?php echo getEstadoOptions($row['Smd_statu'], $row['Smd_sedeb']); ?>
                                        </select>
                                    </div>
                                </td>
                                <td>
                                    <button class="btn-editar" onclick='openEditModal_(<?php echo htmlspecialchars(json_encode($row)); ?>)'>Editar</button>
                                    <button class="btn-detalles" onclick="openDetailsModalMudanza(<?php echo htmlspecialchars(json_encode($row)); ?>)">Detalles</button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="6">No hay permisos de mudanza registrados.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="usuario">
    <div class="container_usuario">
        <div class="table-container">

            <h2>Constancias de Pobreza</h2>
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>Cédula del Solicitante</th>
                            <th>Nombre</th>
                            <th>Apellido</th>
                            <th>Fecha de Solicitud</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result_constancia_pobreza->num_rows > 0): ?>
                            <?php while ($row = $result_constancia_pobreza->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['Per_cedul']); ?></td>
                                    <td><?php echo htmlspecialchars($row['Per_nombr']); ?></td>
                                    <td><?php echo htmlspecialchars($row['Per_apell']); ?></td>
                                    <td><?php echo htmlspecialchars($row['Spo_fsoli']); ?></td>
                                    <td>
                                        <div class="boton_de_estado_constancia">
                                            <select 
                                                style="background-color: <?php echo getEstadoColorSinPago($row['Spo_statu']); ?>;" 
                                                onchange="updateConstanciaStatusPobreza('<?php echo $row['Spo_codig']; ?>', this)">
                                                <?php echo getEstadoOptionsSinPago($row['Spo_statu']); ?>
                                            </select>
                                        </div>
                                    </td>

                                    <td>
                                        <button class="btn-editar" onclick='openEditModal_(<?php echo htmlspecialchars(json_encode($row)); ?>)'>Editar</button>
                                        <button class="btn-detalles" onclick="openDetailsModalPobreza(<?php echo htmlspecialchars(json_encode($row)); ?>)">Detalles</button>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="6">No hay constancias de pobreza registradas.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="usuario">
    <div class="container_usuario">
        <div class="table-container">

            <h2>Constancias de Fe de Vida</h2>
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>Cédula del Solicitante</th>
                            <th>Nombre</th>
                            <th>Apellido</th>
                            <th>Fecha de Solicitud</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result_constancia_fe_vida->num_rows > 0): ?>
                            <?php while ($row = $result_constancia_fe_vida->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['Per_cedul']); ?></td>
                                    <td><?php echo htmlspecialchars($row['Per_nombr']); ?></td>
                                    <td><?php echo htmlspecialchars($row['Per_apell']); ?></td>
                                    <td><?php echo htmlspecialchars($row['Sfe_fsoli']); ?></td>
                                    <td>
                                        <div class="boton_de_estado_constancia">
                                            <select 
                                                style="background-color: <?php echo getEstadoColorSinPago($row['Sfe_statu']); ?>;" 
                                                onchange="updateConstanciaStatusFeVida('<?php echo $row['Sfe_codig']; ?>', this)">
                                                <?php echo getEstadoOptionsSinPago($row['Sfe_statu']); ?>
                                            </select>
                                        </div>
                                    </td>
                                    <td>
                                        <button class="btn-editar" onclick='openEditModal_(<?php echo htmlspecialchars(json_encode($row)); ?>)'>Editar</button>
                                        <button class="btn-detalles" onclick="openDetailsModalFeVida(<?php echo htmlspecialchars(json_encode($row)); ?>)">Detalles</button>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="6">No hay constancias de fe de vida registradas.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Detalles -->
<div id="detailsModal_4" class="modal_detalle">
    <div class="modal-content">
        <span class="close" onclick="closeDetailsModal_4()">&times;</span>
        <h3>Detalles de la Constancia de Desempleo</h3>
        <div id="modal-details-content_4" class="modal-scrollable"></div>
    </div>
</div>

<!-- Modal para Detalles -->
<div id="detailsModal_3" class="modal_detalle">
    <div class="modal-content">
        <span class="close" onclick="closeDetailsModal_3()">&times;</span>
        <h3>Detalles de la Constancia de Evento Público</h3>
        <div id="modal-details-content_3" class="modal-scrollable"></div>
    </div>
</div>

<!-- Modal para Detalles -->
<div id="detailsModalDependencia" class="modal_detalle">
    <div class="modal-content">
        <span class="close" onclick="closeDetailsModalDependencia()">&times;</span>
        <h3>Detalles de la Constancia de Dependencia Económica</h3>
        <div id="modal-details-content-dependencia" class="modal-scrollable">
        </div>
    </div>
</div>

<!-- Modal para Detalles de Asiento Permanente -->
<div id="detailsModalAsiento" class="modal_detalle">
    <div class="modal-content">
        <span class="close" onclick="closeDetailsModalAsiento()">&times;</span>
        <h3>Detalles de la Constancia de Asiento Permanente</h3>
        <div id="modal-details-content-asiento" class="modal-scrollable">
        </div>
    </div>
</div>

<!-- Modal para Detalles -->
<div id="detailsModalBuena" class="modal_detalle">
    <div class="modal-content">
        <span class="close" onclick="closeDetailsModalBuena()">&times;</span>
        <h3>Detalles de la Constancia de Buena Conducta</h3>
        <div id="modal-details-content-buena" class="modal-scrollable">
        </div>
    </div>
</div>

<!-- Modal para Detalles -->
<div id="detailsModalMudanza" class="modal_detalle">
    <div class="modal-content">
        <span class="close" onclick="closeDetailsModalMudanza()">&times;</span>
        <h3>Detalles del permiso de Mudanza</h3>
        <div id="modal-details-content-mudanza" class="modal-scrollable">
        </div>
    </div>
</div>

<!-- Modal para Detalles -->
<div id="detailsModalPobreza" class="modal_detalle">
    <div class="modal-content">
        <span class="close" onclick="closeDetailsModalPobreza()">&times;</span>
        <h3>Detalles de la Constancia de Pobreza</h3>
        <div id="modal-details-content-Pobreza" class="modal-scrollable"></div>
    </div>
</div>

<!-- Modal para Detalles -->
<div id="detailsModalFeVida" class="modal_detalle">
    <div class="modal-content">
        <span class="close" onclick="closeDetailsModalFeVida()">&times;</span>
        <h3>Detalles de la Constancia de Fe de Vida</h3>
        <div id="modal-details-content-FeVida" class="modal-scrollable"></div>
    </div>
</div>

<!-- Modal para Fotos -->
<div id="imageModal_2" class="modal_imagen">
    <span class="close" onclick="closeImageModal_2()">&times;</span>
    <img class="modal-content" id="modalImage_2">
    <a id="downloadLink_2" download="imagen.jpg" class="download-button">Descargar</a>
</div>

<!-- Modal de Confirmación -->
<div id="modalConfirmacionConstancia" class="modal_estado">
    <div class="modal-content">
        <span class="close" onclick="cerrarModalConstancia()">&times;</span>
        <div class="modal-icon">
            <i class='bx bx-check-circle' style="font-size: 40px; color: green;"></i>
        </div>
        <p>Estado actualizado correctamente</p>
    </div>
</div>

<!-- Modal para Motivo de Rechazo -->
<div id="modalMotivoRechazo" class="modal_motivo_rechazo">
    <div class="modal-content">
        <span class="close" onclick="cerrarModalMotivoRechazo()">&times;</span>
        <h3>Motivo del Rechazo</h3>
        <textarea id="motivoRechazo" rows="4" placeholder="Escriba el motivo del rechazo"></textarea>
        <button onclick="confirmarRechazo()" class="btn-confirmar">Aceptar</button>
    </div>
</div>

<!-- Modal para editar constancia -->
<div id="editModal_3" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeEditModal_3()">&times;</span>
        <h2>Editar Constancia</h2>
        <form id="editForm_3" method="post" action="constancias/actualizar_constancia_evento_edicion.php" enctype="multipart/form-data">
            <input type="hidden" name="codigo" id="editCodigo_3">

            <!-- Mostrar cédula del solicitante -->
            <div class="input-box">
                <label for="displayCedula_3"><h5>Cédula del Solicitante</h5></label>
                <input type="text" class="input-field" id="displayCedula_3" readonly>
            </div>

            <!-- Mostrar nombre del solicitante -->
            <div class="input-box">
                <label for="displayNombre_3"><h5>Nombre del Solicitante</h5></label>
                <input type="text" class="input-field" id="displayNombre_3" readonly>
            </div>

            <!-- Mostrar apellido del solicitante -->
            <div class="input-box">
                <label for="displayApellido_3"><h5>Apellido del Solicitante</h5></label>
                <input type="text" class="input-field" id="displayApellido_3" readonly>
            </div>

            <div class="input-box">
                <h5>Tipo de Evento</h5>
                <select class="input-field" name="tipo_evento" id="editTipoEvento_3">
                    <?php
                    include 'conexion.php';
                    $sql = "SELECT Tpe_codig, Tpe_nombr FROM preftmtpe";
                    $result = $conexion->query($sql);
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<option value='" . $row["Tpe_codig"] . "'>" . $row["Tpe_nombr"] . "</option>";
                        }
                    } else {
                        echo "<option value=''>No hay tipos de eventos disponibles</option>";
                    }
                    $conexion->close();
                    ?>
                </select>
            </div>

            <div class="input-box">
                <h5>Motivo</h5>
                <textarea class="input-field" name="motivo" id="editMotivo_3"></textarea>
            </div>

            <div class="input-box">
                <h5>Aldea</h5>
                <select class="input-field" name="aldea" id="editAldea_3">
                    <?php
                    include 'conexion.php';
                    $sql = "SELECT Ald_codig, Ald_nombr FROM preftmald";
                    $result = $conexion->query($sql);
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<option value='" . $row["Ald_codig"] . "'>" . $row["Ald_nombr"] . "</option>";
                        }
                    } else {
                        echo "<option value=''>No hay aldeas disponibles</option>";
                    }
                    $conexion->close();
                    ?>
                </select>
            </div>

            <div class="input-box">
                <h5>Calle</h5>
                <input type="text" class="input-field" name="calle" id="editCalle_3">
            </div>

            <div class="input-box">
                <h5>Carrera</h5>
                <input type="text" class="input-field" name="carrera" id="editCarrera_3">
            </div>

            <div class="input-box">
                <h5>Lugar</h5>
                <input type="text" class="input-field" name="lugar" id="editLugar_3">
            </div>

            <div class="input-box">
                <h5>Fecha de Inicio</h5>
                <input type="date" class="input-field" name="fecha_inicio" id="editFechaInicio_3">
            </div>

            <div class="input-box">
                <h5>Hora de Inicio</h5>
                <input type="time" class="input-field" name="hora_inicio" id="editHoraInicio_3">
            </div>

            <div class="input-box">
                <h5>Fecha de Fin</h5>
                <input type="date" class="input-field" name="fecha_fin" id="editFechaFin_3">
            </div>

            <div class="input-box">
                <h5>Hora de Fin</h5>
                <input type="time" class="input-field" name="hora_fin" id="editHoraFin_3">
            </div>

            <div class="input-box">
                <h5>Duración (Minutos)</h5>
                <input type="number" class="input-field" name="duracion" id="editDuracion_3" readonly>
            </div>

            <div class="input-box">
                <h5>Posible Asistencia</h5>
                <input type="number" class="input-field" name="asistencia" id="editAsistencia_3">
            </div>

            <div class="input-box">
    <h5>Foto del Baucher SEDB</h5>
    <input type="file" name="sedeb_foto" id="sedeb_foto" accept="image/*" onchange="handleImageUpload(event, 'previewSedeb', 'sedeb_foto')" />
    <div class="image-preview">
        <img id="previewSedeb" src="" alt="Vista previa del Baucher SEDB" style="display:none;" />
    </div>
</div>


            <div class="input-box">
                <input type="submit" class="submit" value="Actualizar">
            </div>
        </form>
    </div>
</div>






                    </div>
                    


			  	</section>
         	</div>
			 
			<!-- Portfolio Section -->
          	<div class="page pt-portfolio" data-simplebar>
            	<section class="container">
					
					<!-- Section Title -->
					<div class="header-page mt-70 mob-mt">
						<h2>Portfolio</h2>
    					<span></span>
					</div>
					
					<!-- Portfolio Filter Row Start -->
					<div class="row mt-100">
						<div class="col-lg-12 col-sm-12 portfolio-filter">
							<ul>
								<li class="active" data-filter="*">All</li>
								<li data-filter=".brand">Brand</li>
								<li data-filter=".design">Design</li>
								<li data-filter=".graphic">Graphic</li>
							</ul>
						</div>
					</div>
					
					<!-- Portfolio Item Row Start -->
					<div class="row portfolio-items mt-100 mb-100">
					
						<!-- Portfolio Item -->
						<div class="item col-lg-4 col-sm-6 graphic">
							<figure>
								<img alt="" src="img/portfolio/img-1.jpg">
								<figcaption>
									<h3>Project Name</h3>
									<p>Graphic</p><i class="fas fa-image"></i>
									<a class="image-link" href="../img/portfolio/img-1.jpg"></a>
								</figcaption>
							</figure>
						</div>
					
						<!-- Portfolio Item -->
						<div class="item col-lg-4 col-sm-6 design">
							<figure>
								<img alt="" src="img/portfolio/img-2.jpg">
									<figcaption>
									<h3>Project Name</h3>
									<p>Design</p><i class="fas fa-image"></i>
									<a class="image-link" href="../img/portfolio/img-2.jpg"></a>
								</figcaption>
							</figure>
						</div>
					
						<!-- Portfolio Item -->
						<div class="item col-lg-4 col-sm-6 brand">
							<figure>
								<img alt="" src="img/portfolio/img-3.jpg">
								<figcaption>
									<h3>Project Name</h3>
									<p>Graphic</p><i class="fas fa-video"></i>
									<a class="video-link" href="https://www.youtube.com/watch?v=k_okcNVZqqI"></a>
								</figcaption>
							</figure>
						</div>
					
						<!-- Portfolio Item -->
						<div class="item col-lg-4 col-sm-6 graphic">
							<figure>
								<img alt="" src="img/portfolio/img-4.jpg">
								<figcaption>
									<h3>Project Name</h3>
									<p>Design</p><i class="fas fa-image"></i>
									<a class="image-link" href="../img/portfolio/img-4.jpg"></a>
								</figcaption>
							</figure>
						</div>
					
						<!-- Portfolio Item -->
						<div class="item col-lg-4 col-sm-6 design">
							<figure>
								<img alt="" src="img/portfolio/img-5.jpg">
								<figcaption>
									<h3>Project Name</h3>
									<p>Design</p><i class="fas fa-video"></i>
									<a class="video-link" href="https://www.youtube.com/watch?v=k_okcNVZqqI"></a>
								</figcaption>
							</figure>
						</div>
					
						<!-- Portfolio Item -->
						<div class="item col-lg-4 col-sm-6 brand">
							<figure>
								<img alt="" src="img/portfolio/img-6.jpg">
								<figcaption>
									<h3>Project Name</h3>
									<p>Brand</p><i class="fas fa-image"></i>
									<a class="image-link" href="../img/portfolio/img-6.jpg"></a>
								</figcaption>
							</figure>
						</div>
					
						<!-- Portfolio Item -->
						<div class="item col-lg-4 col-sm-6 graphic">
							<figure>
								<img alt="" src="img/portfolio/img-7.jpg">
								<figcaption>
									<h3>Project Name</h3>
									<p>Brand</p><i class="fas fa-image"></i>
									<a class="image-link" href="img/portfolio/img-7.jpg"></a>
								</figcaption>
							</figure>
						</div>
					
						<!-- Portfolio Item -->
						<div class="item col-lg-4 col-sm-6 design">
							<figure>
								<img alt="" src="img/portfolio/img-8.jpg">
								<figcaption>
									<h3>Project Name</h3>
									<p>Brand</p><i class="fas fa-image"></i>
									<a class="image-link" href="../img/portfolio/img-8.jpg"></a>
								</figcaption>
							</figure>
						</div>
					
						<!-- Portfolio Item -->
						<div class="item col-lg-4 col-sm-6 brand">
							<figure>
								<img alt="" src="img/portfolio/img-9.jpg">
								<figcaption>
									<h3>Project Name</h3>
									<p>Graphic</p><i class="fas fa-image"></i>
									<a class="image-link" href="img/portfolio/img-9.jpg"></a>
								</figcaption>
							</figure>
						</div>
					</div>
				</section>
       	   	</div>
			 <!-- Blog Section -->
          	<div class="page pt-blog" data-simplebar>
            	<section class="container">
				    <div class="usuario">

                        <!-- Usuarios dentro de la parroquia-->
				        <div class="container_usuario">
                            <div class="table-container">
                                <!-- Botón para abrir el modal -->
                                <button onclick="abrirModalAgregarUsuario()" class="btn-agregar-usuario">Agregar Usuario</button>
                                <h2>Usuarios dentro de la parroquia</h2>
                                <div class="table-wrapper">
                                    <table>
                                        <thead>
                                            <tr>
                                                <th>Cédula</th>
                                                <th>Nombre</th>
                                                <th>Apellido</th>
                                                <th>Teléfono</th>
                                                <th>Estado</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if ($result_interior->num_rows > 0): ?>
                                            <?php while ($row = $result_interior->fetch_assoc()): ?>
                                                <tr>
                                                    <td><?php echo $row['Per_cedul']; ?></td>
                                                    <td><?php echo $row['Per_nombr']; ?></td>
                                                    <td><?php echo $row['Per_apell']; ?></td>
                                                    <td><?php echo $row['Per_telef']; ?></td>
                                                    <td>
                                                        <div class="boton_de_estado">
                                                            <select onchange="updateStatus('<?php echo $row['Per_cedul']; ?>', this)" 
                                                                style="background-color: <?php echo $row['Usu_statu'] === 'Activo' ? 'green' : 'red'; ?>; color: white;">
                                                                <option value="Activo" <?php echo $row['Usu_statu'] === 'Activo' ? 'selected' : ''; ?>>Activo</option>
                                                                <option value="Inactivo" <?php echo $row['Usu_statu'] === 'Inactivo' ? 'selected' : ''; ?>>Inactivo</option>
                                                            </select>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <button class="btn-editar" onclick='openEditModal(<?php echo json_encode($row); ?>)'>Editar</button>
                                                        <button class="btn-detalles" onclick="openDetailsModal_1(<?php echo htmlspecialchars(json_encode($row)); ?>)">Detalles</button>
                                                    </td>
                                                </tr>
                                            <?php endwhile; ?>
                                            <?php else: ?>
                                            <tr><td colspan="13">No hay usuarios dentro de la parroquia.</td></tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Usuarios fuera de la parroquia -->
                        <div class="container_usuario">
                            <div class="table-container">
                                <h2>Usuarios fuera de la parroquia</h2>
                                <div class="table-wrapper">
                                    <table>
                                        <thead>
                                            <tr>
                                                <th>Cédula</th>
                                                <th>Nombre</th>
                                                <th>Apellido</th>
                                                <th>Teléfono</th>
                                                <th>Estado</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if ($result_exterior->num_rows > 0): ?>
                                                <?php while ($row = $result_exterior->fetch_assoc()): ?>
                                                    <tr>
                                                        <td><?php echo $row['Per_cedul']; ?></td>
                                                        <td><?php echo $row['Per_nombr']; ?></td>
                                                        <td><?php echo $row['Per_apell']; ?></td>
                                                        <td><?php echo $row['Per_telef']; ?></td>
                                                        <td>
                                                            <div class="boton_de_estado">
                                                                <select onchange="updateStatus('<?php echo $row['Per_cedul']; ?>', this)" 
                                                                    style="background-color: <?php echo $row['Usu_statu'] === 'Activo' ? 'green' : 'red'; ?>; color: white;">
                                                                    <option value="Activo" <?php echo $row['Usu_statu'] === 'Activo' ? 'selected' : ''; ?>>Activo</option>
                                                                    <option value="Inactivo" <?php echo $row['Usu_statu'] === 'Inactivo' ? 'selected' : ''; ?>>Inactivo</option>
                                                                </select>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <button class="btn-editar" onclick='openEditModal(<?php echo json_encode($row); ?>)'>Editar</button>
                                                            <button class="btn-detalles" onclick="openDetailsModal(<?php echo htmlspecialchars(json_encode($row)); ?>)">Detalles</button>
                                                        </td>
                                                    </tr>
                                                <?php endwhile; ?>
                                            <?php else: ?>
                                                <tr><td colspan="6">No hay usuarios fuera de la parroquia.</td></tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Personas en la parroquia sin usuario -->
	                    <div class="container_usuario">
                            <div class="table-container">
                                <h2>Personas en la parroquia sin usuario</h2>
                                <div class="table-wrapper">
                                    <table>
                                        <thead>
                                            <tr>
                                                <th>Cédula</th>
                                                <th>Nombre</th>
                                                <th>Apellido</th>
                                                <th>Teléfono</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if ($result_sin_usuario->num_rows > 0): ?>
                                            <?php while ($row = $result_sin_usuario->fetch_assoc()): ?>
                                                <tr>
                                                    <td><?php echo $row['Per_cedul']; ?></td>
                                                    <td><?php echo $row['Per_nombr']; ?></td>
                                                    <td><?php echo $row['Per_apell']; ?></td>
                                                    <td><?php echo $row['Per_telef']; ?></td>
                                                    <td>
                                                        <button class="btn-editar" onclick='openEditModal_2(<?php echo json_encode($row); ?>)'>Editar</button>
                                                        <button class="btn-detalles" onclick="openDetailsModal_2(<?php echo htmlspecialchars(json_encode($row)); ?>)">Detalles</button>
                                                    </td>
                                                </tr>
                                            <?php endwhile; ?>
                                            <?php else: ?>
                                            <tr><td colspan="13">No hay usuarios dentro de la parroquia.</td></tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>


                        <!-- Modal para editar personas que no tienen usuario-->
                        <div id="editModal_2" class="modal">
                            <div class="modal-content">
                                <span class="close" onclick="closeEditModal_2()">&times;</span>
                                <h2>Editar Usuario</h2>
                                <form id="editForm" method="post" action="actualizar_usuario_ingresar.php" enctype="multipart/form-data">
                                    <input type="hidden" name="cedula" id="editCedula_2">

            		                <!-- Campo para mostrar la cédula actual (solo lectura) -->
                                    <div class="input-box">
                                        <label for="displayCedula_2"><h5>Cedula del usuario</h5></label>
                                        <input type="text" class="input-field" id="displayCedula_2" readonly>
                                    </div>
                                            
                                    <div class="input-box">
		                    		    <h5>Nombre(s)</h5>
                                    <input type="text" class="input-field" name="nombre" id="editNombre_2" placeholder="Nombre" required>
                                    </div>
                                    <div class="input-box">
	    	                        	<h5>Apellido(s)</h5>
                                        <input type="text" class="input-field" name="apellido" id="editApellido_2" placeholder="Apellido" required>
                                    </div>
                                    <div class="input-box">
			                        	<h5>Teléfono</h5>
                                        <input type="text" class="input-field" name="telefono" id="editTelefono_2" placeholder="Teléfono" required>
                                    </div>

                                    <!-- Fotos de Cédula y RIF -->
                                    <div class="input-box">
                                        <label for="editCfoto"><h5>Foto de la Cédula:</h5></label>
                                        <input type="file" class="input-field" name="cfoto" id="editCfoto_2">
                                    </div>
                                    <div class="input-box">
                                        <label for="editRfoto"><h5>Foto del RIF:</h5></label>
                                        <input type="file" class="input-field" name="rfoto" id="editRfoto_2">
                                    </div>

                                    <!-- Dirección Constitución -->
           
                                    <div class="input-box">
                                        <label><h5>Aldea</h5></label>
                                        <select class="input-field" name="aldea" id="editAldea_2">
                                            <?php
                                                // Incluir archivo de conexión
                                                include 'conexion.php';

                                                // Consulta para obtener las aldeas
                                                $sql = "SELECT Ald_codig, Ald_nombr FROM preftmald";
                                                $result = $conexion->query($sql);

                                                if ($result->num_rows > 0) {
                                                    // Imprimir opciones del select
                                                    while ($row = $result->fetch_assoc()) {
                                                    echo "<option value='" . $row["Ald_codig"] . "'>" . $row["Ald_nombr"] . "</option>";
                                                }
                                                } else {
                                                   echo "<option value=''>No hay aldeas disponibles</option>";
                                                }
        
                                                // Cerrar conexión
                                                $conexion->close();
                                            ?>
                                        </select>
                                    </div>

                                    <div class="input-box">
    				                    <h5>Calle</h5>
                                        <input type="text" class="input-field" name="calle1" id="editCalle1_2" placeholder="Calle">
                                    </div>
                                    <div class="input-box">
	    		                    	<h5>Carrera</h5>
                                        <input type="text" class="input-field" name="carre1" id="editCarre1_2" placeholder="Carrera">
                                    </div>
                                    <div class="input-box">
			                            <h5>Nº de casa</h5>
                                        <input type="text" class="input-field" name="ncasa1" id="editNcasa1_2" placeholder="Nº de casa">
                                    </div>

                                    <div class="input-box">
                                        <label><h5>Establecer como usuario</h5></label>
                                        <input type="checkbox" onclick="togglePasswordFields_2()">
                                    </div>
            
                                    <!-- Campos de contraseña, inicialmente ocultos -->
                                    <div id="passwordFields_2" style="display: none;">
                                        <div class="input-box">
                                            <label for="password"><h5>Contraseña</h5></label>
                                            <input type="password" class="input-field" name="password" id="password" placeholder="Contraseña">
                                        </div>
                                        <div class="input-box">
                                            <label for="confirmPassword"><h5>Repetir Contraseña</h5></label>
                                            <input type="password" class="input-field" name="confirmPassword" id="confirmPassword" placeholder="Repetir Contraseña">
                                        </div>
                                    </div>

                                    <div class="input-box">
                                        <input type="submit" class="submit" value="Actualizar">
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Modal para agregar usuario -->
				        <div id="modalAgregarUsuario" class="modal_ingreso">
  				            <div class="modal-content">
   				                <span class="close" onclick="cerrarModalAgregarUsuario()">&times;</span>
   				                <form method="post" action="agregar_usuario.php" enctype="multipart/form-data">
    				                <h3>Agregar Usuario</h3>

          				            <!-- Campos del formulario -->
            			            <div class="input-box">
               			                <input type="text" class="input-field" name="cedula" placeholder="Cédula de identidad" required>
            			            </div>
            			            <div class="input-box">
                    			        <input type="email" class="input-field" name="correo" placeholder="Correo electrónico" required>
            	    		        </div>
            		    	        <div class="input-box">
                		    	        <input type="password" class="input-field" name="contrasena" placeholder="Contraseña" required>
            			            </div>
            			            <div class="input-box">
                    			        <input type="text" class="input-field" name="nombre" placeholder="Nombre" required>
                			        </div>
                			        <div class="input-box">
                    			        <input type="text" class="input-field" name="apellido" placeholder="Apellido" required>
            		    	        </div>
            			            <div class="input-box">
                			            <input type="text" class="input-field" name="telefono" placeholder="Teléfono" required>
            			            </div>

                			        <!-- Fotos de usuario con vista previa -->
                			        <div class="input-box">
        	    		                <label>Foto de la cédula</label>
        		    	                <input type="file" name="cfoto" id="cfoto" accept="image/*" onchange="handleImageUpload(event, 'previewCfoto', 'cfoto')" />
        			                    <div class="image-preview">
            			                    <img id="previewCfoto" src="" alt="Vista previa de la cédula" style="display:none;" />
        			                    </div>
    			                    </div>
        			                <div class="input-box">
            			                <label>Foto del RIF</label>
            			                <input type="file" name="rfoto" id="rfoto" accept="image/*" onchange="handleImageUpload(event, 'previewRfoto', 'rfoto')" />
        	    		                <div class="image-preview">
            	    		                <img id="previewRfoto" src="" alt="Vista previa del RIF" style="display:none;" />
        			                    </div>
    			                    </div>

                                    <!-- Otros campos del formulario -->
                                    <div class="input-box">
                                        <label>Vive en:</label>
                                        <select class="input-field" name="residencia" onchange="toggleResidencia_u()">
                                            <option value="">Seleccione una opción</option>
                                            <option value="constitucion">Parroquia Constitución</option>
                                            <option value="fuera">Fuera de la parroquia</option>
                                        </select>
                                    </div>

                                    <!-- Dirección en Constitución -->
                                    <div id="direccion-constitucion" style="display: none;">
                                        <div class="input-box">
                                            <select class="input-field" name="aldea">
                                                <option value="">Seleccione una aldea</option>
                                                <?php
                                                    include 'conexion.php';
                                                    $sql = "SELECT Ald_codig, Ald_nombr FROM preftmald";
                                                    $result = $conexion->query($sql);
                                                    while ($row = $result->fetch_assoc()) {
                                                        echo "<option value='{$row['Ald_codig']}'>{$row['Ald_nombr']}</option>";
                                                    }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="input-box">
                                            <input type="text" class="input-field" name="calle1" placeholder="Calle">
                                        </div>
                                        <div class="input-box">
                                            <input type="text" class="input-field" name="carre1" placeholder="Carrera">
                                        </div>
                                        <div class="input-box">
                                            <input type="text" class="input-field" name="ncasa1" placeholder="Nº de casa">
                                        </div>
                                    </div>

                                    <!-- Dirección fuera de Constitución -->
                                    <div id="direccion-fuera" style="display: none;">
                                        <div class="input-box">
                                            <select class="input-field" name="municipio">
                                                <option value="">Seleccione un municipio</option>
                                                <?php
                                                    $sql = "SELECT Mun_codig, Mun_nombr FROM preftmmun";
                                                    $result = $conexion->query($sql);
                                                    while ($row = $result->fetch_assoc()) {
                                                        echo "<option value='{$row['Mun_codig']}'>{$row['Mun_nombr']}</option>";
                                                    }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="input-box">
                                            <input type="text" class="input-field" name="calle" placeholder="Calle">
                                        </div>
                                        <div class="input-box">
                                            <input type="text" class="input-field" name="carre" placeholder="Carrera">
                                        </div>
                                        <div class="input-box">
                                            <input type="text" class="input-field" name="ncasa" placeholder="Nº de casa">
                                        </div>
                                    </div>

                                    <div class="input-box">
                                        <input type="submit" class="submit" value="Registrar Usuario">
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Modal para Detalles -->
                        <div id="detailsModal" class="modal_detalle">
                            <div class="modal-content">
                                <span class="close" onclick="closeDetailsModal()">&times;</span>
                                <h3>Detalles del Usuario</h3>
                                <div id="modal-details-content" class="modal-scrollable"></div>
                            </div>
                        </div>

                        <!-- Modal para Detalles de personas sin usuario -->
                        <div id="detailsModal_2" class="modal_detalle">
                            <div class="modal-content">
                                <span class="close" onclick="closeDetailsModal_2()">&times;</span>
                                <h3>Detalles de la Persona</h3>
                                <div id="modal-details-content_2" class="modal-scrollable"></div>
                            </div>
                        </div>

                        

                        <!-- Modal de Confirmación -->
                        <div id="modalConfirmacion" class="modal_estado">
                            <div class="modal-content">
                                <span class="close" onclick="cerrarModal()">&times;</span>
                                <div class="modal-icon">
                                    <i class='bx bx-check-circle' style="font-size: 40px; color: green;"></i>
                                </div>
                                <p>Estado actualizado correctamente</p>
                            </div>
                        </div>
                                    
                        <!-- Modal para editar usuario-->
                    	<div id="editModal" class="modal">
                            <div class="modal-content">
                                <span class="close" onclick="closeEditModal()">&times;</span>
                                <h2>Editar Usuario</h2>
                                <form id="editForm" method="post" action="actualizar_usuario.php" enctype="multipart/form-data">
                                    <input type="hidden" name="cedula" id="editCedula">

			                        <!-- Campo para mostrar la cédula actual (solo lectura) -->
                                    <div class="input-box">
                                        <label for="displayCedula"><h5>Cedula del usuario</h5></label>
                                        <input type="text" class="input-field" id="displayCedula" readonly>
                                    </div>
                                    <div class="input-box">
		                            	<h5>Correo</h5>
                                        <input type="email" class="input-field" name="correo" id="editCorreo" placeholder="Correo" required>
                                    </div>
                                    <div class="input-box">
		                            	<h5>Nombre(s)</h5>
                                        <input type="text" class="input-field" name="nombre" id="editNombre" placeholder="Nombre" required>
                                    </div>
                                    <div class="input-box">
				                        <h5>Apellido(s)</h5>
                                        <input type="text" class="input-field" name="apellido" id="editApellido" placeholder="Apellido" required>
                                    </div>
                                    <div class="input-box">
			                            <h5>Teléfono</h5>
                                        <input type="text" class="input-field" name="telefono" id="editTelefono" placeholder="Teléfono" required>
                                    </div>

                                    <!-- Fotos de Cédula y RIF -->
                                    <div class="input-box">
                                        <label for="editCfoto"><h5>Foto de la Cédula:</h5></label>
                                        <input type="file" class="input-field" name="cfoto" id="editCfoto">
                                    </div>
                                    <div class="input-box">
                                        <label for="editRfoto"><h5>Foto del RIF:</h5></label>
                                        <input type="file" class="input-field" name="rfoto" id="editRfoto">
                                    </div>

                                    <!-- Dirección -->
                                    <div class="input-box">
                                        <label><h5>Residencia:</h5></label>
                                        <select class="input-field" name="residencia" id="editResidencia" onchange="toggleResidencia()">
                                            <option value="constitucion">Parroquia Constitución</option>
                                            <option value="fuera">Fuera de la parroquia</option>
                                        </select>
                                    </div>

                                    <!-- Dirección Constitución -->
                                    <div id="direccion-constitucion">
                                        <div class="input-box">
                                            <label><h5>Aldea</h5></label>
                                            <select class="input-field" name="aldea" id="editAldea">
                                                <?php
                                                    // Incluir archivo de conexión
                                                    include 'conexion.php';

                                                    // Consulta para obtener las aldeas
                                                    $sql = "SELECT Ald_codig, Ald_nombr FROM preftmald";
                                                    $result = $conexion->query($sql);

                                                    if ($result->num_rows > 0) {
                                                        // Imprimir opciones del select
                                                        while ($row = $result->fetch_assoc()) {
                                                            echo "<option value='" . $row["Ald_codig"] . "'>" . $row["Ald_nombr"] . "</option>";
                                                        }
                                                    } else {
                                                        echo "<option value=''>No hay aldeas disponibles</option>";
                                                    }
    
                                                    // Cerrar conexión
                                                    $conexion->close();
                                                ?>
                                            </select>
                                        </div>

                                        <div class="input-box">
				                            <h5>Calle</h5>
                                            <input type="text" class="input-field" name="calle1" id="editCalle1" placeholder="Calle">
                                        </div>
                                        <div class="input-box">
		                            	<h5>Carrera</h5>
                                            <input type="text" class="input-field" name="carre1" id="editCarre1" placeholder="Carrera">
                                        </div>
                                        <div class="input-box">
			                                <h5>Nº de casa</h5>
                                            <input type="text" class="input-field" name="ncasa1" id="editNcasa1" placeholder="Nº de casa">
                                        </div>
                                    </div>

                                    <!-- Dirección Fuera de la Parroquia -->
                                    <div id="direccion-fuera">
                                        <div class="input-box">
                                            <label><h5>Municipio</h5></label>
                                            <select class="input-field" id="editMunicipio" name="municipio">
                                                <option value="">Seleccione un municipio</option>
                                            
                                                <?php
                                                    // Incluir archivo de conexión
                                                    include 'conexion.php';

                                                    // Consulta para obtener los municipios
                                                    $sql = "SELECT Mun_codig, Mun_nombr FROM preftmmun";
                                                    $result = $conexion->query($sql);

                                                    if ($result->num_rows > 0) {
                                                        // Imprimir opciones del select
                                                        while ($row = $result->fetch_assoc()) {
                                                            echo "<option value='" . $row["Mun_codig"] . "'>" . $row["Mun_nombr"] . "</option>";
                                                        }
                                                    } else {
                                                        echo "<option value=''>No hay municipios disponibles</option>";
                                                    }

                                                    // Cerrar conexión
                                                    $conexion->close();
                                                ?>
                                            </select>
                                        </div>

                                        <div class="input-box">
				                            <h5>Calle</h5>
                                            <input type="text" class="input-field" name="calle" id="editCalle" placeholder="Calle">
                                        </div>
                                        <div class="input-box">
		                                    <h5>Carrera</h5>
                                            <input type="text" class="input-field" name="carre" id="editCarre" placeholder="Carrera">
                                        </div>
                                        <div class="input-box">
			                                <h5>Nº de casa</h5>
                                            <input type="text" class="input-field" name="ncasa" id="editNcasa" placeholder="Nº de casa">
                                        </div>
                                        </div>

                                        <div class="input-box">
                                            <input type="submit" class="submit" value="Actualizar">
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Para editar a las personas que tienen usuario-->
                        <script>
                            function openEditModal(userData) {
	                            console.log(userData);

    	                        document.getElementById('displayCedula').value = userData.Per_cedul; // Campo de solo lectura

                                document.getElementById('editCedula').value = userData.Per_cedul;
                                document.getElementById('editCorreo').value = userData.Usu_corre;
                                document.getElementById('editNombre').value = userData.Per_nombr;
                                document.getElementById('editApellido').value = userData.Per_apell;
                                document.getElementById('editTelefono').value = userData.Per_telef;

                                // Mostrar la dirección correcta según la residencia
                                if (userData.Din_aldea) {
                                    document.getElementById('editResidencia').value = 'constitucion';
                                    document.getElementById('editAldea').value = userData.Din_aldea;
                                    document.getElementById('editCalle1').value = userData.Din_calle;
                                    document.getElementById('editCarre1').value = userData.Din_carre;
                                    document.getElementById('editNcasa1').value = userData.Din_ncasa;
                                    toggleResidencia();
                                } else if (userData.Die_munic) {
                                    document.getElementById('editResidencia').value = 'fuera';
                                    document.getElementById('editMunicipio').value = userData.Die_munic;
                                    document.getElementById('editCalle').value = userData.Die_calle;
                                    document.getElementById('editCarre').value = userData.Die_carre;
                                    document.getElementById('editNcasa').value = userData.Die_ncasa;
                                    toggleResidencia();
                                }

                                document.getElementById('editModal').style.display = 'block';
                            }

                            function closeEditModal() {
                                document.getElementById('editModal').style.display = 'none';
                            }

                            function toggleResidencia() {
                                const residencia = document.getElementById('editResidencia').value;
                                document.getElementById('direccion-constitucion').style.display = residencia === 'constitucion' ? 'block' : 'none';
                                document.getElementById('direccion-fuera').style.display = residencia === 'fuera' ? 'block' : 'none';
                            }

                        </script>

                        <!-- Para editar a las personas que no tienen usuario-->
                        <script>
                            function openEditModal_2(userData) {
	                            console.log(userData);

	                            document.getElementById('displayCedula_2').value = userData.Per_cedul; // Campo de solo lectura
                            
                                document.getElementById('editCedula_2').value = userData.Per_cedul;
    
                                document.getElementById('editNombre_2').value = userData.Per_nombr;
                                document.getElementById('editApellido_2').value = userData.Per_apell;
                                document.getElementById('editTelefono_2').value = userData.Per_telef;
                                document.getElementById('passwordFields_2').style.display = 'none';
                                document.getElementById('editAldea_2').value = userData.Din_aldea;
                                document.getElementById('editCalle1_2').value = userData.Din_calle;
                                document.getElementById('editCarre1_2').value = userData.Din_carre;
                                document.getElementById('editNcasa1_2').value = userData.Din_ncasa;
                                document.getElementById('editModal_2').style.display = 'block';
                            }

                            function closeEditModal_2() {
                                document.getElementById('editModal_2').style.display = 'none';
                            }

                            function togglePasswordFields_2() {
                                const passwordFields = document.getElementById('passwordFields_2');
                                passwordFields.style.display = passwordFields.style.display === 'none' ? 'block' : 'none';
                            }

                        </script>

                        <!--Para el estado del usuario-->
                        <script>
                            function updateStatus(cedula, estadoSelect) {
                                const estado = estadoSelect.value;
                                estadoSelect.style.backgroundColor = estado === 'Activo' ? 'green' : 'red';

                                const xhr = new XMLHttpRequest();
                                xhr.open("POST", "actualizar_usuario.php", true);
                                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                                xhr.onreadystatechange = function() {
                                    if (xhr.readyState === 4 && xhr.status === 200) {
                                        mostrarModal(); // Muestra el modal cuando el estado se actualiza
                                    }
                                };
                                xhr.send("cedula=" + cedula + "&estado=" + estado);
                            }

                            // Función para mostrar el modal
                            function mostrarModal() {
                                const modal = document.getElementById("modalConfirmacion");
                                modal.style.display = "block";

                                // Cierra automáticamente el modal después de 2 segundos
                                setTimeout(() => {
                                    cerrarModal();
                                }, 2000);
                            }

                            // Función para cerrar el modal
                            function cerrarModal() {
                                const modal = document.getElementById("modalConfirmacion");
                                modal.style.display = "none";
                            }
                        </script>

                        <script>
                            function abrirModalAgregarUsuario() {
                                document.getElementById("modalAgregarUsuario").style.display = "block";
                            }

                            function cerrarModalAgregarUsuario() {
                                document.getElementById("modalAgregarUsuario").style.display = "none";
                            }

                            function toggleResidencia_u() {
                                const residencia = document.querySelector("select[name='residencia']").value;
                                document.getElementById("direccion-constitucion").style.display = residencia === "constitucion" ? "block" : "none";
                                document.getElementById("direccion-fuera").style.display = residencia === "fuera" ? "block" : "none";
                            }
                        </script>

                        

                        <!-- Para mostrar los detalles de las personas dentro de la parroquia-->
                        <script>
                            function openDetailsModal_1(user) {
                                const modal = document.getElementById("detailsModal");
                                const modalContent = document.getElementById("modal-details-content");

                                modalContent.innerHTML = `
                                    <table>
                                        <tr>
                                            <th colspan="2">Datos Personales</th>
                                        </tr>
                                        <tr>
                                            <td><strong>Cédula:</strong> ${user.Per_cedul}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Nombre(s):</strong> ${user.Per_nombr}</td>
                                            <td><strong>Apellido(s):</strong>${user.Per_apell}</td>
                                        </tr>
                                        <tr>
                                            <td colspan="2">
                                                <div class="image-container">
                                                    <div class="image-item">
                                                        <p><strong>Foto Cédula:</strong></p>
                                                        <img src="${user.Per_cfoto}" alt="Foto Cédula" class="imagen-tabla" onclick="openImageModal('${user.Per_cfoto}')">
                                                    </div>
                                                    <div class="image-item">
                                                        <p><strong>Foto RIF:</strong></p>
                                                        <img src="${user.Per_rifpe}" alt="Foto RIF" class="imagen-tabla" onclick="openImageModal('${user.Per_rifpe}')">
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th colspan="2">Dirección</th>
                                        </tr>
                                        <tr>
                                            <td><strong>Aldea:</strong> ${user.Din_aldea}</td>
                                            <td><strong>Calle:</strong> ${user.Din_calle}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Carrera:</strong> ${user.Din_carre}</td>
                                            <td><strong>Número de Casa:</strong> ${user.Din_ncasa}</td>
                                        </tr>
                                        <tr>
                                            <th colspan="2">Información de Contacto</th>
                                        </tr>
                                        <tr>
                                            <td><strong>Teléfono:</strong> ${user.Per_telef}</td>
                                            <td><strong>Correo:</strong> ${user.Usu_corre}</td>
                                        </tr>
                                    </table>
                                `;

                                modal.style.display = "block";
                            }

                            function closeDetailsModal() {
                                document.getElementById("detailsModal_1").style.display = "none";
                            }

                            function openImageModal(imageSrc) {
                                const modal = document.getElementById("imageModal");
                                const modalImage = document.getElementById("modalImage");
                                const downloadLink = document.getElementById("downloadLink");

                                modal.style.display = "block";
                                modalImage.src = imageSrc;
                                downloadLink.href = imageSrc;
                            }

                            function closeImageModal() {
                                document.getElementById("imageModal").style.display = "none";
                            }

                        </script>

                        <!-- Para mostrar los detalles de las personas fuera de la parroquia -->
                        <script>
                            function openDetailsModal(user) {
                                const modal = document.getElementById("detailsModal");
                                const modalContent = document.getElementById("modal-details-content");

                                modalContent.innerHTML = `
                                    <table>
                                        <tr>
                                            <th colspan="2">Datos Personales</th>
                                        </tr>
                                        <tr>
                                            <td><strong>Cédula:</strong> ${user.Per_cedul}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Nombre(s):</strong> ${user.Per_nombr}</td>
                                            <td><strong>Apellido(s):</strong>${user.Per_apell}</td>
                                        </tr>
                                        <tr>
                                            <td colspan="2">
                                                <div class="image-container">
                                                    <div class="image-item">
                                                        <p><strong>Foto Cédula:</strong></p>
                                                        <img src="${user.Per_cfoto}" alt="Foto Cédula" class="imagen-tabla" onclick="openImageModal('${user.Per_cfoto}')">
                                                    </div>
                                                    <div class="image-item">
                                                        <p><strong>Foto RIF:</strong></p>
                                                        <img src="${user.Per_rifpe}" alt="Foto RIF" class="imagen-tabla" onclick="openImageModal('${user.Per_rifpe}')">
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th colspan="2">Dirección</th>
                                        </tr>
                                        <tr>
                                            <td><strong>Municipio:</strong> ${user.Die_munic}</td>
                                            <td><strong>Calle:</strong> ${user.Die_calle}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Carrera:</strong> ${user.Die_carre}</td>
                                            <td><strong>Número de Casa:</strong> ${user.Die_ncasa}</td>
                                        </tr>
                                        <tr>
                                            <th colspan="2">Información de Contacto</th>
                                        </tr>
                                        <tr>
                                            <td><strong>Teléfono:</strong> ${user.Per_telef}</td>
                                            <td><strong>Correo:</strong> ${user.Usu_corre}</td>
                                        </tr>
                                    </table>
                                `;

                                modal.style.display = "block";
                            }

                            function closeDetailsModal() {
                                document.getElementById("detailsModal").style.display = "none";
                            }

                            function openImageModal(imageSrc) {
                                const modal = document.getElementById("imageModal");
                                const modalImage = document.getElementById("modalImage");
                                const downloadLink = document.getElementById("downloadLink");

                                modal.style.display = "block";
                                modalImage.src = imageSrc;
                                downloadLink.href = imageSrc;
                            }

                            function closeImageModal() {
                                document.getElementById("imageModal").style.display = "none";
                            }

                        </script>

                        
			
                    </div>
            	</section>
			</div>
			 
			<!-- Contact Section -->
         	<div class="page pt-contact" data-simplebar>
            	<section class="container">
					
					<!-- Section Title -->
              		<div class="header-page mt-70 mob-mt">
						<h2>Contact</h2>
    					<span></span>
					</div>
					
					<!-- Form Start -->
					<div class="row mt-100">
						<div class="col-lg-12 col-sm-12">
							<div class="contact-form ">
                        		<form method="post" class="box contact-valid" id="contact-form">
									<div class="row">
                            			<div class="col-lg-6 col-sm-12">
                                			<input type="text" name="name" id="name" class="form-control" placeholder="Name *">
                            			</div>
                            			<div class="col-lg-6 col-sm-12">
                                			<input type="email" name="email" id="email" class="form-control" placeholder="Email *">
                            			</div>
                            			<div class="col-lg-12 col-sm-12">
                                			<textarea class="form-control" name="note"  id="note" placeholder="Your Message"></textarea>
                            			</div>
                             			<div class="col-lg-12 col-sm-12 text-center">
                                			<button type="submit" class="btn-st">Send Message</button>
                                			<div id="loader">
                                    			<i class="fas fa-sync"></i>
                                			</div>
                            			</div>
										<div class="col-lg-12 col-sm-12">
                            				<div class="error-messages">
                                				<div id="success">
													<i class="far fa-check-circle"></i>Thank you, your message has been sent.
												</div>
                                				<div id="error">
													<i class="far fa-times-circle"></i>Error occurred while sending email. Please try again later.
												</div>
											</div>
                            			</div>
									</div>
                    			</form>
                    		</div>
						</div>
					</div>
					
					<!-- Contact Info -->
					<div class="box contact-info">
						<div class="row">
							<div class="col-lg-4 col-sm-12 info">
								<i class="fas fa-paper-plane"></i>
         						<p>example@example.com</p>
          						<span>Email</span>
							</div>
							<div class="col-lg-4 col-sm-12 info">
								<i class="fas fa-map-marker-alt"></i>
         						<p>123 Lorem Ipsum, USA</p>
          						<span>Addres</span>
							</div>	
							<div class="col-lg-4 col-sm-12 info">
								<i class="fas fa-phone"></i>
         						<p>(+1) 123 456 7890</p>
          						<span>Phone</span>
							</div>	
						</div>
					</div>
					
					<!--Google Map Start-->
					<div class="google-map box mt-100 mb-100">
						<div class="row">
							<div class="col-lg-12">
								<div id="map" data-latitude="40.712775" data-longitude="-74.005973" data-zoom="14"></div>
							</div>
						</div>
					</div>
            	</section>
          	</div> 
			
        </div>
      	</div>
    	</div>
  		</div>
		</div>
		</div>

        
		
            <!-- Modal para Fotos -->
            <div id="imageModal" class="modal_imagen">
                            <span class="close" onclick="closeImageModal()">&times;</span>
                            <img class="modal-content" id="modalImage">
                            <a id="downloadLink" download="imagen.jpg" class="download-button">Descargar</a>
                        </div>

            <!-- Modal de Confirmación -->
            <div id="modalConfirmacionEdicion" class="modal_estado">
                            <div class="modal-content">
                                <span class="close" onclick="closemodalConfirmacionEdicionl()">&times;</span>
                                <div class="modal-icon">
                                    <i class='bx bx-check-circle' style="font-size: 40px; color: green;"></i>
                                </div>
                                
        <p id="messageBody"></p>
                            </div>
                        </div>

<!-- Modal de Advertencia -->
<div id="modalAdvertenciaEdicion" class="modal_estado">
    <div class="modal-content">
        <span class="close" onclick="closeModalAdvertenciaEdicion()">&times;</span>
        <div class="modal-icon">
            <i class='bx bx-error-circle' style="font-size: 40px; color: red;"></i>
        </div>
        <p id="advertenciaMensaje"></p>
    </div>
</div>

            <!-- Para mostrar los detalles de las personas que no tienen usuario-->
            <script>
                            function openDetailsModal_2(user) {
                                const modal = document.getElementById("detailsModal_2");
                                const modalContent = document.getElementById("modal-details-content_2");

                                modalContent.innerHTML = `
                                    <table>
                                        <tr>
                                            <th colspan="2">Datos Personales</th>
                                        </tr>
                                        <tr>
                                            <td><strong>Cédula:</strong> ${user.Per_cedul}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Nombre(s):</strong> ${user.Per_nombr}</td>
                                            <td><strong>Apellido(s):</strong>${user.Per_apell}</td>
                                        </tr>
                                        <tr>
                                            <td colspan="2">
                                                <div class="image-container">
                                                    <div class="image-item">
                                                        <p><strong>Foto Cédula:</strong></p>
                                                        <img src="${user.Per_cfoto}" alt="Foto Cédula" class="imagen-tabla" onclick="openImageModal('${user.Per_cfoto}')">
                                                    </div>
                                                    <div class="image-item">
                                                        <p><strong>Foto RIF:</strong></p>
                                                        <img src="${user.Per_rifpe}" alt="Foto RIF" class="imagen-tabla" onclick="openImageModal('${user.Per_rifpe}')">
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th colspan="2">Dirección</th>
                                        </tr>
                                        <tr>
                                            <td><strong>Aldea:</strong> ${user.Din_aldea}</td>
                                            <td><strong>Calle:</strong> ${user.Din_calle}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Carrera:</strong> ${user.Din_carre}</td>
                                            <td><strong>Número de Casa:</strong> ${user.Din_ncasa}</td>
                                        </tr>
                                        <tr>
                                            <th colspan="2">Información de Contacto</th>
                                        </tr>
                                        <tr>
                                            <td><strong>Teléfono:</strong> ${user.Per_telef}</td>
                                            <td><strong>Correo:</strong> ${user.Usu_corre}</td>
                                        </tr>
                                    </table>
                                `;

                                modal.style.display = "block";
                            }

                            function closeDetailsModal_2() {
                                document.getElementById("detailsModal_2").style.display = "none";
                            }

                            function openImageModal(imageSrc) {
                                const modal = document.getElementById("imageModal");
                                const modalImage = document.getElementById("modalImage");
                                const downloadLink = document.getElementById("downloadLink");

                                modal.style.display = "block";
                                modalImage.src = imageSrc;
                                downloadLink.href = imageSrc;
                            }

                            function closeImageModal() {
                                document.getElementById("imageModal").style.display = "none";
                            }

                        </script>

<!-- para redimensionar la imagen antes de enviarla al servidor. Vamos a usar un canvas para modificar 
                        la resolución de la imagen y luego convertimos la imagen redimensionada en un archivo Blob, que se puede enviar al servidor.-->
                        <script>
                            function handleImageUpload(event, previewId, inputId) {
                                const file = event.target.files[0];
                                if (file && file.type.startsWith("image")) {
                                    const reader = new FileReader();
                                    reader.onload = function(e) {
                                        const img = new Image();
                                        img.onload = function() {
                                            const canvas = document.createElement("canvas");
                                            const ctx = canvas.getContext("2d");

                                            // Cambiar tamaño de la imagen (por ejemplo, 500x500 píxeles)
                                            const MAX_WIDTH = 500;
                                            const MAX_HEIGHT = 500;
                                            let width = img.width;
                                            let height = img.height;

                                            if (width > height) {
                                                if (width > MAX_WIDTH) {
                                                    height *= MAX_WIDTH / width;
                                                    width = MAX_WIDTH;
                                                }
                                            } else {
                                                if (height > MAX_HEIGHT) {
                                                    width *= MAX_HEIGHT / height;
                                                    height = MAX_HEIGHT;
                                                }
                                            }

                                            canvas.width = width;
                                            canvas.height = height;
                                            ctx.drawImage(img, 0, 0, width, height);

                                            // Convertir la imagen redimensionada a Blob y reemplazar el archivo
                                            canvas.toBlob(function(blob) {
                                                const newFile = new File([blob], file.name, { type: file.type });
                                                const dataTransfer = new DataTransfer();
                                                dataTransfer.items.add(newFile);
                                                document.getElementById(inputId).files = dataTransfer.files;
                                                                                                        
                                                // Vista previa de la imagen
                                                const previewElement = document.getElementById(previewId);
                                                previewElement.src = URL.createObjectURL(blob);
                                                previewElement.style.display = "block";
                                            }, file.type);
                                        };
                                        img.src = e.target.result;
                                    };
                                    reader.readAsDataURL(file);
                                }
                            }

                        </script>

        <script>
    function openDetailsModal_3(constancia) {
    const modal = document.getElementById("detailsModal_3");
    const modalContent = document.getElementById("modal-details-content_3");

    modalContent.innerHTML = `
        <table>
            <tr>
                <th colspan="2">Datos del Solicitante</th>
            </tr>
            <tr>
                <td><strong>Cédula:</strong> ${constancia.Per_cedul}</td>
            </tr>
            <tr>
                <td><strong>Nombre:</strong> ${constancia.Per_nombr}</td>
                <td><strong>Apellido:</strong> ${constancia.Per_apell}</td>
            </tr>
            <tr>
                <th colspan="2">Información del Evento Público</th>
            </tr>
            <tr>
                <td><strong>Tipo de Evento:</strong> ${constancia.Tpe_nombr}</td>
                <td><strong>Motivo:</strong> ${constancia.Sep_motiv}</td>
            </tr>
            <tr>
                <td><strong>Aldea:</strong> ${constancia.Ald_nombr}</td>
                <td><strong>Calle:</strong> ${constancia.Sep_calle}</td>
            </tr>
            <tr>
                <td><strong>Carrera:</strong> ${constancia.Sep_carre}</td>
                <td><strong>Lugar:</strong> ${constancia.Sep_delug}</td>
            </tr>
            <tr>
                <td><strong>Fecha de Inicio:</strong> ${constancia.Sep_finic}</td>
                <td><strong>Hora de Inicio:</strong> ${constancia.Sep_hinic}</td>
            </tr>
            <tr>
                <td><strong>Fecha de Fin:</strong> ${constancia.Sep_ffinl}</td>
                <td><strong>Hora de Fin:</strong> ${constancia.Sep_hfinl}</td>
            </tr>
            <tr>
                <td><strong>Duración (Minutos):</strong> ${constancia.Sep_durac }</td>
                <td><strong>Posible Asistencia:</strong> ${constancia.Sep_asist}</td>
            </tr>
            <tr>
                <td colspan="2">
                    <div class="image-container">
                        <div class="image-item">
                            <p><strong>Bauché Sedebat:</strong></p>
                            ${constancia.Sep_sedeb ? `<img src="${constancia.Sep_sedeb}" alt="Bauché Sedebat" class="imagen-tabla" onclick="openImageModal('${constancia.Sep_sedeb}')">` : '<p>No disponible</p>'}
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <th colspan="2">Fechas de Solicitud</th>
            </tr>
            <tr>
                <td><strong>Fecha de Solicitud:</strong> ${constancia.Sep_fsoli ? constancia.Sep_fsoli : 'No disponible'}</td>
                <td><strong>Fecha de Emisión:</strong> ${constancia.Sep_femis ? constancia.Sep_femis : 'No disponible'}</td>
            </tr>
            <tr>
                <td><strong>Fecha de Rechazo:</strong> ${constancia.Sep_frech ? constancia.Sep_frech : 'No disponible'}</td>
                <td><strong>Motivo de Rechazo:</strong> ${constancia.Sep_motir ? constancia.Sep_motir : 'No disponible'}</td>
            </tr>
        </table>
    `;

    modal.style.display = "block";
}
        function closeDetailsModal_3() {
            document.getElementById("detailsModal_3").style.display = "none";
        }

        

        
    </script>

<script>
function openDetailsModalDependencia(constancia) {
    const modal = document.getElementById("detailsModalDependencia");
    const modalContent = document.getElementById("modal-details-content-dependencia");

    // Realiza una consulta Ajax para obtener los datos de la persona independiente y los testigos
    fetch(`constancias/constancia_dependencia/get_detalles_dependencia.php?sde_codig=${constancia.Sde_codig}`)
        .then(response => response.json())
        .then(data => {
            const { independiente, testigos } = data;

            // Testigos HTML
            let testigosHtml = testigos.length > 0 ? testigos.map((testigo, index) => `
                <tr>
                    <th colspan="2">Testigo ${index + 1}</th>
                </tr>
                <tr>
                    <td><strong>Cédula:</strong> ${testigo.Per_cedul}</td>
                </tr>
                <tr>
                    <td><strong>Nombre:</strong> ${testigo.Per_nombr}</td>
                    <td><strong>Apellido:</strong> ${testigo.Per_apell}</td>
                </tr>
            `).join('') : '<tr><td colspan="2">No hay testigos disponibles.</td></tr>';

            // Renderizar detalles
            modalContent.innerHTML = `
                <table>
                    <tr>
                        <th colspan="2">Datos del Solicitante</th>
                    </tr>
                    <tr>
                        <td><strong>Cédula:</strong> ${constancia.Per_cedul}</td>
                    </tr>
                    <tr>
                        <td><strong>Nombre:</strong> ${constancia.Per_nombr}</td>
                        <td><strong>Apellido:</strong> ${constancia.Per_apell}</td>
                    </tr>
                    <tr>
                        <th colspan="2">Motivo de la Constancia</th>
                    </tr>
                    <tr>
                        <td colspan="2">${constancia.Sde_motiv}</td>
                    </tr>
                    <tr>
                        <th colspan="2">Información de la Persona Independiente</th>
                    </tr>
                    <tr>
                        <td><strong>Cédula:</strong> ${independiente.Per_cedul}</td>
                    </tr>
                    <tr>
                        <td><strong>Nombre:</strong> ${independiente.Per_nombr}</td>
                        <td><strong>Apellido:</strong> ${independiente.Per_apell}</td>
                    </tr>
                    ${testigosHtml}
                    <tr>
                        <td colspan="2">
                            <div class="image-container">
                                <div class="image-item">
                                    <p><strong>Bauché Sedebat:</strong></p>
                                    ${constancia.Sde_sedeb ? `<img src="${constancia.Sde_sedeb}" alt="Bauché Sedebat" class="imagen-tabla" onclick="openImageModal('${constancia.Sde_sedeb}')">` : '<p>No disponible</p>'}
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th colspan="2">Fechas</th>
                    </tr>
                    <tr>
                        <td><strong>Fecha de Solicitud:</strong> ${constancia.Sde_fsoli || 'No disponible'}</td>
                        <td><strong>Fecha de Emisión:</strong> ${constancia.Sde_femis || 'No disponible'}</td>
                    </tr>
                    <tr>
                        <td><strong>Fecha de Rechazo:</strong> ${constancia.Sde_frech || 'No disponible'}</td>
                        <td><strong>Motivo de Rechazo:</strong> ${constancia.Sde_motir || 'No disponible'}</td>
                    </tr>
                </table>
            `;

            modal.style.display = "block";
        });
}

function closeDetailsModalDependencia() {
    document.getElementById("detailsModalDependencia").style.display = "none";
}
</script>

<script>
function openDetailsModalAsiento(constancia) {
    const modal = document.getElementById("detailsModalAsiento");
    const modalContent = document.getElementById("modal-details-content-asiento");

    // Realiza una consulta Ajax para obtener los datos del solicitante, persona difunta y testigos
    fetch(`constancias/constancia_asiento/get_detalles_asiento.php?sas_codig=${constancia.Sas_codig}`)
        .then(response => response.json())
        .then(data => {
            const { difunto, testigos } = data;

            // Testigos HTML
            let testigosHtml = testigos.length > 0 ? testigos.map((testigo, index) => `
                <tr>
                    <th colspan="2">Testigo ${index + 1}</th>
                </tr>
                <tr>
                    <td><strong>Cédula:</strong> ${testigo.Per_cedul}</td>
                </tr>
                <tr>
                    <td><strong>Nombre:</strong> ${testigo.Per_nombr}</td>
                    <td><strong>Apellido:</strong> ${testigo.Per_apell}</td>
                </tr>
            `).join('') : '<tr><td colspan="2">No hay testigos disponibles.</td></tr>';

            // Renderizar detalles
            modalContent.innerHTML = `
                <table>
                    <tr>
                        <th colspan="2">Datos del Solicitante</th>
                    </tr>
                    <tr>
                        <td><strong>Cédula:</strong> ${constancia.Per_cedul}</td>
                    </tr>
                    <tr>
                        <td><strong>Nombre:</strong> ${constancia.Per_nombr}</td>
                        <td><strong>Apellido:</strong> ${constancia.Per_apell}</td>
                    </tr>
                    <tr>
                        <th colspan="2">Motivo de la Constancia</th>
                    </tr>
                    <tr>
                        <td colspan="2">${constancia.Sas_motiv}</td>
                    </tr>
                    <tr>
                        <th colspan="2">Información de la Persona Difunta</th>
                    </tr>
                    <tr>
                        <td><strong>Cédula:</strong> ${difunto.Per_cedul}</td>
                    </tr>
                    <tr>
                        <td><strong>Nombre:</strong> ${difunto.Per_nombr}</td>
                        <td><strong>Apellido:</strong> ${difunto.Per_apell}</td>
                    </tr>
                    <tr>
                        <td><strong>Fecha de Fallecimiento:</strong> ${difunto.Pdi_ffall}</td>
                        <td><strong>Hora de Fallecimiento:</strong> ${difunto.Pdi_hfall}</td>
                    </tr>
                    <tr>
                        <td><strong>N° Acta de Defunción:</strong> ${difunto.Pdi_nacta}</td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <div class="image-container">
                                <div class="image-item">
                                    <p><strong>Foto Acta de Defunción:</strong></p>
                                    ${difunto.Pdi_fotoa ? `<img src="${difunto.Pdi_fotoa}" alt="Foto Acta" class="imagen-tabla" onclick="openImageModal('${difunto.Pdi_fotoa}')">` : '<p>No disponible</p>'}
                                </div>
                            </div>
                        </td>
                    </tr>
                    ${testigosHtml}
                    <tr>
                        <td colspan="2">
                            <div class="image-container">
                                <div class="image-item">
                                    <p><strong>Bauché Sedebat:</strong></p>
                                    ${constancia.Sas_sedeb ? `<img src="${constancia.Sas_sedeb}" alt="Bauché Sedebat" class="imagen-tabla" onclick="openImageModal('${constancia.Sas_sedeb}')">` : '<p>No disponible</p>'}
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th colspan="2">Fechas</th>
                    </tr>
                    <tr>
                        <td><strong>Fecha de Solicitud:</strong> ${constancia.Sas_fsoli || 'No disponible'}</td>
                        <td><strong>Fecha de Emisión:</strong> ${constancia.Sas_femis || 'No disponible'}</td>
                    </tr>
                    <tr>
                        <td><strong>Fecha de Rechazo:</strong> ${constancia.Sas_frech || 'No disponible'}</td>
                        <td><strong>Motivo de Rechazo:</strong> ${constancia.Sas_motir || 'No disponible'}</td>
                    </tr>
                </table>
            `;

            modal.style.display = "block";
        });
}

function closeDetailsModalAsiento() {
    document.getElementById("detailsModalAsiento").style.display = "none";
}
</script>

<script>
function openDetailsModalBuena(constancia) {
    const modal = document.getElementById("detailsModalBuena");
    const modalContent = document.getElementById("modal-details-content-buena");

    // Realiza una consulta Ajax para obtener los datos de los testigos
    fetch(`constancias/constancia_buena/get_testigos_buena.php?sbc_codig=${constancia.Sbc_codig}`)
        .then(response => response.json())
        .then(data => {
            const { testigos } = data;

            let testigosHtml = testigos.map((testigo, index) => `
                <tr>
                    <th colspan="2">Testigo ${index + 1}</th>
                </tr>
                <tr>
                    <td><strong>Cédula:</strong> ${testigo.Per_cedul}</td>
                </tr>
                <tr>
                    <td><strong>Nombre:</strong> ${testigo.Per_nombr}</td>
                    <td><strong>Apellido:</strong> ${testigo.Per_apell}</td>
                </tr>
            `).join('');

            modalContent.innerHTML = `
                <table>
                    <tr>
                        <th colspan="2">Datos del Solicitante</th>
                    </tr>
                    <tr>
                        <td><strong>Cédula:</strong> ${constancia.Per_cedul}</td>
                    </tr>
                    <tr>
                        <td><strong>Nombre:</strong> ${constancia.Per_nombr}</td>
                        <td><strong>Apellido:</strong> ${constancia.Per_apell}</td>
                    </tr>
                    <tr>
                        <th colspan="2">Motivo de la Constancia</th>
                    </tr>
                    <tr>
                        <td colspan="2">${constancia.Sbc_motiv}</td>
                    </tr>
                    ${testigosHtml}
                    <tr>
                        <td colspan="2">
                            <div class="image-container">
                                <div class="image-item">
                                    <p><strong>Bauché Sedebat:</strong></p>
                                    ${constancia.Sbc_sedeb ? `<img src="${constancia.Sbc_sedeb}" alt="Bauché Sedebat" class="imagen-tabla" onclick="openImageModal('${constancia.Sbc_sedeb}')">` : '<p>No disponible</p>'}
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th colspan="2">Fechas</th>
                    </tr>
                    <tr>
                        <td><strong>Fecha de Solicitud:</strong> ${constancia.Sbc_fsoli || 'No disponible'}</td>
                        <td><strong>Fecha de Emisión:</strong> ${constancia.Sbc_femis || 'No disponible'}</td>
                    </tr>
                    <tr>
                        <td><strong>Fecha de Rechazo:</strong> ${constancia.Sbc_frech || 'No disponible'}</td>
                        <td><strong>Motivo de Rechazo:</strong> ${constancia.Sbc_motir || 'No disponible'}</td>
                    </tr>
                </table>
            `;

            modal.style.display = "block";
        });
}

function closeDetailsModalBuena() {
    document.getElementById("detailsModalBuena").style.display = "none";
}
</script>

<script>
function openDetailsModalMudanza(permiso) {
    const modal = document.getElementById("detailsModalMudanza");
    const modalContent = document.getElementById("modal-details-content-mudanza");

    // Realiza una consulta Ajax para obtener los bienes asociados
    fetch(`constancias/constancia_mudanza/lista_bienes.php?smd_codig=${permiso.Smd_codig}`)
        .then(response => response.json())
        .then(bienes => {
            let bienesHtml = bienes.length > 0 ? bienes.map((bien, index) => `
                <tr>
                    <td>${index + 1}</td>
                    <td>${bien.nombre_bien}</td>
                    <td>${bien.cantidad}</td>
                </tr>
            `).join('') : '<tr><td colspan="3">No hay bienes disponibles.</td></tr>';

    modalContent.innerHTML = `
        <table>
            <tr>
                <th colspan="2">Datos del Solicitante</th>
            </tr>
            <tr>
                <td><strong>Cédula:</strong> ${permiso.solicitante_cedula}</td>
            </tr>
            <tr>
                <td><strong>Nombre:</strong> ${permiso.solicitante_nombre}</td>
                <td><strong>Apellido:</strong> ${permiso.solicitante_apellido}</td>
            </tr>
            <tr>
                <th colspan="2">Datos del Punto de Partida</th>
            </tr>
            <tr>
                <td><strong>Aldea:</strong> ${permiso.partida_aldea || 'No disponible'}</td>
            </tr>
            <tr>
                <th colspan="2">Datos del Destino</th>
            </tr>
            <tr>
                <td><strong>Lugar:</strong> ${permiso.destino_lugar || 'No disponible'}</td>
                <td><strong>Municipio:</strong> ${permiso.destino_municipio || 'No disponible'}</td>
            </tr>
            <tr>
                <th colspan="2">Datos del Conductor</th>
            </tr>
            <tr>
                <td><strong>Cédula:</strong> ${permiso.conductor_cedula || 'No disponible'}</td>
            </tr>
            <tr>
                <td><strong>Nombre:</strong> ${permiso.conductor_nombre || 'No disponible'}</td>
                <td><strong>Apellido:</strong> ${permiso.conductor_apellido || 'No disponible'}</td>
            </tr>
            <tr>
                <td><strong>Teléfono:</strong> ${permiso.conductor_telefono || 'No disponible'}</td>
            </tr>
            <tr>
                <th colspan="2">Datos del Vehículo</th>
            </tr>
            <tr>
                <td><strong>Marca:</strong> ${permiso.vehiculo_marca || 'No disponible'}</td>
                <td><strong>Modelo:</strong> ${permiso.vehiculo_modelo || 'No disponible'}</td>
            </tr>
            <tr>
                <td><strong>Año:</strong> ${permiso.vehiculo_año || 'No disponible'}</td>
                <td><strong>Color:</strong> ${permiso.vehiculo_color || 'No disponible'}</td>
            </tr>
            <tr>
                <td><strong>Clase:</strong> ${permiso.vehiculo_clase || 'No disponible'}</td>
                <td><strong>Placa:</strong> ${permiso.vehiculo_placa || 'No disponible'}</td>
            </tr>
            <tr>
                <td><strong>Serial Motor:</strong> ${permiso.vehiculo_serial_motor || 'No disponible'}</td>
                <td><strong>Serial Carrocería:</strong> ${permiso.vehiculo_serial_carroceria || 'No disponible'}</td>
            </tr>
            <tr>
                <td colspan="2">
                    <div class="image-container">
                        <div class="image-item">
                            <p><strong>Bauché Sedebat:</strong></p>
                            ${permiso.Smd_sedeb ? `<img src="${permiso.Smd_sedeb}" alt="Bauché Sedebat" class="imagen-tabla" onclick="openImageModal('${permiso.Smd_sedeb}')">` : '<p>No disponible</p>'}
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <th colspan="2">Fechas</th>
            </tr>
            <tr>
                <td><strong>Fecha de Solicitud:</strong> ${permiso.Smd_fsoli || 'No disponible'}</td>
                <td><strong>Fecha de Emisión:</strong> ${permiso.Smd_femis || 'No disponible'}</td>
            </tr>
            <tr>
                <td><strong>Fecha de Rechazo:</strong> ${permiso.Smd_frech || 'No disponible'}</td>
                <td><strong>Motivo de Rechazo:</strong> ${permiso.Smd_motir || 'No disponible'}</td>
            </tr>
        </table>
        <table class="tabla-bienes">
            <tr>
                <th colspan="3">Lista de Bienes</th>
            </tr>
            <tr>
                <th>N°</th>
                <th>Nombre del Bien</th>
                <th>Cantidad</th>
            </tr>
            ${bienesHtml}
        </table>
    `;

    modal.style.display = "block";
});
}


function closeDetailsModalMudanza() {
    document.getElementById("detailsModalMudanza").style.display = "none";
}
</script>

<script>
    function openDetailsModalPobreza(constancia) {
        const modal = document.getElementById("detailsModalPobreza");
        const modalContent = document.getElementById("modal-details-content-Pobreza");

        modalContent.innerHTML = `
            <table>
                <tr>
                    <th colspan="2">Datos del Solicitante</th>
                </tr>
                <tr>
                    <td><strong>Cédula:</strong> ${constancia.Per_cedul}</td>
                </tr>
                <tr>
                    <td><strong>Nombre:</strong> ${constancia.Per_nombr}</td>
                    <td><strong>Apellido:</strong> ${constancia.Per_apell}</td>
                </tr>
                <tr>
                    <th colspan="2">Información de la Constancia</th>
                </tr>
                <tr>
                    <td><strong>Motivo:</strong> ${constancia.Spo_motiv}</td>
                    <td><strong>Estado:</strong> ${constancia.Spo_statu}</td>
                </tr>
                <tr>
                    <th colspan="2">Fechas</th>
                </tr>
                <tr>
                    <td><strong>Fecha de Solicitud:</strong> ${constancia.Spo_fsoli ? constancia.Spo_fsoli : 'No disponible'}</td>
                    <td><strong>Fecha de Emisión:</strong> ${constancia.Spo_femis ? constancia.Spo_femis : 'No disponible'}</td>
                </tr>
                <tr>
                    <td><strong>Fecha de Rechazo:</strong> ${constancia.Spo_frech ? constancia.Spo_frech : 'No disponible'}</td>
                    <td><strong>Motivo de Rechazo:</strong> ${constancia.Spo_motir ? constancia.Spo_motir : 'No disponible'}</td>
                </tr>
            </table>
        `;

        modal.style.display = "block";
    }

    function closeDetailsModalPobreza() {
        document.getElementById("detailsModalPobreza").style.display = "none";
    }
</script>

<script>
    function openDetailsModalFeVida(constancia) {
        const modal = document.getElementById("detailsModalFeVida");
        const modalContent = document.getElementById("modal-details-content-FeVida");

        modalContent.innerHTML = `
            <table>
                <tr>
                    <th colspan="2">Datos del Solicitante</th>
                </tr>
                <tr>
                    <td><strong>Cédula:</strong> ${constancia.Per_cedul}</td>
                </tr>
                <tr>
                    <td><strong>Nombre:</strong> ${constancia.Per_nombr}</td>
                    <td><strong>Apellido:</strong> ${constancia.Per_apell}</td>
                </tr>
                <tr>
                    <th colspan="2">Información de la Constancia</th>
                </tr>
                <tr>
                    <td><strong>Motivo:</strong> ${constancia.Sfe_motiv}</td>
                    <td><strong>Estado:</strong> ${constancia.Sfe_statu}</td>
                </tr>
                <tr>
                    <th colspan="2">Fechas</th>
                </tr>
                <tr>
                    <td><strong>Fecha de Solicitud:</strong> ${constancia.Sfe_fsoli ? constancia.Sfe_fsoli : 'No disponible'}</td>
                    <td><strong>Fecha de Emisión:</strong> ${constancia.Sfe_femis ? constancia.Sfe_femis : 'No disponible'}</td>
                </tr>
                <tr>
                    <td><strong>Fecha de Rechazo:</strong> ${constancia.Sfe_frech ? constancia.Sfe_frech : 'No disponible'}</td>
                    <td><strong>Motivo de Rechazo:</strong> ${constancia.Sfe_motir ? constancia.Sfe_motir : 'No disponible'}</td>
                </tr>
            </table>
        `;

        modal.style.display = "block";
    }

    function closeDetailsModalFeVida() {
        document.getElementById("detailsModalFeVida").style.display = "none";
    }
</script>

<script>
let estadoTemporal = null; // Almacena temporalmente el estado para rechazo
let selectTemporal = null; // Almacena el select del que proviene la solicitud

// Inicio constancia evento
function updateConstanciaStatusEvento(sepCodig, estadoSelect, sedebArchivo) {
    const nuevoEstado = estadoSelect.value;

    // Si se selecciona "Rechazada", abrir el modal para escribir el motivo
    if (nuevoEstado === "Rechazada") {
        estadoTemporal = { sepCodig, sedebArchivo };
        selectTemporal = estadoSelect;
        abrirModalMotivoRechazo();
        return; // Detener el cambio de estado hasta que se confirme el motivo
    }

    enviarEstadoEvento(sepCodig, nuevoEstado, estadoSelect, sedebArchivo, "");
}

function enviarEstadoEvento(sepCodig, nuevoEstado, estadoSelect, sedebArchivo, motivoRechazo) {
    const botonEstado = estadoSelect;

    const colores = {
        'Enviada': 'blue',
        'En revision': 'orange',
        'Rechazada': 'red',
        'Aprobada pendiente de pago': 'darkgreen',
        'Pago en revision': 'lightgreen',
        'Finalizada': 'white'
    };

    const xhr = new XMLHttpRequest();
    xhr.open("POST", "constancias/constancia_evento/actualizar_estado_evento.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4) {
            if (xhr.status === 200 && xhr.responseText.trim() === "success") {
                botonEstado.style.backgroundColor = colores[nuevoEstado] || 'gray';
                const opcionesActualizadas = generarOpcionesEstado(nuevoEstado, sedebArchivo);
                estadoSelect.innerHTML = opcionesActualizadas;
                mostrarModalConstancia();
            } else {
                console.error('Error al actualizar el estado');
            }
        }
    };

    xhr.send(`Sep_codig=${sepCodig}&Sep_statu=${nuevoEstado}&Sep_sedeb=${sedebArchivo}&motivo_rechazo=${motivoRechazo}`);
}
// Fin constancia evento

// Inicio constancia desempleo
function updateConstanciaStatusDesempleo(sepCodig, estadoSelect, sedebArchivo) {
    const nuevoEstado = estadoSelect.value;

    // Si se selecciona "Rechazada", abrir el modal para escribir el motivo
    if (nuevoEstado === "Rechazada") {
        estadoTemporal = { sepCodig, sedebArchivo };
        selectTemporal = estadoSelect;
        abrirModalMotivoRechazo();
        return; // Detener el cambio de estado hasta que se confirme el motivo
    }

    enviarEstadoDesempleo(sepCodig, nuevoEstado, estadoSelect, sedebArchivo, "");
}

function enviarEstadoDesempleo(sepCodig, nuevoEstado, estadoSelect, sedebArchivo, motivoRechazo) {
    const botonEstado = estadoSelect;

    const colores = {
        'Enviada': 'blue',
        'En revision': 'orange',
        'Rechazada': 'red',
        'Aprobada pendiente de pago': 'darkgreen',
        'Pago en revision': 'lightgreen',
        'Finalizada': 'white'
    };

    const xhr = new XMLHttpRequest();
    xhr.open("POST", "constancias/constancia_desempleo/actualizar_estado_desempleo.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4) {
            if (xhr.status === 200 && xhr.responseText.trim() === "success") {
                botonEstado.style.backgroundColor = colores[nuevoEstado] || 'gray';
                const opcionesActualizadas = generarOpcionesEstado(nuevoEstado, sedebArchivo);
                estadoSelect.innerHTML = opcionesActualizadas;
                mostrarModalConstancia();
            } else {
                console.error('Error al actualizar el estado');
            }
        }
    };

    xhr.send(`Sds_codig=${sepCodig}&Sds_statu=${nuevoEstado}&Sds_sedeb=${sedebArchivo}&motivo_rechazo=${motivoRechazo}`);
}
// Fin constancia desempleo

// Inicio constancia dependencia económica
function updateConstanciaStatusDependencia(sdeCodig, estadoSelect, sedebArchivo) {
    const nuevoEstado = estadoSelect.value;

    // Si se selecciona "Rechazada", abrir el modal para escribir el motivo
    if (nuevoEstado === "Rechazada") {
        estadoTemporal = { sdeCodig, sedebArchivo };
        selectTemporal = estadoSelect;
        abrirModalMotivoRechazo();
        return; // Detener el cambio de estado hasta que se confirme el motivo
    }

    enviarEstadoDependencia(sdeCodig, nuevoEstado, estadoSelect, sedebArchivo, "");
}

function enviarEstadoDependencia(sdeCodig, nuevoEstado, estadoSelect, sedebArchivo, motivoRechazo) {
    const botonEstado = estadoSelect;

    const colores = {
        'Enviada': 'blue',
        'En revision': 'orange',
        'Rechazada': 'red',
        'Aprobada pendiente de pago': 'darkgreen',
        'Pago en revision': 'lightgreen',
        'Finalizada': 'white'
    };

    const xhr = new XMLHttpRequest();
    xhr.open("POST", "constancias/constancia_dependencia/actualizar_estado_dependencia.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4) {
            if (xhr.status === 200 && xhr.responseText.trim() === "success") {
                botonEstado.style.backgroundColor = colores[nuevoEstado] || 'gray';
                const opcionesActualizadas = generarOpcionesEstado(nuevoEstado, sedebArchivo);
                estadoSelect.innerHTML = opcionesActualizadas;
                mostrarModalConstancia();
            } else {
                console.error('Error al actualizar el estado');
            }
        }
    };

    xhr.send(`Sde_codig=${sdeCodig}&Sde_statu=${nuevoEstado}&Sde_sedeb=${sedebArchivo}&motivo_rechazo=${motivoRechazo}`);
}
// Fin constancia dependencia económica

// Inicio constancia asiento permanente
function updateConstanciaStatusAsiento(sasCodig, estadoSelect, sedebArchivo) {
    const nuevoEstado = estadoSelect.value;

    // Si se selecciona "Rechazada", abrir el modal para escribir el motivo
    if (nuevoEstado === "Rechazada") {
        estadoTemporal = { sasCodig, sedebArchivo };
        selectTemporal = estadoSelect;
        abrirModalMotivoRechazo();
        return; // Detener el cambio de estado hasta que se confirme el motivo
    }

    enviarEstadoAsiento(sasCodig, nuevoEstado, estadoSelect, sedebArchivo, "");
}

function enviarEstadoAsiento(sasCodig, nuevoEstado, estadoSelect, sedebArchivo, motivoRechazo) {
    const botonEstado = estadoSelect;

    const colores = {
        'Enviada': 'blue',
        'En revision': 'orange',
        'Rechazada': 'red',
        'Aprobada pendiente de pago': 'darkgreen',
        'Pago en revision': 'lightgreen',
        'Finalizada': 'white'
    };

    const xhr = new XMLHttpRequest();
    xhr.open("POST", "constancias/constancia_asiento/actualizar_estado_asiento.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4) {
            if (xhr.status === 200 && xhr.responseText.trim() === "success") {
                botonEstado.style.backgroundColor = colores[nuevoEstado] || 'gray';
                const opcionesActualizadas = generarOpcionesEstado(nuevoEstado, sedebArchivo);
                estadoSelect.innerHTML = opcionesActualizadas;
                mostrarModalConstancia();
            } else {
                console.error('Error al actualizar el estado');
            }
        }
    };

    xhr.send(`Sas_codig=${sasCodig}&Sas_statu=${nuevoEstado}&Sas_sedeb=${sedebArchivo}&motivo_rechazo=${motivoRechazo}`);
}
// Fin constancia asiento permanente

// Inicio constancia buena conducta
function updateConstanciaStatusBuena(sbcCodig, estadoSelect, sedebArchivo) {
    const nuevoEstado = estadoSelect.value;

    // Si se selecciona "Rechazada", abrir el modal para escribir el motivo
    if (nuevoEstado === "Rechazada") {
        estadoTemporal = { sbcCodig, sedebArchivo };
        selectTemporal = estadoSelect;
        abrirModalMotivoRechazo();
        return; // Detener el cambio de estado hasta que se confirme el motivo
    }

    enviarEstadoBuena(sbcCodig, nuevoEstado, estadoSelect, sedebArchivo, "");
}

function enviarEstadoBuena(sbcCodig, nuevoEstado, estadoSelect, sedebArchivo, motivoRechazo) {
    const botonEstado = estadoSelect;

    const colores = {
        'Enviada': 'blue',
        'En revision': 'orange',
        'Rechazada': 'red',
        'Aprobada pendiente de pago': 'darkgreen',
        'Pago en revision': 'lightgreen',
        'Finalizada': 'white'
    };

    const xhr = new XMLHttpRequest();
    xhr.open("POST", "constancias/constancia_buena/actualizar_estado_buena.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4) {
            if (xhr.status === 200 && xhr.responseText.trim() === "success") {
                botonEstado.style.backgroundColor = colores[nuevoEstado] || 'gray';
                const opcionesActualizadas = generarOpcionesEstado(nuevoEstado, sedebArchivo);
                estadoSelect.innerHTML = opcionesActualizadas;
                mostrarModalConstancia();
            } else {
                console.error('Error al actualizar el estado');
            }
        }
    };

    xhr.send(`Sbc_codig=${sbcCodig}&Sbc_statu=${nuevoEstado}&Sbc_sedeb=${sedebArchivo}&motivo_rechazo=${motivoRechazo}`);
}
// Fin constancia buena conducta

// inicio permiso de mudanza
function updatePermisoMudanzaStatus(smdCodig, estadoSelect, sedebArchivo) {
    const nuevoEstado = estadoSelect.value;

    // Si se selecciona "Rechazada", abrir el modal para escribir el motivo
    if (nuevoEstado === "Rechazada") {
        estadoTemporal = { smdCodig, sedebArchivo };
        selectTemporal = estadoSelect;
        abrirModalMotivoRechazo();  // Función que debes implementar para obtener el motivo de rechazo
        return;  // Detener el cambio de estado hasta que se confirme el motivo
    }

    enviarEstadoMudanza(smdCodig, nuevoEstado, estadoSelect, sedebArchivo, "");
}

function enviarEstadoMudanza(smdCodig, nuevoEstado, estadoSelect, sedebArchivo, motivoRechazo) {
    const botonEstado = estadoSelect;

    const colores = {
        'Enviada': 'blue',
        'En revision': 'orange',
        'Rechazada': 'red',
        'Aprobada pendiente de pago': 'darkgreen',
        'Pago en revision': 'lightgreen',
        'Finalizada': 'white'
    };

    const xhr = new XMLHttpRequest();
    xhr.open("POST", "constancias/constancia_mudanza/actualizar_estado_mudanza.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4) {
            if (xhr.status === 200 && xhr.responseText.trim() === "success") {
                botonEstado.style.backgroundColor = colores[nuevoEstado] || 'gray';
                const opcionesActualizadas = generarOpcionesEstado(nuevoEstado, sedebArchivo);  // Esta función debe actualizar las opciones
                estadoSelect.innerHTML = opcionesActualizadas;
                mostrarModalConstancia();  // Función para mostrar el modal después de actualizar
            } else {
                console.error('Error al actualizar el estado');
            }
        }
    };

    xhr.send(`Smd_codig=${smdCodig}&Smd_statu=${nuevoEstado}&Smd_sedeb=${sedebArchivo}&motivo_rechazo=${motivoRechazo}`);
}



// Fin permiso de mudanza

// Inicio constancia de pobreza
function updateConstanciaStatusPobreza(spoCodig, estadoSelect) {
    const nuevoEstado = estadoSelect.value;

    // Si se selecciona "Rechazada", abrir el modal para escribir el motivo
    if (nuevoEstado === "Rechazada") {
        estadoTemporal = { spoCodig };
        selectTemporal = estadoSelect;
        abrirModalMotivoRechazo();
        return; // Detener el cambio de estado hasta que se confirme el motivo
    }

    enviarEstadoPobreza(spoCodig, nuevoEstado, estadoSelect, "");
}

function enviarEstadoPobreza(spoCodig, nuevoEstado, estadoSelect, motivoRechazo) {
    const botonEstado = estadoSelect;

    const colores = {
        'Enviada': 'blue',
        'En revision': 'orange',
        'Rechazada': 'red',
        'Finalizada': 'green'
    };

    const xhr = new XMLHttpRequest();
    xhr.open("POST", "constancias/constancia_pobreza/actualizar_estado_pobreza.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4) {
            if (xhr.status === 200 && xhr.responseText.trim() === "success") {
                botonEstado.style.backgroundColor = colores[nuevoEstado] || 'gray';
                const opcionesActualizadas = generarOpcionesEstadoSinPago(nuevoEstado);
                estadoSelect.innerHTML = opcionesActualizadas;
                mostrarModalConstancia();
            } else {
                console.error('Error al actualizar el estado');
            }
        }
    };

    xhr.send(`Spo_codig=${spoCodig}&Spo_statu=${nuevoEstado}&motivo_rechazo=${motivoRechazo}`);
}

// Fin constancia de pobreza

// Inicio constancia de fe de vida
function updateConstanciaStatusFeVida(sfeCodig, estadoSelect) {
    const nuevoEstado = estadoSelect.value;

    // Si se selecciona "Rechazada", abrir el modal para escribir el motivo
    if (nuevoEstado === "Rechazada") {
        estadoTemporal = { sfeCodig };
        selectTemporal = estadoSelect;
        abrirModalMotivoRechazo();
        return; // Detener el cambio de estado hasta que se confirme el motivo
    }

    enviarEstadoFeVida(sfeCodig, nuevoEstado, estadoSelect, "");
}

function enviarEstadoFeVida(sfeCodig, nuevoEstado, estadoSelect, motivoRechazo) {
    const botonEstado = estadoSelect;

    const colores = {
        'Enviada': 'blue',
        'En revision': 'orange',
        'Rechazada': 'red',
        'Finalizada': 'green'
    };

    const xhr = new XMLHttpRequest();
    xhr.open("POST", "constancias/constancia_fe/actualizar_estado_fe.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4) {
            if (xhr.status === 200 && xhr.responseText.trim() === "success") {
                botonEstado.style.backgroundColor = colores[nuevoEstado] || 'gray';
                const opcionesActualizadas = generarOpcionesEstadoSinPago(nuevoEstado);
                estadoSelect.innerHTML = opcionesActualizadas;
                mostrarModalConstancia();
            } else {
                console.error('Error al actualizar el estado');
            }
        }
    };

    xhr.send(`Sfe_codig=${sfeCodig}&Sfe_statu=${nuevoEstado}&motivo_rechazo=${motivoRechazo}`);
}
// Fin constancia de fe de vida


function generarOpcionesEstadoSinPago(estadoActual) {
    const opciones = {
        'Enviada': ['En revision'],
        'En revision': ['Rechazada', 'Finalizada'],
        'Rechazada': [],
        'Finalizada': ['Rechazada']
    };

    let html = `<option value="${estadoActual}" selected>${estadoActual}</option>`;
    (opciones[estadoActual] || []).forEach(opcion => {
        html += `<option value="${opcion}">${opcion}</option>`;
    });

    return html;
}

// Función para generar las opciones del select
function generarOpcionesEstado(estadoActual, sedebArchivo) {
    const opciones = {
        'Enviada': ['En revision'],
        'En revision': ['Rechazada', 'Aprobada pendiente de pago'],
        'Rechazada': [],
        'Aprobada pendiente de pago': sedebArchivo ? ['Pago en revision'] : ['Rechazada'],
        'Pago en revision': ['Rechazada', 'Finalizada'],
        'Finalizada': ['Rechazada']
    };

    let html = `<option value="${estadoActual}" selected>${estadoActual}</option>`;
    (opciones[estadoActual] || []).forEach(opcion => {
        html += `<option value="${opcion}">${opcion}</option>`;
    });

    return html;
}

// Modal de confirmación
function mostrarModalConstancia() {
    const modal = document.getElementById("modalConfirmacionConstancia");
    modal.style.display = "block";

    setTimeout(() => {
        cerrarModalConstancia();
    }, 2000);
}

function cerrarModalConstancia() {
    const modal = document.getElementById("modalConfirmacionConstancia");
    modal.style.display = "none";
}

// Funciones para el modal de motivo de rechazo
function abrirModalMotivoRechazo() {
    const modal = document.getElementById("modalMotivoRechazo");
    modal.style.display = "block";
}

function cerrarModalMotivoRechazo() {
    const modal = document.getElementById("modalMotivoRechazo");
    modal.style.display = "none";
    estadoTemporal = null;
    selectTemporal.value = ""; // Resetear el select si se cancela
}

function confirmarRechazo() {
    const motivo = document.getElementById("motivoRechazo").value.trim();
    if (!motivo) {
        alert("Por favor, escriba un motivo para el rechazo.");
        return;
    }

// Enviar el estado y cerrar el modal de motivo
if (estadoTemporal) {
    if (estadoTemporal.smdCodig) {
        // Manejar Permiso de Mudanza
        enviarEstadoMudanza(estadoTemporal.smdCodig, "Rechazada", selectTemporal, estadoTemporal.sedebArchivo, motivo);
    } else if (estadoTemporal.sepCodig) {
        // Manejar constancia de Evento
        enviarEstadoEvento(estadoTemporal.sepCodig, "Rechazada", selectTemporal, estadoTemporal.sedebArchivo, motivo);
    } else if (estadoTemporal.sdsCodig) {
        // Manejar constancia de Desempleo
        enviarEstadoDesempleo(estadoTemporal.sdsCodig, "Rechazada", selectTemporal, estadoTemporal.sedebArchivo, motivo);
    } else if (estadoTemporal.sdeCodig) {
        // Manejar constancia de Dependencia Económica
        enviarEstadoDependencia(estadoTemporal.sdeCodig, "Rechazada", selectTemporal, estadoTemporal.sedebArchivo, motivo);
    } else if (estadoTemporal.sasCodig) {
        // Manejar constancia de Asiento Permanente
        enviarEstadoAsiento(estadoTemporal.sasCodig, "Rechazada", selectTemporal, estadoTemporal.sedebArchivo, motivo);
    } else if (estadoTemporal.sbcCodig) {
        // Manejar constancia de Buena Conducta
        enviarEstadoBuena(estadoTemporal.sbcCodig, "Rechazada", selectTemporal, estadoTemporal.sedebArchivo, motivo);
    } else if (estadoTemporal.spoCodig) {
        // Manejar constancia de Pobreza
        enviarEstadoPobreza(estadoTemporal.spoCodig, "Rechazada", selectTemporal, motivo);
    } else if (estadoTemporal.sfeCodig) {
        // Manejar constancia de Fe de Vida
        enviarEstadoFeVida(estadoTemporal.sfeCodig, "Rechazada", selectTemporal, motivo);
    }
}




cerrarModalMotivoRechazo();
mostrarModalConstancia();

}
</script>


<script>
function openEditModal_3(constanciaData) {
    // Verificar si el estado es "Finalizada" o "Rechazada"
    if (constanciaData.Sep_statu === 'Finalizada' || constanciaData.Sep_statu === 'Rechazada') {
        // Mostrar el modal de advertencia
        document.getElementById('modalAdvertenciaEdicion').style.display = 'block';
        document.getElementById('advertenciaMensaje').innerText = 
            `No se puede editar la constancia porque su estado es "${constanciaData.Sep_statu}".`;

        // Cerrar el modal automáticamente después de 3 segundos
        setTimeout(() => {
            document.getElementById('modalAdvertenciaEdicion').style.display = 'none';
        }, 3000); // 3000 milisegundos = 3 segundos
        return; // Salir de la función para evitar abrir el modal de edición
    }

    // Mostrar datos del solicitante en campos de solo lectura
    document.getElementById('displayCedula_3').value = constanciaData.Per_cedul;
    document.getElementById('displayNombre_3').value = constanciaData.Per_nombr;
    document.getElementById('displayApellido_3').value = constanciaData.Per_apell;

    // Mostrar datos en campos de edición
    document.getElementById('editCodigo_3').value = constanciaData.Sep_codig;
    document.getElementById('editTipoEvento_3').value = constanciaData.Sep_tipoe;
    document.getElementById('editMotivo_3').value = constanciaData.Sep_motiv;
    document.getElementById('editAldea_3').value = constanciaData.Sep_aldea;
    document.getElementById('editCalle_3').value = constanciaData.Sep_calle;
    document.getElementById('editCarrera_3').value = constanciaData.Sep_carre;
    document.getElementById('editLugar_3').value = constanciaData.Sep_delug;
    document.getElementById('editFechaInicio_3').value = constanciaData.Sep_finic;
    document.getElementById('editHoraInicio_3').value = constanciaData.Sep_hinic;
    document.getElementById('editFechaFin_3').value = constanciaData.Sep_ffinl;
    document.getElementById('editHoraFin_3').value = constanciaData.Sep_hfinl;
    document.getElementById('editDuracion_3').value = calcularDuracion_3(
        constanciaData.Sep_finic,
        constanciaData.Sep_hinic,
        constanciaData.Sep_ffinl,
        constanciaData.Sep_hfinl
    );
    document.getElementById('editAsistencia_3').value = constanciaData.Sep_asist;

    // Mostrar el modal
    document.getElementById('editModal_3').style.display = 'block';
}

function closeEditModal_3() {
    document.getElementById('editModal_3').style.display = 'none';
}

function calcularDuracion_3(fechaInicio, horaInicio, fechaFin, horaFin) {
    const inicio = new Date(`${fechaInicio}T${horaInicio}`);
    const fin = new Date(`${fechaFin}T${horaFin}`);
    return Math.round((fin - inicio) / 60000); // Convertir milisegundos a minutos
}

function closeModalAdvertenciaEdicion() {
    document.getElementById('modalAdvertenciaEdicion').style.display = 'none';
}
</script>

<!--Para el modal de confirmacion de edicion -->
<script>
document.getElementById('editForm_3').addEventListener('submit', function (event) {
    event.preventDefault(); // Evita el envío tradicional del formulario

    const formData = new FormData(this);

    fetch(this.action, {
        method: 'POST',
        body: formData,
    })
        .then((response) => response.text())
        .then((data) => {
    // Mostrar el modal de confirmación con el mensaje de éxito
    showmodalConfirmacionEdicion('Datos actualizados correctamente');

    // Cerrar automáticamente el modal de edición
    closeEditModal_3();

    // Esperar 2 segundos antes de recargar la página para que el usuario vea el mensaje de éxito
    setTimeout(() => {
        location.reload();  // Recarga la página
    }, 2000);  // 2000 milisegundos = 2 segundos
})

        .catch((error) => {
            // Mostrar el modal con el mensaje de error
            showmodalConfirmacionEdicion('Hubo un problema al actualizar los datos. Intente nuevamente.');
            console.error('Error:', error);
        });
});

function showmodalConfirmacionEdicion(message) {
    const modal = document.getElementById('modalConfirmacionEdicion');
    document.getElementById('messageBody').textContent = message;
    modal.style.display = 'block';

    // Cierra automáticamente el modal después de 2 segundos
    setTimeout(() => {
        closemodalConfirmacionEdicionl();
        
    }, 2000);
}

function closemodalConfirmacionEdicionl() {
    document.getElementById('modalConfirmacionEdicion').style.display = 'none';
}

function closeEditModal_3() {
    document.getElementById('editModal_3').style.display = 'none';
}

</script>

<script>
    function openDetailsModal_4(constancia) {
        const modal = document.getElementById("detailsModal_4");
        const modalContent = document.getElementById("modal-details-content_4");

        // Realiza una consulta Ajax para obtener los datos de los testigos
        fetch(`constancias/constancia_desempleo/get_testigos_desempleo.php?sds_codig=${constancia.Sds_codig}`)
            .then(response => response.json())
            .then(testigos => {
                let testigosHtml = testigos.length > 0 ? testigos.map((testigo, index) => `
                    <tr>
                        <th colspan="2">Testigo ${index + 1}</th>
                    </tr>
                    <tr>
                        <td><strong>Cédula:</strong> ${testigo.Per_cedul}</td>
                    </tr>
                    <tr>
                        <td><strong>Nombre:</strong> ${testigo.Per_nombr}</td>
                        <td><strong>Apellido:</strong> ${testigo.Per_apell}</td>
                    </tr>
                `).join('') : '<tr><td colspan="2">No hay testigos disponibles.</td></tr>';

                // Renderizar detalles con testigos
                modalContent.innerHTML = `
                    <table>
                        <tr>
                            <th colspan="2">Datos del Solicitante</th>
                        </tr>
                        <tr>
                            <td><strong>Cédula:</strong> ${constancia.Per_cedul}</td>
                        </tr>
                        <tr>
                            <td><strong>Nombre:</strong> ${constancia.Per_nombr}</td>
                            <td><strong>Apellido:</strong> ${constancia.Per_apell}</td>
                        </tr>
                        <tr>
                            <th colspan="2">Motivo de la Constancia</th>
                        </tr>
                        <tr>
                            <td colspan="2">${constancia.Sds_motiv}</td>
                        </tr>
                        ${testigosHtml}
                        <tr>
                            <td colspan="2">
                                <div class="image-container">
                                    <div class="image-item">
                                        <p><strong>Bauché Sedebat:</strong></p>
                                        ${constancia.Sds_sedeb ? `<img src="${constancia.Sds_sedeb}" alt="Bauché Sedebat" class="imagen-tabla" onclick="openImageModal('${constancia.Sds_sedeb}')">` : '<p>No disponible</p>'}
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th colspan="2">Fechas</th>
                        </tr>
                        <tr>
                            <td><strong>Fecha de Solicitud:</strong> ${constancia.Sds_fsoli || 'No disponible'}</td>
                            <td><strong>Fecha de Emisión:</strong> ${constancia.Sds_femis || 'No disponible'}</td>
                        </tr>
                        <tr>
                            <td><strong>Fecha de Rechazo:</strong> ${constancia.Sds_frech || 'No disponible'}</td>
                            <td><strong>Motivo de Rechazo:</strong> ${constancia.Sds_motir || 'No disponible'}</td>
                        </tr>
                    </table>
                `;

                modal.style.display = "block";
            });
    }

    function closeDetailsModal_4() {
        document.getElementById("detailsModal_4").style.display = "none";
    }
</script>



		<!-- All Script -->
		<script src="../assets/js/sesion/jquery.min.js"></script>
		<script src="../assets/js/sesion/isotope.pkgd.min.js"></script>
		<script src="../assets/js/sesion/bootstrap.min.js"></script>
		<script src="../assets/js/sesion/simplebar.js"></script>
		<script src="../assets/js/sesion/owl.carousel.min.js"></script>
		<script src="../assets/js/sesion/jquery.magnific-popup.min.js"></script>
		<script src="../assets/js/sesion/jquery.animatedheadline.min.js"></script>
		<script src="../assets/js/sesion/jquery.easypiechart.js"></script>
		<script src="../assets/js/sesion/jquery.validation.js"></script>
		<script src="../assets/js/sesion/tilt.js"></script>
        <script src="../assets/js/sesion/main.js"></script>
		<script src="../assets/js/sesion/main-demo.js"></script>
        <script src="https://maps.google.com/maps/api/js?sensor=false"></script>
		
    </body>

</html>