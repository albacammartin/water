<?php 
$raiz= '../../';
$pagina= 'login';

require $raiz . 'includes/conexion.php';

$errores = [];

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $usuario= trim($_POST['usuario'] ?? '');
    $password= $_POST['password'] ?? '';

    /* Errores campo vacío */

    if($usuario === ''){
        $errores[]= 'El usuario está vacío';
    }
    if($password === ''){
        $errores[] = 'La contraseña está vacía.';
    }

    if(empty($errores)){
        $consulta = $pdo->prepare('SELECT users_login.idUser, users_login.password, users_login.rol, users_data.nombre
        FROM users_login
        INNER JOIN users_data ON users_login.idUser = users_data.idUser
        WHERE users_login.usuario = ?');

        $consulta->execute([$usuario]);
        $fila = $consulta->fetch(); 

        if($fila && password_verify($password, $fila['password'])){
            session_regenerate_id(true);
            $_SESSION['idUser'] = $fila['idUser'];
            $_SESSION['nombre'] = $fila['nombre'];
            $_SESSION['rol'] = $fila['rol'];

            header('Location: ../../index.php?login=ok');
            exit;
        }else{
            $errores[] = 'Usuario o contraseña incorrectos.';
        }
    }
    
}

require $raiz . 'includes/cabecera.php';
?>
<main>

<section class="panel-fondo">
    <div class="panel panel-estrecho">
        <h1>Inicia sesión</h1>
        <p class="panel-intro">¿No tienes cuenta? <a href="registro.php">Regístrate aquí</a>.</p>
        <?php
        /* Mensaje de registro completado.
        Viene de registro.php: cuando el registro sale bien, hace
        header('Location: login.php?registro=ok'), y ese "registro=ok"
        de la URL llega aquí en $_GET.
        Solo se muestra si vale exactamente 'ok' (no pintamos lo que
        venga en la URL, porque cualquiera puede escribir ahí). */
        ?>
        <?php if(($_GET['registro'] ?? '') === 'ok'){ ?>
            <p class="aviso aviso-exito">¿Registro completado! Ya puedes iniciar sesión. </p>
        <?php } ?>
    
        <?php if(!empty($errores)){?>
            <ul class="aviso aviso-error">
                <?php foreach($errores as $error){ ?>
                <li><?= htmlspecialchars($error) ?></li>
                <?php } ?>
            </ul>
        <?php } ?>
    
        <form action="login.php" method="post" class="formulario"> 
            <div class="campo">
                <label for="usuario">Usuario </label>
                <input type="text" id="usuario" name="usuario" value="<?=htmlspecialchars($usuario ?? '') ?>">
            </div>
            <div class="campo">
                <label for="password">Contraseña </label>
                <input type="password" id="password" name="password">
            </div>
            <button type="submit" class="boton">Entrar</button>
        </form>

    </div>
</section>
</main>

<?php 
require $raiz . 'includes/pie.php';
?>