<?php
session_start();
require 'dbcon.php';
$message = isset($_SESSION['message']) ? $_SESSION['message'] : ''; // Obtener el mensaje de la sesión

if (!empty($message)) {
    // HTML y JavaScript para mostrar la alerta...
    echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                const message = " . json_encode($message) . ";
                Swal.fire({
                    title: 'NOTIFICACIÓN',
                    text: message,
                    icon: 'info',
                    confirmButtonText: 'OK'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Hacer algo si se confirma la alerta
                    }
                });
            });
        </script>";
    unset($_SESSION['message']); // Limpiar el mensaje de la sesión
}

// //Verificar si existe una sesión activa y los valores de usuario y contraseña están establecidos
// if (isset($_SESSION['username'])) {
//     $username = $_SESSION['username'];

//     // Consultar la base de datos para verificar si los valores coinciden con algún registro en la tabla de usuarios
//     $query = "SELECT * FROM user WHERE username = '$username'";
//     $result = mysqli_query($con, $query);

//     // Si se encuentra un registro coincidente, el usuario está autorizado
//     if (mysqli_num_rows($result) > 0) {
//         // El usuario está autorizado, se puede acceder al contenido
//     } else {
//         // Redirigir al usuario a una página de inicio de sesión
//         header('Location: login.php');
//         exit(); // Finalizar el script después de la redirección
//     }
// } else {
//     // Redirigir al usuario a una página de inicio de sesión si no hay una sesión activa
//     header('Location: login.php');
//     exit(); // Finalizar el script después de la redirección
// }
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
                </div>
            </div>
        </div>
    </div>



    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-pprn3073KE6tl6bjs2QrFaJGz5/SUsLqktiwsUTF55Jfv3qYSDhgCecCxMW52nD2" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src='https://cdn.jsdelivr.net/npm/sweetalert2@10'></script>

</body>

</html>