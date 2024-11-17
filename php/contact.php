<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="utf-8">
        <title>Parroquia Constitución</title>
        <meta content="width=device-width, initial-scale=1.0" name="viewport">
        <meta content="" name="keywords">
        <meta content="" name="description">

        <!-- BOXICONS -->
        <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

        <!-- Google Web Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500;600&family=Playfair+Display:wght@400;500;600&display=swap" rel="stylesheet"> 

        <!-- Icon Font Stylesheet -->
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css"/>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

        <!-- Libraries Stylesheet -->
        <link href="../assets/css/animate.min.css" rel="stylesheet">
        <link href="../assets/css/owl.carousel.min.css" rel="stylesheet">


        <!-- Customized Bootstrap Stylesheet -->
        <link href="../assets/css/bootstrap.min.css" rel="stylesheet">

        <!-- Template Stylesheet -->
        <link href="../assets/css/contacto.css" rel="stylesheet">

        <!-- Icono de la pagina -->
        <link rel="icon" href="../assets/img/icono/logo-escudo.ico">
    </head>

    <body>

        <!-- Spinner Start -->
        <div id="spinner" class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
            <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>
        <!-- Spinner End -->


        <!----------------------------- nav ----------------------------------->

        <nav class="nav">
            <div class="nav-logo">
                <img src="../assets/img/logo-removebg-preview.png" alt="">
            </div>
            <div class="nav-menu" id="navMenu">
                <ul>
                    <li><a href="../index.php" class="link">Principal</a></li>
                    <li><a href="contact.php" class="link active">Contáctanos</a></li>
                    <li class="nav-button">
                        <a href="login.php?section=login" class="btn" id="loginBtn">Iniciar sesión</a>
                        <a href="login.php?section=register" class="btn" id="registerBtn">Crear sesión</a>
                    </li>
                </ul>
            </div>
            
            <div class="nav-menu-btn">
                <i class="bx bx-menu" onclick="myMenuFunction()"></i>
            </div>
        </nav>

        <!----------------------------- nav end ----------------------------------->

        <!-- Header Start -->
        <div class="container-fluid bg-breadcrumb">
            <div class="container text-center py-5" style="max-width: 900px;">
                <h3 class="text-white display-3 mb-4 wow fadeInDown" data-wow-delay="0.1s">Contáctenos</h1>   
            </div>
        </div>
        <!-- Header End -->


        <!-- Contact Start -->
        <div class="container-fluid contact py-5">
            <div class="container py-5">
                <div class="section-title mb-5 wow fadeInUp" data-wow-delay="0.1s">
                    <div class="sub-style mb-4">
                        <h4 class="sub-title text-white px-3 mb-0">Contáctenos</h4>
                    </div>
                    
                </div>
                <div class="row g-4 align-items-center">
                    <div class="col-lg-5 col-xl-5 contact-form wow fadeInLeft" data-wow-delay="0.1s">
                        <h2 class="display-5 text-white mb-2">Póngase en contacto con nosotros</h2>
                        <p class="mb-4 text-white">Si quieres obtener una consulta sin necesidad de crear un usuario, no te preocupes, envíanos un mensaje a través del siguiente formulario y estaremos dispuestos a contestarte lo más pronto posible. <!--<a class="text-dark fw-bold" href="https://htmlcodex.com/contact-form">Download Now</a>.</p> -->
                        <form>
                            <div class="row g-3">
                                <div class="col-lg-12 col-xl-6">
                                    <div class="form-floating">
                                        <input type="text" class="form-control bg-transparent border border-white" id="name" placeholder="Your Name">
                                        <label for="name">Nombre(S)</label>
                                    </div>
                                </div>
                                <div class="col-lg-12 col-xl-6">
                                    <div class="form-floating">
                                        <input type="text" class="form-control bg-transparent border border-white" id="name" placeholder="Your Name">
                                        <label for="name">Apellido(S)</label>
                                    </div>
                                </div>
                                <div class="col-lg-12 col-xl-6">
                                    <div class="form-floating">
                                        <input type="email" class="form-control bg-transparent border border-white" id="email" placeholder="Your Email">
                                        <label for="email">Correo electrónico</label>
                                    </div>
                                </div>
                                <div class="col-lg-12 col-xl-6">
                                    <div class="form-floating">
                                        <input type="phone" class="form-control bg-transparent border border-white" id="phone" placeholder="Phone">
                                        <label for="phone">Teléfono</label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-floating">
                                        <input type="text" class="form-control bg-transparent border border-white" id="subject" placeholder="Subject">
                                        <label for="subject">Asunto</label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-floating">
                                        <textarea class="form-control bg-transparent border border-white" placeholder="Leave a message here" id="message" style="height: 160px"></textarea>
                                        <label for="message">Mensaje</label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <button class="btn btn-light text-primary w-100 py-3">Enviar mensaje</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="col-lg-2 col-xl-2 wow fadeInUp" data-wow-delay="0.5s">
                        <div class="bg-transparent rounded">
                            <div class="d-flex flex-column align-items-center text-center mb-4">
                                <div class="bg-white d-flex align-items-center justify-content-center mb-3" style="width: 90px; height: 90px; border-radius: 50px;"><i class="fa fa-map-marker-alt fa-2x text-primary"></i></div>
                                <h4 class="text-dark">Ubicación</h4>
                                <p class="mb-0 text-white">Paseo turístico, Borotá, Táchira, Venezuela</p>
                            </div>
                            <div class="d-flex flex-column align-items-center text-center mb-4">
                                <div class="bg-white d-flex align-items-center justify-content-center mb-3" style="width: 90px; height: 90px; border-radius: 50px;"><i class="fa fa-phone-alt fa-2x text-primary"></i></div>
                                <h4 class="text-dark">Télefono(s)</h4>
                                <p class="mb-0 text-white">+58 000 0000000</p>
                                <p class="mb-0 text-white">+58 000 0000000</p>
                            </div>
                           
                            <div class="d-flex flex-column align-items-center text-center">
                                <div class="bg-white d-flex align-items-center justify-content-center mb-3" style="width: 90px; height: 90px; border-radius: 50px;"><i class="fa fa-envelope-open fa-2x text-primary"></i></div>
                                <h4 class="text-dark">Correo(s) electrónico(s)</h4>
                                <p class="mb-0 text-white">----@gmail.com</p>
                                <p class="mb-0 text-white">----@gmail.com</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-5 col-xl-5 wow fadeInRight" data-wow-delay="0.3s">
                        <div class="d-flex justify-content-center mb-4">
                            <a class="btn btn-lg-square btn-light rounded-circle mx-2" href=""><i class="fab fa-facebook-f"></i></a>
                            <a class="btn btn-lg-square btn-light rounded-circle mx-2" href=""><i class="fab fa-twitter"></i></a>
                            <a class="btn btn-lg-square btn-light rounded-circle mx-2" href=""><i class="fab fa-instagram"></i></a>
                            <a class="btn btn-lg-square btn-light rounded-circle mx-2" href=""><i class="fab fa-linkedin-in"></i></a>
                        </div>
                        <div class="rounded h-100">
                            <iframe class="rounded w-100" 
                            style="height: 500px;" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3171.333705642122!2d-72.2401026617167!3d7.893289746207562!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x8e6669d55b0e3c8f%3A0x59144561e1016b60!2sDPPC%20PARROQUIA%20CONSTITUCI%C3%93N!5e0!3m2!1ses!2sus!4v1716776085503!5m2!1ses!2sus" 
                            loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Contact End -->

        <!------------------- footer -------------------------->
    <footer>

        <div class="logo-footer">
            <img src="../assets/img/logos-gobernacion-del-tachira.png" alt="gobernacion_del_tachira">

        </div>
        <div class="footerContainer">
            <div class="socialIcons">
                <a href=""><i class="fa-brands fa-facebook"></i></a>
                <a href=""><i class="fa-brands fa-instagram"></i></a>
                <a href=""><i class="fa-brands fa-twitter"></i></a>
                <a href=""><i class="fa-brands fa-google-plus"></i></a>
                <a href=""><i class="fa-brands fa-youtube"></i></a>
            </div>
            
        </div>
        <div class="footerBottom">
            <p>Copyright &copy;2024; Designed by <span class="designer">Brandon Sayago</span></p>
        </div>
    </footer>


        <!------------------- footer end -------------------------->

        


        <!-- Back to Top -->
        <a href="#" class="btn btn-primary btn-lg-square back-to-top"><i class="fa fa-arrow-up"></i></a>   

        
        <!-- JavaScript Libraries -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
        <script src="../assets/js/wow.min.js"></script>
        <script src="../assets/js/easing.min.js"></script>
        <script src="../assets/js/waypoints.min.js"></script>
        <script src="../assets/js/owl.carousel.min.js"></script>
        

        <!-- Template Javascript -->
        <script src="../assets/js/main.js"></script>
        
        <script>
       
            function myMenuFunction() {
             var i = document.getElementById("navMenu");
             if(i.className === "nav-menu") {
                 i.className += " responsive";
             } else {
                 i.className = "nav-menu";
             }
            }
          
        </script>
    </body>

</html>