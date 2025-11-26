<?php
require 'dbcon.php';
header("Content-Type: application/json; charset=UTF-8");

$codigo = mysqli_real_escape_string($con, $_POST['codigo']);
$subtotal = floatval($_POST['subtotal']);

// 🔹 1. Buscar el cupón
$sql = "SELECT * FROM cupones WHERE codigo = '$codigo' AND estatus = 1 LIMIT 1";
$query = mysqli_query($con, $sql);

if (mysqli_num_rows($query) == 0) {
    echo json_encode(["ok" => false, "msg" => "El cupón no existe o está desactivado"]);
    exit;
}

$cupon = mysqli_fetch_assoc($query);

// 🔹 2. Contar cuántas veces se ha canjeado
$sqlCanjes = "SELECT COUNT(*) AS usados FROM cuponescanjeados WHERE codigo = '$codigo'";
$resCanjes = mysqli_query($con, $sqlCanjes);
$rowCanjes = mysqli_fetch_assoc($resCanjes);

$usados = intval($rowCanjes['usados']);
$limite = intval($cupon['canjes']);

if ($usados >= $limite) {
    echo json_encode(["ok" => false, "msg" => "Este cupón ya alcanzó su límite de usos"]);
    exit;
}

// 🔹 3. Validar monto mínimo
$minimo = floatval($cupon['minimo']);

if ($subtotal < $minimo) {
    echo json_encode([
        "ok" => false,
        "msg" => "El monto mínimo para canjear este cupón es de $".number_format($minimo,2)
    ]);
    exit;
}

// 🔹 4. Calcular descuento
$porcentaje = floatval($cupon['porcentaje']);
$maximo = floatval($cupon['maximo']);

$descuentoCalculado = ($subtotal * ($porcentaje / 100));

// Tope por máximo
if ($descuentoCalculado > $maximo) {
    $descuentoCalculado = $maximo;
}

echo json_encode([
    "ok" => true,
    "descuento" => $descuentoCalculado
]);
