<?php

#Salir si alguno de los datos no está presente
if(
	!isset($_POST["nombre"]) || 
	!isset($_POST["cantidad"]) || 
	!isset($_POST["unidad"]) || 
	!isset($_POST["totalCompra"]) || 
	!isset($_POST["existencia"]) ||
	!isset($_POST["fecha"]) || 
	!isset($_POST["id"])
) exit();

#Si todo va bien, se ejecuta esta parte del código...

include_once "base_de_datos.php";
$id = $_POST["id"];
$nombre = $_POST["nombre"];
$cantidad = $_POST["cantidad"];
$unidad = $_POST["unidad"];
$totalCompra = $_POST["totalCompra"];
$existencia = $_POST["existencia"];
$fecha = $_POST["fecha"];

$sentencia = $base_de_datos->prepare("UPDATE insumo SET nombre_Insumo = ?, cantidadComprada = ?, unidadMedida = ?, total_Compra = ?, existencia = ?, fecha_compra = ? WHERE id = ?;");
$resultado = $sentencia->execute([$nombre, $cantidad, $unidad, $totalCompra, $existencia, $fecha, $id]);

if($resultado === TRUE){
	header("Location: ./insumos.php");
	exit;
}
else echo "Algo salió mal. Por favor verifica que la tabla exista, así como el ID del producto";
?>