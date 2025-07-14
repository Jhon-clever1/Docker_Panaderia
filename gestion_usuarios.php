<?php
include_once "encabezado.php";
include_once "base_de_datos.php";

// Verificar rol de administrador
if($_SESSION['rol'] != 'administrador'){
    header("Location: dashboard.php");
    exit;
}

// Obtener lista de usuarios (excepto el actual)
$sentencia = $base_de_datos->prepare("SELECT id_usuario, nombre, usuario, rol, email, fecha_creacion, activo FROM usuario WHERE id_usuario != ?");
$sentencia->execute([$_SESSION['id_usuario']]);
$usuarios = $sentencia->fetchAll(PDO::FETCH_OBJ);
?>

<style>
    .container {
        max-width: 95%;
        margin-top: 20px;
    }
    
    .table-container {
        background: white;
        border-radius: 15px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        padding: 20px;
    }
    
    .badge-admin {
        background-color: #d35400;
        color: white;
        padding: 3px 8px;
        border-radius: 10px;
        font-size: 0.8em;
    }
    
    .badge-empleado {
        background-color: #3498db;
        color: white;
        padding: 3px 8px;
        border-radius: 10px;
        font-size: 0.8em;
    }
    
    .badge-inactive {
        background-color: #95a5a6;
        color: white;
        padding: 3px 8px;
        border-radius: 10px;
        font-size: 0.8em;
    }
</style>

<div class="container">
    <h1>Gestión de Usuarios</h1>
    
    <div class="mb-4">
        <a class="btn btn-success" href="./nuevo_usuario.php">
            <i class="fa fa-plus"></i> Nuevo Usuario
        </a>
    </div>
    
    <div class="table-container">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Usuario</th>
                    <th>Rol</th>
                    <th>Email</th>
                    <th>Fecha Creación</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($usuarios as $usuario): ?>
                <tr>
                    <td><?= $usuario->id_usuario ?></td>
                    <td><?= htmlspecialchars($usuario->nombre) ?></td>
                    <td><?= htmlspecialchars($usuario->usuario) ?></td>
                    <td>
                        <span class="badge-<?= $usuario->rol ?>">
                            <?= ucfirst($usuario->rol) ?>
                        </span>
                    </td>
                    <td><?= $usuario->email ?? 'N/A' ?></td>
                    <td><?= date('d/m/Y', strtotime($usuario->fecha_creacion)) ?></td>
                    <td>
                        <?php if($usuario->activo): ?>
                            <span class="badge badge-success">Activo</span>
                        <?php else: ?>
                            <span class="badge badge-secondary">Inactivo</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a class="btn btn-warning btn-sm" href="./editar_usuario.php?id=<?= $usuario->id_usuario ?>">
                            <i class="fa fa-edit"></i> Editar
                        </a>
                        <a class="btn btn-danger btn-sm" href="./eliminar_usuario.php?id=<?= $usuario->id_usuario ?>" onclick="return confirm('¿Estás seguro de eliminar este usuario?')">
                            <i class="fa fa-trash"></i> Eliminar
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include_once "pie.php"; ?>