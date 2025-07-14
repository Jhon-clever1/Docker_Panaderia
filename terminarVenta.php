<?php
if(!isset($_POST["total"])) exit;

session_start();
if(empty($_SESSION["carrito"])) {
    header("Location: ./vender.php?status=6");
    exit;
}
include_once "base_de_datos.php";

$total = $_POST["total"];
$ahora = date("Y-m-d H:i:s");

// Insertar la venta
$sentencia = $base_de_datos->prepare("INSERT INTO ventas(fecha, total) VALUES (?, ?);");
$sentencia->execute([$ahora, $total]);

// Obtener el ID de la venta recién insertada
$idVenta = $base_de_datos->lastInsertId();

// Guardar los productos vendidos y actualizar existencias
$base_de_datos->beginTransaction();
$sentencia = $base_de_datos->prepare("INSERT INTO productos_vendidos(id_producto, id_venta, cantidad) VALUES (?, ?, ?);");
$sentenciaExistencia = $base_de_datos->prepare("UPDATE productos SET existencia = existencia - ? WHERE id = ?;");

foreach ($_SESSION["carrito"] as $producto) {
    $sentencia->execute([$producto->id, $idVenta, $producto->cantidad]);
    $sentenciaExistencia->execute([$producto->cantidad, $producto->id]);
}
$base_de_datos->commit();

// Generar comprobante
$comprobante = generarComprobante($idVenta, $base_de_datos);

// Limpiar carrito
unset($_SESSION["carrito"]);
$_SESSION["carrito"] = [];

// Redirigir al comprobante
header("Location: ./comprobante.php?id=" . $idVenta);
exit;

function generarComprobante($idVenta, $base_de_datos) {
    // Obtener datos de la venta
    $sentencia = $base_de_datos->prepare("SELECT * FROM ventas WHERE id = ? LIMIT 1;");
    $sentencia->execute([$idVenta]);
    $venta = $sentencia->fetch(PDO::FETCH_OBJ);
    
    // Obtener productos vendidos
    $sentencia = $base_de_datos->prepare("
        SELECT p.codigo, p.descripcion, pv.cantidad, p.precioVenta, (pv.cantidad * p.precioVenta) as subtotal 
        FROM productos_vendidos pv
        JOIN productos p ON pv.id_producto = p.id
        WHERE pv.id_venta = ?
    ");
    $sentencia->execute([$idVenta]);
    $productos = $sentencia->fetchAll(PDO::FETCH_OBJ);
    
    return [
        'venta' => $venta,
        'productos' => $productos
    ];
}
?>