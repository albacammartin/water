<?php
$raiz = '../../';
$pagina = 'noticias-admin';
require $raiz . 'includes/conexion.php';

$errores = [];
$editando = false;
$titulo = '';
$texto = '';

// Protección: solo entra el admin
if(($_SESSION['rol'] ?? '') !== 'admin'){
    header('Location: ../../index.php');
    exit;
}

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $accion = $_POST['accion'] ?? '';

    // ---- BORRAR ----
    if($accion === 'borrar'){
        $idNoticia = $_POST['idNoticia'] ?? '';

        $consulta = $pdo->prepare('DELETE FROM noticias WHERE idNoticia = ?');
        $consulta->execute([$idNoticia]);

        header('Location: noticias-administracion.php?borrada=ok');
        exit;
    }

    // --- CREAR ---
    if($accion === 'crear'){
        $titulo = trim($_POST['titulo'] ?? '');
        $texto = trim($_POST['texto'] ?? '');
        // El nombre del archivo que han elegido (si no eligen nada, llega vacío)
        $nombreImagen = $_FILES['imagen']['name'] ?? '';

        if($titulo === ''){
            $errores[] = 'El título es obligatorio.';
        }
        if($texto === ''){
            $errores[] = 'El texto es obligatorio.';
        }
        if($nombreImagen === ''){
            $errores[] = 'La imagen es obligatoria.';
        }

        // El título es UNIQUE: comprobamos que no exista
        $consulta = $pdo->prepare('SELECT idNoticia FROM noticias WHERE titulo = ?');
        $consulta->execute([$titulo]);
        if($consulta->fetch()){
            $errores[] = 'Ya existe una noticia con ese título.';
        }

        if(empty($errores)){
            // Movemos la imagen de la carpeta temporal a img/noticias/
            move_uploaded_file($_FILES['imagen']['tmp_name'], $raiz . 'img/noticias/' . $nombreImagen);

            // En la BD guardamos la ruta desde la raíz (sin ../../)
            $consulta = $pdo->prepare('INSERT INTO noticias (idUser, titulo, imagen, texto, fecha) VALUES (?, ?, ?, ?, ?)');
            $consulta->execute([$_SESSION['idUser'], $titulo, 'img/noticias/' . $nombreImagen, $texto, date('Y-m-d')]);

            header('Location: noticias-administracion.php?creada=ok');
            exit;
        }
    }

    // MODIFICAR 
    if($accion === 'modificar'){
        $idNoticia = $_POST['idNoticia'] ?? '';
        $titulo = trim($_POST['titulo'] ?? '');
        $texto = trim($_POST['texto'] ?? '');
        $nombreImagen = $_FILES['imagen']['name'] ?? '';

        if($titulo === ''){
            $errores[] = 'El título es obligatorio.';
        }
        if($texto === ''){
            $errores[] = 'El texto es obligatorio.';
        }
        // Aquí la imagen NO es obligatoria: si no eligen, se queda la que tenía

        // Título repetido, pero sin contarse a sí misma
        $consulta = $pdo->prepare('SELECT idNoticia FROM noticias WHERE titulo = ? AND idNoticia != ?');
        $consulta->execute([$titulo, $idNoticia]);
        if($consulta->fetch()){
            $errores[] = 'Ya existe una noticia con ese título.';
        }

        if(empty($errores)){
            if($nombreImagen === ''){
                // No han elegido imagen, solo cambiamos título y texto
                $consulta = $pdo->prepare('UPDATE noticias SET titulo = ?, texto = ? WHERE idNoticia = ?');
                $consulta->execute([$titulo, $texto, $idNoticia]);
            }else{
                // Han elegido imagen, la movemos y cambiamos también la imagen
                move_uploaded_file($_FILES['imagen']['tmp_name'], $raiz . 'img/noticias/' . $nombreImagen);

                $consulta = $pdo->prepare('UPDATE noticias SET titulo = ?, texto = ?, imagen = ? WHERE idNoticia = ?');
                $consulta->execute([$titulo, $texto, 'img/noticias/' . $nombreImagen, $idNoticia]);
            }

            header('Location: noticias-administracion.php?modificada=ok');
            exit;
        }

        // Si hay errores, seguimos en modo modificar
        $editando = ['idNoticia' => $idNoticia];
    }
}

// ¿Viene ?editar=X en la URL? Cargamos esa noticia para rellenar el formulario
if(isset($_GET['editar'])){
    $consulta = $pdo->prepare('SELECT idNoticia, titulo, texto, imagen FROM noticias WHERE idNoticia = ?');
    $consulta->execute([$_GET['editar']]);
    $editando = $consulta->fetch();

    if($editando){
        $titulo = $editando['titulo'];
        $texto = $editando['texto'];
    }
}

// Ver las noticias. Obtener tabla de SQL
$sql = 'SELECT noticias.idNoticia, noticias.titulo, noticias.fecha, users_data.nombre, users_data.apellidos
FROM noticias
INNER JOIN users_data
ON noticias.idUser = users_data.idUser
ORDER BY noticias.fecha DESC';
// Recojo la tabla en la variable $noticias
$noticias = $pdo->query($sql)->fetchAll();

require $raiz . 'includes/cabecera.php';
?>

<main>
    <section class="panel-fondo">

        <?php /* Panel 1, la lista de noticias */ ?>
        <div class="panel panel-completo">
            <h1>Gestión de noticias</h1>

            <?php /* mensajes con el aviso verde */ ?>
            <?php if (($_GET['borrada'] ?? '') === 'ok') { ?>
                <p class="aviso aviso-exito">Noticia borrada correctamente.</p>
            <?php } ?>
            <?php if (($_GET['creada'] ?? '') === 'ok') { ?>
                <p class="aviso aviso-exito">Noticia creada correctamente.</p>
            <?php } ?>
            <?php if (($_GET['modificada'] ?? '') === 'ok') { ?>
                <p class="aviso aviso-exito">Noticia modificada correctamente.</p>
            <?php } ?>

            <?php if (empty($noticias)) { ?>
                <p class="vacio">Todavía no hay noticias.</p>
            <?php } else { ?>
                <div class="tabla-scroll">
                    <table class="tabla">
                        <tr>
                            <th>Título</th>
                            <th>Fecha</th>
                            <th>Autor</th>
                            <th>Acciones</th>
                        </tr>
                        <?php foreach ($noticias as $noticia) { ?>
                            <tr>
                                <td><?= htmlspecialchars($noticia['titulo']) ?></td>
                                <td><?= date('d/m/Y', strtotime($noticia['fecha'])) ?></td>
                                <td><?= htmlspecialchars($noticia['nombre']) ?> <?= htmlspecialchars($noticia['apellidos']) ?></td>
                                <td>
                                    <?php /* los mismos botones pequeños de usuarios y citas */ ?>
                                    <div class="acciones">
                                        <a href="noticias-administracion.php?editar=<?= $noticia['idNoticia'] ?>" class="boton-mini">Modificar</a>
                                        <form action="noticias-administracion.php" method="post" class="form-borrar">
                                            <input type="hidden" name="idNoticia" value="<?= $noticia['idNoticia'] ?>">
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

        <?php /*  Panel 2, el formulario */ ?>
        <div class="panel panel-completo">
            <?php /* el mismo formulario sirve para crear y para modificar */ ?>
            <h2><?php if ($editando) { echo 'Modificar noticia'; } else { echo 'Nueva noticia'; } ?></h2>

            <?php /* Aquí todos los errores son de crear o modificar (borrar no tiene) */ ?>
            <?php if (!empty($errores)) { ?>
                <ul class="aviso aviso-error">
                    <?php foreach ($errores as $error) { ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php } ?>
                </ul>
            <?php } ?>

            <?php /* enctype="multipart/form-data" → sin esto la imagen NO llega */ ?>
            <form action="noticias-administracion.php" method="post" enctype="multipart/form-data" class="formulario">
                <?php /* la acción cambia según si creamos o modificamos */ ?>
                <?php if ($editando) { ?>
                    <input type="hidden" name="accion" value="modificar">
                    <input type="hidden" name="idNoticia" value="<?= $editando['idNoticia'] ?>">
                <?php } else { ?>
                    <input type="hidden" name="accion" value="crear">
                <?php } ?>

                <div class="campo">
                    <label for="titulo">Título *</label>
                    <input type="text" id="titulo" name="titulo" value="<?= htmlspecialchars($titulo) ?>">
                </div>

                <?php /* .fila-imagen pone la imagen actual y el campo de subir uno al lado del otro */ ?>
                <div class="fila-imagen">
                    <?php /* al modificar enseñamos la imagen actual */ ?>
                    <?php if ($editando && !empty($editando['imagen'])) { ?>
                        <img src="<?= htmlspecialchars($raiz . $editando['imagen']) ?>" alt="Imagen actual" class="vista-previa">
                    <?php } ?>

                    <div class="campo">
                        <label for="imagen">
                            <?php if ($editando) { echo 'Cambiar imagen (déjalo vacío para mantener la actual)'; } else { echo 'Imagen *'; } ?>
                        </label>
                        <?php /* el input file NO lleva value */ ?>
                        <input type="file" id="imagen" name="imagen" accept="image/*">
                    </div>
                </div>

                <div class="campo">
                    <label for="texto">Texto *</label>
                    <textarea id="texto" name="texto" rows="8"><?= htmlspecialchars($texto) ?></textarea>
                </div>

                <p class="nota">Los campos con * son obligatorios.</p>
                <div class="botones-form">
                    <button type="submit" class="boton"><?php if ($editando) { echo 'Guardar cambios'; } else { echo 'Crear noticia'; } ?></button>

                    <?php /* salir del modo modificar */ ?>
                    <?php if ($editando) { ?>
                        <a href="noticias-administracion.php">Cancelar</a>
                    <?php } ?>
                </div>
            </form>
        </div>

    </section>
</main>

<?php require $raiz . 'includes/pie.php'; ?>