<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Cambiar "localhost" si es necesario
$host = "127.0.0.1"; // prueba también 127.0.0.1
$user = "fastpack";
$pass = "Jcasarin22.";
$dbname = "fastpack_fastpack";

echo "Intentando conectar...<br>";

$con = mysqli_connect($host, $user, $pass, $dbname);

if (!$con) {
    die('❌ Error de conexión: ' . mysqli_connect_error());
}

echo "✅ ¡Conexión exitosa!<br>";

mysqli_close($con);
