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
    <link rel="shortcut icon" type="image/x-icon" href="images/ics.ico">
    <title>Productos | Fastpack</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-0evHe/X+R7YkIZDRvuzKMRqM+OrBnVFBL6DOitfPri4tjfHxaWutUpFmBp4vmVor" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.css">
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
                                <h4 style="color:#fff" class="m-1">PRODUCTOS OCULTOS</h4>
                            </div>
                            <div class="card-body" style="overflow-y:scroll;">
                                <table id="miTabla" class="table table-bordered table-striped" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Título</th>
                                            <th>Subtítulo</th>
                                            <th>Industria</th>
                                            <th>Categoría</th>
                                            <th>Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $query = "SELECT p.id, 
                                                    p.titulo, 
                                                    p.subtitulo, 
                                                    GROUP_CONCAT(DISTINCT c.categoria ORDER BY c.categoria ASC SEPARATOR ', ') AS categorias,
                                                    GROUP_CONCAT(DISTINCT i.industria ORDER BY i.industria ASC SEPARATOR ', ') AS industrias
                                                FROM productos p
                                                LEFT JOIN categoriasasociadas c ON p.id = c.idproducto
                                                LEFT JOIN industriaasociada i ON p.id = i.idproducto
                                                WHERE p.estatus = 0
                                                GROUP BY p.id
                                                ORDER BY p.id DESC;
";

                                        $query_run = mysqli_query($con, $query);
                                        if (mysqli_num_rows($query_run) > 0) {
                                            foreach ($query_run as $registro) {
                                        ?>
                                                <tr>
                                                    <td>
                                                        <p><?= $registro['id']; ?></p>
                                                    </td>
                                                    <td>
                                                        <p><?= $registro['titulo']; ?></p>
                                                    </td>
                                                    <td>
                                                        <p><?= $registro['subtitulo']; ?></p>
                                                    </td>
                                                    <td>
                                                        <p><?= $registro['industrias']; ?></p>
                                                    </td>
                                                    <td>
                                                        <p><?= $registro['categorias']; ?></p>
                                                    </td>
                                                    <td>
                                                        <a href="editarproducto.php?id=<?= $registro['id']; ?>" class="btn btn-warning btn-sm m-1"><i class="bi bi-pencil-square"></i></a>

                                                        <form action="codeproductos.php" method="POST" class="d-inline">
                                                            <button type="submit" name="delete" value="<?= $registro['id']; ?>" class="btn btn-danger btn-sm m-1"><i class="bi bi-trash-fill"></i></button>
                                                        </form>
                                                    </td>
                                                </tr>
                                        <?php
                                            }
                                        } else {
                                            echo "<tr><td colspan='6'><p>No se encontró ningún registro</p></td></tr>";
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

    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">NUEVO PRODUCTO</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="codeproductos.php" method="POST" class="row" enctype="multipart/form-data">
                        <div class="col-12 col-md-12 form-floating mb-3">
                            <input type="text" class="form-control" name="titulo" id="titulo" placeholder="Titulo" autocomplete="off" required>
                            <label for="titulo">Título</label>
                        </div>

                        <div class="col-12 col-md-12 form-floating mb-3">
                            <input type="text" class="form-control" name="subtitulo" id="subtitulo" placeholder="Subtitulo" autocomplete="off" required>
                            <label for="subtitulo">Subtítulo</label>
                        </div>

                        <div class="col-12 col-md-12 form-floating mb-3">
                            <input type="text" class="form-control" name="detalles" id="detalles" placeholder="Detalles" autocomplete="off" required>
                            <label for="detalles">Detalles</label>
                        </div>

                        <div class="col-12 mb-3">
                            <label for="categoria">Categorías</label>
                            <div id="categoria">
                                <?php
                                $query = "SELECT * FROM categorias";
                                $result = mysqli_query($con, $query);
                                if (mysqli_num_rows($result) > 0) {
                                    while ($categoria = mysqli_fetch_assoc($result)) {
                                        $opcion = $categoria['categoria'];
                                        echo "
                <div class='form-check'>
                    <input class='form-check-input' type='checkbox' name='categoria[]' value='$opcion' id='categoria_$opcion'>
                    <label class='form-check-label' for='categoria_$opcion'>$opcion</label>
                </div>";
                                    }
                                }
                                ?>
                            </div>
                        </div>

                        <div class="col-12 mb-3">
                            <label for="industria">Industrias</label>
                            <div id="industria">
                                <?php
                                $query = "SELECT * FROM industrias";
                                $result = mysqli_query($con, $query);
                                if (mysqli_num_rows($result) > 0) {
                                    while ($industria = mysqli_fetch_assoc($result)) {
                                        $opcion = $industria['industria'];
                                        echo "
                <div class='form-check'>
                    <input class='form-check-input' type='checkbox' name='industria[]' value='$opcion' id='industria_$opcion'>
                    <label class='form-check-label' for='industria_$opcion'>$opcion</label>
                </div>";
                                    }
                                }
                                ?>
                            </div>
                        </div>

                        <div class="col-12 mb-3">
                            <label for="medio">Medios</label>
                            <input type="file" name="medios[]" id="medio" class="form-control" multiple>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-primary" name="save">Guardar</button>
                </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-pprn3073KE6tl6bjs2QrFaJGz5/SUsLqktiwsUTF55Jfv3qYSDhgCecCxMW52nD2" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.js"></script>
    <script src='https://cdn.jsdelivr.net/npm/sweetalert2@10'></script>
    <script>
        $(document).ready(function() {
            $('#miTabla').DataTable({
                "order": [
                    [0, "desc"]
                ],
                "pageLength" : 25
            });
        });
    </script>

</body>

</html>