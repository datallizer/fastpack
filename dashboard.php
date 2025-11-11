<?php
session_start();
require 'dbcon.php';

$alert = isset($_SESSION['alert']) ? $_SESSION['alert'] : null;

if (!empty($alert)) {
    $title = isset($alert['title']) ? json_encode($alert['title']) : '"Notificación"';
    $message = isset($alert['message']) ? json_encode($alert['message']) : '""';
    $icon = isset($alert['icon']) ? json_encode($alert['icon']) : '"info"';

    echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    title: $title,
                    " . (!empty($alert['message']) ? "text: $message," : "") . "
                    icon: $icon,
                    confirmButtonText: 'OK'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Hacer algo si se confirma la alerta
                    }
                });
            });
        </script>";
    unset($_SESSION['alert']);
}

if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];

    $query = "SELECT * FROM usuarios WHERE username = '$username'";
    $result = mysqli_query($con, $query);

    if (mysqli_num_rows($result) > 0) {
    } else {
        $_SESSION['alert'] = [
            'title' => 'USUARIO NO ENCONTRADO',
            'icon' => 'error'
        ];
        header('Location: login.php');
        exit();
    }
} else {
    $_SESSION['alert'] = [
        'message' => 'Para acceder debes iniciar sesión primero',
        'title' => 'SESIÓN NO INICIADA',
        'icon' => 'error'
    ];
    header('Location: login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="shortcut icon" type="image/x-icon" href="images/ico.ico">
    <title>Dashboard | Fastpack</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-0evHe/X+R7YkIZDRvuzKMRqM+OrBnVFBL6DOitfPri4tjfHxaWutUpFmBp4vmVor" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.css">
    <link rel="stylesheet" href="css/styles.css">
</head>

<body class="sb-nav-fixed">
    <?php include 'sidenav.php'; ?>
    <div id="layoutSidenav">
        <div id="layoutSidenav_content">
            <div class="container-fluid">
                <div class="row mt-4 mb-5">
                    <div class="col-md-3 text-center">
                        <a href="usuarios.php" style="color:#171717;text-decoration:none;">
                            <div style="background-color: #e7e7e7;font-size:25px;" class="p-5">
                                <i class="bi bi-people-fill"></i>
                                <p>Usuarios</p>
                            </div>
                        </a>
                    </div>

                    <div class="col-md-3 text-center">
                        <a href="vigentes.php" style="color:#171717;text-decoration:none;">
                            <div style="background-color: #e7e7e7;font-size:25px;" class="p-5">
                                <i class="bi bi-bag-fill"></i>
                                <p>Productos</p>
                            </div>
                        </a>
                    </div>

                    <div class="col-md-3 text-center">
                        <a href="categorias.php" style="color:#171717;text-decoration:none;">
                            <div style="background-color: #e7e7e7;font-size:25px;" class="p-5">
                                <i class="bi bi-card-checklist"></i>
                                <p>Categorías</p>
                            </div>
                        </a>
                    </div>

                    <div class="col-md-3 text-center">
                        <a href="industrias.php" style="color:#171717;text-decoration:none;">
                            <div style="background-color: #e7e7e7;font-size:25px;" class="p-5">
                                <i class="bi bi-building-fill"></i>
                                <p>Industrias</p>
                            </div>
                        </a>
                    </div>

                    <div class="col-md-3 text-center mt-3">
                        <a href="miscatalogos.php" style="color:#171717;text-decoration:none;">
                            <div style="background-color: #e7e7e7;font-size:25px;" class="p-5">
                                <i class="bi bi-journal-arrow-down"></i>
                                <p>Catálogos</p>
                            </div>
                        </a>
                    </div>

                    <div class="col-md-3 text-center mt-3">
                        <a href="carga-tienda-en-linea.php" style="color:#171717;text-decoration:none;">
                            <div style="background-color: #e7e7e7;font-size:25px;" class="p-5">
                                <i class="bi bi-cart2"></i>
                                <p>Tienda en línea</p>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>



    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-pprn3073KE6tl6bjs2QrFaJGz5/SUsLqktiwsUTF55Jfv3qYSDhgCecCxMW52nD2" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src='https://cdn.jsdelivr.net/npm/sweetalert2@10'></script>

</body>

</html>