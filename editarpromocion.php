<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
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
    <title>Editar promoción | Fastpack</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-0evHe/X+R7YkIZDRvuzKMRqM+OrBnVFBL6DOitfPri4tjfHxaWutUpFmBp4vmVor" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
    <link rel="shortcut icon" type="image/x-icon" href="images/ico.ico" />
    <link rel="stylesheet" href="css/styles.css">
</head>

<body class="sb-nav-fixed">
    <?php include 'sidenav.php'; ?>
    <div id="layoutSidenav">
        <div id="layoutSidenav_content">
            <div class="container mt-4 mb-5">

                <div class="row justify-content-center">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>EDITAR PROMOCIÓN
                                    <a href="promociones.php" class="btn btn-danger btn-sm float-end">Regresar</a>
                                </h4>
                            </div>
                            <div class="card-body">

                                <?php

                                if (isset($_GET['id'])) {
                                    $registro_id = mysqli_real_escape_string($con, $_GET['id']);
                                    $query = "SELECT * FROM promociones WHERE id='$registro_id' ";
                                    $query_run = mysqli_query($con, $query);

                                    if (mysqli_num_rows($query_run) > 0) {
                                        $registro = mysqli_fetch_array($query_run);
                                        $estatus_actual = $registro['estatus'];

                                ?>

                                        <form action="codepromociones.php" method="POST" enctype="multipart/form-data">
                                            <input type="hidden" name="id" value="<?= $registro['id']; ?>">

                                            <div class="row mt-1">
                                                <div class="form-group mt-3 col-5 mb-3">
                                                    <label for="nuevaFoto">Seleccionar nueva foto:</label>
                                                    <input type="file" class="form-control" id="nuevaFoto" accept=".jpg, .jpeg, .png" name="nuevaFoto">

                                                    <div class="form-floating mt-3">
                                                        <input type="text" class="form-control" name="nombre" id="nombre" value="<?= $registro['nombre']; ?>">
                                                        <label for="nombre">Nombre</label>
                                                    </div>

                                                    <div class="form-floating mt-3">
                                                        <select class="form-select" name="estatus" id="estatus">
                                                            <option disabled>Seleccione un estatus</option>
                                                            <option value="0" <?= ($estatus_actual == 0) ? 'selected' : ''; ?>>Inactivo</option>
                                                            <option value="1" <?= ($estatus_actual == 1) ? 'selected' : ''; ?>>Activo</option>
                                                        </select>
                                                        <label for="estatus">Estatus</label>
                                                    </div>
                                                </div>
                                                <div class="form-group mb-3 col-7 text-center">
                                                    <?php
                                                    // Mostrar la imagen actual si existe
                                                    if (!empty($registro['medio'])) {
                                                        echo '<img src="' . $registro['medio'] . '" alt="Foto actual" style="width:100%;">';
                                                    } else {
                                                        echo 'No hay foto actual.';
                                                    }
                                                    ?>
                                                </div>

                                                <div class="form-floating col-12 mt-3">
                                                    <input type="text" class="form-control" name="url" id="url" value="<?= $registro['url']; ?>">
                                                    <label for="url">URL</label>
                                                </div>
                                                <div class="col-12 mt-3">
                                                    <p class="small">Selecciona alguna industria, categoría o subcategoría para autocompletar la URL automaticamente y asociar el banner promocional con el filtrado de la tienda en línea</p>
                                                </div>

                                                <div class="col-12 col-md-4 mt-3">
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

                                                <div class="col-12 col-md-4 mt-3">
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

                                                <div class="col-12 col-md-4 mt-3 mb-3">
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

                                                <div class="col-12">
                                                    <div class="p-3 mt-3" style="background-color: #ebbc5d78;border:2px solid #b5790066;border-radius:10px">
                                                        <p class="text-dark" style="margin:0;"><small><i style="background-color: #b692133b;color: #393939ff;padding:5px 5px 5px 10px;border-radius:50px;" class="bi bi-exclamation-triangle-fill"></i> Al actualizar la imagen del banner promocional los cambios pueden demorar hasta 30 min en verse reflejados.</small></p>
                                                    </div>
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
    <script src='https://cdn.jsdelivr.net/npm/sweetalert2@10'></script>
    <script>
        const inputFile = document.getElementById('nuevaFoto');

        inputFile.addEventListener('change', function() {


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
                } else {}
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