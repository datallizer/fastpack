<?php
session_start();
require 'dbcon.php';

if (isset($_POST['delete'])) {
    $registro_id = mysqli_real_escape_string($con, $_POST['delete']);

    $query = "DELETE FROM cupones WHERE id='$registro_id' ";
    $query_run = mysqli_query($con, $query);

    if ($query_run) {
        $_SESSION['alert'] = [
            'message' => 'El cupón se elimino exitosamente',
            'title' => 'CUPÓN ELIMINADO',
            'icon' => 'success'
        ];
        header("Location: cupones.php");
        exit(0);
    } else {
        $_SESSION['alert'] = [
            'message' => 'Contacta a soporte',
            'title' => 'ERROR',
            'icon' => 'error'
        ];
        header("Location: cupones.php");
        exit(0);
    }
}

if (isset($_POST['update'])) {
    $id = mysqli_real_escape_string($con, $_POST['id']);
    $cupon = mysqli_real_escape_string($con, $_POST['cupon']);
    $codigo = mysqli_real_escape_string($con, $_POST['codigo']);
    $porcentaje = mysqli_real_escape_string($con, $_POST['porcentaje']);
    $minimo = mysqli_real_escape_string($con, $_POST['minimo']);
    $maximo = mysqli_real_escape_string($con, $_POST['maximo']);
    $canjes = mysqli_real_escape_string($con, $_POST['canjes']);
    $estatus = mysqli_real_escape_string($con, $_POST['estatus']);

    $query = "UPDATE `cupones` SET `cupon` = '$cupon', `codigo` = '$codigo', `porcentaje` = '$porcentaje', `minimo` = '$minimo', `maximo` = '$maximo', `canjes` = '$canjes', `estatus` = '$estatus' WHERE `cupones`.`id` = '$id'";
    $query_run = mysqli_query($con, $query);

    if ($query_run) {
        $_SESSION['alert'] = [
            'message' => 'Se actualizo el cupón',
            'title' => 'CUPÓN ACTUALIZADO',
            'icon' => 'success'
        ];
        header("Location: cupones.php");
        exit(0);
    } else {
        $_SESSION['alert'] = [
            'message' => 'Contacta a soporte',
            'title' => 'ERROR',
            'icon' => 'error'
        ];
        header("Location: cupones.php");
        exit(0);
    }
}


if (isset($_POST['save'])) {
    $cupon = mysqli_real_escape_string($con, $_POST['cupon']);
    $codigo = mysqli_real_escape_string($con, $_POST['codigo']);
    $porcentaje = mysqli_real_escape_string($con, $_POST['porcentaje']);
    $minimo = mysqli_real_escape_string($con, $_POST['minimo']);
    $maximo = mysqli_real_escape_string($con, $_POST['maximo']);
    $canjes = mysqli_real_escape_string($con, $_POST['canjes']);
    $estatus = 1;

    $query = "INSERT INTO cupones SET cupon='$cupon', codigo='$codigo', porcentaje='$porcentaje', minimo='$minimo', maximo='$maximo', canjes='$canjes', estatus='$estatus'";

    $query_run = mysqli_query($con, $query);
    if ($query_run) {
        $_SESSION['alert'] = [
            'message' => 'Los clientes pueden comenzar a canjearlo',
            'title' => 'CUPÓN REGISTRADO',
            'icon' => 'success'
        ];
        header("Location: cupones.php");
        exit(0);
    } else {
        $_SESSION['alert'] = [
            'message' => 'Contacta a soporte',
            'title' => 'ERROR',
            'icon' => 'error'
        ];
        header("Location: cupones.php");
        exit(0);
    }
}
