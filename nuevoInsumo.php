<?php
#Salir si alguno de los datos no está presente
if(!isset($_POST["nombre"]) || !isset($_POST["cantidad"]) || !isset($_POST["unidad"]) || !isset($_POST["totalCompra"]) || !isset($_POST["existencia"]) || !isset($_POST["fecha"]) ) exit();

#Si todo va bien, se ejecuta esta parte del código...

include_once "base_de_datos.php";
$nombre = $_POST["nombre"];
$cantidad = $_POST["cantidad"];
$unidad = $_POST["unidad"];
$totalCompra = $_POST["totalCompra"];
$existencia = $_POST["existencia"];
$fecha = $_POST["fecha"];

$sentencia = $base_de_datos->prepare("INSERT INTO insumo(nombre_insumo, cantidadComprada, unidadMedida, total_Compra, existencia, fecha_compra) VALUES (?, ?, ?, ?, ?, ?);");
$resultado = $sentencia->execute([$nombre, $cantidad, $unidad, $totalCompra, $existencia, $fecha]);

if($resultado === TRUE){
	header("Location: ./insumos.php");
	exit;
}
else echo "Algo salió mal. Por favor verifica que la tabla exista";


?>
<?php include_once "pie.php" ?>