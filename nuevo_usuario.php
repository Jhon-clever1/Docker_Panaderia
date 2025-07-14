<?php
include_once "encabezado.php";

// Verificar rol de administrador
if($_SESSION['rol'] != 'administrador'){
    header("Location: dashboard.php");
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
    <h1>Nuevo Usuario</h1>
    
    <form action="guardar_usuario.php" method="post">
        <div class="form-group">
            <label for="nombre">Nombre Completo:</label>
            <input type="text" class="form-control" name="nombre" required>
        </div>
        
        <div class="form-group">
            <label for="usuario">Nombre de Usuario:</label>
            <input type="text" class="form-control" name="usuario" required>
        </div>
        
        <div class="form-group">
            <label for="password">Contraseña:</label>
            <input type="password" class="form-control" name="password" required>
        </div>
        
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" class="form-control" name="email">
        </div>
        
        <div class="form-group">
            <label for="rol">Rol:</label>
            <select class="form-control" name="rol" required>
                <option value="empleado">Empleado</option>
                <option value="administrador">Administrador</option>
            </select>
        </div>
        
        <div class="form-group">
            <label for="activo">Estado:</label>
            <select class="form-control" name="activo" required>
                <option value="1">Activo</option>
                <option value="0">Inactivo</option>
            </select>
        </div>
        
        <div class="form-group text-right">
            <a href="gestion_usuarios.php" class="btn btn-secondary">Cancelar</a>
            <button type="submit" class="btn btn-primary">Guardar</button>
        </div>
    </form>
</div>

<?php include_once "pie.php"; ?>