<?php
session_start();
require 'dbcon.php';
$message = isset($_SESSION['message']) ? $_SESSION['message'] : '';

if (!empty($message)) {
    echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                const message = " . json_encode($message) . ";
                Swal.fire({
                    //title: 'NOTIFICACIÓN',
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
    unset($_SESSION['message']);
}

if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];

    $query = "SELECT * FROM usuarios WHERE username = '$username'";
    $result = mysqli_query($con, $query);

    if (mysqli_num_rows($result) > 0) {
        // Se puede acceder al contenido
    } else {
        header('Location: ingresar.php');
        exit();
    }
} else {
    header('Location: ingresar.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Editar usuarios | Fastpack</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-0evHe/X+R7YkIZDRvuzKMRqM+OrBnVFBL6DOitfPri4tjfHxaWutUpFmBp4vmVor" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
    <link rel="shortcut icon" type="image/x-icon" href="images/ico.ico" />
    <link rel="stylesheet" href="css/styles.css">
</head>

<body class="sb-nav-fixed">
    <?php include 'sidenav.php'; ?>
    <div id="layoutSidenav">
        <div id="layoutSidenav_content">
            <div class="container mt-4">

                <div class="row justify-content-center">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>EDITAR USUARIO</h4>
                            </div>
                            <div class="card-body">

                                <?php

                                if (isset($_GET['id'])) {
                                    $registro_id = mysqli_real_escape_string($con, $_GET['id']);
                                    $query = "SELECT * FROM usuarios WHERE id='$registro_id' ";
                                    $query_run = mysqli_query($con, $query);

                                    if (mysqli_num_rows($query_run) > 0) {
                                        $registro = mysqli_fetch_array($query_run);
                                        $rol_actual = $registro['rol'];
                                        $estatus_actual = $registro['estatus'];

                                ?>

                                        <form action="codeusuarios.php" method="POST" enctype="multipart/form-data">
                                            <input type="hidden" name="id" value="<?= $registro['id']; ?>">

                                            <div class="row mt-1">
                                                <div class="form-group mt-3 col-11 mb-3">
                                                    <label for="nuevaFoto">Seleccionar nueva foto:</label>
                                                    <input type="file" class="form-control" id="nuevaFoto" name="nuevaFoto">
                                                </div>
                                                <div class="form-group mb-3 col-1 text-center">
                                                    <?php
                                                    // Mostrar la imagen actual si existe
                                                    if (!empty($registro['medio'])) {
                                                        echo '<img src="data:image/jpeg;base64,' . base64_encode($registro['medio']) . '" alt="Foto actual" style="width:100%;">';
                                                    } else {
                                                        echo 'No hay foto actual.';
                                                    }
                                                    ?>
                                                </div>

                                                <div class="form-floating col-12">
                                                    <input type="text" class="form-control" name="nombre" id="nombre" value="<?= $registro['nombre']; ?>">
                                                    <label for="nombre">Nombre</label>
                                                </div>

                                                <div class="form-floating col-12 col-md-6 mt-3">
                                                    <input type="text" class="form-control" name="apellidopaterno" id="apellidopaterno" value="<?= $registro['apellidopaterno']; ?>">
                                                    <label for="apellidopaterno">Apellido Paterno</label>
                                                </div>

                                                <div class="form-floating col-12 col-md-6 mt-3">
                                                    <input type="text" class="form-control" name="apellidomaterno" id="apellidomaterno" value="<?= $registro['apellidomaterno']; ?>">
                                                    <label for="apellidomaterno">Apellido materno</label>
                                                </div>

                                                <div class="form-floating col-12 mt-3">
                                                    <input type="text" class="form-control" name="username" id="username" value="<?= $registro['username']; ?>" readonly>
                                                    <label for="username">Correo</label>
                                                </div>

                                                <div class="form-floating col-12 col-md-7 mt-3">
                                                    <select class="form-select" name="estatus" id="estatus">
                                                        <option disabled>Seleccione un estatus</option>
                                                        <option value="0" <?= ($estatus_actual == 0) ? 'selected' : ''; ?>>Inactivo</option>
                                                        <option value="1" <?= ($estatus_actual == 1) ? 'selected' : ''; ?>>Activo</option>
                                                    </select>
                                                    <label for="estatus">Estatus</label>
                                                </div>

                                                <div class="form-floating col-12 col-md-5 mt-3">
                                                    <select class="form-select" name="rol" id="rol">
                                                        <option disabled>Seleccione un rol</option>
                                                        <option value="1" <?= ($rol_actual == 1) ? 'selected' : ''; ?>>Administrador</option>
                                                        <option value="2" <?= ($rol_actual == 2) ? 'selected' : ''; ?>>Colaborador</option>
                                                    </select>
                                                    <label for="rol">Rol</label>
                                                </div>

                                                <div class="col-12 text-center mt-3">
                                                    <button type="submit" name="update" class="btn btn-primary">
                                                        Actualizar usuario
                                                    </button>
                                                </div>


                                            </div>
                            </div>

                            </form>
                    <?php
                                    } else {
                                        echo "<h4>No Such Id Found</h4>";
                                    }
                                }
                    ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-pprn3073KE6tl6bjs2QrFaJGz5/SUsLqktiwsUTF55Jfv3qYSDhgCecCxMW52nD2" crossorigin="anonymous"></script>
</body>

</html>