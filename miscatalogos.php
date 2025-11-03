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
    <title>Catálogos | Fastpack</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-0evHe/X+R7YkIZDRvuzKMRqM+OrBnVFBL6DOitfPri4tjfHxaWutUpFmBp4vmVor" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.css">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="shortcut icon" href="images/ico.ico" type="image/x-icon">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.min.js"></script>
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
                                <h4 style="color:#fff" class="m-1">CATÁLOGOS
                                    <button type="button" class="btn btn-primary btn-sm float-end btn-sm" data-bs-toggle="modal" data-bs-target="#exampleModal">
                                        Nuevo catálogo
                                    </button>
                                </h4>
                            </div>
                            <div class="card-body" style="overflow-y:scroll;">
                                <table id="miTabla" class="table table-bordered table-striped" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Nombre</th>
                                            <th>Estatus</th>
                                            <th>Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $query = "SELECT * FROM catalogos ORDER BY id DESC";
                                        $query_run = mysqli_query($con, $query);
                                        if (mysqli_num_rows($query_run) > 0) {
                                            foreach ($query_run as $registro) {
                                                $estatus_actual = $registro['estatus'];
                                        ?>
                                                <tr>
                                                    <td>
                                                        <p><?= $registro['id']; ?></p>
                                                    </td>
                                                    <td>
                                                        <div class="row">
                                                            <div class="col-4">
                                                                <canvas id="pdfCanvas<?= $registro['id']; ?>" style="width: 100px; height: auto;"></canvas>
                                                            </div>
                                                            <div class="col-8">
                                                                <p><?= $registro['nombre']; ?></p>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    </td>
                                                    <td>
                                                        <p><?= $registro['estatus'] == 1 ? 'Activo' : 'Inactivo'; ?></p>

                                                    </td>
                                                    <td>
                                                        <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#pdfModal<?= $registro['id']; ?>">
                                                            <i class="bi bi-pencil-square"></i>
                                                        </button>
                                                        <div style="max-height: 95vh;" class="modal fade" id="pdfModal<?= $registro['id']; ?>" tabindex="-1" aria-labelledby="pdfModalLabel" aria-hidden="true">
                                                            <div class="modal-dialog">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title" id="pdfModalLabel"><?= $registro['nombre']; ?></h5>
                                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                    </div>
                                                                    <form action="codecatalogos.php" method="POST">
                                                                        <div class="modal-body">
                                                                            <input type="hidden" name="id" value="<?= $registro['id']; ?>">
                                                                            <div class="col-12 col-md-12 form-floating mb-3">
                                                                                <input type="text" class="form-control" name="nombre" id="nombre" placeholder="Nombre" value="<?= $registro['nombre']; ?>" required>
                                                                                <label class="m-0" for="nombre">Nombre</label>
                                                                            </div>

                                                                            <div class="form-floating col-12 mt-3">
                                                                                <select class="form-select" name="estatus" id="estatus">
                                                                                    <option disabled>Seleccione un estatus</option>
                                                                                    <option value="0" <?= ($estatus_actual == 0) ? 'selected' : ''; ?>>Inactivo</option>
                                                                                    <option value="1" <?= ($estatus_actual == 1) ? 'selected' : ''; ?>>Activo</option>
                                                                                </select>
                                                                                <label class="m-0" for="estatus">Estatus</label>
                                                                            </div>
                                                                        </div>
                                                                        <div class="modal-footer">
                                                                            <button type="submit" name="update" class="btn btn-warning">Guardar</button>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <form action="codecatalogos.php" method="POST" class="d-inline">
                                                            <button type="submit" name="delete" value="<?= $registro['id']; ?>" class="btn btn-danger btn-sm m-1"><i class="bi bi-trash-fill"></i></button>
                                                        </form>

                                                    </td>
                                                </tr>

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
                                        <?php
                                            }
                                        } else {
                                            echo "<td colspan='4'><p> No se encontro ningun pdf</p></td>";
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
                    <h1 class="modal-title fs-5" id="exampleModalLabel">NUEVO CATÁLOGO</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="codecatalogos.php" method="POST" enctype="multipart/form-data" class="row">

                        <div class="col-12 col-md-12 form-floating mb-3">
                            <input type="text" class="form-control" name="nombre" id="nombre" placeholder="Nombre" autocomplete="off" required>
                            <label for="nombre">Nombre</label>
                        </div>

                        <div class="mb-3">
                            <label for="formFile" class="form-label">Selecciona el PDF</label>
                            <input class="form-control" type="file" id="formFile" name="medio" accept=".pdf">
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
                ]
            });
        });
    </script>

</body>

</html>