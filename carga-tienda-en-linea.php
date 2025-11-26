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
    <title>Carga tienda en línea | Fastpack</title>
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
                                <h4 style="color:#fff" class="m-1">TIENDA EN LÍNEA <small>(PRODUCTOS ACTIVOS)</small>
                                    <button type="button" class="btn btn-primary btn-sm float-end btn-sm" data-bs-toggle="modal" data-bs-target="#exampleModal">
                                        Nuevo producto
                                    </button>
                                </h4>
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
                                                FROM productosventa p
                                                LEFT JOIN categoriasasociadasventa c ON p.id = c.idproducto
                                                LEFT JOIN industriaasociadaventa i ON p.id = i.idproducto
                                                WHERE p.estatus = 1
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
                                                        <a href="editarproductoventa.php?id=<?= $registro['id']; ?>" class="btn btn-warning btn-sm m-1"><i class="bi bi-pencil-square"></i></a>

                                                        <form action="codeproductosventa.php" method="POST" class="d-inline">
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
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">NUEVO PRODUCTO</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="codeproductosventa.php" method="POST" class="row" enctype="multipart/form-data">
                        <div class="col-12 col-md-12 form-floating mb-3">
                            <input type="text" class="form-control" name="titulo" id="titulo" placeholder="Titulo" autocomplete="off" required>
                            <label for="titulo">Título</label>
                        </div>

                        <div class="col-12 col-md-12 form-floating mb-3">
                            <textarea class="form-control" name="subtitulo" id="subtitulo" placeholder="Subtitulo" required style="min-height: 100px;"></textarea>
                            <label for="subtitulo">Subtítulo</label>
                        </div>

                        <div class="col-12 col-md-12 form-floating mb-3">
                            <textarea class="form-control" name="detalles" id="detalles" placeholder="Detalles" required style="min-height: 150px;"></textarea>
                            <label for="detalles">Detalles</label>
                        </div>

                        <div class="col-12 col-md-4 mb-3">
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

                        <div class="col-12 col-md-4 mb-3">
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

                        <div class="col-12 col-md-4 mb-3">
                            <label for="subcategoria">Subcategorías</label>
                            <div id="subcategoria">
                                <?php
                                $query = "SELECT * FROM subcategorias";
                                $result = mysqli_query($con, $query);
                                if (mysqli_num_rows($result) > 0) {
                                    while ($subcategoria = mysqli_fetch_assoc($result)) {
                                        $opcion = $subcategoria['subcategoria'];
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

                        <div class="col-12 col-md-2 form-floating mb-3">
                            <input type="number" class="form-control" name="stock" id="stock" placeholder="Stock" autocomplete="off" required>
                            <label for="stock">Stock</label>
                        </div>

                        <div class="col-12 col-md-10 form-floating mb-3">
                            <input type="text" class="form-control" name="sku" id="sku" placeholder="SKU" autocomplete="off" required>
                            <label for="sku">SKU</label>
                        </div>

                        <div class="col-12 col-md-3 form-floating mb-3">
                            <input type="number" class="form-control" name="stockminimo" id="stockminimo" placeholder="Stock minimo" autocomplete="off" required>
                            <label for="stockminimo">Stock minimo</label>
                        </div>

                        <div class="col-12 col-md-3 form-floating mb-3">
                            <input type="text" class="form-control" name="preciounitario" id="preciounitario" placeholder="Precio unitario" autocomplete="off" required>
                            <label for="preciounitario">Precio unitario</label>
                        </div>

                        <div class="col-12 col-md-6 form-floating mb-3">
                            <input type="text" class="form-control" name="preciomayoreo" id="preciomayoreo" placeholder="Precio mayoreo" autocomplete="off" required>
                            <label for="preciomayoreo">Precio mayoreo</label>
                        </div>

                        <div class="col-12 col-md-4 form-floating mb-3">
                            <input type="number" class="form-control" name="cantidadmayoreo" id="cantidadmayoreo" placeholder="Cantidad mayoreo" autocomplete="off" required>
                            <label for="cantidadmayoreo">Cantidad minima para mayoreo</label>
                        </div>

                        <div class="col-12 col-md-8 form-floating mb-3">
                            <input type="text" class="form-control" name="descuento" id="descuento" placeholder="Descuento" autocomplete="off" required>
                            <label for="descuento">Descuento</label>
                        </div>

                        <div class="col-12 mb-3">
                            <label for="medio">Medios</label>
                            <input type="file" name="medios[]" id="medio" class="form-control" accept=".jpg, .jpeg, .png" multiple>
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
                "pageLength": 25
            });
        });

        document.addEventListener("DOMContentLoaded", function() {
            // Campos que solo aceptan enteros
            const soloEnteros = ["stock", "stockminimo", "cantidadmayoreo"];

            // Campos que aceptan decimales (ej. 1000.00)
            const soloDecimales = ["descuento", "precio", "preciomayoreo", "preciounitario"];

            // Validación para números enteros
            soloEnteros.forEach(id => {
                const input = document.getElementById(id);
                if (input) {
                    input.addEventListener("input", function() {
                        this.value = this.value.replace(/[^0-9]/g, ''); // solo dígitos
                    });
                }
            });

            // Validación para decimales
            soloDecimales.forEach(id => {
                const input = document.getElementById(id);
                if (input) {
                    input.addEventListener("input", function() {
                        // Permite solo números y un punto decimal (ej. 1000.00)
                        this.value = this.value
                            .replace(/[^0-9.]/g, '') // quita letras y símbolos
                            .replace(/(\..*)\./g, '$1'); // evita más de un punto
                    });
                }
            });
        });
    </script>

</body>

</html>