<?php
if (!isset($_POST["busqueda"])) {
    header("Location: ./vender.php?status=4");
    exit;
}

include_once "base_de_datos.php";

$busqueda = $_POST["busqueda"];
$tipoBusqueda = $_POST["tipo_busqueda"] ?? "auto";

// Determinar el tipo de búsqueda
if ($tipoBusqueda == "auto") {
    // Autodetección: primero intenta por código, si no encuentra, busca por nombre
    $sentencia = $base_de_datos->prepare("SELECT * FROM productos WHERE codigo = ? LIMIT 1;");
    $sentencia->execute([$busqueda]);
    $producto = $sentencia->fetch(PDO::FETCH_OBJ);
    
    if (!$producto) {
        $sentencia = $base_de_datos->prepare("SELECT * FROM productos WHERE descripcion LIKE ? LIMIT 1;");
        $sentencia->execute(["%$busqueda%"]);
        $producto = $sentencia->fetch(PDO::FETCH_OBJ);
    }
} elseif ($tipoBusqueda == "codigo") {
    $sentencia = $base_de_datos->prepare("SELECT * FROM productos WHERE codigo = ? LIMIT 1;");
    $sentencia->execute([$busqueda]);
    $producto = $sentencia->fetch(PDO::FETCH_OBJ);
} else { // búsqueda por nombre
    $sentencia = $base_de_datos->prepare("SELECT * FROM productos WHERE descripcion LIKE ? LIMIT 1;");
    $sentencia->execute(["%$busqueda%"]);
    $producto = $sentencia->fetch(PDO::FETCH_OBJ);
}

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
    $producto->cantidad = 1;
    $producto->total = $producto->precioVenta;
    array_push($_SESSION["carrito"], $producto);
} else {
    # Si ya existe, se agrega la cantidad
    $cantidadExistente = $_SESSION["carrito"][$indice]->cantidad;
    # si al sumarle uno supera lo que existe, no se agrega
    if ($cantidadExistente + 1 > $producto->existencia) {
        header("Location: ./vender.php?status=5");
        exit;
    }
    $_SESSION["carrito"][$indice]->cantidad++;
    $_SESSION["carrito"][$indice]->total = $_SESSION["carrito"][$indice]->cantidad * $_SESSION["carrito"][$indice]->precioVenta;
}

header("Location: ./vender.php");
?>