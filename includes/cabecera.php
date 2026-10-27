<?php 
//Rol de quien navega: 'user', 'admin' o '' si no ha hecho login
$rol = $_SESSION['rol'] ?? '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wäter</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600&family=Playfair+Display:wght@600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= $raiz ?>css/estilos.css">
    <script src="<?= $raiz ?>js/main.js" defer></script>
</head>
<body>
    <header class="cabecera"> 
        <a href="<?= $raiz ?>index.php" class="logo"> WÄTER </a>
        <nav class="menu">
            <ul>
                <?php /* Inicio y noticias siempre están en el navegador independientemente del rol. */ ?>
                <li><a href="<?= $raiz ?>index.php" <?php if($pagina === 'inicio'){echo 'class="activo"';}?>> Inicio</a></li>
                <li><a href="<?= $raiz ?>paginas/publicas/noticias.php" <?php if($pagina === 'noticias'){echo 'class="activo"';}?>>Noticias</a></li>

                <?php if($rol === ''){ ?>
                    <li><a href="<?= $raiz ?>paginas/publicas/registro.php" <?php if($pagina === 'registro'){echo 'class="activo"';}?>>Registro</a></li>
                    <li><a href="<?= $raiz ?>paginas/publicas/login.php" <?php if($pagina === 'login'){echo 'class="activo"';}?>>Login</a></li>
                <?php } elseif ($rol === 'user'){ ?>
                    <li><a href="<?= $raiz ?>paginas/usuario/citaciones.php" <?php if($pagina === 'citaciones'){echo 'class="activo"';}?>>Citaciones</a></li>
                    <li><a href="<?= $raiz ?>paginas/usuario/perfil.php" <?php if($pagina === 'perfil'){echo 'class="activo"';}?>>Perfil</a></li>
                    <li><a href="<?= $raiz ?>paginas/usuario/logout.php">Cerrar sesión</a></li>
                <?php } elseif($rol === 'admin') { ?>
                    <li><a href="<?= $raiz ?>paginas/admin/usuarios-administracion.php" <?php if($pagina === 'usuarios-admin'){echo 'class="activo"';}?>>Gestión usuarios</a></li>
                    <li><a href="<?= $raiz ?>paginas/admin/citas-administracion.php" <?php if($pagina === 'citas-admin'){echo 'class="activo"';}?>>Citas</a></li>
                    <li><a href="<?= $raiz ?>paginas/admin/noticias-administracion.php" <?php if($pagina === 'noticias-admin'){echo 'class="activo"';}?>>Gestión noticias</a></li>
                    <li><a href="<?= $raiz ?>paginas/usuario/perfil.php" <?php if($pagina === 'perfil'){echo 'class="activo"';}?>>Perfil</a></li>
                    <li><a href="<?= $raiz ?>paginas/usuario/logout.php">Cerrar sesión</a></li>
                <?php } ?> 
            </ul>
        </nav>
    </header>
