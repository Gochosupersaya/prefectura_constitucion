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
        <link href="assets/css/animate.min.css" rel="stylesheet">
        <link href="assets/css/owl.carousel.min.css" rel="stylesheet">


        <!-- Customized Bootstrap Stylesheet -->
        <link href="assets/css/bootstrap.min.css" rel="stylesheet">

        <!-- Template Stylesheet -->
        <link href="assets/css/contacto.css" rel="stylesheet">

        <!-- Icono de la pagina -->
        <link rel="icon" href="assets/img/icono/logo-escudo.ico">
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
                <img src="assets/img/logo-removebg-preview.png" alt="">
            </div>
            <div class="nav-menu" id="navMenu">
                <ul>
                    <li><a href="index.php" class="link active">Principal</a></li>
                    <li><a href="./php/contact.php" class="link">Contáctanos</a></li>
                    <li class="nav-button">
                        <a href="./php/login.php?section=login" class="btn" id="loginBtn">Iniciar sesión</a>
                        <a href="./php/login.php?section=register" class="btn" id="registerBtn">Crear sesión</a>
                    </li>
                </ul>
            </div>
            
            <div class="nav-menu-btn">
                <i class="bx bx-menu" onclick="myMenuFunction()"></i>
            </div>
        </nav>

        <!----------------------------- nav end ----------------------------------->

                 <!-- Carousel Start -->
                 <div class="header-carousel owl-carousel">
                <div class="header-carousel-item">
                    <img src="assets/img/IMG_20230602_182304.jpg" class="img-fluid w-100" alt="Image">
                    <div class="carousel-caption">
                        <div class="carousel-caption-content p-3">
                            <h1 class="display-1 text-capitalize text-white mb-4">Borotá, estado Táchira, Venezuela</h1>
                            
                        </div>
                    </div>
                </div>
                <div class="header-carousel-item">
                    <img src="assets/img/prefectura1.jpg" class="img-fluid w-100" alt="Image">
                    <div class="carousel-caption">
                        <div class="carousel-caption-content p-3">
                            
                            <h1 class="display-1 text-capitalize text-white mb-4">Prefectura, Parroquia constitución</h1>
                            
                        </div>
                    </div>
                </div>
            </div>
            <!-- Carousel End -->
        </div>
        <!-- Navbar & Hero End -->


        <!-- Services Start -->
        <div class="container-fluid service py-5">
            <div class="container py-5">
                <div class="section-title mb-5 wow fadeInUp" data-wow-delay="0.2s">
                    <div class="sub-style">
                        <h4 class="sub-title px-3 mb-0">----</h4>
                    </div>
                    <h1 class="display-3 mb-4">Conoce el nuevo sistema de la Prefectura de la Parroquia Constitución</h1>
                    <p class="mb-0">Un sistema con el objetivo de facilitar los principales procesos que podemos encontrar en la Prefectura de la Parroquia Constitución en Borotá. Beneficia mayormente a la población en la jurisdicción de la institución</p>
                </div>
                
            </div>
        </div>
        <!-- Services End -->


        

        <!-- Feature Start -->
        <div class="container-fluid feature py-5">
            <div class="container py-5">
                
                <div class="row g-4 justify-content-center">
                    
                    
                    <div class="col-md-6 col-lg-4 col-xl-3 wow fadeInUp" data-wow-delay="0.7s">
                        <div class="row-cols-1 feature-item p-4">
                            <div class="col-12">
                                <div class="feature-icon mb-4">
                                    <div class="p-3 d-inline-flex bg-white rounded">
                                        <i class="fas fa-users fa-4x text-primary"></i>
                                    </div>
                                </div>
                                <div class="feature-content d-flex flex-column">
                                    <h5 class="mb-4">Denuncias</h5>
                                    <p class="mb-0">Existe un cierto nivel de denuncias locales que se pueden resolver en la prefectura, el sistema será capaz de facilitar su desarrollo</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6 col-lg-4 col-xl-3 wow fadeInUp" data-wow-delay="0.5s">
                        <div class="row-cols-1 feature-item p-4">
                            <div class="col-12">
                                <div class="feature-icon mb-4">
                                    <div class="p-3 d-inline-flex bg-white rounded">
                                        <i class="fab fa-pied-piper fa-4x text-primary"></i>
                                    </div>
                                </div>
                                <div class="feature-content d-flex flex-column">
                                    <h5 class="mb-4">Trámites</h5>
                                    <p class="mb-0">La Prefectura es responsable de una variada emision de constancias, las cuales se facilitaran a través del sistema</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    
                </div>
            </div>
        </div>
        <!-- Feature End -->



    <!------------------- footer -------------------------->
    <footer>

        <div class="logo-footer">
            <img src="assets/img/logos-gobernacion-del-tachira.png" alt="gobernacion_del_tachira">

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
        <script src="assets/js/wow.min.js"></script>
        <script src="assets/js/easing.min.js"></script>
        <script src="assets/js/waypoints.min.js"></script>
        <script src="assets/js/owl.carousel.min.js"></script>
        

        <!-- Template Javascript -->
        <script src="assets/js/main.js"></script>
        
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