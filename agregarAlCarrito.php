<?php
if (!isset($_POST["busqueda"])) {
    header("Location: ./vender.php?status=4");
    exit;
}

include_once "base_de_datos.php";

$busqueda = $_POST["busqueda"];
$tipoBusqueda = $_POST["tipo_busqueda"] ?? "auto";
$cantidad = isset($_POST["cantidad"]) ? max(1, intval($_POST["cantidad"])) : 1;

// Determinar el tipo de búsqueda
if ($tipoBusqueda == "id") {
    $sentencia = $base_de_datos->prepare("SELECT * FROM productos WHERE id = ? LIMIT 1;");
    $sentencia->execute([$busqueda]);
    $producto = $sentencia->fetch(PDO::FETCH_OBJ);
    
    if(!$producto) {
        header("Location: ./vender.php?status=4");
        exit;
    }
} else{

    # Si no existe, salimos y lo indicamos
    if (!$producto) {
        header("Location: ./vender.php?status=4");
        exit;
    }

    # Si no hay existencia...
    if ($producto->existencia < 1) {
        header("Location: ./vender.php?status=5");
        exit;
    }
}
session_start();
# Buscar producto dentro del carrito
$indice = false;
for ($i = 0; $i < count($_SESSION["carrito"]); $i++) {
    if ($_SESSION["carrito"][$i]->id === $producto->id) {
        $indice = $i;
        break;
    }
}

# Si no existe, lo agregamos como nuevo
if ($indice === false) {
    $producto->cantidad = $cantidad;
    $producto->total = $producto->precioVenta*$cantidad;
    array_push($_SESSION["carrito"], $producto);
} else {
    # Si ya existe, se agrega la cantidad
    $nuevaCantidad = $_SESSION["carrito"][$indice]->cantidad + $cantidad;
    # si al sumarle uno supera lo que existe, no se agrega
    if ($nuevaCantidad > $producto->existencia) {
        header("Location: ./vender.php?status=5");
        exit;
    }
    $_SESSION["carrito"][$indice]->cantidad = $nuevaCantidad;
    $_SESSION["carrito"][$indice]->total = $nuevaCantidad * $_SESSION["carrito"][$indice]->precioVenta;
}

header("Location: ./vender.php");
?>