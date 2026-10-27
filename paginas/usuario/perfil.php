<?php 
    $raiz='../../';
    $pagina= 'perfil';
    require $raiz . 'includes/conexion.php';

    if(!isset($_SESSION['idUser'])){
        header('Location: ../publicas/login.php');
        exit;
    }

    $consulta = $pdo->prepare('SELECT users_data.*, users_login.usuario
    FROM users_data
    INNER JOIN users_login ON users_data.idUser = users_login.idUser
    WHERE users_data.idUser = ?');
    $consulta->execute([$_SESSION['idUser']]);
    $datos = $consulta->fetch();

    //Al entrar, el formulario muestra lo que hay en la BBDD
    $nombre = $datos['nombre'];
    $apellidos = $datos['apellidos'];
    $email = $datos['email'];
    $telefono = $datos['telefono'];
    $fechaNacimiento = $datos['fechaNacimiento'];
    $direccion = $datos['direccion'] ?? '';
    $sexo = $datos['sexo'] ?? '';

    $errores = [];
    $accion = ''; // NUEVO: para saber debajo de qué formulario enseñar los errores
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        // ¿qué formulario se ha enviado? (campo oculto "accion")
        $accion = $_POST['accion'] ?? '';

        // si es el formulario de datos personales...
        if ($accion === 'datos') {
            $nombre= trim($_POST['nombre'] ?? ''); 
            /*dame lo que llegó en nombre, y si no llegó nada, usa un texto vacío. ''trim(...) → quita los espacios del principio y del final.*/
            $apellidos= trim($_POST['apellidos'] ?? '');
            $email= trim($_POST['email'] ?? '');
            $telefono= trim($_POST['telefono'] ?? '');
            $fechaNacimiento= trim($_POST['fechaNacimiento'] ?? '');
            $direccion= trim($_POST['direccion'] ?? '');
            $sexo= trim($_POST['sexo'] ?? '');

            if($nombre === ''){
                $errores[]= 'El nombre está vacío.';
            }
            if($apellidos === ''){
                $errores[]= 'Los apellidos están vacíos';
            }
            if($email === ''){
                $errores[]='El email está vacío';
            }elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errores[] = 'El email no tiene un formato válido.';
            }
            if ($telefono === '') {
                $errores[] = 'El teléfono es obligatorio.';
            }
            if($fechaNacimiento === ''){
                $errores[]= 'La fecha de nacimiento está incompleta';
            }elseif ($fechaNacimiento >= date('Y-m-d')) {
                $errores[] = 'La fecha de nacimiento tiene que ser anterior a hoy.';
            }
            
            $consulta = $pdo->prepare('SELECT idUser FROM users_data WHERE email = ? AND idUser != ?');
            $consulta->execute([$email, $_SESSION['idUser']]);
            if ($consulta->fetch()) {
                $errores[] = 'Ese email ya lo usa otra persona.';
            }   
            
            if(empty($errores)){
                $direccion = $direccion === '' ? null : $direccion;
                $sexo = $sexo === '' ? null : $sexo;

                $consulta = $pdo->prepare('UPDATE users_data
                SET nombre = ?, apellidos = ?, email = ?, telefono = ?, fechaNacimiento = ?, direccion = ?, sexo = ?
                WHERE idUser = ?');
                $consulta->execute([$nombre,$apellidos,$email,$telefono,$fechaNacimiento,$direccion,$sexo,$_SESSION['idUser']]);

                header('Location: perfil.php?guardado=ok');
                exit;
            }

        // ...y si es el formulario de la contraseña
        } elseif ($accion === 'password') {
            $passwordNueva = $_POST['passwordNueva'] ?? '';
            $passwordRepetir = $_POST['passwordRepetir'] ?? '';

            if (strlen($passwordNueva) < 6) {
                $errores[] = 'La contraseña nueva debe tener al menos 6 caracteres.';
            } elseif ($passwordNueva !== $passwordRepetir) {
                $errores[] = 'Las contraseñas no coinciden.';
            }

            if (empty($errores)) {
                $consulta = $pdo->prepare('UPDATE users_login SET password = ? WHERE idUser = ?');
                $consulta->execute([password_hash($passwordNueva, PASSWORD_DEFAULT), $_SESSION['idUser']]);

                header('Location: perfil.php?password=ok');
                exit;
            }
        }
    }
    require $raiz . 'includes/cabecera.php';
?>

<main>
    <?php /* el mismo esquema que Mis citas, fondo mas dos paneles en columnas */ ?>
    <section class="panel-fondo">
        <div class="columnas-panel">

            <?php /* Columna izquierda, datos personales*/ ?>
            <div class="panel">
                <h1>Mi perfil</h1>

                <?php if (($_GET['guardado'] ?? '') === 'ok') { ?>
                    <p class="aviso aviso-exito">Cambios guardados correctamente.</p>
                <?php } ?>

                <?php /* solo los errores del formulario de datos */ ?>
                <?php if (!empty($errores) && $accion === 'datos') { ?>
                    <ul class="aviso aviso-error">
                        <?php foreach ($errores as $error) { ?>
                            <li><?= htmlspecialchars($error) ?></li>
                        <?php } ?>
                    </ul>
                <?php } ?>

                <form action="perfil.php" method="post" class="formulario">
                    <?php /* etiqueta oculta para saber que es este formulario */ ?>
                    <input type="hidden" name="accion" value="datos">

                    <fieldset>
                        <legend>Datos personales</legend>

                        <div class="fila-2">
                            <div class="campo">
                                <label for="nombre">Nombre *</label>
                                <input type="text" id="nombre" name="nombre" value="<?= htmlspecialchars($nombre) ?>">
                            </div>
                            <div class="campo">
                                <label for="apellidos">Apellidos *</label>
                                <input type="text" id="apellidos" name="apellidos" value="<?= htmlspecialchars($apellidos) ?>">
                            </div>
                        </div>

                        <div class="fila-2">
                            <div class="campo">
                                <label for="email">Correo electrónico *</label>
                                <input type="email" id="email" name="email" value="<?= htmlspecialchars($email) ?>">
                            </div>
                            <div class="campo">
                                <label for="telefono">Teléfono *</label>
                                <input type="tel" id="telefono" name="telefono" value="<?= htmlspecialchars($telefono) ?>">
                            </div>
                        </div>

                        <div class="fila-2">
                            <div class="campo">
                                <label for="fechaNacimiento">Fecha de nacimiento *</label>
                                <input type="date" id="fechaNacimiento" name="fechaNacimiento" value="<?= htmlspecialchars($fechaNacimiento) ?>">
                            </div>
                            <div class="campo">
                                <label for="sexo">Sexo</label>
                                <select id="sexo" name="sexo">
                                    <option value="">Prefiero no decirlo</option>
                                    <option value="MUJER" <?php if ($sexo === 'MUJER') { echo 'selected'; } ?>>Mujer</option>
                                    <option value="HOMBRE" <?php if ($sexo === 'HOMBRE') { echo 'selected'; } ?>>Hombre</option>
                                    <option value="OTRO" <?php if ($sexo === 'OTRO') { echo 'selected'; } ?>>Otro</option>
                                </select>
                            </div>
                        </div>

                        <div class="campo">
                            <label for="direccion">Dirección</label>
                            <input type="text" id="direccion" name="direccion" value="<?= htmlspecialchars($direccion) ?>">
                        </div>
                    </fieldset>

                    <p class="nota">Los campos con * son obligatorios.</p>
                    <button type="submit" class="boton">Guardar cambios</button>
                </form>
            </div>

            <?php /* Columna derecha, datos de acceso  */ ?>
            <div class="panel">
                <h2>Mi cuenta</h2>
                <p class="panel-intro">Usuario: <strong><?= htmlspecialchars($datos['usuario']) ?></strong></p>

                <?php /* mensaje de contraseña cambiada */ ?>
                <?php if (($_GET['password'] ?? '') === 'ok') { ?>
                    <p class="aviso aviso-exito">Contraseña cambiada correctamente.</p>
                <?php } ?>

                <?php /* solo los errores del formulario de contraseña */ ?>
                <?php if (!empty($errores) && $accion === 'password') { ?>
                    <ul class="aviso aviso-error">
                        <?php foreach ($errores as $error) { ?>
                            <li><?= htmlspecialchars($error) ?></li>
                        <?php } ?>
                    </ul>
                <?php } ?>

                <?php /* segundo formulario, solo para la contraseña */ ?>
                <form action="perfil.php" method="post" class="formulario">
                    <input type="hidden" name="accion" value="password">

                    <fieldset>
                        <legend>Cambiar contraseña</legend>
                        <div class="campo">
                            <label for="passwordNueva">Contraseña nueva</label>
                            <input type="password" id="passwordNueva" name="passwordNueva">
                        </div>
                        <div class="campo">
                            <label for="passwordRepetir">Repite la contraseña</label>
                            <input type="password" id="passwordRepetir" name="passwordRepetir">
                        </div>
                    </fieldset>

                    <button type="submit" class="boton">Cambiar contraseña</button>
                </form>
            </div>

        </div>
    </section>
</main>

<?php require $raiz . 'includes/pie.php'; ?>