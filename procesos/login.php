<?php 
    session_start();
    require "Conexion.php";

    $usuario = $_POST['usuario'];
    $password = $_POST['password'];

    $q = "SELECT COUNT(*) as contar from usuario where usuario = '$usuario' and contraseña = '$password' ";
    $consulta = mysqli_query($conexion, $q);

    $array = mysqli_fetch_array($consulta);

    if($array['contar']>0){
        $_SESSION['usuario'] = $usuario;
        header("location: ../listar.php");
    }else{
        echo "Datos incorrectos";
    }


 ?>