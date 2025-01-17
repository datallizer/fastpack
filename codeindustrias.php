<?php
session_start();
require 'dbcon.php';

if (isset($_POST['delete'])) {
    $registro_id = mysqli_real_escape_string($con, $_POST['delete']);

    $query = "DELETE FROM industrias WHERE id='$registro_id' ";
    $query_run = mysqli_query($con, $query);

    if ($query_run) {
        header("Location: industrias.php");
        exit(0);
    } else {
        header("Location: industrias.php");
        exit(0);
    }
}

if (isset($_POST['update'])) {

    $id = mysqli_real_escape_string($con, $_POST['id']);
    $industria = mysqli_real_escape_string($con, $_POST['industria']);

    $query = "UPDATE `industrias` SET `industria` = '$industria' WHERE `industrias`.`id` = '$id'";
    $query_run = mysqli_query($con, $query);

    if ($query_run) {
        header("Location: industrias.php");
        exit(0);
    } else {
        header("Location: industrias.php");
        exit(0);
    }
}


if (isset($_POST['save'])) {
    $industria = mysqli_real_escape_string($con, $_POST['industria']);

    $query = "INSERT INTO industrias SET industria='$industria'";

    $query_run = mysqli_query($con, $query);
    if ($query_run) {
        header("Location: industrias.php");
        exit(0);
    } else {
        header("Location: industrias.php");
        exit(0);
    }
}
