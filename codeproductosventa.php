<?php
session_start();
require 'dbcon.php';

if (isset($_POST['delete'])) {
    $id = mysqli_real_escape_string($con, $_POST['delete']);

    $query = "DELETE FROM productosventa WHERE id='$id' ";
    $query_run = mysqli_query($con, $query);
    $querymedio = "DELETE FROM mediosventa WHERE idproducto='$id' ";
    $querymedio_run = mysqli_query($con, $querymedio);
    $queryindustria = "DELETE FROM industriaasociadaventa WHERE idproducto='$id' ";
    $queryindustria_run = mysqli_query($con, $queryindustria);
    $querycategoria = "DELETE FROM categoriasasociadasventa WHERE idproducto='$id' ";
    $querycategoria_run = mysqli_query($con, $querycategoria);

    if ($query_run) {
        $_SESSION['message'] = "Eliminado con exito";
        header("Location: carga-tienda-en-linea.php");
        exit(0);
    } else {
        $_SESSION['message'] = "No se pudo eliminar, contace a su proveedor";
        header("Location: carga-tienda-en-linea.php");
        exit(0);
    }
}

if (isset($_POST['deletemedio'])) {
    $id = mysqli_real_escape_string($con, $_POST['deletemedio']);
    $idproducto = mysqli_real_escape_string($con, $_POST['idproducto']);

    $query = "DELETE FROM mediosventa WHERE id='$id' ";
    $query_run = mysqli_query($con, $query);

    if ($query_run) {
        $_SESSION['message'] = 'Medio eliminado.';
        header('Location: editarproductoventa.php?id=' . $idproducto);
        exit();
    } else {
        $_SESSION['message'] = 'Medio no eliminado.';
        header('Location: editarproductoventa.php?id=' . $idproducto);
        exit();
    }
}


if (isset($_POST['update'])) {
    $idproducto = intval($_POST['id']); // Asegúrate de que sea un número
    $titulo = mysqli_real_escape_string($con, $_POST['titulo']);
    $subtitulo = mysqli_real_escape_string($con, $_POST['subtitulo']);
    $estatus = mysqli_real_escape_string($con, $_POST['estatus']);
    $detalles = mysqli_real_escape_string($con, $_POST['detalles']);
    $stock = mysqli_real_escape_string($con, $_POST['stock']);
    $sku = mysqli_real_escape_string($con, $_POST['sku']);
    $stockminimo = mysqli_real_escape_string($con, $_POST['stockminimo']);
    $preciounitario = mysqli_real_escape_string($con, $_POST['preciounitario']);
    $preciomayoreo = mysqli_real_escape_string($con, $_POST['preciomayoreo']);
    $cantidadmayoreo = mysqli_real_escape_string($con, $_POST['cantidadmayoreo']);
    $descuento = mysqli_real_escape_string($con, $_POST['descuento']);
    $medios_delete = isset($_POST['medios_delete']) ? $_POST['medios_delete'] : [];

    // Actualizar producto
    $query = "UPDATE productosventa SET titulo='$titulo', subtitulo='$subtitulo', estatus='$estatus', detalles='$detalles', stock='$stock', sku='$sku', stockminimo='$stockminimo', preciounitario='$preciounitario', preciomayoreo='$preciomayoreo', cantidadmayoreo='$cantidadmayoreo', descuento='$descuento' WHERE id='$idproducto'";
    if (!mysqli_query($con, $query)) {
        $_SESSION['message'] = "Error al actualizar el producto: " . mysqli_error($con);
        header('Location: editarproductoventa.php?id=' . $idproducto);
        exit();
    }

    // Eliminar categorías e industrias anteriores
    mysqli_query($con, "DELETE FROM categoriasasociadasventa WHERE idproducto = $idproducto");
    mysqli_query($con, "DELETE FROM industriaasociadaventa WHERE idproducto = $idproducto");

    // Insertar nuevas categorías
    if (!empty($_POST['categoria'])) {
        foreach ($_POST['categoria'] as $categoria) {
            $categoria = mysqli_real_escape_string($con, $categoria);
            mysqli_query($con, "INSERT INTO categoriasasociadasventa (idproducto, categoria) VALUES ('$idproducto', '$categoria')");
        }
    }

    // Insertar nuevas industrias
    if (!empty($_POST['industria'])) {
        foreach ($_POST['industria'] as $industria) {
            $industria = mysqli_real_escape_string($con, $industria);
            mysqli_query($con, "INSERT INTO industriaasociadaventa (idproducto, industria) VALUES ('$idproducto', '$industria')");
        }
    }

    // Eliminar medios seleccionados y sus archivos
    if (!empty($medios_delete) && is_array($medios_delete)) {
        foreach ($medios_delete as $medio_id) {
            $medio_id = intval($medio_id); // Asegúrate de que sea un número

            // Obtener la ruta del archivo
            $query_medio = "SELECT medio FROM mediosventa WHERE id = '$medio_id'";
            $result_medio = mysqli_query($con, $query_medio);
            if ($row = mysqli_fetch_assoc($result_medio)) {
                $file_path = $row['medio'];
                if (file_exists($file_path)) {
                    unlink($file_path); // Eliminar el archivo
                }
            }

            // Eliminar la entrada de la base de datos
            $query_delete_medios = "DELETE FROM mediosventa WHERE id = '$medio_id'";
            mysqli_query($con, $query_delete_medios);
        }
    }

    // Guardar nuevos medios en la carpeta y almacenar sus rutas en la base de datos
    if (isset($_FILES['medios']) && !empty($_FILES['medios']['tmp_name'][0])) {
        $directorio = 'productosventa/';
        if (!is_dir($directorio)) {
            mkdir($directorio, 0777, true);
        }

        foreach ($_FILES['medios']['tmp_name'] as $key => $tmp_name) {
            $nombre_original = $_FILES['medios']['name'][$key];
            $tipo = $_FILES['medios']['type'][$key];
            $ext = pathinfo($nombre_original, PATHINFO_EXTENSION);

            // Generar un nombre de archivo único
            $nombre_archivo = uniqid() . ".jpg";

            if (in_array($tipo, ['image/jpeg', 'image/png', 'image/jpg'])) {
                $imagen = imagecreatefromstring(file_get_contents($tmp_name));
                if ($imagen !== false) {
                    imagejpeg($imagen, $directorio . $nombre_archivo);
                    imagedestroy($imagen);
                }
            } elseif ($ext === 'pdf' || $ext === 'mp4') {
                $nombre_archivo = uniqid() . "." . $ext;
                move_uploaded_file($tmp_name, $directorio . $nombre_archivo);
            } else {
                continue; // Saltar archivos no permitidos
            }

            $ruta_archivo = $directorio . $nombre_archivo;
            $query_medio = "INSERT INTO mediosventa (idproducto, medio) VALUES ('$idproducto', '$ruta_archivo')";
            mysqli_query($con, $query_medio);
        }
    }

    $_SESSION['message'] = 'Producto actualizado con éxito.';
    header('Location: editarproductoventa.php?id=' . $idproducto);
    exit();
}



if (isset($_POST['save'])) {
    $titulo = mysqli_real_escape_string($con, $_POST['titulo']);
    $subtitulo = mysqli_real_escape_string($con, $_POST['subtitulo']);
    $detalles = mysqli_real_escape_string($con, $_POST['detalles']);
    $stock = mysqli_real_escape_string($con, $_POST['stock']);
    $sku = mysqli_real_escape_string($con, $_POST['sku']);
    $stockminimo = mysqli_real_escape_string($con, $_POST['stockminimo']);
    $preciounitario = mysqli_real_escape_string($con, $_POST['preciounitario']);
    $preciomayoreo = mysqli_real_escape_string($con, $_POST['preciomayoreo']);
    $cantidadmayoreo = mysqli_real_escape_string($con, $_POST['cantidadmayoreo']);
    $descuento = mysqli_real_escape_string($con, $_POST['descuento']);
    $estatus = '1';

    $query = "INSERT INTO productosventa SET titulo='$titulo', subtitulo='$subtitulo', detalles='$detalles', stock='$stock', sku='$sku', stockminimo='$stockminimo', preciounitario='$preciounitario', preciomayoreo='$preciomayoreo', cantidadmayoreo='$cantidadmayoreo', descuento='$descuento', estatus='$estatus'";
    $query_run = mysqli_query($con, $query);

    if ($query_run) {
        $idproducto = mysqli_insert_id($con);

        if (!empty($_POST['categoria'])) {
            foreach ($_POST['categoria'] as $categoria) {
                $categoria = mysqli_real_escape_string($con, $categoria);
                $query_categoria = "INSERT INTO categoriasasociadasventa SET idproducto='$idproducto', categoria='$categoria'";
                mysqli_query($con, $query_categoria);
            }
        }

        if (!empty($_POST['industria'])) {
            foreach ($_POST['industria'] as $industria) {
                $industria = mysqli_real_escape_string($con, $industria);
                $query_industria = "INSERT INTO industriaasociadaventa SET idproducto='$idproducto', industria='$industria'";
                mysqli_query($con, $query_industria);
            }
        }

        if (isset($_FILES['medios']) && !empty($_FILES['medios']['tmp_name'][0])) {
            $directorio = 'productosventa/';
            if (!is_dir($directorio)) {
                mkdir($directorio, 0777, true);
            }

            foreach ($_FILES['medios']['tmp_name'] as $key => $tmp_name) {
                $nombre_original = $_FILES['medios']['name'][$key];
                $tipo = $_FILES['medios']['type'][$key];
                $ext = pathinfo($nombre_original, PATHINFO_EXTENSION);

                $nombre_archivo = uniqid() . ".jpg"; // Nombre final del archivo

                // Verificar y convertir a JPG si es necesario
                if (in_array($tipo, ['image/jpeg', 'image/png', 'image/jpg'])) {
                    $imagen = imagecreatefromstring(file_get_contents($tmp_name));
                    if ($imagen !== false) {
                        imagejpeg($imagen, $directorio . $nombre_archivo);
                        imagedestroy($imagen);
                    }
                } elseif ($ext == 'pdf' || $ext == 'mp4') {
                    $nombre_archivo = uniqid() . "." . $ext; // Mantener la extensión original
                    move_uploaded_file($tmp_name, $directorio . $nombre_archivo);
                } else {
                    continue; // Saltar archivos no permitidos
                }

                $ruta_archivo = $directorio . $nombre_archivo;
                $query_medio = "INSERT INTO mediosventa SET idproducto='$idproducto', medio='$ruta_archivo'";
                mysqli_query($con, $query_medio);
            }
        }

        $_SESSION['message'] = "Registro fue exitoso";
        header("Location: carga-tienda-en-linea.php");
        exit(0);
    } else {
        $_SESSION['message'] = "Error al registrar, contacte a su proveedor";
        header("Location: carga-tienda-en-linea.php");
        exit(0);
    }
}
