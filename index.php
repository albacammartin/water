<?php 
$raiz= '';
$pagina= 'inicio';

require $raiz . 'includes/conexion.php';

// Las 3 noticias más recientes, para "Novedades"
$ultimas = $pdo->query('SELECT idNoticia, titulo, imagen, fecha FROM noticias ORDER BY fecha DESC LIMIT 3')->fetchAll();

// Productos destacados: son fijos (no están en la BD), así que los guardo en un array
$destacados = [
    ['nombre' => 'Aqua gel cream',   'medida' => '50ml',  'precio' => '22€', 'imagen' => 'aqua-gel-cream.jpg'],
    ['nombre' => 'Hydra mist',       'medida' => '100ml', 'precio' => '23€', 'imagen' => 'hydra-mist.jpg'],
    ['nombre' => 'Hydra serum',      'medida' => '30ml',  'precio' => '20€', 'imagen' => 'hydra-serum.jpg'],
    ['nombre' => 'Repair hair mask', 'medida' => '200ml', 'precio' => '27€', 'imagen' => 'repair-hair-mask.jpg'],
];

// El botón "Pide tu cita" lleva a un sitio distinto según quién navega
$rol = $_SESSION['rol'] ?? '';
if ($rol === 'user') {
    $enlaceCita = 'paginas/usuario/citaciones.php';
} elseif ($rol === 'admin') {
    $enlaceCita = 'paginas/admin/citas-administracion.php';
} else {
    $enlaceCita = 'paginas/publicas/registro.php';
}
require $raiz . 'includes/cabecera.php';

?>
    <main>
        <?php /* Mensaje de bienvenida: viene de login.php con ?login=ok */ ?>
        <?php if (($_GET['login'] ?? '') === 'ok') { ?>
            <p class="aviso aviso-exito aviso-portada">
                ¡Hola, <?= htmlspecialchars($_SESSION['nombre'] ?? '') ?>! Has iniciado sesión correctamente.
            </p>
        <?php } ?>

        <section class="hero">
            <div class="hero-texto">
                <h1>Hidratación ligera. <br> Piel naturalmente fresca</h1>
                <p>Descubre tu rutina de cuidado facial inspirada en el agua. Fórmulas ligeras con ingredientes de origen natural que hidratan y refrescan tu piel sin dejar sensación grasa ni pesada.</p>
                <a href="<?= $raiz . $enlaceCita ?>" class="boton"> Pide tu cita de diagnóstico </a>
            </div>
            <img src="<?= $raiz ?>img/general/hero-facial.jpg" alt="Sérum, bruma, crema y limpiador Wäter sobre una piedra con agua" class="hero-imagen">
        </section>

        <section class="seccion"> 
            <h2 class="titulo-seccion"> Nuestras líneas</h2>
            <div class="categorias"> 

                <article class="categoria">
                    <img src="<?= $raiz ?>img/general/cat-capilar.jpg" alt="Productos de la línea capilar Wäter">
                    <div class="categoria-texto"> 
                        <h3>Cuidado capilar </h3>
                        <p>Hidratación y movimiento para todo tipo de cabellos</p>
                    </div>
                </article>

                <article class="categoria">
                    <img src="<?= $raiz ?>img/general/cat-facial.jpg" alt="Productos de la línea facial Wäter">
                    <div class="categoria-texto">
                        <h3>Cuidado facial</h3>
                        <p>Limpia, trata, hidrata y refresca en cuatro pasos.</p>
                    </div>
                </article>

                <article class="categoria">
                    <img src="<?= $raiz ?>img/general/cat-maquillaje.jpg" alt="Base, máscara de pestañas y polvos Wäter">
                    <div class="categoria-texto">
                        <h3>Maquillaje</h3>
                        <p>Acabado natural que deja respirar la piel.</p>
                    </div>
                </article>
            </div>
        </section>

        <?php /* 3.BEST SELLER (Los productos del array) */ ?>
        <section class="seccion">
            <h2 class="titulo-seccion">Best seller</h2>
            <div class="productos">
                <?php foreach($destacados as $producto){ ?>
                    <article class="producto">
                        <img src="<?= $raiz ?>img/productos/<?= $producto['imagen'] ?>" alt="<?= htmlspecialchars($producto['nombre']) ?>">
                        <div class="producto-info">
                            <div class="producto-fila">
                                <h3><?= htmlspecialchars($producto['nombre']) ?></h3>
                                <span class="etiqueta">Top ventas</span>
                            </div>
                            <p class="producto-precio"><?= $producto['precio'] ?> <span><?= $producto['medida'] ?></span></p>
                        </div>
                    </article>
                <?php } ?>
            </div>
        </section>

<?php /* 4. NACIMOS DEL AGUA: foto a la izquierda, texto a la derecha */ ?>
    <section class="nosotros">
        <img src="<?= $raiz ?>img/general/nacimos-agua.jpg" alt="Productos Wäter sobre piedras blancas junto al agua">
        <div class="nosotros-texto">
            <h2>Nacimos del agua</h2>
            <p>WÄTER nació de una idea sencilla: cuidar la piel y el cabello no debería sentirse pesado. Inspirados por la pureza, la frescura y la capacidad renovadora del agua, creamos fórmulas ligeras que se integran en tu rutina como una segunda piel.</p>
            <p>Combinamos ingredientes de origen natural con activos cuidadosamente seleccionados. Sin excesos, sin sensaciones incómodas y sin pasos innecesarios.</p>
            <a href="<?= $raiz ?>paginas/publicas/noticias.php" class="boton">Lee nuestras novedades</a>
        </div>
    </section>

    <?php /* 5. NOVEDADES: las 3 últimas noticias de la BD */ ?>
    <?php if (!empty($ultimas)) { ?>
        <section class="seccion">
            <h2 class="titulo-seccion">Novedades</h2>
            <div class="novedades">
                <?php foreach ($ultimas as $noticia) { ?>
                    <a href="<?= $raiz ?>paginas/publicas/noticias.php" class="novedad">
                        <img src="<?= htmlspecialchars($raiz . $noticia['imagen']) ?>" alt="">
                        <p class="fecha"><?= date('d/m/Y', strtotime($noticia['fecha'])) ?></p>
                        <h3><?= htmlspecialchars($noticia['titulo']) ?></h3>
                    </a>
                <?php } ?>
            </div>
        </section>
    <?php } ?>

</main>

<?php require $raiz . 'includes/pie.php'; ?>