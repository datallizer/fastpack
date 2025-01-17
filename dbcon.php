<?php

$con = mysqli_connect("localhost","root","","fastpack");
//$con = mysqli_connect("fastpack.mx","fastpack","Jcasarin22.","fastpack_fastpack");

if(!$con){
    die('Connection Failed'. mysqli_connect_error());
}

?>