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
    <title>Editar cupón | Fastpack</title>
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
                                <h4><a href="cupones.php" class="btn btn-sm btn-danger float-end">Regresar</a> EDITAR CUPÓN</h4>
                            </div>
                            <div class="card-body">

                                <?php

                                if (isset($_GET['id'])) {
                                    $registro_id = mysqli_real_escape_string($con, $_GET['id']);
                                    $query = "SELECT * FROM cupones WHERE id='$registro_id' ";
                                    $query_run = mysqli_query($con, $query);

                                    if (mysqli_num_rows($query_run) > 0) {
                                        $registro = mysqli_fetch_array($query_run);
                                        $estatus_actual = $registro['estatus'];
                                ?>

                                        <form action="codecupones.php" method="POST">
                                            <input type="hidden" name="id" value="<?= $registro['id']; ?>">

                                            <div class="row mt-1">
                                                <div class="form-floating col-12 col-md-6">
                                                    <input type="text" class="form-control" name="cupon" id="cupon" value="<?= $registro['cupon']; ?>">
                                                    <label for="cupon">Nombre del cupón</label>
                                                </div>

                                                <div class="form-floating col-12 col-md-6">
                                                    <input type="text" class="form-control" name="codigo" id="codigo" value="<?= $registro['codigo']; ?>">
                                                    <label for="codigo">Código para canjear el cupón</label>
                                                </div>

                                                <div class="form-floating col-12 mt-3">
                                                    <input type="text" class="form-control" name="porcentaje" id="porcentaje" value="<?= $registro['porcentaje']; ?>">
                                                    <label for="porcentaje">Porcentaje de descuento</label>
                                                </div>

                                                <div class="form-floating col-12 mt-3">
                                                    <input type="text" class="form-control" name="maximo" id="maximo" value="<?= $registro['maximo']; ?>">
                                                    <label for="maximo">Límite máximo de descuento (Pesos MXN)</label>
                                                </div>

                                                <div class="form-floating col-12 mt-3">
                                                    <input type="text" class="form-control" name="minimo" id="minimo" value="<?= $registro['minimo']; ?>">
                                                    <label for="minimo">Monto mínimo de compra (Pesos MXN)</label>
                                                </div>

                                                <div class="form-floating col-12 mt-3">
                                                    <input type="text" class="form-control" name="canjes" id="canjes" value="<?= $registro['canjes']; ?>">
                                                    <label for="canjes">Número de veces que se puede canjear el cupón</label>
                                                </div>

                                                <div class="form-floating mt-3">
                                                    <select class="form-select" name="estatus" id="estatus">
                                                        <option disabled>Seleccione un estatus</option>
                                                        <option value="0" <?= ($estatus_actual == 0) ? 'selected' : ''; ?>>Inactivo</option>
                                                        <option value="1" <?= ($estatus_actual == 1) ? 'selected' : ''; ?>>Activo</option>
                                                    </select>
                                                    <label for="estatus">Estatus</label>
                                                </div>

                                                <div class="col-12 text-center mt-3">
                                                    <button type="submit" name="update" class="btn btn-primary">
                                                        Actualizar
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
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script>
        $(document).ready(function() {
            $("#codigo").on("input", function() {

                let valor = $(this).val();
                valor = valor.replace(/[^A-Za-z0-9]/g, "");
                valor = valor.toUpperCase();
                $(this).val(valor);
            });

            $("#porcentaje, #maximo, #minimo, #canjes").on("input", function() {
                let valor = $(this).val();
                valor = valor.replace(/[^0-9]/g, "");
                $(this).val(valor);
            });

        });
    </script>
</body>

</html>