<?php
include_once "encabezado.php";
include_once "base_de_datos.php";

// Verificar rol de administrador
if($_SESSION['rol'] != 'administrador'){
    header("Location: dashboard.php");
    exit;
}

if(!isset($_GET['id'])) exit();
$id = $_GET['id'];

$sentencia = $base_de_datos->prepare("SELECT id_usuario, nombre, usuario, email, rol, activo FROM usuario WHERE id_usuario = ?");
$sentencia->execute([$id]);
$usuario = $sentencia->fetch(PDO::FETCH_OBJ);

if(!$usuario){
    header("Location: gestion_usuarios.php");
    exit;
}
?>

<style>
    .container {
        max-width: 800px;
        margin: 30px auto;
        padding: 30px;
        background: white;
        border-radius: 15px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
</style>

<div class="container">
    <h1>Editar Usuario</h1>
    
    <form action="actualizar_usuario.php" method="post">
        <input type="hidden" name="id" value="<?= $usuario->id_usuario ?>">
        
        <div class="form-group">
            <label for="nombre">Nombre Completo:</label>
            <input type="text" class="form-control" name="nombre" value="<?= htmlspecialchars($usuario->nombre) ?>" required>
        </div>
        
        <div class="form-group">
            <label for="usuario">Nombre de Usuario:</label>
            <input type="text" class="form-control" name="usuario" value="<?= htmlspecialchars($usuario->usuario) ?>" required>
        </div>
        
        <div class="form-group">
            <label for="password">Nueva Contraseña (dejar en blanco para no cambiar):</label>
            <input type="password" class="form-control" name="password">
        </div>
        
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($usuario->email ?? '') ?>">
        </div>
        
        <div class="form-group">
            <label for="rol">Rol:</label>
            <select class="form-control" name="rol" required>
                <option value="empleado" <?= $usuario->rol == 'empleado' ? 'selected' : '' ?>>Empleado</option>
                <option value="administrador" <?= $usuario->rol == 'administrador' ? 'selected' : '' ?>>Administrador</option>
            </select>
        </div>
        
        <div class="form-group">
            <label for="activo">Estado:</label>
            <select class="form-control" name="activo" required>
                <option value="1" <?= $usuario->activo ? 'selected' : '' ?>>Activo</option>
                <option value="0" <?= !$usuario->activo ? 'selected' : '' ?>>Inactivo</option>
            </select>
        </div>
        
        <div class="form-group text-right">
            <a href="gestion_usuarios.php" class="btn btn-secondary">Cancelar</a>
            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
        </div>
    </form>
</div>

<?php include_once "pie.php"; ?>