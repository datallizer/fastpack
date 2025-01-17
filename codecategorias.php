<?php
session_start();
require 'dbcon.php';

if (isset($_POST['delete'])) {
    $registro_id = mysqli_real_escape_string($con, $_POST['delete']);

    $query = "DELETE FROM categorias WHERE id='$registro_id' ";
    $query_run = mysqli_query($con, $query);

    if ($query_run) {
        header("Location: categorias.php");
        exit(0);
    } else {
        header("Location: categorias.php");
        exit(0);
    }
}

if (isset($_POST['update'])) {

    $id = mysqli_real_escape_string($con, $_POST['id']);
    $categoria = mysqli_real_escape_string($con, $_POST['categoria']);

    $query = "UPDATE `categorias` SET `categoria` = '$categoria' WHERE `categorias`.`id` = '$id'";
    $query_run = mysqli_query($con, $query);

    if ($query_run) {
        header("Location: categorias.php");
        exit(0);
    } else {
        header("Location: categorias.php");
        exit(0);
    }
}


if (isset($_POST['save'])) {
    $categoria = mysqli_real_escape_string($con, $_POST['categoria']);

    $query = "INSERT INTO categorias SET categoria='$categoria'";

    $query_run = mysqli_query($con, $query);
    if ($query_run) {
        header("Location: categorias.php");
        exit(0);
    } else {
        header("Location: categorias.php");
        exit(0);
    }
}
