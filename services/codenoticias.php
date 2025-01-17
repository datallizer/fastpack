<?php
session_start();
require '../dbcon.php';

if(isset($_POST['delete']))
{
    $registro_id = mysqli_real_escape_string($con, $_POST['delete']);

    $query = "DELETE FROM noticias WHERE id='$registro_id' ";
    $query_run = mysqli_query($con, $query);

    if($query_run)
    {
        $_SESSION['message'] = "Noticia eliminada exitosamente";
        header("Location: monitornoticias.php");
        exit(0);
    }
    else
    {
        $_SESSION['message'] = "Error contacte a soporte";
        header("Location: ../monitornoticias.php");
        exit(0);
    }
}

if(isset($_POST['update']))
{
    $id = mysqli_real_escape_string($con,$_POST['id']);
    $titulo = mysqli_real_escape_string($con, $_POST['titulo']);
    $descripcion = mysqli_real_escape_string($con, $_POST['descripcion']);
    $nota = mysqli_real_escape_string($con, $_POST['nota']);
    $fecha = mysqli_real_escape_string($con, $_POST['fecha']);
    $username_id = mysqli_real_escape_string($con, $_POST['username_id']);
    $categoria_id = mysqli_real_escape_string($con, $_POST['categoria_id']);
    $anuncio_id = mysqli_real_escape_string($con, $_POST['username_id']);
    $medios =addslashes (file_get_contents($_FILES['medios']['tmp_name']));

    $query = "UPDATE `noticias` SET `titulo` = '$titulo', `descripcion` = '$descripcion', `nota` = '$nota', `fecha` = '$fecha', `username_id` = '$username_id' WHERE `noticias`.`id` = '$id'";
    $query_run = mysqli_query($con, $query);

    if($query_run)
    {
        $_SESSION['message'] = "Noticia editada exitosamente";
        header("Location: ../monitornoticias.php");
        exit(0);
    }
    else
    {
        $_SESSION['message'] = "Error contacte a soporte";
        header("Location: ../monitornoticias.php");
        exit(0);
    }

}

if(isset($_POST['save']))
{
    $titulo = mysqli_real_escape_string($con, $_POST['titulo']);
    $descripcion = mysqli_real_escape_string($con, $_POST['descripcion']);
    $nota = mysqli_real_escape_string($con, $_POST['nota']);
    $fecha = mysqli_real_escape_string($con, $_POST['fecha']);
    $username_id = mysqli_real_escape_string($con, $_POST['username_id']);
    $categoria_id = mysqli_real_escape_string($con, $_POST['categoria_id']);
    $anuncio_id = mysqli_real_escape_string($con, $_POST['username_id']);
    $imagen =addslashes (file_get_contents($_FILES['imagen']['tmp_name']));

    $query = "INSERT INTO noticias SET titulo='$titulo', descripcion='$descripcion', nota='$nota', fecha='$fecha', username_id='$username_id', categoria_id='$categoria_id', anuncio_id='$anuncio_id', imagen='$imagen'";

    $query_run = mysqli_query($con, $query);
    if($query_run)
    {
        $_SESSION['message'] = "Noticia creada exitosamente";
        header("Location: ../monitornoticias.php");
        exit(0);
    }
    else
    {
        $_SESSION['message'] = "Error contacte a soporte";
        header("Location: ../monitornoticias.php");
        exit(0);
    }
}

?>