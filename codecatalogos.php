<?php
session_start();
require 'dbcon.php';

if (isset($_POST['delete'])) {
    $registro_id = mysqli_real_escape_string($con, $_POST['delete']);

    // Obtén la ruta del archivo a eliminar
    $query = "SELECT path FROM catalogos WHERE id='$registro_id'";
    $query_run = mysqli_query($con, $query);
    $registro = mysqli_fetch_assoc($query_run);

    if ($registro) {
        $file_path = $registro['path']; // Ruta almacenada en la base de datos

        // Intenta eliminar el archivo
        if (file_exists($file_path)) {
            unlink($file_path); // Elimina el archivo PDF
        }

        // Elimina el registro de la base de datos
        $delete_query = "DELETE FROM catalogos WHERE id='$registro_id'";
        $delete_query_run = mysqli_query($con, $delete_query);

        if ($delete_query_run) {
            $_SESSION['message'] = "PDF eliminado exitosamente";
        } else {
            $_SESSION['message'] = "Error al eliminar el PDF de la base de datos, contacte a soporte";
        }
    } else {
        $_SESSION['message'] = "El registro no existe o ya fue eliminado";
    }

    header("Location: miscatalogos.php");
    exit(0);
}


if (isset($_POST['update'])) {
    $id = mysqli_real_escape_string($con, $_POST['id']);
    $nombre = mysqli_real_escape_string($con, $_POST['nombre']);
    $estatus = mysqli_real_escape_string($con, $_POST['estatus']);

        $query = "UPDATE `catalogos` SET `nombre` = '$nombre', `estatus` = '$estatus' WHERE `catalogos`.`id` = '$id'";
        $query_run = mysqli_query($con, $query);

        if ($query_run) {
            $_SESSION['message'] = "PDF editado exitosamente";
            header("Location: miscatalogos.php");
            exit(0);
        } else {
            $_SESSION['message'] = "Error al editar el PDF, contacte a soporte";
            header("Location: miscatalogos.php");
            exit(0);
        }
    
}

if (isset($_POST['save'])) {
    $nombre = mysqli_real_escape_string($con, $_POST['nombre']);
    
    // Verificar si se ha subido un archivo
    if (isset($_FILES['medio']) && $_FILES['medio']['error'] === UPLOAD_ERR_OK) {
        // Insertar primero el registro sin el campo del video
        $query = "INSERT INTO catalogos (nombre) VALUES ('$nombre')";
        $query_run = mysqli_query($con, $query);

        if ($query_run) {
            // Obtener el ID del último registro insertado
            $id = mysqli_insert_id($con);
            
            // Crear la carpeta 'videos' si no existe
            if (!is_dir('catalogos')) {
                mkdir('catalogos', 0777, true);
            }

            // Obtener la extensión del archivo (asegurarse de que sea mp4)
            $fileTmpPath = $_FILES['medio']['tmp_name'];
            $fileName = $id . ".pdf"; // Guardar como id.mp4
            $destinationPath = './catalogos/' . $fileName;

            // Mover el archivo subido a la carpeta 'videos'
            if (move_uploaded_file($fileTmpPath, $destinationPath)) {
                // Actualizar el registro con la ruta del video
                $videoPath = mysqli_real_escape_string($con, $destinationPath);
                $update_query = "UPDATE catalogos SET path='$videoPath' WHERE id='$id'";
                $update_query_run = mysqli_query($con, $update_query);

                if ($update_query_run) {
                    header("Location: miscatalogos.php");
                    exit(0);
                } else {
                    echo "Error al actualizar la ruta del pdf";
                }
            } else {
                echo "Error al mover el archivo pdf.";
            }
        } else {
            echo "Error al insertar el registro.";
        }
    } else {
        echo "No se ha subido ningún archivo de video o hubo un error.";
    }
}
