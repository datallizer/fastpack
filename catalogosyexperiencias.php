<?php
session_start();
require 'dbcon.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="shortcut icon" type="image/x-icon" href="images/ics.ico">
    <title>Productos | Fastpack</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-0evHe/X+R7YkIZDRvuzKMRqM+OrBnVFBL6DOitfPri4tjfHxaWutUpFmBp4vmVor" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css" />
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/slickslider.css">
    <link rel="shortcut icon" href="images/ico.ico" type="image/x-icon">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.min.js"></script>
</head>
<style>
    .category_list {
        position: sticky;
        top: 100px;
        /* Ajusta esto según la altura de tu menú fijo o encabezado */
        height: calc(100%);
        /* Altura completa de la ventana menos el espacio de arriba */
        overflow-y: auto;
        /* Agrega scroll interno si la lista de categorías es más alta que la ventana */
        padding-right: 10px;
        /* Espacio entre la lista y el contenido principal */
    }
</style>

<body style="background-color: #f5f5f5;">
    <?php include 'componentes/menu.php'; ?>
    <?php include 'whatsapp.php'; ?>
    <div class="container-fluid">
        <div class="row mt-5" style="margin-top: 85px !important;padding:50px 50px;background-color:#20395d;">
            <h2 class="mb-3" style="color:#fff;">CATÁLOGOS</h2>
            <div class="slickcard mb-3">
                <?php
                $query = "SELECT * FROM catalogos WHERE estatus = 1 ORDER BY id DESC";
                $query_run = mysqli_query($con, $query);
                if (mysqli_num_rows($query_run) > 0) {
                    foreach ($query_run as $registro) {

                ?>
                        <div class="slickc">
                            <a href="descargar.php?file=<?= urlencode($registro['path']); ?>" style="text-decoration:none;" class="image-container">
                                <canvas id="pdfCanvas<?= $registro['id']; ?>" style="width: 100%; height: 230px;object-fit:cover;"></canvas>
                                <div class="overlay">
                                    <p class="mt-1" style="text-transform: uppercase; font-weight: 500;color:#fff;"><?= $registro['nombre']; ?></p>
                                </div>
                                
                            </a>
                            <a class="btn btn-danger btn-sm w-100" href="descargar.php?file=<?= urlencode($registro['path']); ?>"><i class="bi bi-download"></i> Descargar</a>

                            <script>
                                const pdfPath<?= $registro['id']; ?> = "<?= $registro['path']; ?>"; // Ruta al PDF

                                const canvas<?= $registro['id']; ?> = document.getElementById("pdfCanvas<?= $registro['id']; ?>");
                                const context<?= $registro['id']; ?> = canvas<?= $registro['id']; ?>.getContext("2d");

                                // Configura PDF.js
                                const loadingTask<?= $registro['id']; ?> = pdfjsLib.getDocument(pdfPath<?= $registro['id']; ?>);
                                loadingTask<?= $registro['id']; ?>.promise.then(function(pdf) {
                                    // Carga la primera página
                                    pdf.getPage(1).then(function(page) {
                                        const viewport = page.getViewport({
                                            scale: 0.5
                                        }); // Escala para ajustar
                                        canvas<?= $registro['id']; ?>.width = viewport.width;
                                        canvas<?= $registro['id']; ?>.height = viewport.height;

                                        // Renderiza la página en el canvas
                                        const renderContext = {
                                            canvasContext: context<?= $registro['id']; ?>,
                                            viewport: viewport
                                        };
                                        page.render(renderContext);
                                    });
                                }).catch(function(error) {
                                    console.error("Error al cargar el PDF: ", error);
                                });
                            </script>
                        </div>
                <?php
                    }
                } else {
                    echo "<p style='color:#fff;text-align:center;'>No hay catálogos disponibles</p>";
                }
                ?>
            </div>
        </div>
        <div class="row mb-5 mt-5 justify-content-start align-items-center" style="padding:0px 50px;">

            <h2 class="mb-3">VIDEOS</h2>
            <?php
            $query = "SELECT * FROM videos WHERE estatus = 1";

            $query_run = mysqli_query($con, $query);
            if (mysqli_num_rows($query_run) > 0) {
                foreach ($query_run as $registro) {
            ?>
                    <div class="col-12 col-md-4 mt-3">
                        <video style="height:240px;background-color:#e7e7e7;object-fit:cover;" class="w-100 videoPlayer" src="<?= $registro['path']; ?>" controls></video>
                        <h5 style="text-transform: uppercase; font-weight: 500;"><?= $registro['nombre']; ?></h5>
                    </div>

            <?php
                }
            } else {
                echo "<div class='col-12' style='text-align: center;'><p>No se encontró ningún video</p></div>";
            }
            ?>
        </div>
    </div>
    <?php include 'footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-pprn3073KE6tl6bjs2QrFaJGz5/SUsLqktiwsUTF55Jfv3qYSDhgCecCxMW52nD2" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script type="text/javascript" src="//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>
    <script src="js/slickslider.js"></script>
    <script>
        // Obtener todos los elementos de video con la clase 'videoPlayer'
        const videos = document.querySelectorAll('.videoPlayer');

        videos.forEach(function(video) {
            // Escuchar cuando cada video empieza a reproducirse
            video.addEventListener('play', function() {
                // Cambiar el object-fit a contain
                video.style.objectFit = 'contain';

                // Revisar si se puede poner en pantalla completa
                if (video.requestFullscreen) {
                    video.requestFullscreen();
                } else if (video.mozRequestFullScreen) { // Para Firefox
                    video.mozRequestFullScreen();
                } else if (video.webkitRequestFullscreen) { // Para Chrome, Safari y Opera
                    video.webkitRequestFullscreen();
                } else if (video.msRequestFullscreen) { // Para IE/Edge
                    video.msRequestFullscreen();
                }
            });

            // Escuchar cuando cada video termina de reproducirse
            video.addEventListener('ended', function() {
                // Cambiar el object-fit de vuelta a cover
                video.style.objectFit = 'cover';

                // Salir del modo pantalla completa si está activo
                if (document.fullscreenElement) {
                    document.exitFullscreen();
                } else if (document.mozFullScreenElement) { // Para Firefox
                    document.mozCancelFullScreen();
                } else if (document.webkitFullscreenElement) { // Para Chrome, Safari y Opera
                    document.webkitExitFullscreen();
                } else if (document.msFullscreenElement) { // Para IE/Edge
                    document.msExitFullscreen();
                }
            });
        });
    </script>


</body>

</html>