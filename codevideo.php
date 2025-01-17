<?php
session_start();
require 'dbcon.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

if (isset($_POST['delete'])) {
    $registro_id = mysqli_real_escape_string($con, $_POST['delete']);

    $query = "DELETE FROM usuarios WHERE id='$registro_id' ";
    $query_run = mysqli_query($con, $query);

    if ($query_run) {
        $_SESSION['message'] = "Usuario eliminado exitosamente";
        header("Location: usuarios.php");
        exit(0);
    } else {
        $_SESSION['message'] = "Error al eliminar el usuario, contacte a soporte";
        header("Location: usuarios.php");
        exit(0);
    }
}

if (isset($_POST['update'])) {
    $id = mysqli_real_escape_string($con, $_POST['id']);
    $nombre = mysqli_real_escape_string($con, $_POST['nombre']);
    $estatus = mysqli_real_escape_string($con, $_POST['estatus']);

        $query = "UPDATE `videos` SET `nombre` = '$nombre', `estatus` = '$estatus' WHERE `videos`.`id` = '$id'";
        $query_run = mysqli_query($con, $query);

        if ($query_run) {
            $_SESSION['message'] = "Video editado exitosamente";
            header("Location: misvideos.php");
            exit(0);
        } else {
            $_SESSION['message'] = "Error al editar el video, contacte a soporte";
            header("Location: misvideos.php");
            exit(0);
        }
    
}

if (isset($_POST['save'])) {
    // Obtener el nombre del video desde el formulario
    $nombre = mysqli_real_escape_string($con, $_POST['nombre']);
    
    // Verificar si se ha subido un archivo
    if (isset($_FILES['video']) && $_FILES['video']['error'] === UPLOAD_ERR_OK) {
        // Insertar primero el registro sin el campo del video
        $query = "INSERT INTO videos (nombre) VALUES ('$nombre')";
        $query_run = mysqli_query($con, $query);

        if ($query_run) {
            // Obtener el ID del último registro insertado
            $id = mysqli_insert_id($con);
            
            // Crear la carpeta 'videos' si no existe
            if (!is_dir('videos')) {
                mkdir('videos', 0777, true);
            }

            // Obtener la extensión del archivo (asegurarse de que sea mp4)
            $fileTmpPath = $_FILES['video']['tmp_name'];
            $fileName = $id . ".mp4"; // Guardar como id.mp4
            $destinationPath = './videos/' . $fileName;

            // Mover el archivo subido a la carpeta 'videos'
            if (move_uploaded_file($fileTmpPath, $destinationPath)) {
                // Actualizar el registro con la ruta del video
                $videoPath = mysqli_real_escape_string($con, $destinationPath);
                $update_query = "UPDATE videos SET path='$videoPath' WHERE id='$id'";
                $update_query_run = mysqli_query($con, $update_query);

                if ($update_query_run) {
                    header("Location: misvideos.php");
                    exit(0);
                } else {
                    echo "Error al actualizar la ruta del video";
                }
            } else {
                echo "Error al mover el archivo de video.";
            }
        } else {
            echo "Error al insertar el registro.";
        }
    } else {
        echo "No se ha subido ningún archivo de video o hubo un error.";
    }
}
