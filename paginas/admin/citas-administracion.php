<?php 
$raiz = '../../';
$pagina = 'citas-admin';

require $raiz . 'includes/conexion.php';

/* Este bloque es de protección. Si no eres admin te manda a la página de inicio */
if(($_SESSION['rol'] ?? '') !== 'admin'){
    header('Location: ../../index.php');
    exit;
}

// Valores del formulario (vacíos al entrar)
$idUserCita = '';
$fechaCita = '';
$motivoCita = '';
$errores = [];
$editando = false; //¿estamos modificando una cita?

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $accion = $_POST['accion'] ?? '';

    if($accion === 'borrar'){
        $idCita = $_POST['idCita'] ?? '';

        // El admin borra cualquier cita, solo hace falta su idCita
        $consulta = $pdo->prepare('DELETE FROM citas WHERE idCita = ?');
        $consulta->execute([$idCita]);

        header('Location: citas-administracion.php?borrada=ok');
        exit;
    }

    // Crear o modificar: los dos recogen y comprueban lo mismo
    if($accion === 'crear' || $accion === 'modificar'){
        // Aquí el idUser SÍ viene del formulario: el admin elige para quién es la cita
        $idUserCita = $_POST['idUser'] ?? '';
        $fechaCita = trim($_POST['fechaCita'] ?? '');
        $motivoCita = trim($_POST['motivoCita'] ?? '');

        if($idUserCita === ''){
            $errores[] = 'Elige un usuario.';
        }
        if($fechaCita === ''){
            $errores[] = 'La fecha de la cita es obligatoria.';
        }elseif($fechaCita <= date('Y-m-d')){
            $errores[] = 'La cita tiene que ser a partir de mañana.';
        }

        if(empty($errores)){
            $motivoCita = $motivoCita === '' ? null : $motivoCita;

            if($accion === 'crear'){
                $consulta = $pdo->prepare('INSERT INTO citas (idUser, fechaCita, motivoCita) VALUES (?, ?, ?)');
                $consulta->execute([$idUserCita, $fechaCita, $motivoCita]);

                header('Location: citas-administracion.php?creada=ok');
                exit;
            } else {
                // El admin puede cambiar cualquier cita (solo WHERE idCita)
                $idCita = $_POST['idCita'] ?? '';
                $consulta = $pdo->prepare('UPDATE citas SET idUser = ?, fechaCita = ?, motivoCita = ? WHERE idCita = ?');
                $consulta->execute([$idUserCita, $fechaCita, $motivoCita, $idCita]);

                header('Location: citas-administracion.php?modificada=ok');
                exit;
            }
        }

        // si había errores al modificar, seguimos en modo "modificar"
        if($accion === 'modificar'){
            $editando = ['idCita' => $_POST['idCita'] ?? ''];
        }
    }
}

// ¿viene ?editar=X en la URL? Cargamos esa cita para rellenar el formulario
if(isset($_GET['editar'])){
    $consulta = $pdo->prepare('SELECT idCita, idUser, fechaCita, motivoCita FROM citas WHERE idCita = ?');
    $consulta->execute([$_GET['editar']]);
    $editando = $consulta->fetch();

    if($editando){
        $idUserCita = (string)$editando['idUser'];
        $fechaCita = $editando['fechaCita'];
        $motivoCita = $editando['motivoCita'] ?? '';
    }
}

// Todas las citas de todos los usuarios, con el nombre de cada uno
$sql = 'SELECT citas.idCita, citas.fechaCita, citas.motivoCita, users_data.nombre, users_data.apellidos
FROM citas
INNER JOIN users_data
ON citas.idUser = users_data.idUser
ORDER BY citas.fechaCita DESC';

$citas = $pdo->query($sql)->fetchAll();

// Todos los usuarios, para el desplegable
$usuarios = $pdo->query('SELECT idUser, nombre, apellidos FROM users_data ORDER BY nombre')->fetchAll();

require $raiz . 'includes/cabecera.php';
?>

<main>
    <?php /* la lista a la izquierda y el formulario a la derecha */ ?>
    <section class="panel-fondo">
        <div class="columnas-panel">

            <?php /* Columna izquierda, todas las citas */ ?>
            <div class="panel">
                <h1>Citas</h1>

                <?php /*  mensajes con el aviso verde */ ?>
                <?php if (($_GET['borrada'] ?? '') === 'ok') { ?>
                    <p class="aviso aviso-exito">Cita borrada correctamente.</p>
                <?php } ?>
                <?php if (($_GET['creada'] ?? '') === 'ok') { ?>
                    <p class="aviso aviso-exito">Cita creada correctamente.</p>
                <?php } ?>
                <?php if (($_GET['modificada'] ?? '') === 'ok') { ?>
                    <p class="aviso aviso-exito">Cita modificada correctamente.</p>
                <?php } ?>

                <?php if (empty($citas)) { ?>
                    <p class="vacio">No hay citas.</p>
                <?php } else { ?>
                    <div class="tabla-scroll">
                        <table class="tabla">
                            <tr>
                                <th>Fecha</th>
                                <th>Usuario</th>
                                <th>Motivo</th>
                                <th>Acciones</th>
                            </tr>
                            <?php foreach ($citas as $cita) { ?>
                                <tr>
                                    <td><?= date('d/m/Y', strtotime($cita['fechaCita'])) ?></td>
                                    <td><?= htmlspecialchars($cita['nombre']) ?> <?= htmlspecialchars($cita['apellidos']) ?></td>
                                    <td><?= htmlspecialchars($cita['motivoCita'] ?? '—') ?></td>
                                    <td>
                                        <?php /* los mismos botones pequeños de las otras páginas */ ?>
                                        <div class="acciones">
                                            <a href="citas-administracion.php?editar=<?= $cita['idCita'] ?>" class="boton-mini">Modificar</a>
                                            <form action="citas-administracion.php" method="post" class="form-borrar">
                                                <input type="hidden" name="idCita" value="<?= $cita['idCita'] ?>">
                                                <input type="hidden" name="accion" value="borrar">
                                                <button class="boton-mini boton-peligro">Borrar</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php } ?>
                        </table>
                    </div>
                <?php } ?>
            </div>

            <?php /*Columna derecha,crear o modificar */ ?>
            <div class="panel">
                <?php /* El mismo formulario sirve para crear y para modificar */ ?>
                <h2><?php if ($editando) { echo 'Modificar cita'; } else { echo 'Nueva cita'; } ?></h2>

                <?php if (!empty($errores)) { ?>
                    <ul class="aviso aviso-error">
                        <?php foreach ($errores as $error) { ?>
                            <li><?= htmlspecialchars($error) ?></li>
                        <?php } ?>
                    </ul>
                <?php } ?>

                <form action="citas-administracion.php" method="post" class="formulario">
                    <?php /* la acción cambia según si creamos o modificamos */ ?>
                    <?php if ($editando) { ?>
                        <input type="hidden" name="accion" value="modificar">
                        <input type="hidden" name="idCita" value="<?= $editando['idCita'] ?>">
                    <?php } else { ?>
                        <input type="hidden" name="accion" value="crear">
                    <?php } ?>

                    <div class="campo">
                        <label for="idUser">Usuario *</label>
                        <select id="idUser" name="idUser">
                            <option value="">Elige un usuario</option>
                            <?php foreach ($usuarios as $usuario) { ?>
                                <option value="<?= $usuario['idUser'] ?>" <?php if ((string)$usuario['idUser'] === $idUserCita) { echo 'selected'; } ?>>
                                    <?= htmlspecialchars($usuario['nombre']) ?> <?= htmlspecialchars($usuario['apellidos']) ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="campo">
                        <label for="fechaCita">Fecha de la cita *</label>
                        <input type="date" id="fechaCita" name="fechaCita" value="<?= htmlspecialchars($fechaCita) ?>">
                    </div>

                    <div class="campo">
                        <label for="motivoCita">Motivo de la cita</label>
                        <textarea id="motivoCita" name="motivoCita" rows="4"><?= htmlspecialchars($motivoCita) ?></textarea>
                    </div>

                    <div class="botones-form">
                        <button type="submit" class="boton"><?php if ($editando) { echo 'Guardar cambios'; } else { echo 'Crear cita'; } ?></button>
                        <?php if ($editando) { ?>
                            <a href="citas-administracion.php">Cancelar</a>
                        <?php } ?>
                    </div>
                </form>
            </div>

        </div>
    </section>
</main>

<?php require $raiz . 'includes/pie.php'; ?>