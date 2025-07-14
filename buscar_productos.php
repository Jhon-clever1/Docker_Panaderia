<?php
include_once "base_de_datos.php";

$term = $_GET['term'] ?? '';
$tipo = $_GET['tipo'] ?? 'auto';

header('Content-Type: application/json');

if (strlen($term) < 2) {
    echo json_encode([]);
    exit;
}

if ($tipo == 'auto') {
    // Buscar tanto por código como por nombre
    $sentencia = $base_de_datos->prepare("
        SELECT id, codigo, descripcion, precioVenta, existencia 
        FROM productos 
        WHERE codigo LIKE ? OR descripcion LIKE ?
        LIMIT 10
    ");
    $sentencia->execute(["%$term%", "%$term%"]);
} elseif ($tipo == 'codigo') {
    $sentencia = $base_de_datos->prepare("
        SELECT id, codigo, descripcion, precioVenta, existencia 
        FROM productos 
        WHERE codigo LIKE ?
        LIMIT 10
    ");
    $sentencia->execute(["%$term%"]);
} else { // nombre
    $sentencia = $base_de_datos->prepare("
        SELECT id, codigo, descripcion, precioVenta, existencia 
        FROM productos 
        WHERE descripcion LIKE ?
        LIMIT 10
    ");
    $sentencia->execute(["%$term%"]);
}

$productos = $sentencia->fetchAll(PDO::FETCH_OBJ);

$resultados = [];
foreach ($productos as $producto) {
    $resultados[] = [
        'label' => $producto->descripcion,
        'value' => $producto->codigo,
        'codigo' => $producto->codigo,
        'precio' => number_format($producto->precioVenta, 2),
        'stock' => $producto->existencia,
        'id' => $producto->id
    ];
}

echo json_encode($resultados);
?>