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
    <title>Promociones | Fastpack</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-0evHe/X+R7YkIZDRvuzKMRqM+OrBnVFBL6DOitfPri4tjfHxaWutUpFmBp4vmVor" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="shortcut icon" type="image/x-icon" href="images/ico.ico">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.css">
    <link rel="stylesheet" href="css/styles.css">
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
                                <h4>PROMOCIONES
                                    <button type="button" class="btn btn-primary btn-sm float-end" data-bs-toggle="modal" data-bs-target="#exampleModal">
                                        Nueva promoción
                                    </button>
                                </h4>
                            </div>
                            <div class="card-body" style="overflow-y:scroll;">
                                <table id="miTabla" class="table table-bordered table-striped" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>Nombre</th>
                                            <th>URL / Categoría</th>
                                            <th>Estatus</th>
                                            <th>Accion</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $query = "SELECT * FROM promociones ORDER BY id DESC";
                                        $query_run = mysqli_query($con, $query);
                                        if (mysqli_num_rows($query_run) > 0) {
                                            foreach ($query_run as $registro) {
                                        ?>
                                                <tr>
                                                    <td><?= $registro['nombre']; ?></td>
                                                    <td><?= $registro['url']; ?></td>
                                                    <td>
                                                        <?php
                                                        if ($registro['estatus'] === '0') {
                                                            echo "<span class='bg-danger text-light p-1' style='border-radius:10px'>Inactivo</span>";
                                                        } else if ($registro['estatus'] === '1') {
                                                            echo "<span class='bg-success text-light p-1' style='border-radius:10px'>Activo</span>";
                                                        } else {
                                                            echo "Error, contacte a soporte";
                                                        }
                                                        ?>
                                                    </td>
                                                    <td>
                                                        <a href="editarpromocion.php?id=<?= $registro['id']; ?>" class="btn btn-warning btn-sm m-1"><i class="bi bi-pencil-square"></i></a>

                                                        <form action="codepromociones.php" method="POST" class="d-inline">
                                                            <button type="submit" name="delete" value="' . $registro['id'] . '" class="btn btn-danger btn-sm m-1 deletebtn"><i class="bi bi-trash-fill"></i></button>
                                                        </form>
                                                    </td>
                                                </tr>
                                        <?php
                                            }
                                        } else {
                                            echo "<td colspan='4'><p>No se encontro ningun usuario</p></td>";
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
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">NUEVA PROMOCIÓN</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="codepromociones.php" method="POST" class="row" enctype="multipart/form-data">
                        <div class="form-floating col-12 mt-1">
                            <input type="text" class="form-control" name="nombre" id="nombre" placeholder="Nombre" autocomplete="off" required>
                            <label for="nombre">Nombre</label>
                        </div>

                        <div class="form-floating col-12 mt-3">
                            <input type="text" class="form-control" name="url" id="url" placeholder="URL" value="#" required>
                            <label for="url">URL</label>
                        </div>

                        <div class="col-12 mt-3">
                            <label for="medio" class="form-label">Banner</label>
                            <input type="file" class="form-control" name="medio" id="medio" accept=".jpg, .jpeg, .png" required>
                        </div>

                        <div class="col-12">
                            <div class="p-3 mt-3" style="background-color: #ebbc5d78;border:2px solid #b5790066;border-radius:10px">
                                <p class="text-dark" style="margin:0;"><small><i style="background-color: #b692133b;color: #393939ff;padding:5px 5px 5px 10px;border-radius:50px;" class="bi bi-exclamation-triangle-fill"></i> La imagen del banner promocional debe tener una relación de aspecto (Aspect ratio) de 5:2. Ejemplo: 700x280 o 1200x480.</small></p>
                            </div>
                        </div>

                        <div class="col-12 mt-3">
                            <p class="small">Selecciona alguna industria, categoría o subcategoría para autocompletar la URL automaticamente y asociar el banner promocional con el filtrado de la tienda en línea</p>
                        </div>

                        <div class="col-12 mt-3">
                            <select class="form-select" name="idIndustria" id="industria">
                                <option value="" disabled selected>Selecciona una industria</option>
                                <?php
                                $query = "SELECT * FROM industrias ORDER BY id ASC";
                                $result = mysqli_query($con, $query);

                                if (mysqli_num_rows($result) > 0) {
                                    while ($registro = mysqli_fetch_assoc($result)) {
                                        $industria = $registro['industria'];
                                        $idIndustria = $registro['id'];
                                        echo "<option value='$idIndustria'>" . $industria . "</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>

                        <div class="col-12 mt-3">
                            <select class="form-select" name="idCategoria" id="categoria">
                                <option value="" disabled selected>Selecciona una categoría</option>
                                <?php
                                $query = "SELECT * FROM categorias ORDER BY id ASC";
                                $result = mysqli_query($con, $query);

                                if (mysqli_num_rows($result) > 0) {
                                    while ($registro = mysqli_fetch_assoc($result)) {
                                        $categoria = $registro['categoria'];
                                        $idCategoria = $registro['id'];
                                        echo "<option value='$idCategoria'>" . $categoria . "</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>

                        <div class="col-12 mt-3 mb-3">
                            <select class="form-select" name="idSubcategoria" id="subcategoria">
                                <option value="" disabled selected>Selecciona una subcategoría</option>
                                <?php
                                $query = "SELECT * FROM subcategorias ORDER BY id ASC";
                                $result = mysqli_query($con, $query);

                                if (mysqli_num_rows($result) > 0) {
                                    while ($registro = mysqli_fetch_assoc($result)) {
                                        $subcategoria = $registro['subcategoria'];
                                        $idSubcategoria = $registro['id'];
                                        echo "<option value='$idSubcategoria'>" . $subcategoria . "</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                            <button type="submit" class="btn btn-primary" name="save" id="btnGuardar" disabled>Guardar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-pprn3073KE6tl6bjs2QrFaJGz5/SUsLqktiwsUTF55Jfv3qYSDhgCecCxMW52nD2" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
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

        const inputFile = document.getElementById('medio');
        const btnGuardar = document.getElementById('btnGuardar');

        inputFile.addEventListener('change', function() {
            btnGuardar.disabled = true; // Siempre deshabilitar primero

            const file = this.files[0];
            if (!file) return;

            const img = new Image();
            img.src = URL.createObjectURL(file);

            img.onload = function() {
                const ratio = img.width / img.height;

                // Relación válida: entre 2.4 y 2.6 (≈ 5:2)
                if (ratio < 2.4 || ratio > 2.6) {
                    Swal.fire({
                        title: 'Relación incorrecta',
                        text: `La imagen debe tener relación de aspecto 5:2.\nEjemplo: 700x280 o 1200x480.\nRelación detectada: ${ratio.toFixed(2)}`,
                        icon: 'warning',
                        confirmButtonText: 'OK'
                    });

                    inputFile.value = ""; // Limpiar la imagen
                    btnGuardar.disabled = true; // Mantener deshabilitado
                } else {
                    btnGuardar.disabled = false; // Habilitar si es correcta
                }
            };
        });

        const inputUrl = document.getElementById("url");
        const selIndustria = document.getElementById("industria");
        const selCategoria = document.getElementById("categoria");
        const selSubcategoria = document.getElementById("subcategoria");

        function limpiarSelects(excepto) {
            if (excepto !== "industria") selIndustria.selectedIndex = 0;
            if (excepto !== "categoria") selCategoria.selectedIndex = 0;
            if (excepto !== "subcategoria") selSubcategoria.selectedIndex = 0;
        }

        function formatearTexto(texto) {
            return texto.toLowerCase().replace(/\s+/g, "-");
        }

        function actualizarUrl(tipo, texto) {
            const textoFormateado = formatearTexto(texto);
            inputUrl.value = `tienda-en-linea.php?${tipo}=${textoFormateado}`;
        }

        // --- INDUSTRIA ---
        selIndustria.addEventListener("change", function() {
            limpiarSelects("industria");

            const texto = selIndustria.options[selIndustria.selectedIndex].text;
            actualizarUrl("industry", texto);
        });

        // --- CATEGORÍA ---
        selCategoria.addEventListener("change", function() {
            limpiarSelects("categoria");

            const texto = selCategoria.options[selCategoria.selectedIndex].text;
            actualizarUrl("category", texto);
        });

        // --- SUBCATEGORÍA ---
        selSubcategoria.addEventListener("change", function() {
            limpiarSelects("subcategoria");

            const texto = selSubcategoria.options[selSubcategoria.selectedIndex].text;
            actualizarUrl("subcategory", texto);
        });

        // --- SI ESCRIBE MANUALMENTE UNA URL ---
        inputUrl.addEventListener("input", function() {
            limpiarSelects(null); // reinicia todos
        });
    </script>
</body>

</html>