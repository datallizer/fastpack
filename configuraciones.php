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
    <link rel="shortcut icon" type="image/x-icon" href="images/ics.ico">
    <title>Configuraciones | Fastpack</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-0evHe/X+R7YkIZDRvuzKMRqM+OrBnVFBL6DOitfPri4tjfHxaWutUpFmBp4vmVor" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="shortcut icon" href="images/ico.ico" type="image/x-icon">
</head>

<body class="sb-nav-fixed">
    <?php include 'sidenav.php'; ?>
    <div id="layoutSidenav">
        <div id="layoutSidenav_content">
            <div class="container-fluid">
                <div class="row mb-5 mt-4">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 style="color:#fff" class="m-1">CONFIGURACIONES</h4>
                            </div>
                            <div class="card-body" style="overflow-y:scroll;">
                                <table id="miTabla" class="table table-bordered table-striped" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>Configuracion</th>
                                            <th></th>
                                            <th></th>
                                            <th>Editar</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $query = "SELECT * FROM configuraciones ORDER BY id ASC";
                                        $query_run = mysqli_query($con, $query);
                                        if (mysqli_num_rows($query_run) > 0) {
                                            foreach ($query_run as $registro) {
                                        ?>
                                                <tr>
                                                    <td>
                                                        <p class="mb-0" style="font-weight: 500;"><b><?= $registro['nombre']; ?></b></p>
                                                        <p><small><?= $registro['detalle']; ?></small></p>
                                                    </td>
                                                    <td>
                                                        <p class="mb-0" style="font-weight: 500;">
                                                            <?php
                                                            if ($registro['id'] === '1') {
                                                                echo "Monto minimo para envío gratis";
                                                            } else if ($registro['id'] === '2') {
                                                                echo "Texto advertencia";
                                                            } else if ($registro['id'] === '3') {
                                                                echo "Cliente/a";
                                                            } else {
                                                                echo "Error, contacte a soporte";
                                                            }
                                                            ?></p>
                                                        <p><?= $registro['valoruno']; ?></p>
                                                    </td>
                                                    <td><p class="mb-0" style="font-weight: 500;">
                                                            <?php
                                                            if ($registro['id'] === '1') {
                                                                echo "Costo de envío";
                                                            } else if ($registro['id'] === '2') {
                                                                echo "URL WhatsApp";
                                                            } else if ($registro['id'] === '3') {
                                                                echo "Cliente/a";
                                                            } else {
                                                                echo "Error, contacte a soporte";
                                                            }
                                                            ?></p>
                                                        <p><?= $registro['valordos']; ?></p>
                                                    </td>
                                                    <td>
                                                        <a href="editarconfiguracion.php?id=<?= $registro['id']; ?>" class="btn btn-warning btn-sm m-1"><i class="bi bi-pencil-square"></i></a>
                                                    </td>
                                                </tr>
                                        <?php
                                            }
                                        } else {
                                            echo "<td colspan='5'><p> No se encontro ningun usuario </p></td>";
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
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