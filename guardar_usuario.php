<?php
include_once "base_de_datos.php";

// Verificar rol de administrador
session_start();
if($_SESSION['rol'] != 'administrador'){
    header("Location: dashboard.php");
    exit;
}

$nombre = $_POST['nombre'];
$usuario = $_POST['usuario'];
$password = $_POST['password'];
$email = $_POST['email'] ?? null;
$rol = $_POST['rol'];
$activo = $_POST['activo'];

try {
    $sentencia = $base_de_datos->prepare("INSERT INTO usuario(nombre, usuario, contraseña, email, rol, fecha_creacion, activo) VALUES (?, ?, ?, ?, ?, NOW(), ?)");
    $resultado = $sentencia->execute([$nombre, $usuario, $password, $email, $rol, $activo]);
    
    if($resultado){
        header("Location: gestion_usuarios.php?status=1");
    }else{
        header("Location: nuevo_usuario.php?status=2");
    }
} catch(Exception $e) {
    header("Location: nuevo_usuario.php?status=3");
}
?>