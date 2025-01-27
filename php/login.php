<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/inicio.css">
    <link rel="icon" href="../assets/img/icono/logo-escudo.ico">
    <title>Parroquia Constitución</title>
</head>
<body>
    <div class="wrapper">
        <nav class="nav">
            <div class="nav-logo">
                <img src="../assets/img/logo-removebg-preview.png" alt="">
            </div>
            <div class="nav-menu" id="navMenu">
                <ul>
                    <li><a href="../index.php" class="link">Principal</a></li>
                    <li><a href="contact.php" class="link">Contáctanos</a></li>
                    <li class="nav-button">
                        <button class="btn white-btn" id="loginBtn" onclick="login()">Iniciar sesión</button>
                        <button class="btn" id="registerBtn" onclick="register()">Crear sesión</button>
                    </li>
                </ul>
            </div>
            <div class="nav-menu-btn">
                <i class="bx bx-menu" onclick="myMenuFunction()"></i>
            </div>
        </nav>
        
        <!----------------------------- Form box ----------------------------------->
        <div class="form-box">
            <!------------------- login form -------------------------->
            <div class="login-container" id="login">
                <form method="post" action="iniciar_usuario.php" id="loginForm">
                    <div class="top">
                        <span>¿No tienes una cuenta? <a href="#" onclick="register()">Crea una sesión</a></span>
                        <header>Iniciar Sesión</header>
                    </div>
                    <div class="input-box">
                        <input type="text" class="input-field" name="cedula" placeholder="Cédula de identidad" required>
                        <i class="bx bx-user"></i>
                    </div>
                    <div class="input-box">
                        <input type="password" class="input-field" name="contrasena" placeholder="Contraseña" required>
                        <i class="bx bx-lock-alt"></i>
                    </div>
                    <div class="input-box">
                        <input type="submit" class="submit" value="Iniciar Sesión">
                    </div>
                    <div class="two-col">
                        <div class="one">
                        <input type="checkbox" id="login-check">
                        <label for="login-check">Recordar</label>
                    </div>
                    <div class="two">
                        <label><a href="#">¿Has olvidado tu contraseña?</a></label>
                    </div>
                </form>
            </div>
        </div>

        <!------------------- Modal de Error -------------------------->
        <div id="errorModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeModal()">&times;</span>
                <div class="modal-icon">
                    <!-- Ícono para el mensaje de error -->
                    <i id="modalIcon" class="fas fa-exclamation-circle" style="font-size: 60px; color: #f44336;"></i>
                </div>
                <p id="errorMessage"></p>
                <button class="close-button" onclick="closeModal()">Cerrar</button>
            </div>
        </div>
            
        <!------------------- registration form -------------------------->
            
        <div class="register-container" id="register">
            <form method="post" action="registro_usuario.php" enctype="multipart/form-data">
                <div class="top">
                    <span>¿Tienes una cuenta? <a href="#" onclick="login()">Inicia sesión</a></span>
                    <header>Crear cuenta</header>
                </div>
                <div class="input-box">
                    <input type="text" class="input-field" name="cedula" id="cedula" placeholder="Cédula de identidad" required>
                    <i class="bx bx-user"></i>
                </div>
                <div class="input-box">
                    <input type="email" class="input-field" name="correo" id="correo" placeholder="Correo electrónico" required>
                    <i class="bx bx-envelope"></i>
                </div>
                <div class="input-box">
                    <input type="password" class="input-field" name="contrasena" id="contrasena" placeholder="Contraseña" required>
                    <i class="bx bx-lock-alt"></i>
                </div>
                <div class="input-box">
                    <input type="button" class="submit" value="Continuar" onclick="nextRegisterStep()">
                </div>

                <!-- Modal para Cédula -->
                <div id="modalCedula" class="modal">
                    <div class="modal-content">
                        <span class="close" onclick="closeModal('modalCedula')">&times;</span>
                        <div class="modal-body">
                            <div class="modal-icon">
                                <i class="bx bx-error-circle" style="font-size: 60px; color: #f44336;"></i>
                            </div>
                            <p id="errorMessage">La cédula debe tener entre 7 y 8 dígitos.</p>
                            <button class="close-button" onclick="closeModal('modalCedula')">Cerrar</button>
                        </div>
                    </div>
                </div>

                <!-- Modal para Correo -->
                <div id="modalCorreo" class="modal">
                    <div class="modal-content">
                        <span class="close" onclick="closeModal('modalCorreo')">&times;</span>
                        <div class="modal-body">
                            <div class="modal-icon">
                                <i class="bx bx-error-circle" style="font-size: 60px; color: #f44336;"></i>
                            </div>
                            <p id="errorMessage">El correo electrónico debe contener un '@'.</p>
                            <button class="close-button" onclick="closeModal('modalCorreo')">Cerrar</button>
                        </div>
                    </div>
                </div>

                <!-- Modal para Contraseña -->
                <div id="modalContrasena" class="modal">
                    <div class="modal-content">
                        <span class="close" onclick="closeModal('modalContrasena')">&times;</span>
                        <div class="modal-body">
                            <div class="modal-icon">
                                <i class="bx bx-error-circle" style="font-size: 60px; color: #f44336;"></i>
                            </div>
                            <p id="errorMessage">La contraseña debe tener al menos 7 caracteres.</p>
                            <button class="close-button" onclick="closeModal('modalContrasena')">Cerrar</button>
                        </div>
                    </div>
                </div>

        </div>
            
            <!------------------- additional registration form -------------------------->
            <?php
                // Función para obtener el nombre de la aldea a partir de su código
                function obtenerNombreAldea($codigo_aldea) {
                    include('conexion.php'); // Incluye el archivo de conexión

                    $sql = "SELECT Ald_nombr FROM preftmald WHERE Ald_codig = ?";
                    $stmt = $conexion->prepare($sql);
                    $stmt->bind_param("i", $codigo_aldea);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($result->num_rows > 0) {
                        $row = $result->fetch_assoc();
                        return $row['Ald_nombr'];
                    } else {
                        return "Aldea Desconocida";
                    }
                }
                function obtenerNombreMunicipio($codigo_municipio) {
                    include('conexion.php'); // Incluye el archivo de conexión

                    $sql = "SELECT Mun_nombr FROM preftmmun WHERE Mun_codig = ?";
                    $stmt = $conexion->prepare($sql);
                    $stmt->bind_param("i", $codigo_municipio);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($result->num_rows > 0) {
                        $row = $result->fetch_assoc();
                        return $row['Mun_nombr'];
                    } else {
                        return "Municipio Desconocida";
                    }
                }
    
            ?>

            <div class="register-container" id="additional-register">
                
                <div class="top">
                    <span>Volver a <a href="#" onclick="register()">registro inicial</a></span>
                    <header>Completar registro</header>
                </div>
                <div class="two-forms">
                    <div class="input-box">
                        <input type="text" class="input-field" name="nombre" placeholder="Nombre" required>
                            <i class="bx bx-user"></i>
                        </div>
                        <div class="input-box">
                            <input type="text" class="input-field" name="apellido" placeholder="Apellido" required>
                            <i class="bx bx-user"></i>
                        </div>
                    </div>
                    <div class="input-box">
                        <input type="text" class="input-field" name="telefono" placeholder="Teléfono" required>
                        <i class="bx bx-phone"></i>
                    </div>
                    <div class="two-forms">
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
                    </div>
                    

                    <div class="input-box">
                    <label>vive en:</label>
                        <select class="input-field" name="residencia" id="residencia" onchange="toggleResidencia()" required>
                            <option value="">Seleccione una opcion</option>
                            <option value="constitucion">Parroquia Constitución</option>
                            <option value="fuera">Fuera de la parroquia</option>
                        </select>
                    </div>
                    <div class="direccion_fei" id="direccion-constitucion" >
                        <div class="input-box">
                            <select class="input-field" id="aldea" name="aldea">
                                <option value="">Seleccione una aldea</option>
						
                                <?php
    					            include('conexion.php');

   						            // Consulta para obtener las aldeas
    					            $sql = "SELECT Ald_codig, Ald_nombr FROM preftmald";
    					            $result = $conexion->query($sql);

    					            if ($result->num_rows > 0) {
        				                // Imprimir opciones del select
        				                while($row = $result->fetch_assoc()) {
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
                        <div class="two-forms">
                            <div class="input-box">
                                <input type="text" class="input-field" name="calle1" placeholder="Calle">
                            </div>
                            <div class="input-box">
                                <input type="text" class="input-field" name="carre1" placeholder="Carrera">
                            </div>
                        </div>
                        
                        <div class="input-box">
                            <input type="text" class="input-field" name="ncasa1" placeholder="Nº de casa">
                        </div>
                    </div>

                    <div class="direccion_fei" id="direccion-fuera">
                        <div class="input-box">
                            <select class="input-field" id="municipio" name="municipio">
                                <option value="">Seleccione un municipio</option>
						
                                <?php
    					            include('conexion.php');

   						            // Consulta para obtener los municipios
    					            $sql = "SELECT Mun_codig, Mun_nombr FROM preftmmun";
    					            $result = $conexion->query($sql);

    					            if ($result->num_rows > 0) {
        				            // Imprimir opciones del select
        				                while($row = $result->fetch_assoc()) {
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
                        <div class="two-forms">
                            <div class="input-box">
                                <input type="text" class="input-field" name="calle" placeholder="Calle">
                            </div>
                            <div class="input-box">
                                <input type="text" class="input-field" name="carre" placeholder="Carrera">
                            </div>
                        </div>
                        <div class="input-box">
                            <input type="text" class="input-field" name="ncasa" placeholder="Nº de casa">
                        </div>
                    </div>
                    <div class="input-box submit-container">
                        <input type="submit" class="submit" value="Registrar">
                    </div>
                </form>
            </div>
        </div>
    </div>

    <footer>
        <div class="logo-footer">
            <img src="../assets/img/logos-gobernacion-del-tachira.png" alt="Gobernación del Táchira">
        </div>
        <div class="footerContainer">
            <div class="socialIcons">
                <a href="#"><i class="fab fa-facebook"></i></a>
                <a href="#"><i class="fab fa-instagram"></i></a>
                <a href="#"><i class="fab fa-twitter"></i></a>
                <a href="#"><i class="fab fa-google-plus"></i></a>
                <a href="#"><i class="fab fa-youtube"></i></a>
            </div>
        </div>
        <div class="footerBottom">
            <p class="text-center">© 2023 Parroquia Constitución. Todos los derechos reservados.</p>
            <p class="text-center designer">Diseñado por Parroquia Constitución</p>
        </div>
    </footer>
   

    
    <script>
       function login() {
            document.getElementById("login").style.left = "4px";
            document.getElementById("register").style.right = "-520px";
            document.getElementById("additional-register").style.right = "-1040px";
        }

        function register() {
            document.getElementById("login").style.left = "-520px";
            document.getElementById("register").style.right = "4px";
            document.getElementById("additional-register").style.right = "-520px";
        }


        function toggleResidencia() {
        var residencia = document.getElementById("residencia").value;
        if (residencia === "constitucion") {
            document.getElementById("direccion-constitucion").style.display = "block";
            document.getElementById("direccion-fuera").style.display = "none";
        } else {
            document.getElementById("direccion-constitucion").style.display = "none";
            document.getElementById("direccion-fuera").style.display = "block";
            }
        }
    </script>


    <script>
        document.getElementById('loginForm').addEventListener('submit', function(event) {
            event.preventDefault(); // Evita el envío normal del formulario

            const formData = new FormData(this);

            // Enviar el formulario mediante fetch
            fetch('iniciar_usuario.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === "success") {
                    // Redirigir si es exitoso
                    window.location.href = data.redirect;
                } else {
                    // Selecciona el ícono adecuado según el mensaje de error
                    let iconClass;
                    if (data.message === "Usuario no encontrado") {
                        iconClass = 'fas fa-user-slash'; // Ícono de usuario no encontrado
                    } else if (data.message === "Contraseña incorrecta") {
                        iconClass = 'fas fa-lock'; // Ícono de candado para contraseña incorrecta
                    } else {
                        iconClass = 'fas fa-exclamation-circle'; // Ícono de error general
                    }

                    // Cambiar el ícono y mostrar el mensaje de error en el modal
                    document.getElementById('modalIcon').className = iconClass;
                    document.getElementById('errorMessage').textContent = data.message;
                    document.getElementById('errorModal').style.display = 'flex';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('modalIcon').className = 'fas fa-exclamation-circle'; // Ícono de error general
                document.getElementById('errorMessage').textContent = 'Ocurrió un error. Inténtalo de nuevo.';
                document.getElementById('errorModal').style.display = 'flex';
            });
        });

        // Función para cerrar el modal
        function closeModal() {
            document.getElementById('errorModal').style.display = 'none';
        }

    </script>

    <script>
        // Esta es la función combinada
        function nextRegisterStep() {
            // Obtener los valores de los campos
            const cedula = document.getElementById("cedula").value;
            const correo = document.getElementById("correo").value;
            const contrasena = document.getElementById("contrasena").value;

            // Validación de Cédula
            if (cedula.length < 7 || cedula.length > 8) {
                openModal('modalCedula');
                return;  // Detener el proceso si la validación falla
            }

            // Validación de Correo
            if (!correo.includes('@')) {
                openModal('modalCorreo');
                return;  // Detener el proceso si la validación falla
            }

            // Validación de Contraseña
            if (contrasena.length < 7) {
                openModal('modalContrasena');
                return;  // Detener el proceso si la validación falla
            }

            // Si todas las validaciones son correctas, mover a la siguiente parte del registro
            document.getElementById("register").style.right = "520px";  // Mover la primera sección
            document.getElementById("additional-register").style.right = "4px";  // Mover la segunda sección
        }

        // Función para abrir el modal
        function openModal(modalId) {
            const modal = document.getElementById(modalId);
            modal.style.display = "flex";  // Usamos flex para centrar el modal
        }

        // Función para cerrar el modal
        function closeModal(modalId) {
            const modal = document.getElementById(modalId);
            modal.style.display = "none";
        }

        // Cerrar modales cuando se hace clic fuera de la ventana modal
        window.onclick = function(event) {
            if (event.target.className === 'modal') {
            event.target.style.display = "none";
            }
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
                    
                }, file.type);
            };
            img.src = e.target.result;
        };
        reader.readAsDataURL(file);
    }
}

</script>

</body>
</html>
