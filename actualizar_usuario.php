<?php
include_once "base_de_datos.php";

// Verificar rol de administrador
session_start();
if($_SESSION['rol'] != 'administrador'){
    header("Location: dashboard.php");
    exit;
}

$id = $_POST['id'];
$nombre = $_POST['nombre'];
$usuario = $_POST['usuario'];
$password = $_POST['password'];
$email = $_POST['email'] ?? null;
$rol = $_POST['rol'];
$activo = $_POST['activo'];

try {
    if(!empty($password)){
        $sentencia = $base_de_datos->prepare("UPDATE usuario SET nombre = ?, usuario = ?, contraseña = ?, email = ?, rol = ?, activo = ? WHERE id_usuario = ?");
        $resultado = $sentencia->execute([$nombre, $usuario, $password, $email, $rol, $activo, $id]);
    }else{
        $sentencia = $base_de_datos->prepare("UPDATE usuario SET nombre = ?, usuario = ?, email = ?, rol = ?, activo = ? WHERE id_usuario = ?");
        $resultado = $sentencia->execute([$nombre, $usuario, $email, $rol, $activo, $id]);
    }
    
    if($resultado){
        header("Location: gestion_usuarios.php?status=4");
    }else{
        header("Location: editar_usuario.php?id=$id&status=5");
    }
} catch(Exception $e) {
    header("Location: editar_usuario.php?id=$id&status=6");
}
?>