<?php 
    session_start();
    require "Conexion.php";

    $usuario = $_POST['usuario'];
    $password = $_POST['password'];

    $q = "SELECT id_usuario, nombre, usuario, rol FROM usuario WHERE usuario = '$usuario' AND contraseña = '$password' AND activo = 1";
    $consulta = mysqli_query($conexion, $q);

    if(mysqli_num_rows($consulta) > 0){
        $usuario_data = mysqli_fetch_assoc($consulta);
        $_SESSION['usuario'] = $usuario_data['usuario'];
        $_SESSION['nombre'] = $usuario_data['nombre'];
        $_SESSION['id_usuario'] = $usuario_data['id_usuario'];
        $_SESSION['rol'] = $usuario_data['rol'];
        header("location: ../dashboard.php");
    }else{
        echo "Datos incorrectos o usuario inactivo";
    }
?>