<?php
$raiz = '../../';
$pagina = 'usuarios-admin';
require $raiz . 'includes/conexion.php';

$errores = [];
$editando = false; // ¿estamos modificando un usuario?

/* Protección: solo entra el admin */
if(($_SESSION['rol'] ?? '') !== 'admin'){
    header('Location: ../../index.php');
    exit;
}

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $accion = $_POST['accion'] ?? '';

    // Recogemos los datos del formulario
    $nombre = trim($_POST['nombre'] ?? '');
    $apellidos = trim($_POST['apellidos'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $fechaNacimiento = trim($_POST['fechaNacimiento'] ?? '');
    $direccion = trim($_POST['direccion'] ?? '');
    $sexo = trim($_POST['sexo'] ?? '');
    $rol = trim($_POST['rol'] ?? '');
    $usuario = trim($_POST['usuario'] ?? '');
    $password = $_POST['password'] ?? '';

    // --- BORRAR -----
    if($accion === 'borrar'){
        $idUser = $_POST['idUser'] ?? '';

        // Contamos cuántas noticias ha escrito este usuario
        $consulta = $pdo->prepare('SELECT COUNT(*) FROM noticias WHERE idUser = ?');
        $consulta->execute([$idUser]);
        $numNoticias = $consulta->fetchColumn();

        if((string)$idUser === (string)$_SESSION['idUser']){
            // 1 El admin no puede borrarse a sí mismo
            $errores[] = 'No puedes borrar tu propia cuenta.';
        }elseif($numNoticias > 0){
            // 2 Si tiene noticias, la BD no deja borrarlo (RESTRICT)
            $errores[] = 'No se puede borrar este usuario porque tiene noticias publicadas.';
        }else{
            // 3 Todo bien, borramos. 
            $consulta = $pdo->prepare('DELETE FROM users_data WHERE idUser = ?');
            $consulta->execute([$idUser]);

            header('Location: usuarios-administracion.php?borrada=ok');
            exit;
        }
    }

    // --- CREAR ------
    if($accion === 'crear'){
        if($nombre === ''){
            $errores[] = 'El nombre está vacío.';
        }
        if($apellidos === ''){
            $errores[] = 'Los apellidos están vacíos.';
        }
        if($email === ''){
            $errores[] = 'El email está vacío.';
        }elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)){
            $errores[] = 'El email no tiene un formato válido.';
        }
        if($telefono === ''){
            $errores[] = 'El teléfono es obligatorio.';
        }
        if($fechaNacimiento === ''){
            $errores[] = 'La fecha de nacimiento está incompleta.';
        }elseif($fechaNacimiento >= date('Y-m-d')){
            $errores[] = 'La fecha de nacimiento tiene que ser anterior a hoy.';
        }
        if($rol !== 'user' && $rol !== 'admin'){
            $errores[] = 'Elige un rol válido.';
        }
        if($usuario === ''){
            $errores[] = 'El usuario está vacío.';
        }
        if($password === ''){
            $errores[] = 'La contraseña está vacía.';
        }elseif(strlen($password) < 6){
            $errores[] = 'La contraseña tiene menos de 6 caracteres.';
        }

        // ¿El email ya existe?
        $consulta = $pdo->prepare('SELECT idUser FROM users_data WHERE email = ?');
        $consulta->execute([$email]);
        if($consulta->fetch()){
            $errores[] = 'Este email ya existe.';
        }

        // ¿El usuario ya existe?
        $consulta = $pdo->prepare('SELECT idLogin FROM users_login WHERE usuario = ?');
        $consulta->execute([$usuario]);
        if($consulta->fetch()){
            $errores[] = 'Este usuario ya existe.';
        }

        if(empty($errores)){
            // Campos opcionales: si están vacíos, guardamos null
            $direccion = $direccion === '' ? null : $direccion;
            $sexo = $sexo === '' ? null : $sexo;

            // 1 Datos personales
            $consulta = $pdo->prepare('INSERT INTO users_data (nombre, apellidos, email, telefono, fechaNacimiento, direccion, sexo) VALUES (?, ?, ?, ?, ?, ?, ?)');
            $consulta->execute([$nombre, $apellidos, $email, $telefono, $fechaNacimiento, $direccion, $sexo]);

            // 2 El id que acaba de crear MySQL
            $idNuevo = $pdo->lastInsertId();

            // 3 Login con la contraseña cifrada y el rol que eligió el admin
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $consulta = $pdo->prepare('INSERT INTO users_login (idUser, usuario, password, rol) VALUES (?, ?, ?, ?)');
            $consulta->execute([$idNuevo, $usuario, $hash, $rol]);

            header('Location: usuarios-administracion.php?creado=ok');
            exit;
        }
    }

    // ------ MODIFICAR -----
    if($accion === 'modificar'){
        $idUser = $_POST['idUser'] ?? '';

        if($nombre === ''){
            $errores[] = 'El nombre está vacío.';
        }
        if($apellidos === ''){
            $errores[] = 'Los apellidos están vacíos.';
        }
        if($email === ''){
            $errores[] = 'El email está vacío.';
        }elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)){
            $errores[] = 'El email no tiene un formato válido.';
        }
        if($telefono === ''){
            $errores[] = 'El teléfono es obligatorio.';
        }
        if($fechaNacimiento === ''){
            $errores[] = 'La fecha de nacimiento está incompleta.';
        }elseif($fechaNacimiento >= date('Y-m-d')){
            $errores[] = 'La fecha de nacimiento tiene que ser anterior a hoy.';
        }
        if($rol !== 'user' && $rol !== 'admin'){
            $errores[] = 'Elige un rol válido.';
        }
        if($usuario === ''){
            $errores[] = 'El usuario está vacío.';
        }
        // La contraseña aquí NO es obligatoria: vacía = no se cambia.
        // Pero si escriben una, tiene que tener 6 caracteres o más
        if($password !== '' && strlen($password) < 6){
            $errores[] = 'La contraseña tiene menos de 6 caracteres.';
        }

        // El admin no puede quitarse a sí mismo el rol de admin
        if((string)$idUser === (string)$_SESSION['idUser'] && $rol !== 'admin'){
            $errores[] = 'No puedes quitarte a ti misma el rol de admin.';
        }

        // ¿El email lo tiene OTRO usuario? (él mismo sí puede tenerlo)
        $consulta = $pdo->prepare('SELECT idUser FROM users_data WHERE email = ? AND idUser != ?');
        $consulta->execute([$email, $idUser]);
        if($consulta->fetch()){
            $errores[] = 'Este email ya existe.';
        }

        // ¿El usuario lo tiene OTRO?
        $consulta = $pdo->prepare('SELECT idLogin FROM users_login WHERE usuario = ? AND idUser != ?');
        $consulta->execute([$usuario, $idUser]);
        if($consulta->fetch()){
            $errores[] = 'Este usuario ya existe.';
        }

        if(empty($errores)){
            $direccion = $direccion === '' ? null : $direccion;
            $sexo = $sexo === '' ? null : $sexo;

            // 1 Datos personales
            $consulta = $pdo->prepare('UPDATE users_data SET nombre = ?, apellidos = ?, email = ?, telefono = ?, fechaNacimiento = ?, direccion = ?, sexo = ? WHERE idUser = ?');
            $consulta->execute([$nombre, $apellidos, $email, $telefono, $fechaNacimiento, $direccion, $sexo, $idUser]);

            // 2 Usuario y rol
            $consulta = $pdo->prepare('UPDATE users_login SET usuario = ?, rol = ? WHERE idUser = ?');
            $consulta->execute([$usuario, $rol, $idUser]);

            // 3 La contraseña SOLO si han escrito una nueva
            if($password !== ''){
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $consulta = $pdo->prepare('UPDATE users_login SET password = ? WHERE idUser = ?');
                $consulta->execute([$hash, $idUser]);
            }

            header('Location: usuarios-administracion.php?modificado=ok');
            exit;
        }

        // Si hay errores, seguimos en modo modificar
        $editando = ['idUser' => $idUser];
    }
}

// ¿Viene ?editar=X en la URL? Cargamos ese usuario para rellenar el formulario
if(isset($_GET['editar'])){
    $consulta = $pdo->prepare('SELECT users_data.*, users_login.usuario, users_login.rol
    FROM users_data
    LEFT JOIN users_login
    ON users_data.idUser = users_login.idUser
    WHERE users_data.idUser = ?');
    $consulta->execute([$_GET['editar']]);
    $editando = $consulta->fetch();

    if($editando){
        $nombre = $editando['nombre'];
        $apellidos = $editando['apellidos'];
        $email = $editando['email'];
        $telefono = $editando['telefono'];
        $fechaNacimiento = $editando['fechaNacimiento'];
        $direccion = $editando['direccion'] ?? '';
        $sexo = $editando['sexo'] ?? '';
        $usuario = $editando['usuario'] ?? '';
        $rol = $editando['rol'] ?? 'user';
    }
}

// Tabla con todos los usuarios
$sql = 'SELECT users_data.idUser, users_data.nombre, users_data.apellidos, users_data.email, users_login.usuario, users_login.rol
FROM users_data
LEFT JOIN users_login
ON users_data.idUser = users_login.idUser
ORDER BY users_data.apellidos';
$usuarios = $pdo->query($sql)->fetchAll();

require $raiz . 'includes/cabecera.php';
?>

<main>
    <section class="panel-fondo">

        <?php /* Panel 1. la lista de usuarios */ ?>
        <div class="panel panel-completo">
            <h1>Usuarios</h1>

            <?php /* mensajes con el aviso verde */ ?>
            <?php if (($_GET['borrada'] ?? '') === 'ok') { ?>
                <p class="aviso aviso-exito">Usuario borrado correctamente.</p>
            <?php } ?>
            <?php if (($_GET['creado'] ?? '') === 'ok') { ?>
                <p class="aviso aviso-exito">Usuario creado correctamente.</p>
            <?php } ?>
            <?php if (($_GET['modificado'] ?? '') === 'ok') { ?>
                <p class="aviso aviso-exito">Usuario modificado correctamente.</p>
            <?php } ?>

            <?php /* los errores de BORRAR salen aquí, junto a la tabla */ ?>
            <?php if (!empty($errores) && $accion === 'borrar') { ?>
                <ul class="aviso aviso-error">
                    <?php foreach ($errores as $error) { ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php } ?>
                </ul>
            <?php } ?>

            <div class="tabla-scroll">
                <table class="tabla">
                    <tr>
                        <th>Usuario</th>
                        <th>Nombre</th>
                        <th>Correo electrónico</th>
                        <th>Rol</th>
                        <th>Acciones</th>
                    </tr>
                    <?php foreach ($usuarios as $fila) { ?>
                        <tr>
                            <td><?= htmlspecialchars($fila['usuario'] ?? '—') ?></td>
                            <td><?= htmlspecialchars($fila['nombre']) ?> <?= htmlspecialchars($fila['apellidos']) ?></td>
                            <td><?= htmlspecialchars($fila['email']) ?></td>
                            <td><span class="rol <?php if (($fila['rol'] ?? '') === 'admin') { echo 'rol-admin'; } ?>"><?= htmlspecialchars($fila['rol'] ?? '—') ?></span></td>
                            <td>
                                <div class="acciones">
                                    <a href="usuarios-administracion.php?editar=<?= $fila['idUser'] ?>" class="boton-mini">Modificar</a>
                                    <form action="usuarios-administracion.php" method="post" class="form-borrar">
                                        <input type="hidden" name="idUser" value="<?= $fila['idUser'] ?>">
                                        <input type="hidden" name="accion" value="borrar">
                                        <button class="boton-mini boton-peligro">Borrar</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php } ?>
                </table>
            </div>
        </div>

        <?php /* Panel 2. el formulario  */ ?>
        <div class="panel panel-completo">
            <?php /* el mismo formulario sirve para crear y para modificar */ ?>
            <h2><?php if ($editando) { echo 'Modificar usuario'; } else { echo 'Nuevo usuario'; } ?></h2>

            <?php /* los errores de CREAR o MODIFICAR salen aquí, junto al formulario */ ?>
            <?php if (!empty($errores) && $accion !== 'borrar') { ?>
                <ul class="aviso aviso-error">
                    <?php foreach ($errores as $error) { ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php } ?>
                </ul>
            <?php } ?>

            <form action="usuarios-administracion.php" method="post" class="formulario">
                <?php /* la acción cambia según si creamos o modificamos */ ?>
                <?php if ($editando) { ?>
                    <input type="hidden" name="accion" value="modificar">
                    <input type="hidden" name="idUser" value="<?= $editando['idUser'] ?>">
                <?php } else { ?>
                    <input type="hidden" name="accion" value="crear">
                <?php } ?>

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
                            <label for="rol">Rol *</label>
                            <select id="rol" name="rol">
                                <option value="user" <?php if (($rol ?? '') === 'user') { echo 'selected'; } ?>>User</option>
                                <option value="admin" <?php if (($rol ?? '') === 'admin') { echo 'selected'; } ?>>Admin</option>
                            </select>
                        </div>
                    </div>
                    <div class="campo">
                        <label for="password">
                            <?php if ($editando) { echo 'Nueva contraseña (déjala vacía para no cambiarla)'; } else { echo 'Contraseña *'; } ?>
                        </label>
                        <input type="password" id="password" name="password">
                    </div>
                </fieldset>

                <p class="nota">Los campos con * son obligatorios.</p>
                <div class="botones-form">
                    <button type="submit" class="boton"><?php if ($editando) { echo 'Guardar cambios'; } else { echo 'Crear usuario'; } ?></button>
                    <?php if ($editando) { ?>
                        <a href="usuarios-administracion.php">Cancelar</a>
                    <?php } ?>
                </div>
            </form>
        </div>

    </section>
</main>

<?php require $raiz . 'includes/pie.php'; ?>