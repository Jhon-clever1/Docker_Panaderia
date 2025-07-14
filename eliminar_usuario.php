<?php
include_once "base_de_datos.php";

// Verificar rol de administrador
session_start();
if($_SESSION['rol'] != 'administrador'){
    header("Location: dashboard.php");
    exit;
}

if(!isset($_GET['id'])) exit();
$id = $_GET['id'];

// En lugar de eliminar, marcamos como inactivo
$sentencia = $base_de_datos->prepare("UPDATE usuario SET activo = 0 WHERE id_usuario = ?");
$resultado = $sentencia->execute([$id]);

if($resultado){
    header("Location: gestion_usuarios.php?status=7");
}else{
    header("Location: gestion_usuarios.php?status=8");
}
?>