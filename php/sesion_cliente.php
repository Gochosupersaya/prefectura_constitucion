<?php
session_start();
include('conexion.php'); // Asegúrate de tener el archivo de conexión correcto

// Verificar si el usuario está logueado
if (!isset($_SESSION['cedula'])) {
    echo "Debe iniciar sesión para ver sus denuncias.";
    exit;
}

$cedula_actual = $_SESSION['cedula'];

// Obtener el código del cliente actual
$sql_get_cliente_actual = $conexion->prepare("SELECT Cli_codig FROM prefttcli WHERE Cli_cedul = ?");
$sql_get_cliente_actual->bind_param("s", $cedula_actual);
$sql_get_cliente_actual->execute();
$result_cliente_actual = $sql_get_cliente_actual->get_result();

if ($result_cliente_actual->num_rows > 0) {
    $row_cliente_actual = $result_cliente_actual->fetch_assoc();
    $codigo_cliente_actual = $row_cliente_actual['Cli_codig'];

    $estado_seleccionado = isset($_GET['estado']) ? $_GET['estado'] : 'historial';

    $estado_condicion = '';
    if ($estado_seleccionado != 'historial') {
        $estado_condicion = "AND d.Den_statu = ?";
    }

    // Obtener las denuncias del usuario actual según el estado seleccionado
    $sql_denuncias = $conexion->prepare("
        SELECT 
            d.Den_codig, d.Den_tipod, d.Den_motiv, d.Den_fecha, d.Den_statu,
            p.Per_cedul AS cedula_denunciado, p.Per_nombr AS nombre_denunciado, p.Per_apell AS apellido_denunciado, 
            di.Din_aldea, di.Din_calle, di.Din_carre, di.Din_ncasa
        FROM prefttden d
        JOIN prefttdtd dt1 ON d.Den_codig = dt1.Dtd_denun AND dt1.Dtd_rolde = 1
        JOIN prefttcli c1 ON dt1.Dtd_clien = c1.Cli_codig
        JOIN prefttdtd dt2 ON d.Den_codig = dt2.Dtd_denun AND dt2.Dtd_rolde = 2
        JOIN prefttcli c2 ON dt2.Dtd_clien = c2.Cli_codig
        JOIN preftmper p ON c2.Cli_cedul = p.Per_cedul
        JOIN prefttdii di ON p.Per_cedul = di.Din_cedul
        WHERE c1.Cli_codig = ? $estado_condicion
    ");
    if ($estado_seleccionado != 'historial') {
        $sql_denuncias->bind_param("is", $codigo_cliente_actual, $estado_seleccionado);
    } else {
        $sql_denuncias->bind_param("i", $codigo_cliente_actual);
    }
    $sql_denuncias->execute();
    $result_denuncias = $sql_denuncias->get_result();
} else {
    echo "No se encontraron denuncias para el usuario actual.";
    exit;
}

function obtenerNombreAldea($codigo_aldea, $conexion) {
    $sql = $conexion->prepare("SELECT Ald_nombr FROM preftmald WHERE Ald_codig = ?");
    $sql->bind_param("i", $codigo_aldea);
    $sql->execute();
    $result = $sql->get_result();
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['Ald_nombr'];
    }
    return "N/A";
}

function obtenerTipoDenuncia($codigo_denuncia, $conexion) {
    $sql = $conexion->prepare("SELECT Tdn_nombr FROM preftmtdn WHERE Tdn_codig = ?");
    $sql->bind_param("i", $codigo_denuncia);
    $sql->execute();
    $result = $sql->get_result();
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['Tdn_nombr'];
    }
    return "N/A";
}

function obtenerEstadoDenuncia($estado) {
    $estados = [
		'enviada' => 'Enviada',
		'rechazada' => 'Rechazada',
		'aprobada' => 'Aprobada',
		'culminada' => 'Culminada',
		'en_revision' => 'En revisión' // Nuevo estado
	];
    return isset($estados[$estado]) ? $estados[$estado] : htmlspecialchars($estado);
}
?>

<?php

include('conexion.php'); // Asegúrate de tener el archivo de conexión correcto

// Verificar si el usuario está logueado
if (!isset($_SESSION['cedula'])) {
    echo "Debe iniciar sesión para ver sus citaciones.";
    exit;
}

$cedula_actual = $_SESSION['cedula'];

// Obtener el código del cliente actual
$sql_get_cliente_actual = $conexion->prepare("SELECT Cli_codig FROM prefttcli WHERE Cli_cedul = ?");
$sql_get_cliente_actual->bind_param("s", $cedula_actual);
$sql_get_cliente_actual->execute();
$result_cliente_actual = $sql_get_cliente_actual->get_result();

if ($result_cliente_actual->num_rows > 0) {
    $row_cliente_actual = $result_cliente_actual->fetch_assoc();
    $codigo_cliente_actual = $row_cliente_actual['Cli_codig'];

    // Obtener las citaciones relacionadas con las denuncias hechas por el usuario actual
    $sql_citaciones = $conexion->prepare("
        SELECT 
            d.Den_tipod, d.Den_motiv, c.Cit_fecha, c.Cit_horad
        FROM prefttcit c
        JOIN prefttdtd dt ON c.Cit_perde = dt.Dtd_codig
        JOIN prefttden d ON dt.Dtd_denun = d.Den_codig
        WHERE d.Den_codig IN (
            SELECT Dtd_denun 
            FROM prefttdtd 
            WHERE Dtd_clien = ?
        ) AND c.Cit_statu = 'pendiente'
    ");
    $sql_citaciones->bind_param("i", $codigo_cliente_actual);
    $sql_citaciones->execute();
    $result_citaciones = $sql_citaciones->get_result();
} else {
    echo "No se encontraron citaciones para el usuario actual.";
    exit;
}


?>

<?php

include('conexion.php'); // Asegúrate de tener el archivo de conexión correcto

// Verificar si el usuario está logueado
if (!isset($_SESSION['cedula'])) {
    echo "Debe iniciar sesión para ver sus constancias.";
    exit;
}

$cedula_actual = $_SESSION['cedula'];

// Obtener el código del cliente actual
$sql_get_cliente_actual = $conexion->prepare("SELECT Cli_codig FROM Prefttcli WHERE Cli_cedul = ?");
$sql_get_cliente_actual->bind_param("s", $cedula_actual);
$sql_get_cliente_actual->execute();
$result_cliente_actual = $sql_get_cliente_actual->get_result();
$cliente_actual = $result_cliente_actual->fetch_assoc()['Cli_codig'];

// Notificación de Evento Público
$sql_notificacion_evento = $conexion->prepare("
    SELECT Tpe_nombr, Sep_motiv, Ald_nombr, Sep_calle, Sep_carre, Sep_delug, Sep_finic, Sep_hinic, Sep_ffinl, Sep_hfinl, TIMESTAMPDIFF(MINUTE, CONCAT(Sep_finic, ' ', Sep_hinic), CONCAT(Sep_ffinl, ' ', Sep_hfinl)) AS Sep_durac, Sep_asist, Sep_fsoli, Sep_statu
    FROM prefttsep
    JOIN preftmald ON prefttsep.Sep_aldea = preftmald.Ald_codig
    JOIN preftmtpe ON prefttsep.Sep_tipoe = preftmtpe.Tpe_codig
    WHERE Sep_clien = ?
");
$sql_notificacion_evento->bind_param("i", $cliente_actual);
$sql_notificacion_evento->execute();
$result_notificacion_evento = $sql_notificacion_evento->get_result();


// Constancia de Pobreza
$sql_constancia_pobreza = $conexion->prepare("
    SELECT Spo_motiv, Spo_fsoli, Spo_statu
    FROM prefttspo
    WHERE Spo_clien = ?
");
$sql_constancia_pobreza->bind_param("i", $cliente_actual);
$sql_constancia_pobreza->execute();
$result_constancia_pobreza = $sql_constancia_pobreza->get_result();

// Constancia de Fe de Vida
$sql_constancia_fe_de_vida = $conexion->prepare("
    SELECT Sfe_motiv, Sfe_fsoli, Sfe_statu
    FROM prefttsfe
    WHERE Sfe_clien = ?
");
$sql_constancia_fe_de_vida->bind_param("i", $cliente_actual);
$sql_constancia_fe_de_vida->execute();
$result_constancia_fe_de_vida = $sql_constancia_fe_de_vida->get_result();
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

		<link rel="stylesheet" href="../assets/css/formulario.css">
		<link rel="stylesheet" href="../assets/css/sesion/mostrar_denuncias_usu.css">
		
    </head>
    <body>
		
		<!--Theme Options Start-->
        <div class="style-options">
            <div class="toggle-btn">
                <span><i class="fas fa-cog"></i></span>
            </div>
            <div class="style-menu">
                <div class="style-nav">
                    <h4 class="mt-15 mb-10">Opciones</h4>
                </div>
				<div class="style-back">
                    <h4 class="mt-85 mb-10">Modo</h4>
                    <ul>
                        <li><a href="../assets/css/sesion/style-dark.css"><i class="fas fa-moon"></i> Oscuro</a></li>
                        <li><a href="../assets/css/sesion/style-light.css"><i class="far fa-lightbulb"></i> Clarito</a></li>
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
            			<a href="#about" class="icon-a fas fa-user-tie"><br>Denuncias</a>
					</li>
					<li data-tooltip="Trámites" data-position="top">
            			<a href="#resume" class="icon-r fas fa-address-book"><br>Trámites</a>
					</li>
					<!--<li data-tooltip="Notificaciones" data-position="bottom">
						<a href="#contact" class="icon-c fas fa-bell"><br>Notificaciones</a>
					</li>-->
					<li data-tooltip="Información de usuario" data-position="top">
            			<a href="#portfolio" class="icon-p fas fa-user"><br>Información <br>de usuario</a>
					</li>
					<li data-tooltip="Cerrar sesión" data-position="top">
    					<a href="cerrar_sesion.php" class="icon-logout fas fa-sign-out-alt">Cerrar Sesión</a>
					</li>

          		</ul>
				
				
			 </nav>
			
			<!-- Home Section -->
          	<div class="pt-home" style="background-image: url('../assets/img/prefectura.jpg')">
             	<section>
					
					<!-- Banner -->
					<div class="banner">
  						<h1>Bienvenido al nuevo sistema de la prefectura Parroquia Constitucion</h1>
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
			
			
					
				<div class="page pt-about" data-simplebar>
    <section class="container">
		
	<h1>Citaciones Pendientes</h1>
    <p>Recuerda que el lugar de la citación siempre sera en la Prefectura Constitución</p>
    <?php if ($result_citaciones->num_rows > 0): ?>
        <table class="citaciones_pendientes">
            <thead>
                <tr>
                    <th>Tipo de Denuncia</th>
                    <th>Motivo</th>
                    <th>Fecha de Citación</th>
                    <th>Hora de Citación</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result_citaciones->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars(obtenerTipoDenuncia($row['Den_tipod'], $conexion)); ?></td>
                        <td><?php echo htmlspecialchars($row['Den_motiv']); ?></td>
                        <td><?php echo htmlspecialchars($row['Cit_fecha']); ?></td>
                        <td><?php echo htmlspecialchars($row['Cit_horad']); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No se encontraron citaciones pendientes.</p>
    <?php endif; ?>
	<p>Consulta nuestra ubicación </p>
	<div class="rounded h-100">
                            <iframe class="rounded w-100" 
                            style="height: 500px;" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3171.333705642122!2d-72.2401026617167!3d7.893289746207562!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x8e6669d55b0e3c8f%3A0x59144561e1016b60!2sDPPC%20PARROQUIA%20CONSTITUCI%C3%93N!5e0!3m2!1ses!2sus!4v1716776085503!5m2!1ses!2sus" 
                            loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                        </div>

		<div class="mostrar_den">
		<h1>Mis Denuncias</h1>
    <form method="get" action="">
        <label for="estado">Filtrar por estado:</label>
        <select name="estado" id="estado">
            <option value="enviada" <?php echo ($estado_seleccionado == 'enviada') ? 'selected' : ''; ?>>Denuncias Enviadas</option>
            <option value="aprobada" <?php echo ($estado_seleccionado == 'aprobada') ? 'selected' : ''; ?>>Denuncias Aprobadas</option>
            <option value="rechazada" <?php echo ($estado_seleccionado == 'rechazada') ? 'selected' : ''; ?>>Denuncias Rechazadas</option>
			<option value="culminada" <?php echo ($estado_seleccionado == 'culminada') ? 'selected' : ''; ?>>Denuncias Culminadas</option>
			<option value="historial" <?php echo ($estado_seleccionado == 'historial') ? 'selected' : ''; ?>>Historial de Denuncias</option>
        </select>
        <button type="submit">Procesar</button>
    </form>
    <?php if ($result_denuncias->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Cédula del Denunciado</th>
                    <th>Nombre del Denunciado</th>
                    <th>Apellido del Denunciado</th>
                    <th>Aldea</th>
                    <th>Calle</th>
                    <th>Carrera</th>
                    <th>Número de Casa</th>
                    <th>Tipo de Denuncia</th>
                    <th>Motivo</th>
                    <th>Fecha</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result_denuncias->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['cedula_denunciado']); ?></td>
                        <td><?php echo htmlspecialchars($row['nombre_denunciado']); ?></td>
                        <td><?php echo htmlspecialchars($row['apellido_denunciado']); ?></td>
                        <td><?php echo obtenerNombreAldea($row['Din_aldea'], $conexion); ?></td>
                        <td><?php echo htmlspecialchars($row['Din_calle']); ?></td>
                        <td><?php echo htmlspecialchars($row['Din_carre']); ?></td>
                        <td><?php echo htmlspecialchars($row['Din_ncasa']); ?></td>
                        <td><?php echo obtenerTipoDenuncia($row['Den_tipod'], $conexion); ?></td>
                        <td><?php echo htmlspecialchars($row['Den_motiv']); ?></td>
                        <td><?php echo htmlspecialchars($row['Den_fecha']); ?></td>
                        <td><?php echo obtenerEstadoDenuncia($row['Den_statu']); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No se encontraron denuncias para el usuario actual.</p>
    <?php endif; ?>

    

		</div>
		<br><br>
        <h1>Realizar una denuncia</h1>
        <p>Para realizar una denuncia por favor llene el siguiente formulario:</p>

		
        <form id="denunciaForm" action="pro_de_usu.php" method="post" class="form-denuncia">
            <fieldset>
                <legend>Información del denunciado</legend>
                <div class="form-group">
                    <label for="cedulad">Cédula:</label>
                    <input type="text" id="cedulad" name="cedulad" required>
                </div>
                <div class="form-group">
                    <label for="nombres">Nombres:</label>
                    <input type="text" id="nombres" name="nombres" required>
                </div>
                <div class="form-group">
                    <label for="apellidos">Apellidos:</label>
                    <input type="text" id="apellidos" name="apellidos" required>
                </div>
				<div class="form-group">
                    <label for="telefono">Teléfono</label>
                    <input type="text" id="telefono" name="telefono" required>
                </div>
                <div class="form-group">
                    <label for="aldea">Aldea:</label>
                    <select id="aldea" name="aldea" required>
                        <option value="">Seleccione una aldea</option>
						
                        <?php
    					// Conexión a la base de datos
    					$servername = "localhost";
    					$username = "root";
    					$password = "";
    					$dbname = "Sisbdpref";

    					$conn = new mysqli($servername, $username, $password, $dbname);

   						// Consulta para obtener las aldeas
    					$sql = "SELECT Ald_codig, Ald_nombr FROM preftmald";
    					$result = $conn->query($sql);

    					if ($result->num_rows > 0) {
        				// Imprimir opciones del select
        				while($row = $result->fetch_assoc()) {
            				echo "<option value='" . $row["Ald_codig"] . "'>" . $row["Ald_nombr"] . "</option>";
        				}
    					} else {
        					echo "<option value=''>No hay aldeas disponibles</option>";
    					}

    					// Cerrar conexión
    					$conn->close();
    					?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="calle">Calle:</label>
                    <input type="text" id="calle" name="calle" required>
                </div>
                <div class="form-group">
                    <label for="carrera">Carrera:</label>
                    <input type="text" id="carrera" name="carrera" required>
                </div>
                <div class="form-group">
                    <label for="num_casa">Número de Casa:</label>
                    <input type="text" id="num_casa" name="num_casa" required>
                </div>
            </fieldset>
            <fieldset>
                <legend>Denuncia</legend>
                <label for="tipo_denuncia">Tipo de denuncia:</label>

        		<select id="tipo_denuncia" name="tipo_denuncia" required>
					<option value="">Seleccione el tipo de Denuncia</option>
					<?php
    				// Conexión a la base de datos
    				$servername = "localhost";
    				$username = "root";
    				$password = "";
    				$dbname = "Sisbdpref";

    				$conn = new mysqli($servername, $username, $password, $dbname);

    				// Verificar conexión
    				if ($conn->connect_error) {
        				die("Conexión fallida: " . $conn->connect_error);
    				}

    				// Consulta para obtener los tipos de denuncia
    				$sql = "SELECT Tdn_codig, Tdn_nombr FROM preftmtdn";
    				$result = $conn->query($sql);

    				if ($result->num_rows > 0) {
        			// Imprimir opciones del select
        			while($row = $result->fetch_assoc()) {
            			echo "<option value='" . $row["Tdn_codig"] . "'>" . $row["Tdn_nombr"] . "</option>";
        			}
    				} else {
        				echo "<option value=''>No hay tipos de denuncia disponibles</option>";
    				}

    				// Cerrar conexión
    				$conn->close();
    				?>
            
        		</select><br>
        	<label for="descripcion">Descripción:</label><br>
        	<textarea id="descripcion" name="descripcion" rows="4" cols="50" required></textarea><br>
            </fieldset>
            	<input type="submit" value="Enviar" class="btn-submit">
    	</form>
    </section>
</div>

			  	
			 
			<!-- Resume Section -->
          	<div class="page pt-resume" data-simplebar>
            	<section class="container">
					
					<!-- Section Title -->
					<div class="header-page mt-70 mob-mt">
						<h2>Trámites</h2>
    					<span></span>
					</div>
					

					<div class="container-tramites">
        <label for="tramite-select">Selecciona el tipo de trámite:</label>
        <select id="tramite-select" class="form-select">
            <option value="">Seleccione</option>
            <option value="notificacion-evento-publico">Notificación de Evento Público</option>
            <option value="constancia-desempleo">Constancia de Desempleo</option>
            <option value="constancia-dependencia-economica">Constancia de Dependencia Económica</option>
            <option value="constancia-asiento-permanente">Constancia de Asiento Permanente</option>
            <option value="permiso-mudanza">Permiso de Mudanza</option>
            <option value="constancia-buena-conducta">Constancia de Buena Conducta</option>
            <option value="constancia-pobreza">Constancia de Pobreza</option>
            <option value="fe-de-vida">Fe de Vida</option>
        </select>

        <!-- Notificación de Evento Público -->
        <section id="notificacion-evento-publico" class="form-section">
            <h2>Notificación de Evento Público</h2>
            <form action="registrar_evento_publico.php" method="post">
                <label for="evento-tipo">Tipo de Evento:</label>
                <select id="evento-tipo" name="evento-tipo">
				<?php
    					// Conexión a la base de datos
    					$servername = "localhost";
    					$username = "root";
    					$password = "";
    					$dbname = "Sisbdpref";

    					$conn = new mysqli($servername, $username, $password, $dbname);

   						// Consulta para obtener las aldeas
    					$sql = "SELECT Tpe_codig, Tpe_nombr FROM preftmtpe";
    					$result = $conn->query($sql);

    					if ($result->num_rows > 0) {
        				// Imprimir opciones del select
        				while($row = $result->fetch_assoc()) {
            				echo "<option value='" . $row["Tpe_codig"] . "'>" . $row["Tpe_nombr"] . "</option>";
        				}
    					} else {
        					echo "<option value=''>No hay aldeas disponibles</option>";
    					}

    					// Cerrar conexión
    					$conn->close();
    					?>
                    </select>
                
                <label for="evento-motivo">Motivo:</label>
                <input type="text" id="evento-motivo" name="evento-motivo">
                
                <label for="evento-aldea">Aldea:</label>
                <select id="evento-aldea" name="evento-aldea">
				<?php
    					// Conexión a la base de datos
    					$servername = "localhost";
    					$username = "root";
    					$password = "";
    					$dbname = "Sisbdpref";

    					$conn = new mysqli($servername, $username, $password, $dbname);

   						// Consulta para obtener las aldeas
    					$sql = "SELECT Ald_codig, Ald_nombr FROM preftmald";
    					$result = $conn->query($sql);

    					if ($result->num_rows > 0) {
        				// Imprimir opciones del select
        				while($row = $result->fetch_assoc()) {
            				echo "<option value='" . $row["Ald_codig"] . "'>" . $row["Ald_nombr"] . "</option>";
        				}
    					} else {
        					echo "<option value=''>No hay aldeas disponibles</option>";
    					}

    					// Cerrar conexión
    					$conn->close();
    					?>
                    </select>
                
                <label for="evento-calle">Calle:</label>
                <input type="text" id="evento-calle" name="evento-calle">
                
                <label for="evento-carrera">Carrera:</label>
                <input type="text" id="evento-carrera" name="evento-carrera">
                
                <label for="evento-lugar">Lugar:</label>
                <input type="text" id="evento-lugar" name="evento-lugar">
                
                <label for="evento-fecha-inicio">Fecha Inicio:</label>
                <input type="date" id="evento-fecha-inicio" name="evento-fecha-inicio">

				<label for="evento-hora-inicio">Hora de Inicio:</label>
                <input type="time" id="evento-hora-inicio" name="evento-hora-inicio">
                
                <label for="evento-fecha-fin">Fecha de Fin:</label>
                <input type="date" id="evento-fecha-fin" name="evento-fecha-fin">

				<label for="evento-hora-fin">Hora de Fin:</label>
                <input type="time" id="evento-hora-fin" name="evento-hora-fin">

				<label for="evento-duracion">Duracion (minutos):</label>
                <input type="text" id="evento-duracion" name="evento-duracion">
                
                <label for="evento-asistencia">Posible Asistencia de Personas:</label>
                <input type="number" id="evento-asistencia" name="evento-asistencia">
                
                <button type="submit">Enviar</button>
            </form>
        </section>

        <!-- Constancia de Desempleo -->
        <section id="constancia-desempleo" class="form-section">
            <h2>Constancia de Desempleo</h2>
            <form action="registrar_desempleo.php" method="post" enctype="multipart/form-data">
                <label for="desempleo-motivo">Motivo:</label>
                <input type="text" id="desempleo-motivo" name="desempleo-motivo">
                
                <fieldset>
                    <legend>Datos del Testigo 1</legend>
                    <label for="testigo1-cedula">Cédula:</label>
                    <input type="text" id="testigo1-cedula" name="testigo1-cedula">
                    
                    <label for="testigo1-nombre">Nombre:</label>
                    <input type="text" id="testigo1-nombre" name="testigo1-nombre">
                    
                    <label for="testigo1-apellido">Apellido:</label>
                    <input type="text" id="testigo1-apellido" name="testigo1-apellido">
                    
                    <label for="testigo1-telefono">Teléfono:</label>
                    <input type="text" id="testigo1-telefono" name="testigo1-telefono">
                    
                    <label for="testigo1-foto-cedula">Foto de la Cédula:</label>
                    <input type="file" id="testigo1-foto-cedula" name="testigo1-foto-cedula">
                    
                    <label for="testigo1-foto-rif">Foto del RIF:</label>
                    <input type="file" id="testigo1-foto-rif" name="testigo1-foto-rif">
                    
                    <label for="testigo1-aldea">Aldea:</label>
                    <select id="testigo1-aldea" name="testigo1-aldea">
					<?php
    					// Conexión a la base de datos
    					$servername = "localhost";
    					$username = "root";
    					$password = "";
    					$dbname = "Sisbdpref";

    					$conn = new mysqli($servername, $username, $password, $dbname);

   						// Consulta para obtener las aldeas
    					$sql = "SELECT Ald_codig, Ald_nombr FROM preftmald";
    					$result = $conn->query($sql);

    					if ($result->num_rows > 0) {
        				// Imprimir opciones del select
        				while($row = $result->fetch_assoc()) {
            				echo "<option value='" . $row["Ald_codig"] . "'>" . $row["Ald_nombr"] . "</option>";
        				}
    					} else {
        					echo "<option value=''>No hay aldeas disponibles</option>";
    					}

    					// Cerrar conexión
    					$conn->close();
    					?>
                    </select>
                    
                    <label for="testigo1-calle">Calle:</label>
                    <input type="text" id="testigo1-calle" name="testigo1-calle">
                    
                    <label for="testigo1-carrera">Carrera:</label>
                    <input type="text" id="testigo1-carrera" name="testigo1-carrera">
                    
                    <label for="testigo1-casa">N° de Casa:</label>
                    <input type="text" id="testigo1-casa" name="testigo1-casa">
                </fieldset>
                
                <fieldset>
                    <legend>Datos del Testigo 2</legend>
                    <label for="testigo2-cedula">Cédula:</label>
                    <input type="text" id="testigo2-cedula" name="testigo2-cedula">
                    
                    <label for="testigo2-nombre">Nombre:</label>
                    <input type="text" id="testigo2-nombre" name="testigo2-nombre">
                    
                    <label for="testigo2-apellido">Apellido:</label>
                    <input type="text" id="testigo2-apellido" name="testigo2-apellido">
                    
                    <label for="testigo2-telefono">Teléfono:</label>
                    <input type="text" id="testigo2-telefono" name="testigo2-telefono">
                    
                    <label for="testigo2-foto-cedula">Foto de la Cédula:</label>
                    <input type="file" id="testigo2-foto-cedula" name="testigo2-foto-cedula">
                    
                    <label for="testigo2-foto-rif">Foto del RIF:</label>
                    <input type="file" id="testigo2-foto-rif" name="testigo2-foto-rif">
                    
                    <label for="testigo2-aldea">Aldea:</label>
                    <select id="testigo2-aldea" name="testigo2-aldea">
					<?php
    					// Conexión a la base de datos
    					$servername = "localhost";
    					$username = "root";
    					$password = "";
    					$dbname = "Sisbdpref";

    					$conn = new mysqli($servername, $username, $password, $dbname);

   						// Consulta para obtener las aldeas
    					$sql = "SELECT Ald_codig, Ald_nombr FROM preftmald";
    					$result = $conn->query($sql);

    					if ($result->num_rows > 0) {
        				// Imprimir opciones del select
        				while($row = $result->fetch_assoc()) {
            				echo "<option value='" . $row["Ald_codig"] . "'>" . $row["Ald_nombr"] . "</option>";
        				}
    					} else {
        					echo "<option value=''>No hay aldeas disponibles</option>";
    					}

    					// Cerrar conexión
    					$conn->close();
    					?>
                    </select>
                    
                    <label for="testigo2-calle">Calle:</label>
                    <input type="text" id="testigo2-calle" name="testigo2-calle">
                    
                    <label for="testigo2-carrera">Carrera:</label>
                    <input type="text" id="testigo2-carrera" name="testigo2-carrera">
                    
                    <label for="testigo2-casa">N° de Casa:</label>
                    <input type="text" id="testigo2-casa" name="testigo2-casa">
                </fieldset>
                
                <button type="submit">Enviar</button>
            </form>
        </section>

        <!-- Constancia de Dependencia Económica -->
        <section id="constancia-dependencia-economica" class="form-section">
            <h2>Constancia de Dependencia Económica</h2>
            <form action="registrar_dependencia_economica.php" method="post" enctype="multipart/form-data">
                <label for="dependencia-motivo">Motivo:</label>
                <input type="text" id="dependencia-motivo" name="dependencia-motivo">
                
                <fieldset>
                    <legend>Datos de la Persona Independiente</legend>
                    <label for="Independiente-cedula">Cédula:</label>
                    <input type="text" id="Independiente-cedula" name="Independiente-cedula">
                    
                    <label for="Independientee-nombre">Nombre:</label>
                    <input type="text" id="Independiente-nombre" name="Independiente-nombre">
                    
                    <label for="Independiente-apellido">Apellido:</label>
                    <input type="text" id="Independiente-apellido" name="Independiente-apellido">
                    
                    <label for="Independiente-telefono">Teléfono:</label>
                    <input type="text" id="Independiente-telefono" name="Independiente-telefono">
                    
                    <label for="Independiente-foto-cedula">Foto de la Cédula:</label>
                    <input type="file" id="Independiente-foto-cedula" name="Independiente-foto-cedula">
                    
                    <label for="Independiente-foto-rif">Foto del RIF:</label>
                    <input type="file" id="Independiente-foto-rif" name="Independiente-foto-rif">
                    
                    <label for="Independiente-aldea">Aldea:</label>
                    <select id="Independiente-aldea" name="Independiente-aldea">
					<?php
    					// Conexión a la base de datos
    					$servername = "localhost";
    					$username = "root";
    					$password = "";
    					$dbname = "Sisbdpref";

    					$conn = new mysqli($servername, $username, $password, $dbname);

   						// Consulta para obtener las aldeas
    					$sql = "SELECT Ald_codig, Ald_nombr FROM preftmald";
    					$result = $conn->query($sql);

    					if ($result->num_rows > 0) {
        				// Imprimir opciones del select
        				while($row = $result->fetch_assoc()) {
            				echo "<option value='" . $row["Ald_codig"] . "'>" . $row["Ald_nombr"] . "</option>";
        				}
    					} else {
        					echo "<option value=''>No hay aldeas disponibles</option>";
    					}

    					// Cerrar conexión
    					$conn->close();
    					?>
                    </select>
                    
                    <label for="Independiente-calle">Calle:</label>
                    <input type="text" id="Independiente-calle" name="Independiente-calle">
                    
                    <label for="Independiente-carrera">Carrera:</label>
                    <input type="text" id="Independiente-carrera" name="Independiente-carrera">
                    
                    <label for="Independiente-casa">N° de Casa:</label>
                    <input type="text" id="Independiente-casa" name="Independiente-casa">
                </fieldset>
                
                <fieldset>
                    <legend>Datos del Testigo 1</legend>
                    <label for="testigo1-cedula">Cédula:</label>
                    <input type="text" id="testigo1-cedula" name="testigo1-cedula">
                    
                    <label for="testigo1-nombre">Nombre:</label>
                    <input type="text" id="testigo1-nombre" name="testigo1-nombre">
                    
                    <label for="testigo1-apellido">Apellido:</label>
                    <input type="text" id="testigo1-apellido" name="testigo1-apellido">
                    
                    <label for="testigo1-telefono">Teléfono:</label>
                    <input type="text" id="testigo1-telefono" name="testigo1-telefono">
                    
                    <label for="testigo1-foto-cedula">Foto de la Cédula:</label>
                    <input type="file" id="testigo1-foto-cedula" name="testigo1-foto-cedula">
                    
                    <label for="testigo1-foto-rif">Foto del RIF:</label>
                    <input type="file" id="testigo1-foto-rif" name="testigo1-foto-rif">
                    
                    <label for="testigo1-aldea">Aldea:</label>
                    <select id="testigo1-aldea" name="testigo1-aldea">
					<?php
    					// Conexión a la base de datos
    					$servername = "localhost";
    					$username = "root";
    					$password = "";
    					$dbname = "Sisbdpref";

    					$conn = new mysqli($servername, $username, $password, $dbname);

   						// Consulta para obtener las aldeas
    					$sql = "SELECT Ald_codig, Ald_nombr FROM preftmald";
    					$result = $conn->query($sql);

    					if ($result->num_rows > 0) {
        				// Imprimir opciones del select
        				while($row = $result->fetch_assoc()) {
            				echo "<option value='" . $row["Ald_codig"] . "'>" . $row["Ald_nombr"] . "</option>";
        				}
    					} else {
        					echo "<option value=''>No hay aldeas disponibles</option>";
    					}

    					// Cerrar conexión
    					$conn->close();
    					?>
                    </select>
                    
                    <label for="testigo1-calle">Calle:</label>
                    <input type="text" id="testigo1-calle" name="testigo1-calle">
                    
                    <label for="testigo1-carrera">Carrera:</label>
                    <input type="text" id="testigo1-carrera" name="testigo1-carrera">
                    
                    <label for="testigo1-casa">N° de Casa:</label>
                    <input type="text" id="testigo1-casa" name="testigo1-casa">
                </fieldset>
                
                <fieldset>
                    <legend>Datos del Testigo 2</legend>
                    <label for="testigo2-cedula">Cédula:</label>
                    <input type="text" id="testigo2-cedula" name="testigo2-cedula">
                    
                    <label for="testigo2-nombre">Nombre:</label>
                    <input type="text" id="testigo2-nombre" name="testigo2-nombre">
                    
                    <label for="testigo2-apellido">Apellido:</label>
                    <input type="text" id="testigo2-apellido" name="testigo2-apellido">
                    
                    <label for="testigo2-telefono">Teléfono:</label>
                    <input type="text" id="testigo2-telefono" name="testigo2-telefono">
                    
                    <label for="testigo2-foto-cedula">Foto de la Cédula:</label>
                    <input type="file" id="testigo2-foto-cedula" name="testigo2-foto-cedula">
                    
                    <label for="testigo2-foto-rif">Foto del RIF:</label>
                    <input type="file" id="testigo2-foto-rif" name="testigo2-foto-rif">
                    
                    <label for="testigo2-aldea">Aldea:</label>
                    <select id="testigo2-aldea" name="testigo2-aldea">
					<?php
    					// Conexión a la base de datos
    					$servername = "localhost";
    					$username = "root";
    					$password = "";
    					$dbname = "Sisbdpref";

    					$conn = new mysqli($servername, $username, $password, $dbname);

   						// Consulta para obtener las aldeas
    					$sql = "SELECT Ald_codig, Ald_nombr FROM preftmald";
    					$result = $conn->query($sql);

    					if ($result->num_rows > 0) {
        				// Imprimir opciones del select
        				while($row = $result->fetch_assoc()) {
            				echo "<option value='" . $row["Ald_codig"] . "'>" . $row["Ald_nombr"] . "</option>";
        				}
    					} else {
        					echo "<option value=''>No hay aldeas disponibles</option>";
    					}

    					// Cerrar conexión
    					$conn->close();
    					?>
                    </select>
                    
                    <label for="testigo2-calle">Calle:</label>
                    <input type="text" id="testigo2-calle" name="testigo2-calle">
                    
                    <label for="testigo2-carrera">Carrera:</label>
                    <input type="text" id="testigo2-carrera" name="testigo2-carrera">
                    
                    <label for="testigo2-casa">N° de Casa:</label>
                    <input type="text" id="testigo2-casa" name="testigo2-casa">
                </fieldset>
                
                <button type="submit">Enviar</button>
            </form>
        </section>

        <!-- Constancia de Asiento Permanente -->
        <section id="constancia-asiento-permanente" class="form-section">
            <h2>Constancia de Asiento Permanente</h2>
            <form action="registrar_asiento.php" method="post" enctype="multipart/form-data">
                <label for="asiento-motivo">Motivo:</label>
                <input type="text" id="asiento-motivo" name="asiento-motivo">
                
                <fieldset>
                    <legend>Datos de la Persona Difunta</legend>
                    <label for="difunto-cedula">Cédula:</label>
                    <input type="text" id="difunto-cedula" name="difunto-cedula">
                    
                    <label for="difunto-nombre">Nombre:</label>
                    <input type="text" id="difunto-nombre" name="difunto-nombre">
                    
                    <label for="difunto-apellido">Apellido:</label>
                    <input type="text" id="difunto-apellido" name="difunto-apellido">
                    
                    <label for="difunto-fecha-fallecimiento">Fecha Fallecimiento:</label>
                    <input type="date" id="difunto-fecha-fallecimiento" name="difunto-fecha-fallecimiento">

					<label for="difunto-hora-fallecimiento">Hora de Fallecimiento:</label>
                    <input type="time" id="difunto-hora-fallecimiento" name="difunto-hora-fallecimiento">
                    
                    <label for="difunto-foto-cedula">Foto de la Cédula:</label>
                    <input type="file" id="difunto-foto-cedula" name="difunto-foto-cedula">
                    
                    <label for="difunto-foto-rif">Foto del RIF:</label>
                    <input type="file" id="difunto-foto-rif" name="difunto-foto-rif">
                    
                    <label for="difunto-acta-defuncion-numero">N° de Acta de Defunción:</label>
                    <input type="text" id="difunto-acta-defuncion-numero" name="difunto-acta-defuncion-numero">
                    
                    <label for="difunto-acta-defuncion-foto">Foto del Acta de Defunción:</label>
                    <input type="file" id="difunto-acta-defuncion-foto" name="difunto-acta-defuncion-foto">
                    
                    <label for="difunto-aldea">Aldea:</label>
                    <select id="difunto-aldea" name="difunto-aldea">
					<?php
    					// Conexión a la base de datos
    					$servername = "localhost";
    					$username = "root";
    					$password = "";
    					$dbname = "Sisbdpref";

    					$conn = new mysqli($servername, $username, $password, $dbname);

   						// Consulta para obtener las aldeas
    					$sql = "SELECT Ald_codig, Ald_nombr FROM preftmald";
    					$result = $conn->query($sql);

    					if ($result->num_rows > 0) {
        				// Imprimir opciones del select
        				while($row = $result->fetch_assoc()) {
            				echo "<option value='" . $row["Ald_codig"] . "'>" . $row["Ald_nombr"] . "</option>";
        				}
    					} else {
        					echo "<option value=''>No hay aldeas disponibles</option>";
    					}

    					// Cerrar conexión
    					$conn->close();
    					?>
                    </select>
                    
                    <label for="difunto-calle">Calle:</label>
                    <input type="text" id="difunto-calle" name="difunto-calle">
                    
                    <label for="difunto-carrera">Carrera:</label>
                    <input type="text" id="difunto-carrera" name="difunto-carrera">
                    
                    <label for="difunto-casa">N° de Casa:</label>
                    <input type="text" id="difunto-casa" name="difunto-casa">
                </fieldset>
                
                <fieldset>
                    <legend>Datos del Testigo 1</legend>
                    <label for="testigo1-cedula">Cédula:</label>
                    <input type="text" id="testigo1-cedula" name="testigo1-cedula">
                    
                    <label for="testigo1-nombre">Nombre:</label>
                    <input type="text" id="testigo1-nombre" name="testigo1-nombre">
                    
                    <label for="testigo1-apellido">Apellido:</label>
                    <input type="text" id="testigo1-apellido" name="testigo1-apellido">
                    
                    <label for="testigo1-telefono">Teléfono:</label>
                    <input type="text" id="testigo1-telefono" name="testigo1-telefono">
                    
                    <label for="testigo1-foto-cedula">Foto de la Cédula:</label>
                    <input type="file" id="testigo1-foto-cedula" name="testigo1-foto-cedula">
                    
                    <label for="testigo1-foto-rif">Foto del RIF:</label>
                    <input type="file" id="testigo1-foto-rif" name="testigo1-foto-rif">
                    
                    <label for="testigo1-aldea">Aldea:</label>
                    <select id="testigo1-aldea" name="testigo1-aldea">
					<?php
    					// Conexión a la base de datos
    					$servername = "localhost";
    					$username = "root";
    					$password = "";
    					$dbname = "Sisbdpref";

    					$conn = new mysqli($servername, $username, $password, $dbname);

   						// Consulta para obtener las aldeas
    					$sql = "SELECT Ald_codig, Ald_nombr FROM preftmald";
    					$result = $conn->query($sql);

    					if ($result->num_rows > 0) {
        				// Imprimir opciones del select
        				while($row = $result->fetch_assoc()) {
            				echo "<option value='" . $row["Ald_codig"] . "'>" . $row["Ald_nombr"] . "</option>";
        				}
    					} else {
        					echo "<option value=''>No hay aldeas disponibles</option>";
    					}

    					// Cerrar conexión
    					$conn->close();
    					?>
                    </select>
                    
                    <label for="testigo1-calle">Calle:</label>
                    <input type="text" id="testigo1-calle" name="testigo1-calle">
                    
                    <label for="testigo1-carrera">Carrera:</label>
                    <input type="text" id="testigo1-carrera" name="testigo1-carrera">
                    
                    <label for="testigo1-casa">N° de Casa:</label>
                    <input type="text" id="testigo1-casa" name="testigo1-casa">
                </fieldset>
                
                <fieldset>
                    <legend>Datos del Testigo 2</legend>
                    <label for="testigo2-cedula">Cédula:</label>
                    <input type="text" id="testigo2-cedula" name="testigo2-cedula">
                    
                    <label for="testigo2-nombre">Nombre:</label>
                    <input type="text" id="testigo2-nombre" name="testigo2-nombre">
                    
                    <label for="testigo2-apellido">Apellido:</label>
                    <input type="text" id="testigo2-apellido" name="testigo2-apellido">
                    
                    <label for="testigo2-telefono">Teléfono:</label>
                    <input type="text" id="testigo2-telefono" name="testigo2-telefono">
                    
                    <label for="testigo2-foto-cedula">Foto de la Cédula:</label>
                    <input type="file" id="testigo2-foto-cedula" name="testigo2-foto-cedula">
                    
                    <label for="testigo2-foto-rif">Foto del RIF:</label>
                    <input type="file" id="testigo2-foto-rif" name="testigo2-foto-rif">
                    
                    <label for="testigo2-aldea">Aldea:</label>
                    <select id="testigo2-aldea" name="testigo2-aldea">
					<?php
    					// Conexión a la base de datos
    					$servername = "localhost";
    					$username = "root";
    					$password = "";
    					$dbname = "Sisbdpref";

    					$conn = new mysqli($servername, $username, $password, $dbname);

   						// Consulta para obtener las aldeas
    					$sql = "SELECT Ald_codig, Ald_nombr FROM preftmald";
    					$result = $conn->query($sql);

    					if ($result->num_rows > 0) {
        				// Imprimir opciones del select
        				while($row = $result->fetch_assoc()) {
            				echo "<option value='" . $row["Ald_codig"] . "'>" . $row["Ald_nombr"] . "</option>";
        				}
    					} else {
        					echo "<option value=''>No hay aldeas disponibles</option>";
    					}

    					// Cerrar conexión
    					$conn->close();
    					?>
                    </select>
                    
                    <label for="testigo2-calle">Calle:</label>
                    <input type="text" id="testigo2-calle" name="testigo2-calle">
                    
                    <label for="testigo2-carrera">Carrera:</label>
                    <input type="text" id="testigo2-carrera" name="testigo2-carrera">
                    
                    <label for="testigo2-casa">N° de Casa:</label>
                    <input type="text" id="testigo2-casa" name="testigo2-casa">
                </fieldset>
                
                <button type="submit">Enviar</button>
            </form>
        </section>

        <!-- Permiso de Mudanza -->
        <section id="permiso-mudanza" class="form-section">
            <h2>Permiso de Mudanza</h2>
            <form action="registrar_mudanza.php" method="post" enctype="multipart/form-data">
                <!--<label for="mudanza-destino">Destino de la Mudanza:</label>
                <select id="mudanza-destino" name="mudanza-destino">
                    <option value="dentro-parroquia">Dentro de la parroquia Constitución</option>
                    <option value="fuera-parroquia">Fuera de la parroquia Constitución</option>
                </select>-->
                
				<!--
                <fieldset id="mudanza-dentro-parroquia">
                    <legend>Datos del Destino (Dentro de la Parroquia)</legend>
                    <label for="mudanza-aldea">Aldea:</label>
                    <select id="mudanza-aldea" name="mudanza-aldea"><?php
    					// Conexión a la base de datos
    					$servername = "localhost";
    					$username = "root";
    					$password = "";
    					$dbname = "Sisbdpref";

    					$conn = new mysqli($servername, $username, $password, $dbname);

   						// Consulta para obtener las aldeas
    					$sql = "SELECT Ald_codig, Ald_nombr FROM preftmald";
    					$result = $conn->query($sql);

    					if ($result->num_rows > 0) {
        				// Imprimir opciones del select
        				while($row = $result->fetch_assoc()) {
            				echo "<option value='" . $row["Ald_codig"] . "'>" . $row["Ald_nombr"] . "</option>";
        				}
    					} else {
        					echo "<option value=''>No hay aldeas disponibles</option>";
    					}

    					//Cerrar conexión
    					$conn->close();
    					?>
                    </select>
                    
                    <label for="mudanza-calle">Calle:</label>
                    <input type="text" id="mudanza-calle" name="mudanza-calle">
                    
                    <label for="mudanza-carrera">Carrera:</label>
                    <input type="text" id="mudanza-carrera" name="mudanza-carrera">
                    
                    <label for="mudanza-casa">N° de Casa:</label>
                    <input type="text" id="mudanza-casa" name="mudanza-casa">
                </fieldset> -->
                
                <fieldset id="mudanza-fuera-parroquia">
                    <legend>Datos del Destino <!--(Fuera de la Parroquia)--></legend>
                    <label for="mudanza-municipio">Municipio:</label>
                    <select id="mudanza-municipio" name="mudanza-municipio"><?php
    					// Conexión a la base de datos
    					$servername = "localhost";
    					$username = "root";
    					$password = "";
    					$dbname = "Sisbdpref";

    					$conn = new mysqli($servername, $username, $password, $dbname);

   						// Consulta para obtener las aldeas
    					$sql = "SELECT Mun_codig, Mun_nombr FROM preftmmun";
    					$result = $conn->query($sql);

    					if ($result->num_rows > 0) {
        				// Imprimir opciones del select
        				while($row = $result->fetch_assoc()) {
            				echo "<option value='" . $row["Mun_codig"] . "'>" . $row["Mun_nombr"] . "</option>";
        				}
    					} else {
        					echo "<option value=''>No hay aldeas disponibles</option>";
    					}

    					// Cerrar conexión
    					$conn->close();
    					?>
                    </select>
                    
                    <label for="mudanza-fuera-calle">lugar:</label>
                    <input type="text" id="mudanza-fuera-calle" name="mudanza-fuera-calle">
                    
                </fieldset>
                
                <fieldset>
                    <legend>Datos del Vehículo Transportador</legend>
                    <label for="vehiculo-marca">Marca:</label>
                    <select id="vehiculo-marca" name="vehiculo-marca">
					<?php
    					// Conexión a la base de datos
    					$servername = "localhost";
    					$username = "root";
    					$password = "";
    					$dbname = "Sisbdpref";

    					$conn = new mysqli($servername, $username, $password, $dbname);

   						// Consulta para obtener las aldeas
    					$sql = "SELECT Car_codig, Car_marca FROM preftmcar";
    					$result = $conn->query($sql);

    					if ($result->num_rows > 0) {
        				// Imprimir opciones del select
        				while($row = $result->fetch_assoc()) {
            				echo "<option value='" . $row["Car_codig"] . "'>" . $row["Car_marca"] . "</option>";
        				}
    					} else {
        					echo "<option value=''>No hay aldeas disponibles</option>";
    					}

    					// Cerrar conexión
    					$conn->close();
    					?>
                    </select>
                    
                    <label for="vehiculo-modelo">Modelo:</label>
                    <select id="vehiculo-modelo" name="vehiculo-modelo">
					<?php
    					// Conexión a la base de datos
    					$servername = "localhost";
    					$username = "root";
    					$password = "";
    					$dbname = "Sisbdpref";

    					$conn = new mysqli($servername, $username, $password, $dbname);

   						// Consulta para obtener las aldeas
    					$sql = "SELECT Mca_codig, Mca_model FROM prefttmca";
    					$result = $conn->query($sql);

    					if ($result->num_rows > 0) {
        				// Imprimir opciones del select
        				while($row = $result->fetch_assoc()) {
            				echo "<option value='" . $row["Mca_codig"] . "'>" . $row["Mca_model"] . "</option>";
        				}
    					} else {
        					echo "<option value=''>No hay aldeas disponibles</option>";
    					}

    					// Cerrar conexión
    					$conn->close();
    					?>
                    </select>
                    
                    <label for="vehiculo-anio">Año:</label>
                    <input type="date" id="vehiculo-anio" name="vehiculo-anio">
                    
                    <label for="vehiculo-color">Color:</label>
                    <input type="text" id="vehiculo-color" name="vehiculo-color">
                    
                    <label for="vehiculo-clase">Clase:</label>
                    <input type="text" id="vehiculo-clase" name="vehiculo-clase">
                    
                    <label for="vehiculo-placa">Placa:</label>
                    <input type="text" id="vehiculo-placa" name="vehiculo-placa">
                    
                    <label for="vehiculo-serial-motor">Serial del Motor:</label>
                    <input type="text" id="vehiculo-serial-motor" name="vehiculo-serial-motor">
                    
                    <label for="vehiculo-serial-carroceria">Serial de Carrocería:</label>
                    <input type="text" id="vehiculo-serial-carroceria" name="vehiculo-serial-carroceria">
                </fieldset>
                
                <fieldset>
                    <legend>Datos del Conductor</legend>
                    <label for="conductor-cedula">Cédula:</label>
                    <input type="text" id="conductor-cedula" name="conductor-cedula">
                    
                    <label for="conductor-nombre">Nombre:</label>
                    <input type="text" id="conductor-nombre" name="conductor-nombre">
                    
                    <label for="conductor-apellido">Apellido:</label>
                    <input type="text" id="conductor-apellido" name="conductor-apellido">
                    
                    <label for="conductor-telefono">Teléfono:</label>
                    <input type="text" id="conductor-telefono" name="conductor-telefono">
                </fieldset>
                
				<fieldset>
    <legend>Lista de Bienes a Transportar</legend>
    <div id="bienes-container">
        <div class="bien">
            <label for="bienes-nombre">Nombre del Bien:</label>
            <select name="bienes-nombre[]">
                <?php
                    // Conexión a la base de datos
                    $servername = "localhost";
                    $username = "root";
                    $password = "";
                    $dbname = "Sisbdpref";

                    $conn = new mysqli($servername, $username, $password, $dbname);

                    // Consulta para obtener los nombres de bienes
                    $sql = "SELECT Bie_codig, Bie_nombr FROM preftmbie";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        // Imprimir opciones del select
                        while($row = $result->fetch_assoc()) {
                            echo "<option value='" . $row["Bie_codig"] . "'>" . $row["Bie_nombr"] . "</option>";
                        }
                    } else {
                        echo "<option value=''>No hay bienes disponibles</option>";
                    }

                    // Cerrar conexión
                    $conn->close();
                ?>
            </select>
            <label for="bienes-cantidad">Cantidad:</label>
            <input type="number" name="bienes-cantidad[]">
            <button type="button" class="eliminar-bien">Eliminar</button>
        </div>
    </div>
    <button type="button" id="agregar-bien">Agregar Bien</button>
</fieldset>


                
                <button type="submit">Enviar</button>
            </form>
        </section>

		<script>
document.addEventListener('DOMContentLoaded', function() {
    var agregarBienBtn = document.getElementById('agregar-bien');
    var bienesContainer = document.getElementById('bienes-container');

    function cargarOpcionesSelect(select) {
        var xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function() {
            if (xhr.readyState === XMLHttpRequest.DONE) {
                if (xhr.status === 200) {
                    while (select.options.length > 0) {
                        select.remove(0);
                    }
                    var response = JSON.parse(xhr.responseText);
                    response.forEach(function(option) {
                        var opt = document.createElement('option');
                        opt.value = option.Bie_codig;
                        opt.textContent = option.Bie_nombr;
                        select.appendChild(opt);
                    });
                } else {
                    console.error('Error al cargar opciones del select');
                }
            }
        };
        xhr.open('GET', 'obtener_bienes.php', true);
        xhr.send();
    }
    agregarBienBtn.addEventListener('click', function() {
        var nuevoBien = document.createElement('div');
        nuevoBien.classList.add('bien');
        nuevoBien.innerHTML = `
            <label for="bienes-nombre">Nombre del Bien:</label>
            <select name="bienes-nombre[]"></select>
            <label for="bienes-cantidad">Cantidad:</label>
            <input type="number" name="bienes-cantidad[]">
            <button type="button" class="eliminar-bien">Eliminar</button>
        `;
        bienesContainer.appendChild(nuevoBien);

        var nuevoSelect = nuevoBien.querySelector('select');
        cargarOpcionesSelect(nuevoSelect);
    });

    bienesContainer.addEventListener('click', function(e) {
        if (e.target.classList.contains('eliminar-bien')) {
            e.target.parentElement.remove();
        }
    });

    var primerSelect = document.querySelector('#bienes-container select');
    cargarOpcionesSelect(primerSelect);
});
</script>







        <!-- Constancia de Buena Conducta -->
        <section id="constancia-buena-conducta" class="form-section">
            <h2>Constancia de Buena Conducta</h2>
            <form action="registrar_buena.php" method="post" enctype="multipart/form-data">
                <label for="buena-conducta-motivo">Motivo:</label>
                <input type="text" id="buena-conducta-motivo" name="buena-conducta-motivo">
                
                <fieldset>
                    <legend>Datos del Testigo 1</legend>
                    <label for="testigo1-cedula">Cédula:</label>
                    <input type="text" id="testigo1-cedula" name="testigo1-cedula">
                    
                    <label for="testigo1-nombre">Nombre:</label>
                    <input type="text" id="testigo1-nombre" name="testigo1-nombre">
                    
                    <label for="testigo1-apellido">Apellido:</label>
                    <input type="text" id="testigo1-apellido" name="testigo1-apellido">
                    
                    <label for="testigo1-telefono">Teléfono:</label>
                    <input type="text" id="testigo1-telefono" name="testigo1-telefono">
                    
                    <label for="testigo1-foto-cedula">Foto de la Cédula:</label>
                    <input type="file" id="testigo1-foto-cedula" name="testigo1-foto-cedula">
                    
                    <label for="testigo1-foto-rif">Foto del RIF:</label>
                    <input type="file" id="testigo1-foto-rif" name="testigo1-foto-rif">
                    
                    <label for="testigo1-aldea">Aldea:</label>
                    <select id="testigo1-aldea" name="testigo1-aldea">
					<?php
    					// Conexión a la base de datos
    					$servername = "localhost";
    					$username = "root";
    					$password = "";
    					$dbname = "Sisbdpref";

    					$conn = new mysqli($servername, $username, $password, $dbname);

   						// Consulta para obtener las aldeas
    					$sql = "SELECT Ald_codig, Ald_nombr FROM preftmald";
    					$result = $conn->query($sql);

    					if ($result->num_rows > 0) {
        				// Imprimir opciones del select
        				while($row = $result->fetch_assoc()) {
            				echo "<option value='" . $row["Ald_codig"] . "'>" . $row["Ald_nombr"] . "</option>";
        				}
    					} else {
        					echo "<option value=''>No hay aldeas disponibles</option>";
    					}

    					// Cerrar conexión
    					$conn->close();
    					?>
                    </select>
                    
                    <label for="testigo1-calle">Calle:</label>
                    <input type="text" id="testigo1-calle" name="testigo1-calle">
                    
                    <label for="testigo1-carrera">Carrera:</label>
                    <input type="text" id="testigo1-carrera" name="testigo1-carrera">
                    
                    <label for="testigo1-casa">N° de Casa:</label>
                    <input type="text" id="testigo1-casa" name="testigo1-casa">
                </fieldset>
                
                <fieldset>
                    <legend>Datos del Testigo 2</legend>
                    <label for="testigo2-cedula">Cédula:</label>
                    <input type="text" id="testigo2-cedula" name="testigo2-cedula">
                    
                    <label for="testigo2-nombre">Nombre:</label>
                    <input type="text" id="testigo2-nombre" name="testigo2-nombre">
                    
                    <label for="testigo2-apellido">Apellido:</label>
                    <input type="text" id="testigo2-apellido" name="testigo2-apellido">
                    
                    <label for="testigo2-telefono">Teléfono:</label>
                    <input type="text" id="testigo2-telefono" name="testigo2-telefono">
                    
                    <label for="testigo2-foto-cedula">Foto de la Cédula:</label>
                    <input type="file" id="testigo2-foto-cedula" name="testigo2-foto-cedula">
                    
                    <label for="testigo2-foto-rif">Foto del RIF:</label>
                    <input type="file" id="testigo2-foto-rif" name="testigo2-foto-rif">
                    
                    <label for="testigo2-aldea">Aldea:</label>
                    <select id="testigo2-aldea" name="testigo2-aldea">
					<?php
    					// Conexión a la base de datos
    					$servername = "localhost";
    					$username = "root";
    					$password = "";
    					$dbname = "Sisbdpref";

    					$conn = new mysqli($servername, $username, $password, $dbname);

   						// Consulta para obtener las aldeas
    					$sql = "SELECT Ald_codig, Ald_nombr FROM preftmald";
    					$result = $conn->query($sql);

    					if ($result->num_rows > 0) {
        				// Imprimir opciones del select
        				while($row = $result->fetch_assoc()) {
            				echo "<option value='" . $row["Ald_codig"] . "'>" . $row["Ald_nombr"] . "</option>";
        				}
    					} else {
        					echo "<option value=''>No hay aldeas disponibles</option>";
    					}

    					// Cerrar conexión
    					$conn->close();
    					?>
                    </select>
                    
                    <label for="testigo2-calle">Calle:</label>
                    <input type="text" id="testigo2-calle" name="testigo2-calle">
                    
                    <label for="testigo2-carrera">Carrera:</label>
                    <input type="text" id="testigo2-carrera" name="testigo2-carrera">
                    
                    <label for="testigo2-casa">N° de Casa:</label>
                    <input type="text" id="testigo2-casa" name="testigo2-casa">
                </fieldset>
                
                <button type="submit">Enviar</button>
            </form>
        </section>

        <!-- Constancia de Pobreza -->
        <section id="constancia-pobreza" class="form-section">
            <h2>Constancia de Pobreza</h2>
            <form action="registrar_pobreza.php" method="post" enctype="multipart/form-data">
                <label for="pobreza">Motivo:</label>
                <input type="text" id="pobreza" name="pobreza">

				<button type="submit">Enviar</button>
            </form>
        </section>

        <!-- Constancia de Residencia -->
        <section id="fe-de-vida" class="form-section">
            <h2>Constancia Fe de Vida</h2>
            <form action="registrar_fe.php" method="post" enctype="multipart/form-data">
                <label for="fe-de-vida">Motivo:</label>
                <input type="text" id="fe-de-vida" name="fe-de-vida">
                
                <button type="submit">Enviar</button>
            </form>
        </section>

		
    </div>

	<h1>Mis trámites</h1>

    <h2>Notificación de Evento Público</h2>
    <table class="constancia-table-t">
        <tr>
            <th>Tipo de evento</th>
            <th>Motivo</th>
            <th>Aldea</th>
            <th>Calle</th>
            <th>Carrera</th>
            <th>Lugar</th>
            <th>Fecha de Inicio</th>
            <th>Hora de inicio</th>
            <th>Fecha de fin</th>
            <th>Hora de fin</th>
            <th>Duración (minutos)</th>
            <th>Posible asistencia</th>
            <th>Fecha de solicitud</th>
            <th>Estatus</th>
        </tr>
        <?php while ($row = $result_notificacion_evento->fetch_assoc()): ?>
        <tr>
            <td><?= $row['Tpe_nombr'] ?></td>
            <td><?= $row['Sep_motiv'] ?></td>
            <td><?= $row['Ald_nombr'] ?></td>
            <td><?= $row['Sep_calle'] ?></td>
            <td><?= $row['Sep_carre'] ?></td>
            <td><?= $row['Sep_delug'] ?></td>
            <td><?= $row['Sep_finic'] ?></td>
            <td><?= $row['Sep_hinic'] ?></td>
            <td><?= $row['Sep_ffinl'] ?></td>
            <td><?= $row['Sep_hfinl'] ?></td>
            <td><?= $row['Sep_durac'] ?></td>
            <td><?= $row['Sep_asist'] ?></td>
            <td><?= $row['Sep_fsoli'] ?></td>
            <td><?= $row['Sep_statu'] ?></td>
        </tr>
        <?php endwhile; ?>
    </table>

    <h2>Constancia de Pobreza</h2>
    <table class="constancia-table-t">
        <tr>
            <th>Motivo</th>
            <th>Fecha de solicitud</th>
            <th>Estatus</th>
        </tr>
        <?php while ($row = $result_constancia_pobreza->fetch_assoc()): ?>
        <tr>
            <td><?= $row['Spo_motiv'] ?></td>
            <td><?= $row['Spo_fsoli'] ?></td>
            <td><?= $row['Spo_statu'] ?></td>
        </tr>
        <?php endwhile; ?>
    </table>

    <h2>Constancia Fe de Vida</h2>
    <table class="constancia-table-t">
        <tr>
            <th>Motivo</th>
            <th>Fecha de solicitud</th>
            <th>Estatus</th>
        </tr>
        <?php while ($row = $result_constancia_fe_de_vida->fetch_assoc()): ?>
        <tr>
            <td><?= $row['Sfe_motiv'] ?></td>
            <td><?= $row['Sfe_fsoli'] ?></td>
            <td><?= $row['Sfe_statu'] ?></td>
        </tr>
        <?php endwhile; ?>
    </table>



    <script>

		
document.addEventListener('DOMContentLoaded', () => {
    const selectElement = document.getElementById('tramite-select');
    const sections = document.querySelectorAll('.form-section');

    selectElement.addEventListener('change', (event) => {
        const selectedValue = event.target.value;
        
        sections.forEach(section => {
            if (section.id === selectedValue) {
                section.style.display = 'block';
            } else {
                section.style.display = 'none';
            }
        });
    });
});

    </script>
			  	</section>
         	</div>
			 
			<!-- Portfolio Section -->
          	<div class="page pt-portfolio" data-simplebar>
            	<section class="container">
					
					<!-- Section Title -->
					<div class="header-page mt-70 mob-mt">
						<h2>Información de usuario</h2>
    					<span></span>
					</div>
					
					<div class="usuario_info">
      
        
        <div class="info-section">
            <h3>Datos Personales</h3>
            <div class="info-row">
                <div>
                    <label>Cédula:</label>
                    <p>1</p>
                </div>
                <div>
                    <label>Nombres:</label>
                    <p>Brandon</p>
                </div>
                <div>
                    <label>Apellidos:</label>
                    <p>Sayago</p>
                </div>
            </div>
        </div>
        
        <div class="info-section">
            <h3>Información de Contacto</h3>
            <div class="info-row">
                <div>
                    <label>Teléfono:</label>
                    <p>04148372637</p>
                </div>
                <div>
                    <label>Correo:</label>
                    <p>bran@gmail.com</p>
                </div>
            </div>
        </div>
        
        <div class="info-section">
            <h3>Información de Residencia</h3>
            <div class="info-row">
                <div>
                    <label>Aldea:</label>
                    <p>Borotá</p>
                </div>
                <div>
                    <label>Calle:</label>
                    <p>1</p>
                </div>
            </div>
            <div class="info-row">
                <div>
                    <label>Carrera:</label>
                    <p>2</p>
                </div>
                <div>
                    <label>Número de Casa:</label>
                    <p>123</p>
                </div>
            </div>
        </div>
        
        <div class="info-section">
            <h3>Documentos</h3>
            <div class="photo-container">
                <div class="photo" onclick="openModal('../assets/img/cedula.jpg', 'Foto de la Cédula')">
                    <img src="../assets/img/cedula.jpg" alt="Foto de la Cédula">
                    <p>Foto de la Cédula</p>
                </div>
                <div class="photo" onclick="openModal('../assets/img/rif.jpg', 'Foto del RIF')">
                    <img src="../assets/img/rif.jpg" alt="Foto del RIF">
                    <p>Foto del RIF</p>
                </div>
            </div>
        </div>

    </div>

    <!-- Modal -->
    <div id="myModal" class="modal">
        <span class="close" onclick="closeModal()">&times;</span>
        <img class="modal-content" id="modalImage">
        <div id="caption"></div>
    </div>

    <script>
        function openModal(src, alt) {
            var modal = document.getElementById("myModal");
            var modalImg = document.getElementById("modalImage");
            var captionText = document.getElementById("caption");
            modal.style.display = "block";
            modalImg.src = src;
            modalImg.alt = alt;
            captionText.innerHTML = alt;
        }

        function closeModal() {
            var modal = document.getElementById("myModal");
            modal.style.display = "none";
        }
    </script>
				</section>
       	   	</div>
			 <!-- Blog Section -->
          	<div class="page pt-blog" data-simplebar>
            	<section class="container">
					
					<!-- Section Title -->
             		<div class="header-page mt-70 mob-mt">
						<h2>Blog</h2>
    					<span></span>
					</div>
					
					<!-- Blog Row Start -->
					<div class="row blog-masonry mt-100 mb-50">
						
						<!-- Blog Item -->
						<div class="col-lg-4 col-sm-6">
							<div class="blog-item">
								<div class="thumbnail">
									<a href="single-blog.html"><img alt="" src="../img/blog/img-1.jpg"></a>
								</div>
								<h4><a href="single-blog.html">Road to success</a></h4>
								<ul>
                            		<li><a href="#">15 April 2019</a></li>
                            		<li><a href="#">Lifestyle</a></li>
                           		</ul>
								<div class="blog-btn">
									<a href="single-blog.html" class="btn-st">Read More</a>
								</div>
							</div>
						</div>

						<!-- Blog Item -->
						<div class="col-lg-4 col-sm-6">
							<div class="blog-item">
								<div class="thumbnail">
									<a href="single-blog.html"><img alt="" src="../img/blog/img-2.jpg"></a>
									<a href="https://www.youtube.com/watch?v=k_okcNVZqqI" class="btn-play"></a>
								</div>
								<h4><a href="single-blog.html">Road to success</a></h4>
								<ul>
                            		<li><a href="#">10 March 2019</a></li>
                            		<li><a href="#">Lifestyle</a></li>
                           		</ul>
								<div class="blog-btn">
									<a href="single-blog.html" class="btn-st">Read More</a>
								</div>
							</div>
						</div>

						<!-- Blog Item -->
						<div class="col-lg-4 col-sm-6">
							<div class="blog-item">
								<div class="thumbnail">
									<a href="single-blog.html"><img alt="" src="../img/blog/img-3.jpg"></a>
								</div>
								<h4><a href="single-blog.html">Road to success</a></h4>
								<ul>
                            		<li><a href="#">02 March 2019</a></li>
                            		<li><a href="#">Work</a></li>
                            	</ul>
								<p>Tower Hamlets or mass or members of propaganda bananas real estate. However, a large and a mourning, vel euismod.</p>
								<div class="blog-btn">
									<a href="single-blog.html" class="btn-st">Read More</a>
								</div>
							</div>
						</div>

						<!-- Blog Item -->
						<div class="col-lg-4 col-sm-6">
							<div class="blog-item">
								<div class="thumbnail">
									<a href="single-blog.html"><img alt="" src="../img/blog/img-4.jpg"></a>
								</div>
								<h4><a href="single-blog.html">Road to success</a></h4>
								<ul>
                            		<li><a href="#">29 March 2019</a></li>
                            		<li><a href="#">Career</a></li>
                            	</ul>
								<div class="blog-btn">
									<a href="single-blog.html" class="btn-st">Read More</a>
								</div>
							</div>
						</div>

						<!-- Blog Item -->
						<div class="col-lg-4 col-sm-6">
							<div class="blog-item">
								<div class="thumbnail">
									<a href="single-blog.html"><img alt="" src="../img/blog/img-5.jpg"></a>
								</div>
								<h4><a href="single-blog.html">Road to success</a></h4>
								<ul>
									<li><a href="#">14 April 2019</a></li>
                            		<li><a href="#">Lifestyle</a></li>
                            	</ul>
								<p>Tower Hamlets or mass or members of propaganda bananas real estate. However, a large and a mourning, vel euismod.</p>
								<div class="blog-btn">
									<a href="single-blog.html" class="btn-st">Read More</a>
								</div>
							</div>
						</div>

						<!-- Blog Item -->
						<div class="col-lg-4 col-sm-6">
							<div class="blog-item">
								<div class="thumbnail">
									<a href="single-blog.html"><img alt="" src="../img/blog/img-6.jpg"></a>
									<a href="https://www.youtube.com/watch?v=k_okcNVZqqI" class="btn-play"></a>
								</div>
								<h4><a href="single-blog.html">Road to success</a></h4>
								<ul>
                           		 	<li><a href="#">29 April 2019</a></li>
                           		 	<li><a href="#">Career</a></li>
                        	    </ul>
								<div class="blog-btn">
									<a href="single-blog.html" class="btn-st">Read More</a>
								</div>
							</div>
						</div>
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


<?php
// Cerrar conexión
$conexion->close();
?>