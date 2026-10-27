<?php
$raiz = '../../';
$pagina = 'citaciones';
require $raiz . 'includes/conexion.php';

/* Este es el bloque de protección. Protege de quién no ha hecho login */
if (!isset($_SESSION['idUser'])) {
    header('Location: ../publicas/login.php');
    exit;
}

$fechaCita = '';
$motivoCita = '';
$errores = [];
$editando = false; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $accion = $_POST['accion'] ?? '';

    if ($accion === 'pedir') {
        /* Recoger fechaCita y motivoCita */
        $fechaCita = trim($_POST['fechaCita'] ?? '');
        $motivoCita = trim($_POST['motivoCita'] ?? '');

        /* Comprobar que la fecha no esté vacía y sea futura */
        if ($fechaCita === '') {
            $errores[] = 'La fecha de la cita es obligatoria.';
        } elseif ($fechaCita <= date('Y-m-d')) {
            $errores[] = 'La cita tiene que ser a partir de mañana.';
        }
        if (empty($errores)) {
            $motivoCita = $motivoCita === '' ? null : $motivoCita;
            $consulta = $pdo->prepare('INSERT INTO citas (idUser, fechaCita, motivoCita) VALUES (?, ?, ?)');
            $consulta->execute([$_SESSION['idUser'], $fechaCita, $motivoCita]);

            header('Location: citaciones.php?creada=ok');
            exit;
        }
    }

    if ($accion === 'borrar') {
        $idCita = $_POST['idCita'] ?? '';

        $consulta = $pdo->prepare('DELETE FROM citas WHERE idCita = ? AND idUser = ? AND fechaCita > ?');
        $consulta->execute([$idCita, $_SESSION['idUser'], date('Y-m-d')]);

        header('Location: citaciones.php?borrada=ok');
        exit;
    }

    if ($accion === 'modificar') {
        $idCita = $_POST['idCita'] ?? '';
        $fechaCita = trim($_POST['fechaCita'] ?? '');
        $motivoCita = trim($_POST['motivoCita'] ?? '');

        if ($fechaCita === '') {
            $errores[] = 'La fecha de la cita es obligatoria.';
        } elseif ($fechaCita <= date('Y-m-d')) {
            $errores[] = 'La cita tiene que ser a partir de mañana.';
        }

        if (empty($errores)) {
            $motivoCita = $motivoCita === '' ? null : $motivoCita;
            $consulta = $pdo->prepare('UPDATE citas SET fechaCita = ?, motivoCita = ? WHERE idCita = ? AND idUser = ? AND fechaCita > ?');
            $consulta->execute([$fechaCita, $motivoCita, $idCita, $_SESSION['idUser'], date('Y-m-d')]);

            header('Location: citaciones.php?modificada=ok');
            exit;
        }

        // Si hay errores, seguimos en modo "modificar" para no perder la cita
        $editando = ['idCita' => $idCita];
    }
}

// ¿Viene ?editar=X en la URL? Cargamos esa cita para rellenar el formulario.
// Si no viene, no se hace nada
if (isset($_GET['editar'])) {
    $consulta = $pdo->prepare('SELECT idCita, fechaCita, motivoCita
    FROM citas
    WHERE idCita = ?
    AND idUser = ?
    AND fechaCita > ?');

    $consulta->execute([$_GET['editar'], $_SESSION['idUser'], date('Y-m-d')]);
    $editando = $consulta->fetch();

    if ($editando) {
        $fechaCita = $editando['fechaCita'];
        $motivoCita = $editando['motivoCita'] ?? '';
    }
}

// Mis citas, de la más próxima a la más lejana
$consulta = $pdo->prepare('SELECT idCita, fechaCita, motivoCita
FROM citas
WHERE idUser = ?
ORDER BY fechaCita');
$consulta->execute([$_SESSION['idUser']]);
$citas = $consulta->fetchAll();

require $raiz . 'includes/cabecera.php';
?>

<main>

    <section class="panel-fondo">
        <div class="columnas-panel">

            <?php /* Columna izquierda, mis citas */ ?>
            <div class="panel">
                <h1>Mis citas</h1>

                <?php if (($_GET['creada'] ?? '') === 'ok') { ?>
                    <p class="aviso aviso-exito">Cita solicitada correctamente.</p>
                <?php } ?>
                <?php if (($_GET['borrada'] ?? '') === 'ok') { ?>
                    <p class="aviso aviso-exito">Cita borrada correctamente.</p>
                <?php } ?>
                <?php if (($_GET['modificada'] ?? '') === 'ok') { ?>
                    <p class="aviso aviso-exito">Cita modificada correctamente.</p>
                <?php } ?>

                <?php if (empty($citas)) { ?>
                    <p class="vacio">Todavía no tienes citas.</p>
                <?php } else { ?>
                    <table class="tabla"> 
                        <tr>
                            <th>Fecha</th>
                            <th>Motivo</th>
                            <th>Acciones</th>
                        </tr>
                        <?php foreach ($citas as $cita) { ?>
                            <tr>
                                <td><?= date('d/m/Y', strtotime($cita['fechaCita'])) ?></td>
                                <td><?= htmlspecialchars($cita['motivoCita'] ?? '—') ?></td>
                                <td>
                                    <?php if ($cita['fechaCita'] > date('Y-m-d')) { ?>
                                        
                                        <div class="acciones">
                                            <a href="citaciones.php?editar=<?= $cita['idCita'] ?>" class="boton-mini">Modificar</a>
                                            <form action="citaciones.php" method="post" class="form-borrar">
                                                <input type="hidden" name="idCita" value="<?= $cita['idCita'] ?>">
                                                <input type="hidden" name="accion" value="borrar">
                                                <button class="boton-mini boton-peligro">Borrar</button>
                                            </form>
                                        </div>
                                    <?php } else { ?>
                                        <span class="nota">Pasada</span> 
                                    <?php } ?>
                                </td>
                            </tr>
                        <?php } ?>
                    </table>
                <?php } ?>
            </div>

            <?php /* Columna derecha, pedir o modificar */ ?>
            <div class="panel">
                <h2><?php if ($editando) { echo 'Modificar cita'; } else { echo 'Pedir cita'; } ?></h2>

                <?php if (!empty($errores)) { ?>
                    <ul class="aviso aviso-error">
                        <?php foreach ($errores as $error) { ?>
                            <li><?= htmlspecialchars($error) ?></li>
                        <?php } ?>
                    </ul>
                <?php } ?>

                <form action="citaciones.php" method="post" class="formulario">
                    <?php /* si editamos, la acción es "modificar" y mandamos qué cita es */ ?>
                    <?php if ($editando) { ?>
                        <input type="hidden" name="accion" value="modificar">
                        <input type="hidden" name="idCita" value="<?= $editando['idCita'] ?>">
                    <?php } else { ?>
                        <input type="hidden" name="accion" value="pedir">
                    <?php } ?>

                    <div class="campo">
                        <label for="fechaCita">Fecha de la cita *</label>
                        <input type="date" id="fechaCita" name="fechaCita" value="<?= htmlspecialchars($fechaCita) ?>">
                    </div>
                    <div class="campo">
                        <label for="motivoCita">Motivo de la cita</label>
                        <textarea id="motivoCita" name="motivoCita" rows="4"><?= htmlspecialchars($motivoCita) ?></textarea>
                    </div>

                    <div class="botones-form">
                        <?php /* el texto del botón cambia según lo que hacemos */ ?>
                        <button type="submit" class="boton"><?php if ($editando) { echo 'Guardar cambios'; } else { echo 'Pedir cita'; } ?></button>

                        <?php /* enlace para salir del modo edición */ ?>
                        <?php if ($editando) { ?>
                            <a href="citaciones.php">Cancelar</a>
                        <?php } ?>
                    </div>
                </form>
            </div>

        </div>
    </section>
</main>

<?php require $raiz . 'includes/pie.php'; ?>