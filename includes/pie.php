<footer class="pie">
    <div class="pie-contenido">

        <?php /* dos columnas de enlaces */ ?>
        <div class="pie-enlaces">
            <div>
                <h2>Enlaces</h2>
                <ul>
                    <li><a href="<?= $raiz ?>index.php">Inicio</a></li>
                    <li><a href="<?= $raiz ?>paginas/publicas/noticias.php">Noticias</a></li>
                    <li><a href="<?= $raiz ?>index.php">Sobre nosotros</a></li>
                    <li><a href="mailto:hola@water.es">Contacto</a></li>
                </ul>
            </div>
            <div>
                <h2>Ayuda</h2>
                <ul>
                    <li><a href="<?= $raiz ?>index.php">Opciones de compra</a></li>
                    <li><a href="mailto:devoluciones@water.es">Devoluciones</a></li>
                    <li><a href="#">Política de privacidad</a></li>
                </ul>
            </div>
        </div>

        <?php /* Derecha: logo, redes y newsletter */ ?>
        <div class="pie-marca">
            <a href="<?= $raiz ?>index.php" class="logo logo-pie">WÄTER</a>

            <?php /* Los iconos son SVG(los he pedido en una ia) */ ?>
            <ul class="redes">
                <li><a href="#" aria-label="Facebook"><svg viewBox="0 0 24 24"><path d="M13.5 21v-7.5h2.6l.4-3h-3V8.6c0-.9.3-1.5 1.5-1.5h1.6V4.4c-.3 0-1.2-.1-2.3-.1-2.3 0-3.9 1.4-3.9 4v2.2H7.8v3h2.6V21h3.1z"/></svg></a></li>
                <li><a href="#" aria-label="Instagram"><svg viewBox="0 0 24 24"><path d="M12 7.3A4.7 4.7 0 1 0 12 16.7 4.7 4.7 0 0 0 12 7.3zm0 7.7a3 3 0 1 1 0-6 3 3 0 0 1 0 6zm4.9-8.9a1.1 1.1 0 1 1 0 2.2 1.1 1.1 0 0 1 0-2.2zM20.9 8c-.1-1.5-.4-2.8-1.5-3.9S17 2.7 15.5 2.6C14 2.5 10 2.5 8.5 2.6 7 2.7 5.7 3 4.6 4.1S3.2 6.5 3.1 8c-.1 1.5-.1 6.5 0 8 .1 1.5.4 2.8 1.5 3.9s2.4 1.4 3.9 1.5c1.5.1 5.5.1 7 0 1.5-.1 2.8-.4 3.9-1.5s1.4-2.4 1.5-3.9c.1-1.5.1-6.5 0-8zm-2 9.6a3 3 0 0 1-1.7 1.7c-1.2.5-4 .4-5.2.4s-4 .1-5.2-.4a3 3 0 0 1-1.7-1.7C4.6 16.4 4.7 13.6 4.7 12s-.1-4.4.4-5.6a3 3 0 0 1 1.7-1.7C8 4.2 10.8 4.3 12 4.3s4-.1 5.2.4a3 3 0 0 1 1.7 1.7c.5 1.2.4 4 .4 5.6s.1 4.4-.4 5.6z"/></svg></a></li>
                <li><a href="#" aria-label="TikTok"><svg viewBox="0 0 24 24"><path d="M16.6 5.8A4.3 4.3 0 0 1 15.5 3h-3.1v12.4a2.6 2.6 0 1 1-1.8-2.5V9.7a5.7 5.7 0 1 0 4.9 5.7V9.1a7.4 7.4 0 0 0 4.3 1.4V7.4a4.3 4.3 0 0 1-3.2-1.6z"/></svg></a></li>
                <li><a href="#" aria-label="X"><svg viewBox="0 0 24 24"><path d="M17.8 3h3.1l-6.8 7.7L22 21h-6.2l-4.9-6.4L5.3 21H2.2l7.2-8.3L1.8 3h6.4l4.4 5.8L17.8 3zm-1.1 16.2h1.7L7.4 4.7H5.6l11.1 14.5z"/></svg></a></li>
            </ul>

            <?php /* Newsletter no guarda nada. */ ?>
            <form class="newsletter">
                <label for="newsletter-email">Newsletter</label>
                <div class="newsletter-fila">
                    <input type="email" id="newsletter-email" name="newsletter-email" placeholder="E-mail">
                    <button type="button">Suscríbete</button>
                </div>
            </form>
        </div>

    </div>

    <p class="pie-copy">2020-<?= date('Y') ?> Wäter. Todos los derechos reservados. Empresa ficticia creada para un trabajo de clase.</p>
</footer>

</body>
</html>