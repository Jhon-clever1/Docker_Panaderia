<?php

$contraseña = "pan12345";
$usuario = "panaderia";
$nombre_base_de_datos = "ventas";
try{
	$base_de_datos = new PDO('mysql:host=db;dbname=' . $nombre_base_de_datos, $usuario, $contraseña);
	 $base_de_datos->query("set names utf8;");
    $base_de_datos->setAttribute(PDO::ATTR_EMULATE_PREPARES, FALSE);
    $base_de_datos->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $base_de_datos->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
    $sentencia = $base_de_datos->query("SHOW INDEX FROM ventas WHERE Key_name = 'idx_fecha'");
    if ($sentencia->rowCount() == 0) {
        $base_de_datos->query("CREATE INDEX idx_fecha ON ventas(fecha)");
    }
} catch(Exception $e) {
    // Mostrar error solo en entorno de desarrollo
    if (getenv('ENVIRONMENT') == 'development') {
        echo "Error de base de datos: " . $e->getMessage();
    } else {
        error_log("Error de base de datos: " . $e->getMessage());
        echo "Ocurrió un error con la base de datos. Por favor intente más tarde.";
    }
}
?>