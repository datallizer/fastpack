<?php
session_start();
require 'dbcon.php';

if (isset($_POST['delete'])) {
    $registro_id = mysqli_real_escape_string($con, $_POST['delete']);

    $query = "DELETE FROM subcategorias WHERE id='$registro_id' ";
    $query_run = mysqli_query($con, $query);

    if ($query_run) {
        header("Location: subcategorias.php");
        exit(0);
    } else {
        header("Location: subcategorias.php");
        exit(0);
    }
}

if (isset($_POST['update'])) {

    $id = mysqli_real_escape_string($con, $_POST['id']);
    $subcategoria = mysqli_real_escape_string($con, $_POST['subcategoria']);

    $query = "UPDATE `subcategorias` SET `subcategoria` = '$subcategoria' WHERE `subcategorias`.`id` = '$id'";
    $query_run = mysqli_query($con, $query);

    if ($query_run) {
        header("Location: subcategorias.php");
        exit(0);
    } else {
        header("Location: subcategorias.php");
        exit(0);
    }
}


if (isset($_POST['save'])) {
    $subcategoria = mysqli_real_escape_string($con, $_POST['subcategoria']);

    $query = "INSERT INTO subcategorias SET subcategoria='$subcategoria'";

    $query_run = mysqli_query($con, $query);
    if ($query_run) {
        header("Location: subcategorias.php");
        exit(0);
    } else {
        header("Location: subcategorias.php");
        exit(0);
    }
}
