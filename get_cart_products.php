<?php
require 'dbcon.php';
header("Content-Type: application/json; charset=UTF-8");

if (!isset($_POST['ids'])) {
    echo json_encode(['error' => 'No se enviaron IDs']);
    exit;
}

$ids = $_POST['ids'];
$ids = array_filter($ids, fn($id) => is_numeric($id));

if (empty($ids)) {
    echo json_encode([]);
    exit;
}

$idList = implode(',', array_map('intval', $ids));

$query = "
SELECT 
    p.id AS productoID, 
    p.titulo, 
    p.preciounitario,
    p.descuento,
    (SELECT medio FROM mediosventa WHERE idproducto = p.id ORDER BY id LIMIT 1) AS primer_medio
FROM productosventa p
WHERE p.id IN ($idList)
ORDER BY p.id DESC
";

$result = mysqli_query($con, $query);

$productos = [];
while ($row = mysqli_fetch_assoc($result)) {
    $productos[] = $row;
}

echo json_encode($productos);
