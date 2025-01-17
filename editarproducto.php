<?php
session_start();
require 'dbcon.php';

$message = isset($_SESSION['message']) ? $_SESSION['message'] : '';

if (!empty($message)) {
    echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                const message = " . json_encode($message) . ";
                Swal.fire({
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

    if (mysqli_num_rows($result) === 0) {
        header('Location: ingresar.php');
        exit();
    }
} else {
    header('Location: ingresar.php');
    exit();
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: vigentes.php');
    exit();
}

$idproducto = (int)$_GET['id'];

$query = "SELECT p.titulo, p.estatus, p.subtitulo, p.detalles, 
                 GROUP_CONCAT(DISTINCT c.categoria SEPARATOR ', ') AS categorias,
                 GROUP_CONCAT(DISTINCT i.industria SEPARATOR ', ') AS industrias
          FROM productos p
          LEFT JOIN categoriasasociadas c ON p.id = c.idproducto
          LEFT JOIN industriaasociada i ON p.id = i.idproducto
          WHERE p.id = $idproducto
          GROUP BY p.id";
$result = mysqli_query($con, $query);
$product = mysqli_fetch_assoc($result);

if (!$product) {
    header('Location: vigentes.php');
    exit();
}

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="shortcut icon" href="images/ico.ico" type="image/x-icon">
    <title>Editar Producto | Fastpack</title>
</head>

<body class="sb-nav-fixed">
    <?php include 'sidenav.php'; ?>
    <div id="layoutSidenav">
        <div id="layoutSidenav_content">
            <div class="container-fluid">
                <div class="row justify-content-center mb-5 mt-4">
                    <div class="col-md-12">
                        <div class="card p-3">
                            <div class="card-header">
                                <h2 style="text-transform: uppercase;">Editar Producto <?php echo htmlspecialchars($product['titulo']); ?>
                                    <a href="vigentes.php" class="btn btn-primary btn-sm float-end">Regresar</a>
                                </h2>
                            </div>
                            <form action="codeproductos.php" method="POST" class="row" enctype="multipart/form-data">
                                <input type="hidden" name="id" value="<?php echo htmlspecialchars($idproducto); ?>">

                                <div class="col-12 col-md-12 form-floating mt-3 mb-3">
                                    <input type="text" class="form-control" name="titulo" id="titulo" value="<?php echo htmlspecialchars($product['titulo']); ?>" placeholder="Título" autocomplete="off" required>
                                    <label for="titulo">Título</label>
                                </div>

                                <div class="col-12 col-md-12 form-floating mb-3">
                                    <textarea class="form-control" name="subtitulo" id="subtitulo" placeholder="Subtitulo" autocomplete="off" style="min-height: 150px;"><?php echo htmlspecialchars($product['subtitulo']); ?></textarea>
                                    <label for="subtitulo">Subtítulo</label>
                                </div>

                                <div class="col-12 col-md-12 form-floating mb-3">
                                    <textarea class="form-control" name="detalles" id="detalles" placeholder="Detalles" autocomplete="off" style="min-height: 250px;"><?php echo htmlspecialchars($product['detalles']); ?></textarea>
                                    <label for="detalles">Detalles</label>
                                </div>

                                <div class="col-5 p-3">
                                    <label for="categoria">Categorías</label>
                                    <div id="categoria">
                                        <?php
                                        $query = "SELECT * FROM categorias";
                                        $result = mysqli_query($con, $query);
                                        if (mysqli_num_rows($result) > 0) {
                                            while ($categoria = mysqli_fetch_assoc($result)) {
                                                $opcion = $categoria['categoria'];
                                                $checked = in_array($opcion, explode(', ', $product['categorias'])) ? 'checked' : '';
                                                echo "
            <div class='form-check'>
                <input class='form-check-input' type='checkbox' name='categoria[]' value='$opcion' id='categoria_$opcion' $checked>
                <label class='form-check-label' for='categoria_$opcion'>$opcion</label>
            </div>";
                                            }
                                        }
                                        ?>
                                    </div>
                                </div>

                                <div class="col-4 p-3">
                                    <label for="industria">Industrias</label>
                                    <div id="industria">
                                        <?php
                                        $query = "SELECT * FROM industrias";
                                        $result = mysqli_query($con, $query);
                                        if (mysqli_num_rows($result) > 0) {
                                            while ($industria = mysqli_fetch_assoc($result)) {
                                                $opcion = $industria['industria'];
                                                $checked = in_array($opcion, explode(', ', $product['industrias'])) ? 'checked' : '';
                                                echo "
            <div class='form-check'>
                <input class='form-check-input' type='checkbox' name='industria[]' value='$opcion' id='industria_$opcion' $checked>
                <label class='form-check-label' for='industria_$opcion'>$opcion</label>
            </div>";
                                            }
                                        }
                                        ?>
                                    </div>
                                </div>

                                <?php
                                // Supongamos que $product['estatus'] es 0 o 1
                                $estatus = $product['estatus'];
                                $checked = $estatus == 1 ? 'checked' : '';
                                $labelText = $estatus == 1 ? 'El producto se esta mostrando' : 'El producto esta oculto';
                                ?>
                                <div class="form-check form-switch col-3 mt-3">
                                    <!-- Campo oculto para manejar el valor del checkbox si no está marcado -->
                                    <input type="hidden" name="estatus" value="0">
                                    <!-- Checkbox para el estatus -->
                                    <input name="estatus" class="form-check-input" type="checkbox" role="switch" id="flexSwitchCheckDefault" value="1" <?php echo $checked; ?>>
                                    <label class="form-check-label" for="flexSwitchCheckDefault"><?php echo $labelText; ?></label>
                                </div>



                                <div class="col-12 mb-3">
                                    <label class="mb-1" for="medio">Medios</label>
                                    <input type="file" name="medios[]" id="medio" class="form-control mb-3" multiple>
                                </div>

                                <div class="col-12 text-center">
                                    <button type="submit" class="btn btn-primary" name="update">Actualizar</button>
                                </div>

                            </form>
                            <div class="col-12 mt-3">
                                <?php
                                // Consulta para obtener los medios del producto
                                $query_medios = "SELECT id, medio FROM medios WHERE idproducto = $idproducto";
                                $result_medios = mysqli_query($con, $query_medios);
                                $medios = [];

                                while ($row = mysqli_fetch_assoc($result_medios)) {
                                    $medios[] = $row;
                                }
                                ?>
                                <div style="display: flex; flex-wrap: wrap; gap: 1rem;">
                                    <?php
                                    foreach ($medios as $medio) {
                                        $medio_id = $medio['id'];
                                        $medio_base64 = $medio['medio'];
                                        $extension = strtolower(pathinfo($medio_base64, PATHINFO_EXTENSION)); // Obtener la extensión del medio

                                        echo "<div class='d-inline-block position-relative' id='medio-$medio_id' style='flex: 0 0 auto; margin: 10px;'>";

                                        // Validar el tipo de archivo y mostrar el contenido correspondiente
                                        if ($extension === 'pdf') {
                                            echo "<iframe src='$medio_base64' style='width: 400px; height: 200px;' frameborder='0' allowfullscreen></iframe>";
                                        } elseif ($extension === 'mp4') {
                                            echo "<video width='150' height='200' controls>
                    <source src='$medio_base64' type='video/mp4'>
                    Tu navegador no soporta la etiqueta de video.
                  </video>";
                                        } elseif ($extension === 'jpg' || $extension === 'jpeg' || $extension === 'png') {
                                            echo "<img src='$medio_base64' style='width: 200px; height: 200px; object-fit: cover;' alt='Imagen'>";
                                        } else {
                                            echo "<p>Tipo de medio no soportado</p>"; // Mensaje para otros tipos de archivos
                                        }

                                        echo "
            <form action='codeproductos.php' method='POST'>
                <input type='hidden' name='idproducto' value='$idproducto'>
                <button type='submit' class='btn btn-danger btn-sm position-absolute top-0 end-0' name='deletemedio' value='$medio_id'>
                    <i class='bi bi-x'></i>
                </button>
            </form>
        </div>";
                                    }
                                    ?>
                                </div>


                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>