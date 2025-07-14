<?php

// Redirigir si no hay sesión activa
if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit;
}

// Definir permisos según rol
function soloAdministrador() {
    if ($_SESSION['rol'] != 'administrador') {
        header("Location: dashboard.php");
        exit;
    }
}

function esAdministrador() {
    return ($_SESSION['rol'] == 'administrador');
}
?>