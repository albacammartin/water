<?php
$raiz= '../../';
$pagina= 'registro';
require $raiz . 'includes/conexion.php';
$errores= [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre= trim($_POST['nombre'] ?? ''); 
    /*dame lo que llegó en nombre, y si no llegó nada, usa un texto vacío. ''trim(...) → quita los espacios del principio y del final.*/
    $apellidos= trim($_POST['apellidos'] ?? '');
    $email= trim($_POST['email'] ?? '');
    $telefono= trim($_POST['telefono'] ?? '');
    $fechaNacimiento= trim($_POST['fechaNacimiento'] ?? '');
    $direccion= trim($_POST['direccion'] ?? '');
    $sexo= trim($_POST['sexo'] ?? '');
    $usuario= trim($_POST['usuario'] ?? '');
    $password= $_POST['password'] ?? '';

/* errores */
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
    if($usuario === ''){
        $errores[]='El usuario está vacío';
    }
    if($password === ''){
        $errores[]='La contraseña está vacía';
    }elseif (strlen($password) < 6) {
    $errores[] = 'La contraseña tiene menos de 6 caracteres';
    }

    $consulta = $pdo->prepare('SELECT idUser FROM users_data WHERE email = ?'); /* prepare → le mandas a MySQL la consulta con un hueco ? donde irá el dato. */
    $consulta->execute([$email]); /* execute([...]) → le mandas el dato aparte, para que lo meta en el hueco */

    if($consulta->fetch()){
        $errores[]='Este email ya existe.';
    }

    $consulta = $pdo->prepare('SELECT idLogin FROM users_login WHERE usuario = ?');
    $consulta->execute([$usuario]);

    if($consulta->fetch()){
        $errores[]='Este usuario ya existe.';
    }
/* Cuando el registro no de errores, guardaremos los datos en la tabla user y login de la bbdd */
    if(empty($errores)){
        $direccion = $direccion === '' ? null : $direccion;
        $sexo = $sexo === '' ? null : $sexo;
        $consulta = $pdo->prepare('INSERT INTO users_data (nombre, apellidos, email, telefono, fechaNacimiento, direccion, sexo) VALUES (?, ?, ?, ?, ?, ?, ?)');
        $consulta->execute([$nombre, $apellidos, $email, $telefono, $fechaNacimiento, $direccion, $sexo]);

        $idUser = $pdo->lastInsertId();
        $passwordCifrada = password_hash($password, PASSWORD_DEFAULT);

        $consulta = $pdo->prepare('INSERT INTO users_login (idUser, usuario, password, rol) VALUES (?,?,?,?)');
        $consulta->execute([$idUser, $usuario, $passwordCifrada, 'user']);

// Registro correcto: mandamos al login con ?registro=ok
// para que login.php enseñe el mensaje de éxito.
// exit para el script aquí (si no, seguiría pintando el formulario).
        header('Location: login.php?registro=ok');
        exit;
    }
}

require $raiz . 'includes/cabecera.php';
?>
<main>

    <section class="panel-fondo"> 
        <div class="panel panel-ancho">
            <h1>Crea tu cuenta</h1>
            <p class="panel-intro">¿Ya tienes cuenta? <a href="login.php">Inicia sesión aquí</a>.</p>

            <?php
            /* Lista de errores del formulario.
            !empty($errores) → solo se pinta si la lista NO está vacía
            (al entrar por primera vez no hay errores y no sale nada).
            foreach → recorre la lista y pinta cada error en un <li>.
            htmlspecialchars → limpia el texto antes de mostrarlo. */
            ?>
            <?php if(!empty($errores)){?>
                <ul class="aviso aviso-error">
                    <?php foreach($errores as $error) { ?>
                    <li><?= htmlspecialchars($error) ?></li>
                    <?php } ?>
                </ul>
            <?php } ?>
            

            <form action="registro.php" method="post" class="formulario">
                <fieldset>
                    <legend>Datos personales</legend>
                    <div class="fila-2">
                        <div class="campo">
                            <label for="nombre">Nombre *</label>
                            <input type="text" id="nombre" name="nombre" value="<?= htmlspecialchars($nombre ?? '') ?>">
                        </div>
                
                        <div class="campo">
                            <label for="apellidos">Apellidos *</label>
                            <input type="text" id="apellidos" name="apellidos" value="<?= htmlspecialchars($apellidos ?? '') ?>">
                        </div>
                    </div>

                    <div class="fila-2">
                        <div class="campo">
                            <label for="email">Correo electrónico *</label>
                            <input type="email" id="email" name="email" value="<?= htmlspecialchars($email ?? '') ?>">
                        </div>
                
                        <div class="campo">
                            <label for="telefono">Teléfono *</label>
                            <input type="tel" id="telefono" name="telefono" value="<?= htmlspecialchars($telefono ?? '') ?>">
                        </div>
                    </div>

                    <div class="fila-2">
                        <div class="campo">
                            <label for="fechaNacimiento">Fecha de nacimiento *</label>
                            <input type="date" id="fechaNacimiento" name="fechaNacimiento" value="<?= htmlspecialchars($fechaNacimiento ?? '') ?>">
                        </div>
                
                        <div class="campo">
                            <label for="sexo">Sexo</label>
                            <select id="sexo" name="sexo">
                                <option value="">Prefiero no decirlo</option>
                                <option value="MUJER" <?php if (($sexo ?? '') === 'MUJER') { echo 'selected'; } ?>>Mujer</option>
                                <option value="HOMBRE" <?php if (($sexo ?? '') === 'HOMBRE') { echo 'selected'; } ?>>Hombre</option>
                                <option value="OTRO" <?php if (($sexo ?? '') === 'OTRO') { echo 'selected'; } ?>>Otro</option>
                            </select>
                        </div>
                    </div>
            
                    <div class="campo">
                        <label for="direccion">Dirección</label>
                        <input type="text" id="direccion" name="direccion" value="<?= htmlspecialchars($direccion ?? '') ?>">
                    </div>
                </fieldset>

                <fieldset>
                    <legend>Datos de acceso</legend>
                    <div class="fila-2">
                        <div class="campo">
                            <label for="usuario">Usuario *</label>
                            <input type="text" id="usuario" name="usuario" value="<?= htmlspecialchars($usuario ?? '') ?>">
                        </div>
                
                        <div class="campo">
                            <label for="password">Contraseña *</label>
                            <input type="password" id="password" name="password">
                        </div>
                    </div>
                </fieldset>

                <p class="nota">Los campos con * son obligatorios</p>
                <button type="submit" class="boton">Registrarme</button>

            </form>
        </div>
    </section>
</main>

<?php 
require $raiz . 'includes/pie.php';
?>