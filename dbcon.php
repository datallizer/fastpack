<?php

// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

$con = mysqli_connect("localhost","root","","fastpack");
// $con = mysqli_connect("fastpack.mx","fastpack_web",".X%_6yVH00LfBFzk","fastpack_fastpack");
mysqli_set_charset($con, "utf8mb4");

if(!$con){
    die('Connection Failed'. mysqli_connect_error());
}

?>