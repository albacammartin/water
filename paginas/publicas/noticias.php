<?php
$raiz = '../../';
$pagina = 'noticias';
require $raiz . 'includes/conexion.php';

// Guardo la consulta en una variable.
$sql = 'SELECT noticias.titulo, noticias.texto, noticias.imagen, noticias.fecha, users_data.nombre, users_data.apellidos
FROM noticias
INNER JOIN users_data
ON noticias.idUser = users_data.idUser
ORDER BY noticias.fecha DESC';

/* $pdo es la conexión a la BBDD que tengo en el archivo conexion. Significa: ($pdo->query($sql)) envíale esta pregunta a SQL y (fetchAll()) tráeme todas las filas de la respuesta. Lo guardamos todo en la variable $noticias. */
$noticias = $pdo->query($sql)->fetchAll();


require $raiz . 'includes/cabecera.php';
?>

<main>
    <section class="cabecera-pagina">
        <h1>Noticias</h1>
        <p>Lanzamientos, rutinas y todo lo que pasa en Wäter.</p> 
    </section>

    <section class="seccion">
        <?php if (empty($noticias)) { ?>
            <p class="vacio">Todavía no hay noticias publicadas.</p>
        <?php } ?>

        <?php /* Para cada ficha de la caja $noticias, llámala $noticia (en singular) */ ?>
        <div class="lista-noticias">
            <?php foreach ($noticias as $noticia) { ?>
                <article class="noticia">
                    <img src="<?= $raiz . htmlspecialchars($noticia['imagen']) ?>" alt="<?= htmlspecialchars($noticia['titulo']) ?>">

                    <div class="noticia-cuerpo">
                        <?php /* Fecha en formato español: strtotime pasa la fecha a formato PHP y date la escribe como d/m/Y */ ?>
                        <p class="noticia-meta">
                            <?= date('d/m/Y', strtotime($noticia['fecha'])) ?>
                            · Por <?= htmlspecialchars($noticia['nombre']) ?> <?= htmlspecialchars($noticia['apellidos']) ?>
                        </p>

                        <?php /* De esta ficha, dame el dato con la etiqueta titulo */ ?>
                        <h2><?= htmlspecialchars($noticia['titulo']) ?></h2>

                        <?php /* htmlspecialchars protege, nl2br respeta los párrafos. El título, el nombre y los apellidos solo llevan htmlspecialchars, porque no tienen saltos de línea */ ?>
                        <div class="noticia-texto"><?= nl2br(htmlspecialchars($noticia['texto'])) ?></div>
                    </div>
                </article>
            <?php } ?>
        </div>
    </section>
</main>

<?php require $raiz . 'includes/pie.php'; ?>