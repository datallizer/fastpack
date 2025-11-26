<?php
require 'dbcon.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_POST['delete'])) {
    $registro_id = mysqli_real_escape_string($con, $_POST['delete']);

    // 1. Obtener la ruta de la imagen antes de borrar el registro
    $query_img = "SELECT medio FROM promociones WHERE id='$registro_id' LIMIT 1";
    $query_img_run = mysqli_query($con, $query_img);

    if ($query_img_run && mysqli_num_rows($query_img_run) > 0) {
        $data = mysqli_fetch_assoc($query_img_run);
        $ruta_imagen = $data['medio'];

        // 2. Si la imagen existe, eliminarla
        if (!empty($ruta_imagen) && file_exists($ruta_imagen)) {
            unlink($ruta_imagen);
        }
    }

    // 3. Eliminar el registro
    $query = "DELETE FROM promociones WHERE id='$registro_id' ";
    $query_run = mysqli_query($con, $query);

    if ($query_run) {
        $_SESSION['message'] = "Promoción eliminada correctamente";
        header("Location: promociones.php");
        exit(0);
    } else {
        $_SESSION['alert'] = [
            'message' => 'Contacta a soporte',
            'title' => 'ERROR AL ELIMINAR',
            'icon' => 'error'
        ];
        header("Location: promociones.php");
        exit(0);
    }
}


if (isset($_POST['update'])) {
    $id = mysqli_real_escape_string($con, $_POST['id']);
    $nombre = mysqli_real_escape_string($con, $_POST['nombre']);
    $url = mysqli_real_escape_string($con, $_POST['url']);
    $estatus = mysqli_real_escape_string($con, $_POST['estatus']);

    // Directorio donde se guardarán las imágenes
    $upload_dir = './promociones/';
    $file_path = $upload_dir . $id . '.jpg';

    // Verificar si se ha subido una nueva imagen
    if ($_FILES['nuevaFoto']['size'] > 0) {

        if (file_exists($file_path)) {
            unlink($file_path); // Elimina la imagen anterior si existe
        }

        // Obtener información de la imagen
        $image_tmp_name = $_FILES['nuevaFoto']['tmp_name'];
        $image_info = getimagesize($image_tmp_name);
        $image_type = $image_info[2];

        // Convertir la imagen a formato jpg
        switch ($image_type) {
            case IMAGETYPE_JPEG:
                $src_image = imagecreatefromjpeg($image_tmp_name);
                break;
            case IMAGETYPE_PNG:
                $src_image = imagecreatefrompng($image_tmp_name);
                break;
            case IMAGETYPE_GIF:
                $src_image = imagecreatefromgif($image_tmp_name);
                break;
            default:
                $_SESSION['alert'] = [
                    'message' => 'Formato de imagen no soportado',
                    'title' => 'SELECCIONA OTRA IMAGEN',
                    'icon' => 'warning'
                ];
                header("Location: promociones.php");
                exit(0);
        }

        // Guardar la imagen convertida como .jpg
        imagejpeg($src_image, $file_path);
        imagedestroy($src_image);

        // Actualizar la base de datos con la ruta de la imagen
        $medio = mysqli_real_escape_string($con, $file_path);
        $update_query = "UPDATE promociones SET medio='$medio' WHERE id='$id'";
        $update_result = mysqli_query($con, $update_query);

        if (!$update_result) {
            $_SESSION['alert'] = [
                'message' => 'Contacta a soporte',
                'title' => 'IMAGEN NO ACTUALIZADA',
                'icon' => 'error'
            ];
            header("Location: promociones.php");
            exit(0);
        }
    }

    // Actualizar los datos de la promocion
    $query = "UPDATE `promociones` SET `nombre` = '$nombre', `url` = '$url', `estatus` = '$estatus' WHERE `promociones`.`id` = '$id'";
    $query_run = mysqli_query($con, $query);

    if ($query_run) {
        $_SESSION['alert'] = [
            'message' => 'Se guardaron los cambios exitosamente',
            'title' => 'PROMOCION ACTUALIZADA',
            'icon' => 'success'
        ];
        header("Location: promociones.php");
        exit(0);
    } else {
        $_SESSION['alert'] = [
            'message' => 'Contacta a soporte',
            'title' => 'ERROR',
            'icon' => 'error'
        ];
        header("Location: promociones.php");
        exit(0);
    }
}

if (isset($_POST['save'])) {
    $nombre = mysqli_real_escape_string($con, $_POST['nombre']);
    $url = mysqli_real_escape_string($con, $_POST['url']);
    $estatus = 1;

    // Preparar los datos para la inserción
    $query = "INSERT INTO promociones (nombre, url, estatus) VALUES ('$nombre', '$url', '$estatus')";
    $query_run = mysqli_query($con, $query);

    if ($query_run) {
        $id = mysqli_insert_id($con); // Obtener el ID del nuevo registro

        // Inicializar la ruta de la imagen
        $ruta_imagen = '';

        // Procesar la imagen si se ha subido
        if (isset($_FILES['medio']) && $_FILES['medio']['error'] == 0) {
            $imagen_tmp = $_FILES['medio']['tmp_name'];
            $imagen_ext = strtolower(pathinfo($_FILES['medio']['name'], PATHINFO_EXTENSION));
            $imagen_nombre = $id . '.jpg'; // Nombre del archivo con extensión .jpg
            $imagen_destino = 'promociones/' . $imagen_nombre;

            // Convertir a JPG
            $imagen = imagecreatefromstring(file_get_contents($imagen_tmp));
            if ($imagen) {
                imagejpeg($imagen, $imagen_destino, 100); // Guardar como JPG con calidad 100
                imagedestroy($imagen);

                // Establecer la ruta de la imagen
                $ruta_imagen = $imagen_destino;
            }
        }

        // Actualizar la base de datos con la ruta de la imagen
        $query_update = "UPDATE promociones SET medio='./$ruta_imagen' WHERE id='$id'";
        $query_update_run = mysqli_query($con, $query_update);

        $_SESSION['alert'] = [
            'message' => 'Se registro la promoción correctamente',
            'title' => 'PROMOCIÓN ACTIVA',
            'icon' => 'success'
        ];
        header("Location: promociones.php");
        exit();
    } else {
        $_SESSION['alert'] = [
            'message' => 'Intenta nuevamete y si el error persiste contacta a soporte',
            'title' => 'ERROR',
            'icon' => 'error'
        ];
        header("Location: promociones.php");
        exit(0);
    }
}
