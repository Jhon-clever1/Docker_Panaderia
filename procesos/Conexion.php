<?php
$servidor = "db"; // Nombre del servicio en docker-compose
$usuario = "panaderia";
$password = "pan12345";
$bd = "ventas";

$conexion = mysqli_connect($servidor, $usuario, $password, $bd);

if(!$conexion) {
    die("Error de conexión: " . mysqli_connect_error());
}
// Elimina TODOS los echo/print/output aquí
?>