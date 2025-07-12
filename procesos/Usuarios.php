<?php 

    class usuarios{
        public function loginUser($datos){

            $c=new conectar();
            $conexion=$c->conexion();
            $password=sha1($datos[1]);

            $_SESSION['usuario']=$datos[0];
            $_SESSION['id_usuario']=self::traeID($datos);

            $sql="SELECT * from usuario
                    where usuario='$datos[0]'
                    and contraseña='$password'";
            $result=mysqli_query($conexion,$sql);

            if(mysqli_num_rows($result) > 0){
                return 1;
            }else{
                return 0;
            }
        }

        public function traeID($datos){
            $c=new conectar();
            $conexion=$c->conexion();

            $password=sha1($datos[1]);

            $sql="SELECT id_usuario 
                    from usuario 
                    where usuario='$datos[0]' 
                    and contraseña='$password'";
            $result=mysqli_query($conexion,$sql);

            return mysqli_fetch_row($result)[0];
        }
    }

?>